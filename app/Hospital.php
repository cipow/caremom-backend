<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model {

  public function doctors() {
    return $this->hasMany('App\Doctor', 'hospital_id', 'id');
  }

  public function users() {
    return $this->hasMany('App\User', 'hospital_id', 'id');
  }

  public function articles() {
    return $this->hasMany('App\Article', 'hospital_id', 'id');
  }
}
