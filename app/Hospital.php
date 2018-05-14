<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model {
  protected $fillable = ['name', 'email', 'password', 'city', 'address', 'telephone', 'lat', 'lng', 'logo'];
  protected $hidden = ['password', 'created_at', 'updated_at'];

  public static function toObject($token) {
    $decode = \Firebase\JWT\JWT::decode($token, env('JWT_SECRET'), ['HS256']);
    return Hospital::findOrFail($decode->sub);
  }

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
