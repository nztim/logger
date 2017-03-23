<?php namespace NZTim\Logger;

use Illuminate\Support\ServiceProvider;
use Log;
use NZTim\Logger\Commands\AddMigration;

class LoggerServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/email', 'logger');
        $this->publishes([
            __DIR__.'/config/logger.php' => config_path('logger.php'),
        ]);
        if (config('logger.laravel', false)) {
            Log::listen(function () {
                $args = func_get_args();
                if (count($args) === 1) {
                    // Laravel 5.4 provides \Illuminate\Log\Events\MessageLogged object
                    $level = $args[0]->level;
                    $message = $args[0]->message;
                    $context = $args[0]->context;
                } else {
                    // Laravel <=5.3 provides individual arguments
                    $level = $args[0];
                    $message = $args[1];
                    $context = $args[2];
                }
                LoggerFacade::add('laravel', $level, $message, $context);
            });
        }
    }

    public function register()
    {
        $this->app->bind('logger', function () {
            return $this->app->make(Logger::class);
        });
        $this->mergeConfigFrom(__DIR__.'/config/logger.php', 'logger');
        $this->commands(AddMigration::class);
    }
}
