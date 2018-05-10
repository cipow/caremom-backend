<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model {

  protected $hidden = ['password', 'api_token', 'created_at', 'updated_at'];

  public function doctors() {
    return $this->hasMany('App\Doctor', 'hospital_id', 'id');
  }

  public function users() {
    return $this->hasMany('App\User', 'hospital_id', 'id');
  }

  public function articles() {
    return $this->hasMany('App\Article', 'hospital_id', 'id');
  }

  public function images() {
    return $this->hasMany('App\HospitalImage', 'hospital_id', 'id');
  }
}
