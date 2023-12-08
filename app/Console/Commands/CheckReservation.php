<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use App\Restaurant;
use App\Queue;
use App\QueueResult;
use DB;

class CheckReservation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CheckReservation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks restaurant availability.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //not found
        $notfound = [];

        //Get Queue List
        $this->info("Checking active queues ...");
        $queues = Queue::get_active();
        if(count($queues) == 0) {
            $this->info("No active queues found");
            exit;
        }

        //Check Queue Results Cache
        $this->info("Checking results cache ...");
        $cache_results = array();
        foreach($queues as $queue)
        {
            if($queue->date < date("Y-m-d")) {
                $this->info("Dining reservations is expired - disabled.");
                $sql = "UPDATE queue SET updated_at = '".date("Y-m-d H:i:s")."', availability = 'Date Expired - Please delete', error = 1, success = 0 WHERE date = '{$queue->date}'";
                DB::statement($sql, array());
            }

            $queue_cache = QueueResult::get_cache($queue->date, $queue->time, $queue->size);
            foreach($queue_cache as $qc) {
                $data = array(
                        'entity_id' => $qc->entity_id,
                        'date' => $qc->date,
                        'time' => $qc->time,
                        'size' => $qc->size,
                        'availability' => $qc->availability
                    );

                $cache_results[] = $data;
            }
        }

        $this->update_queue($cache_results, false);

        //Get Remaining Queue List
        $this->info("Checking active queues ...");
        $queues = Queue::get_active();
        if(count($queues) == 0) {
            $this->info("No active queues found");
            exit;
        }

        $this->info("Checking dining reservations system ...");

        //Get Dining List
        $restaurants = Restaurant::get_all();

        //Http
        $client = new Client(['cookies' => true]);

        //Get Website
        $response = $client->request('GET', 'https://disneyworld.disney.go.com/dining/');
        $results = (string)$response->getBody();

        //Get Token
        $response = $client->request('GET', 'https://disneyworld.disney.go.com/authentication/get-client-token/');
        $results = json_decode((string)$response->getBody());
        $token = $results->access_token;
        $this->info("Token: {$token} ...");

        foreach($queues as $queue) {

            $date = $queue->date;
            $time = $queue->time;
            $size = $queue->size;

            $this->info("Searching date: {$date} time: {$time} party size: {$size}");
            $dining_url = 'https://disneyworld.disney.go.com/api/wdpro/explorer-service/public/finder/dining-availability/80007798;entityType=destination?';
            $dining_params = "searchDate={$date}&partySize={$size}&searchTime={$time}";

            try {
                $response = $client->request('GET', $dining_url.$dining_params, ['headers' => [
                    'Authorization' => "BEARER {$token}"
                ]]);
            } catch (RequestException $e) {

            }

            $body = str_replace(array("00006000", "00004000","00000000", "00000838"), "", $response->getBody());
            $body = preg_replace('/\s\s+/', ' ', $body);

            preg_match_all("/ 0000(.*)? /Usi", $body, $matches);
            if(count($matches[0]) > 0) {
                $body = str_replace($matches[0], "", $body);
            }

            $results = json_decode($body, true);
            if(count($results) == 0) {
                $this->error("Error with results - json_decode (failed)");
                continue;
            }

            $dining_results = array();
            foreach($results['availability'] as $key => $availability)
            {
                $tmp = explode(";", $key);
                $entity_id = str_replace(' ', '',$tmp[0]);

                if(!isset($restaurants[$entity_id])) {
                    //$this->info("Not found - $entity_id");
                    $notfound[$entity_id] = $entity_id;
                }

                //'name' => $restaurants[$entity_id]['name'],
                //'url' => $restaurants[$entity_id]['url'],
                $data = array(
                    'entity_id' => $entity_id,
                    'date' => $date,
                    'time' => $time,
                    'size' => $size,
                    'availability' => 'pending',
                    'expires_at' => date("Y-m-d H:i:s", strtotime("+60 minutes"))
                );

                if(isset($availability['availableTimes'][0]['offers'])) {
                    $times = array();
                    foreach($availability['availableTimes'][0]['offers'] as $offer) {
                        $offer = $this->fixArrayKey($offer);
                        if(isset($offer['dateTime'])) {
                            $dt = new \DateTime(str_replace(' ', '', $offer['dateTime']));
                            $times[] = $dt->format('h:i A');
                        } else {
                            print_r($offer);
                        }
                    }

                    if(count($times)) {
                        sort($times);
                        $data['availability'] = implode(", ", $times);
                    }
                } else {
                    continue;
                }

                $dining_results[$entity_id] = $data;
            }

            //Update queue with the results
            $this->update_queue($dining_results);
        }

        if(count($notfound)) {
            $this->info("Missing Events");
            foreach($notfound as $entity_id) {
                $this->info(" --> $entity_id");
            }

            /*
            $data = array(
                'message' => "Missing the following Diving Events:<br>".implode('<br>', $notfound),
                'email' => 'cgonzalez1891@gmail.com'
            );

            \Mail::raw($data['message'], function ($message) use ($data) {
                $message->from('readydining@gmail.com', '');
                $message->subject('ReadyDining: Missing Dining Events');
                $message->priority(1);
                $message->to($data['email']);
            });
            */
        }

        $this->info("Complete");
    }

    public function update_queue($results, $cache = true)
    {
        if(count($results) == 0) {
            $this->info("Nothing to update ...");
            return;
        }

        if($cache) {
            //clean old cache before inserting
            QueueResult::clean_cache();
            DB::table('queue_results')->insert($results);
        }

        foreach($results as $entity_id => $data) 
        {
            $success = ($data['availability'] == 'pending' ? 0 : 1);
            $sql = "UPDATE queue SET
                    success = {$success},
                    availability = '{$data['availability']}',
                    updated_at = '".date("Y-m-d H:i:s")."'
                    WHERE entity_id = '{$data['entity_id']}'
                    AND date = '{$data['date']}'
                    AND time = '{$data['time']}'
                    AND size = '{$data['size']}'";

            DB::statement($sql, array());
        }

        DB::statement("UPDATE queue SET updated_at = '".date("Y-m-d H:i:s")."' WHERE deleted = 0 and alert = 0", array());
        $this->info("Updating queue with results ...");
    }

    public function fixArrayKey($data)
    {
        $new_data = array();
        foreach($data as $key => $value) {
            $new_data[str_replace(" ", "", $key)] = $value;
        }
        return $new_data;
    }
}