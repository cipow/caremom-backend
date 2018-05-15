<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use App\Doctor;
use Exception;

class ExampleController extends Controller
{
  protected $doctor;

  public function __construct() {
    $this->middleware('jwt-auth:doctor', ['except' => ['login']]);

    if (Input::get('token')) {
      try {
        $this->doctor = Doctor::toObject(Input::get('token'));
      } catch (Exception $e) {
        return response()->json([
          'success' => false,
          'message' => 'Internal Server Error'
        ], 500);
      }

    }
  }

  public function login(Request $req) {
    $this->validate($req, [
      'email' => 'required|email|max:250',
      'password' => 'required|min:6|min:32'
    ]);

    if ($doctor = Doctor::where('email', $req->email)->first()) {
      if (Hash::check($req->password, $doctor->password)) {
        $payload = [
          'iss' => 'caremom',
          'iat' => time(),
          'exp' => time() + (((60 * 60) * 24) * 7),
          'sub' => $doctor->id,
          'aud' => 'doctor'
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
      'data' => $this->doctor
    ]);
  }

  private function update(array $attribute) {
    try {
      $this->doctor->update($attribute);
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
      'phone' => 'required|string|max:20'
    ]);
    return $this->update($req->only('name', 'city', 'address', 'phone'));
  }

  public function password(Request $req) {
    $this->validate($req, [
      'old_password' => 'required|min:6|max:32',
      'password' => 'required|min:6|max:32',
      'password_confirmation' => 'required|min:6|max:32|same:password'
    ]);
    return $this->update(['password'=>Hash::make($req->password)]);
  }

  public function avatar(Request $req) {
    $this->validate($req, [
      'avatar' => 'required|image|mimes:jpg,jpeg,png'
    ]);

    $file = $req->file('avatar');
    $path = 'images/doctors/';

    $filename = base64_encode('avatar-'.$this->doctor->id).'.'.$file->getClientOriginalExtension();
    $file->move($path, $filename);

    return $this->update(['avatar' => url('/'.$path.$filename)]);
  }

  public function createCheckup(Request $req) {
    $this->validate($req, [
      'day' => 'required|string|max:6',
      'time' => 'required|string'
    ]);

    try {
      $this->doctor->checkups()->create($req->only('day', 'time'));
      return response()->json([
        'success' => true
      ], 201);
    } catch (Exception $e) {
      return response()->json([
        'success' => false
      ], 400);
    }

  }

  public function allCheckup() {
    return response()->json($this->doctor->checkups, 200);
  }

  private function getCheckup($id) {
    try {
      return $this->hospital->checkups()->findOrFail($id);
    } catch (Exception $e) {
      return response()->json([
        'success' => false
      ], 403);
    }
  }

  public function viewCheckup($id) {
    return response()->json($this->getCheckup($id), 200);
  }

  public function updateCheckup(Request $req, $id) {
    $this->validate($req, [
      'day' => 'required|string|max:6',
      'time' => 'required|string'
    ]);

    $checkup = $this->getCheckup($id);

    try {
      $checkup->update($req->only('day', 'time'));
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

  public function deleteCheckup($id) {
    try {
      $checkup = $this->getCheckup($id);
      $checkup->delete();
      return response()->json([
        'success' => true
      ], 202);
    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Internal Server Error'
      ], 500);
    }
  }



}
