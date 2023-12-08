<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Queue extends Model
{
 	protected $table = 'queue';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];	

	public static function get_all(User $user) {
		$sql = "SELECT 
					q.id,
					q.entity_id,
					r.name,
					q.description,
					q.date,
					q.time,
					q.size,
					q.created_at,
					q.availability,
					q.alert,
					q.error,
					q.alerted_at,
					q.updated_at 
				FROM queue q 
				LEFT JOIN restaurants r ON (r.entity_id = q.entity_id) 
				WHERE user_id = :user_id AND deleted = 0 
				GROUP BY q.id   
				ORDER BY q.availability DESC, q.date ASC";

		$bind['user_id'] = $user->id;
		$results = DB::select( DB::raw($sql), $bind);

		if(count($results)) {
			return $results;
		}

		return array();
	}

	public static function get_active() {
		$sql = "SELECT date, time, size FROM queue WHERE success = 0 AND deleted = 0 AND alert = 0 AND error = 0 GROUP BY date, time, size";
		return DB::select( DB::raw($sql), array());
	}

	
}
