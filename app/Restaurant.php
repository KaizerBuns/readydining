<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
 	protected $table = 'restaurants';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];

	public function get_name() {
		return $this->name;
	}

	public static function get_all() {

		$results = Restaurant::all();

		$restaurants = [];
    	foreach($results as $r) {
    		$restaurants[$r->entity_id] = $r->toArray();
    	} 
    	
    	return $restaurants;
	}
}
