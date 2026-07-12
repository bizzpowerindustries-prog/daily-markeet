<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Messaging;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('firebase.factory', function ($app) {
            return (new Factory)
                ->withServiceAccount(config('firebase.credentials'));
        });

        $this->app->singleton(Auth::class, function ($app) {
            return $app->make('firebase.factory')->createAuth();
        });

        $this->app->singleton(Messaging::class, function ($app) {
            return $app->make('firebase.factory')->createMessaging();
        });
    }

    public function boot()
    {
        //
    }
}
