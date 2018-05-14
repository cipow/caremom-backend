<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $fillable = [
        'name', 'phone', 'password', 'city', 'address', 'avatar'
    ];

    protected $hidden = [
        'password', 'created_at', 'updated_at'
    ];

    public function hospital() {
      return $this->belongsTo('App\Hospital', 'hospital_id', 'id');
    }

    public function checkups() {
      return $this->belongsToMany('App\Chekup', 'checkups_users', 'user_id', 'checkup_id');
    }
}
