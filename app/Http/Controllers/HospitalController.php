<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Hospital;

class HospitalController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => [
            'login',
            'register'
          ]]);
    }

    public function login(Request $req) {
      $hospital = Hospital::where('email', $req->email)->first();

      if (empty($hospital)) {
        return response()->json([
          'status'  => 'error',
          'message' => 'email not registered'
        ], 400);
      }

      if (Hash::check($req->password, $hospital->password)) {
        $hospital->api_token = '1.'.base64_encode(str_random(30)).'.'.base64_encode($hospital->email);
        if ($hospital->save()) {
          return response()->json([
            'status'  => 'success',
            'message' => 'login success',
            'token'   => $hospital->api_token
          ], 200);
        } else {
          return response()->json([
            'status'  => 'error',
            'message' => 'Internal Server Error'
          ], 500);
        }

      } else {
        return response()->json([
          'status'  => 'error',
          'message' => 'invalid password'
        ], 400);
      }
    }

    public function register(Request $req) {
      $validator = Validator::make($req->all(), [
        'name'      => 'required|string',
        'email'     => 'required|email|unique:hospitals',
        'password'  => 'required|string|min:6|max:32',
        'address'   => 'required|string',
        'city'      => 'required|string|max:20',
        'telephone' => 'required|max:20'
      ]);

      if ($validator->fails()) {
        return response()->json([
          'status'  => 'error',
          'message' => $validator->errors()
        ], 400);
      }

      $hospital = new Hospital;
      $hospital->name = $req->name;
      $hospital->email = $req->email;
      $hospital->password = Hash::make($req->password);
      $hospital->address = $req->address;
      $hospital->city = $req->city;
      $hospital->telephone = $req->telephone;
      $hospital->api_token = '1.'.base64_encode(str_random(30)).'.'.base64_encode($hospital->email);

      if ($hospital->save()) {
        return response()->json([
          'status'  => 'success',
          'message' => 'new hospital created'
        ], 201);

      } else {
        return response()->json([
          'status'  => 'error',
          'message' => 'Internal Server Error'
        ], 500);
      }
    }

    public function getProfil(Request $req) {
      $id = Hospital::getIdFromHeader($req->header('Authorization'));
      $hospital = Hospital::find($id);

      return response()->json([
        'status'  => 'success',
        'data'    => $hospital
      ], 200);
    }
}
