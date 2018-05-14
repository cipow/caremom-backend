<?php

namespace App\Http\Controllers\Hospital;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use App\Hospital;
use JWT;
use Exception;

class UserController extends Controller {
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
      'phone' => 'required|string|unique:users|max:20',
      'password' => 'required|min:6|max:32'
    ]);

    try {
      $this->hospital->users()->create([
        'phone' => $req->phone,
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
    return response()->json($this->hospital->users, 200);
  }

  public function get($id) {
    try {
      return response()->json($this->hospital->users()->findOrFail($id));
    } catch (Exception $e) {
      return response()->json([
        'success' => false
      ], 403);
    }

  }

  public function resetPassword($id) {
    try {
      $doctor = $this->hospital->users()->findOrFail($id);
      $pass = str_random(6);
      $doctor->update(['password' => Hash::make($pass)]);
      return response()->json([
        'success' => true,
        'password' => $pass
      ], 200);
    } catch (Exception $e) {
      if ($e instanceof Illuminate\Database\Eloquent\ModelNotFoundException) {
        return response()->json([
          'success' => false
        ], 403);
      }

      return response()->json([
        'success' => false,
        'message' => 'Internal Server Error'
      ], 500);
    }

  }

  public function delete($id) {
    try {
      $doctor = $this->hospital->users()->findOrFail($id);
      $doctor->delete();
      return response()->json([
        'success' => true
      ], 202);
    } catch (Exception $e) {
      if ($e instanceof Illuminate\Database\Eloquent\ModelNotFoundException) {
        return response()->json([
          'success' => false
        ], 403);
      }

      return response()->json([
        'success' => false,
        'message' => 'Internal Server Error'
      ], 500);
    }
  }


}
