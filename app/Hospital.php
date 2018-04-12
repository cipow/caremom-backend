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

  public static function explodeHeader($header) {
    $explodeText = explode(".", $header);
    return (object) [
      'rule'      => $explodeText[0],
      'api_token' => $explodeText[1],
      'email'     => base64_decode($explodeText[2])
    ];
  }

  public static function getIdFromHeader($header) {
    $explodeText = Hospital::explodeHeader($header);
    $hospital = Hospital::where('email', $explodeText->email)
              ->where('api_token', $header)
              ->first();

    return $hospital->id;
  }
}
