<?php namespace NZTim\Logger;

use App;
use Illuminate\Support\ServiceProvider;
use Log;
use Logger;

class LoggerServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/Email', 'logger');
        if(env('LOGGER_LOG', false)) {
            Log::listen(function($level, $message, $context)
            {
                Logger::add('laravel', $level, $message, $context);
            });
        }
    }

    public function register()
    {
        App::bind('logger', function() {
            return new \NZTim\Logger\Logger;
        });
    }
}
