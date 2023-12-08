<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'sms_provider_id', 'sms_number', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function is_active() {
        if($this->status == 'pending') {
            return false;
        }
        return true;
    }

    public function is_admin() {
        if($this->is_admin == 0) {
            return false;
        }
        return true;
    }

    public function is_carrier_set() {
        if(!$this->sms_number) {
            return false;
        }

        if($this->sms_provider_id == 0) {
            return false;
        }

        return true;
    }

    public static function get_users() {
        $sql = "SELECT u.*, p.name as sms_provider FROM users u LEFT JOIN sms_providers p ON (p.id = u.sms_provider_id) GROUP BY u.id";
        return DB::select( DB::raw($sql), array());
    }
}
