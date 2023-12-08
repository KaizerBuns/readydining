<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Provider extends Model
{
 	protected $table = 'sms_providers';
	protected $fillable = [];
	protected $guarded = [];
	protected $hidden = [];	

	public static function get_carrier_by_id($id) {
		return Provider::find($id);
	}
}
