<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Restaurant;

class GetDiningList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:GetDiningList';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add/updates restaurant and events list.';

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
    	$restaurants = Restaurant::get_all();
     	
     	$contents = \File::get(base_path("/list.csv"));
     	$contents = explode("\n", $contents);

     	foreach($contents as $content) 
     	{
     		$content = trim(preg_replace('/\s+/', ' ', $content));
			$content = str_replace(array("\n", "\r", "\n\r"), '', $content);

     		//match entity id and type
     		preg_match("/data-entityID=\"(.*)?\">/Usi", $content, $matches);

     		if(isset($matches[1]) && $matches[1]) {
     			list($entity_id, $type) = explode(";", $matches[1]);
     			$type = str_replace("entityType=","",$type);
     		}

     		//match url
			preg_match("/data-href=\"(.*)?\"/Usi", $content, $matches);

			$url = "";
			if(isset($matches[1]) && $matches[1]) {
				$url = $matches[1];
			}

			//match name
			preg_match("/aria-label=\"(.*)?\"/Usi", $content, $matches);
			$name = $matches[1];      

			$data = array(
				'entity_id' => $entity_id,
				'type' => $type,
				'name' => htmlspecialchars_decode(urldecode($name)),
				'url' => $url
			);
    
			if(!isset($restaurants[$entity_id])) {
				$this->info("Adding new event {$entity_id} - {$data['name']}");
				$res = new Restaurant();
				$res->fill($data);
				$res->save();	
			}
     	}
    }
}
