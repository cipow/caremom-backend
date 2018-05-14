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


}
