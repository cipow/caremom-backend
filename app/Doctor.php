<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model {

  public function hospital() {
    return $this->belongsTo('App\Hospital', 'hospital_id', 'id');
  }

  public function checkpus() {
    return $this->hasMany('App\Checkup', 'doctor_id', 'id');
  }
}
