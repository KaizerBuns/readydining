<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class QueueResult extends Model
{
 	protected $table = 'queue_results';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];

	public static function get_cache($date, $time, $size) {
		$now = date("Y-m-d H:i:s");
		$sql = "SELECT * FROM queue_results WHERE date = '{$date}' AND time = '{$time}' AND size = '{$size}' AND '{$now}' < expires_at";
		return DB::select( DB::raw($sql), array());
	}

	public static function clean_cache() {
		$clean = date("Y-m-d H:i:s", strtotime("-2 hours"));
		$sql = "DELETE FROM queue_results WHERE expires_at < '{$clean}'";
		return DB::select( DB::raw($sql), array());
	}
}
