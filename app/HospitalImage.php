<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class HospitalImage extends Model {
  protected $table = 'hospital_images';

  public function hospital() {
    return $this->belongsTo('App\Hospital', 'hospital_id', 'id');
  }

}
