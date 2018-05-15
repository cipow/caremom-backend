<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use App\User;
use Exception;

class UserController extends Controller
{
  protected $user;

  public function __construct() {
    $this->middleware('jwt-auth:user', ['except' => ['login']]);

    if (Input::get('token')) {
      try {
        $this->user = User::toObject(Input::get('token'));
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
      'phone' => 'required|max:20',
      'password' => 'required|min:6|min:32'
    ]);

    if ($user = User::where('phone', $req->phone)->first()) {
      if (Hash::check($req->password, $user->password)) {
        $payload = [
          'iss' => 'caremom',
          'iat' => time(),
          'exp' => time() + (((60 * 60) * 24) * 7),
          'sub' => $user->id,
          'aud' => 'user'
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
      'data' => $this->user->with('hospital')
    ]);
  }

  private function update(array $attribute) {
    try {
      $this->user->update($attribute);
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
      'address' => 'required|string'
    ]);
    return $this->update($req->only('name', 'city', 'address'));
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
    $path = 'images/users/';

    $filename = base64_encode('avatar-'.$this->user->id).'.'.$file->getClientOriginalExtension();
    $file->move($path, $filename);

    return $this->update(['avatar' => url('/'.$path.$filename)]);
  }

  public function hospitalList() {
    $hospitals = App\Hospital::with(['images', 'doctors.checkups'])->get();
    return response()->json($hospitals, 200);
  }

  public function doctorList() {
    return response()->json($this->user->hospital->doctors()->with('checkup'), 200);
  }


}
