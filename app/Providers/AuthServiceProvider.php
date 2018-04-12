<?php

namespace App\Providers;

use App\User, App\Hospital;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            // if ($request->input('api_token')) {
            //     return User::where('api_token', $request->input('api_token'))->first();
            // }

            if ($request->header('Authorization')) {
              $explodeText = Hospital::explodeHeader($request->header('Authorization'));
              $user = null;
              switch ($explodeText->rule) {
                case '1':
                  $user = Hospital::where([
                    ['email', '=', $explodeText->email],
                    ['api_token', '=', $request->header('Authorization')]
                  ])->first();
                  break;

                case '2':
                  # code...
                  break;

                case '3':
                  # code...
                  break;

              }

              if(empty($user)) {
                return null;
              }

              return $user;
            }

            return null;
        });
    }
}
