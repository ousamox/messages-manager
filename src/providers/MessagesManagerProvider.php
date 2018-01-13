<?php

namespace Ousamox\MessagesManager\Providers;

use Illuminate\Support\ServiceProvider;
use Ousamox\MessagesManager\Services\OMessage;

class MessagesManagerProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish it with : php artisan vendor:publish --tag=omm
        $this->publishes([
            __DIR__ . '/../migrations' => database_path('migrations')
        ], 'omm');

        $this->publishes([
            __DIR__ . '/../config' => config_path(),
        ],'omm');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
//        include __DIR__.'/../routes.php';
//        $this->app->make('Ousamox\MessagesManager\Controllers\MessagesController');
        // -- Merge the package configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/omm.php', 'omm'
        );
        // -- Bind Service 'OMessage' with the Facade
        $this->app->bind('omm-message', function() {
            return new OMessage();
        });
    }
}
