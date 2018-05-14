<?php

namespace App\Http\Controllers\Hospital;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use App\Hospital, App\Doctor;
use JWT;
use Exception;

class DoctorController extends Controller {
  protected $hospital;

  public function __construct() {
    $this->middleware('jwt-auth:hospital');

    try {
      $this->hospital = Hospital::toObject(Input::get('token'));
    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Internal Server Error'
      ], 500);
    }

  }

  public function create(Request $req) {
    $this->validate($req, [
      'email' => 'required|email|unique:doctors|max:250',
      'password' => 'required|min:6|max:32'
    ]);

    try {
      $this->hospital->doctors()->create([
        'email' => $req->email,
        'password' => Hash::make($req->password)
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

  public function all() {
    return response()->json($this->hospital->doctors, 200);
  }

  public function get($id) {
    try {
      return response()->json($this->hospital->doctors()->findOrFail($id));
    } catch (Exception $e) {
      return response()->json([
        'success' => false
      ], 403);
    }

  }
}
