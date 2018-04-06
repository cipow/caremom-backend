<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Article extends Model {

  public function hospital() {
    return $this->belongsTo('App\Hospital', 'hospital_id', 'id');
  }
}
