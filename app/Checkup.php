<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Checkup extends Model {

  public function doctor() {
    return $this->belongsTo('App\Doctor', 'doctor_id', 'id');
  }

  public function users() {
    return $this->belongsToMany('App\User', 'checkups_users', 'checkup_id', 'user_id');
  }
}
