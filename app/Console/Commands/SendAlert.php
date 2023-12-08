<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Restaurant;
use App\Queue;
use App\QueueResult;
use App\Provider;
use DB;
use Clickatell\Rest;
use Clickatell\ClickatellException;

class SendAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SendAlert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends dining queue alerts';

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
        $clickatell = new \Clickatell\Rest('OIoYeouGRReqvKCmaadJYg==');
        $this->info("Sending alerts ... @ ".date("Y-m-d H:i:s"));

        $sql = "SELECT 
                    q.id, 
                    r.name as restaurant_name,
                    q.date,
                    q.time,
                    q.size,
                    q.availability,
                    q.description,
                    u.name username,
                    u.email,
                    u.sms_number,
                    u.sms_provider_id 
                FROM queue q 
                INNER JOIN restaurants r ON (r.entity_id = q.entity_id) 
                INNER JOIN users u ON (u.id = q.user_id)
                WHERE q.success = 1 AND q.alert = 0 AND q.deleted = 0 
                GROUP BY q.id   
                ORDER BY q.availability DESC, q.date ASC";

        $count=0;
        $queues = DB::select( DB::raw($sql), array());
        foreach($queues as $q) {

            $this->info("Sending alert for {$q->restaurant_name} ($q->availability) to $q->username ($q->sms_number)");

            $carrier = Provider::get_carrier_by_id($q->sms_provider_id);
            $data = array( 
                'message' => "$q->description - $q->restaurant_name ($q->date $q->availability) for party of {$q->size}",
                'sms' => str_replace("10digitphonenumber", $q->sms_number, $carrier->email),
                'email' => $q->email
            );
            
            try {
                $results = $clickatell->sendMessage(['to' => ['+1'.$q->sms_number], 'content' => $data['message']]);
                if(count($results) == 0) {
                     \Mail::raw($data['message'], function ($message) use ($data) {
                        $message->from('readydining@gmail.com', '');
                        $message->subject('DiningAlert');
                        $message->priority(1);
                        $message->to($data['sms'])->cc($data['email']);
                    });
                }

                $que = Queue::find($q->id);
                $que->alert = 1;
                $que->alerted_at = date("Y-m-d H:i:s");
                $que->save();
                $count++;

            } catch (ClickatellException $e) {
                \Mail::raw($e->message(), function () {
                    $message->from('readydining@gmail.com', '');
                    $message->subject('ReadyDining - Clickatell Error');
                    $message->priority(1);
                    $message->to('cgonzalez1891@gmail.com');
                });
            }
        }

        $this->info("Done ... {$count} alerts sent ... @ ". date("Y-m-d H:i:s"));
    }
}
