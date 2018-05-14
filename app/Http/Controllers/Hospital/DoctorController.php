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
    
  }
}
