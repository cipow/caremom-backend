<?php

namespace App\Http\Controllers\Hospital;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use App\Hospital;
use JWT;
use Exception;

class HospitalController extends Controller
{
  protected $hospital;

  public function __construct() {
    $this->middleware('jwt-auth:hospital', [ 'except' => [
        'register',
        'login'
      ]]);

    if (Input::get('token')) {
      try {
        $this->hospital = Hospital::toObject(Input::get('token'));
      } catch (Exception $e) {
        return response()->json([
          'success' => false,
          'message' => 'Internal Server Error'
        ], 500);
      }
    }

  }

  public function register(Request $req) {
    $this->validate($req, [
      'name' => 'required|string|max:40',
      'email' => 'required|email|unique:hospitals|max:250',
      'password' => 'required|string|min:6|max:32',
      'city' => 'required|string|max:20',
      'address' => 'required|string',
      'telephone' => 'required|string|max:20'
    ]);

    try {
      $hospital = Hospital::create([
        'name' => $req->name,
        'email' => $req->email,
        'password' => Hash::make($req->password),
        'city' => $req->city,
        'address' => $req->address,
        'telephone' => $req->telephone
      ]);

      return response()->json([
        'success' => true
      ], 201);

    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Internal Server Error'
      ], 500);
    }

  }

  public function login(Request $req) {
    $this->validate($req, [
      'email' => 'required|email|max:250',
      'password' => 'required|string|min:6|max:32'
    ]);

    if ($hospital = Hospital::where('email', $req->email)->first()) {
      if (Hash::check($req->password, $hospital->password)) {
        $payload = [
          'iss' => 'caremom',
          'iat' => time(),
          'exp' => time() + (((60 * 60) * 24) * 7),
          'sub' => $hospital->id,
          'aud' => 'hospital'
        ];
        $token = JWT::encode($payload, env('JWT_SECRET'));

        return response()->json([
          'success' => true,
          'token' => $token
        ], 200);

      }
    }

    return response()->json([
      'success' => false,
      'message' => 'Invalid email or password'
    ], 400);
  }

  public function get() {
    return response()->json([
      'success' => true,
      'data' => $this->hospital
    ], 200);

  }

  private function update(array $attribute) {
    try {
      $this->hospital->update($attribute);
      return response()->json([
        'success' => true
      ], 200);
    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Internal Server Error'
      ], 500);
    }

  }

  public function profil(Request $req) {
    $this->validate($req, [
      'name' => 'required|string|max:40',
      'city' => 'required|string|max:20',
      'address' => 'required|string',
      'telephone' => 'required|string|max:20'
    ]);
    return $this->update($req->only('name', 'city', 'address', 'telephone'));
  }

  public function geolocation(Request $req) {
    $this->validate($req, [
      'latitude' => 'required|numeric',
      'longitude' => 'required|numeric'
    ]);
    return $this->update($req->only('latitude', 'longitude'));
  }

  public function password(Request $req) {
    $this->validate($req, [
      'old_password' => 'required|min:6|max:32',
      'password' => 'required|min:6|max:32',
      'password_confirmation' => 'required|min:6|max:32|same:password'
    ]);
    return $this->update(['password'=>Hash::make($req->password)]);
  }

  public function logo(Request $req) {
    $this->validate($req, [
      'logo' => 'required|image|mimes:jpg,jpeg,png'
    ]);

    $file = $req->file('logo');
    $path = 'images/hospitals/logos/';

    $filename = base64_encode('logo-'.$this->hospital->id).'.'.$file->getClientOriginalExtension();
    $file->move($path, $filename);

    return $this->update(['logo' => url('/'.$path.$filename)]);
  }

}
