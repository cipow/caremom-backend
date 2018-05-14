<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model {

  protected $fillable = ['name', 'email', 'password', 'city', 'address', 'phone', 'avatar'];
  protected $hidden = ['password', 'created_at', 'updated_at'];

  public static function toObject($token) {
    $decode = \Firebase\JWT\JWT::decode($token, env('JWT_SECRET'), ['HS256']);
    return Doctor::findOrFail($decode->sub);
  }

  public function hospital() {
    return $this->belongsTo('App\Hospital', 'hospital_id', 'id');
  }

  public function checkpus() {
    return $this->hasMany('App\Checkup', 'doctor_id', 'id');
  }
}
