<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => '/api'], function($router) {

  $router->group(['prefix' => '/hospital'], function($router) {
    $router->get('/', 'Hospital\HospitalController@get');
    $router->put('/', 'Hospital\HospitalController@profil');
    $router->put('/geo', 'Hospital\HospitalController@geolocation');
    $router->put('/password', 'Hospital\HospitalController@password');
    $router->post('/logo', 'Hospital\HospitalController@logo');
    $router->post('/register', 'Hospital\HospitalController@register');
    $router->post('/login', 'Hospital\HospitalController@login');

    $router->group(['prefix' => '/doctor'], function($router) {
      $router->get('/', 'Hospital\DoctorController@all');
      $router->get('/{id}', 'Hospital\DoctorController@get');
      $router->get('/{id}/reset', 'Hospital\DoctorController@resetPassword');
      $router->delete('/{id}/delete', 'Hospital\DoctorController@delete');
      $router->post('/create', 'Hospital\DoctorController@create');
    });

    $router->group(['prefix' => '/user'], function($router) {
      $router->get('/', 'Hospital\UserController@all');
      $router->get('/{id}', 'Hospital\UserController@get');
      $router->get('/{id}/reset', 'Hospital\UserController@resetPassword');
      $router->delete('/{id}/delete', 'Hospital\UserController@delete');
      $router->post('/create', 'Hospital\UserController@create');
    });

  });

  $router->group(['prefix' => '/doctor'], function($router) {
    $router->get('/', 'DoctorController@get');
    $router->put('/', 'DoctorController@profil');
    $router->put('/password', 'DoctorController@password');
    $router->post('/avatar', 'DoctorController@avatar');
    $router->post('/login', 'DoctorController@login');

    $router->group(['prefix' => '/checkup'], function($router) {
      $router->get('/', 'DoctorController@allCheckup');
      $router->get('/{id}', 'DoctorController@viewCheckup');
      $router->put('/{id}', 'DoctorController@updateCheckup');
      $router->delete('/{id}', 'DoctorController@deleteCheckup');
      $router->post('/create', 'DoctorController@createCheckup');
    });
  });

});
