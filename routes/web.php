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
      $router->post('/create', 'Hospital\DoctorController@create');
    });
  });

});
