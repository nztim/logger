<?php namespace NZTim\Logger;

use Illuminate\Support\ServiceProvider;
use Log;
use Logger;
use NZTim\Logger\Handlers\EmailHandler;
use NZTim\Logger\Handlers\PapertrailHandler;

class LoggerServiceProvider extends ServiceProvider
{

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
        $this->app->bind('NZTim\Logger\Handlers\EmailHandler', function() {
            $mailer = $this->app->make('Illuminate\Mail\Mailer');
            $cache = $this->app->make('Illuminate\Cache\CacheManager');
            $config = $this->app->make('Illuminate\Config\Repository');
            return new EmailHandler($mailer, $cache, $config);
        });
        $this->app->bind('NZTim\Logger\Handlers\PapertrailHandler', function() {
            $config = $this->app->make('Illuminate\Config\Repository');
            return new PapertrailHandler($config);
        });
        $this->app->bind('logger', function() {
            return $this->app->make('NZTim\Logger\Logger');
        });
    }
}
