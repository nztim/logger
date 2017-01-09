<?php namespace NZTim\Logger;

use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Illuminate\Mail\Mailer;
use Illuminate\Support\ServiceProvider;
use Log;
use Logger;
use NZTim\Logger\Handlers\EmailHandler;

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
        $this->app->bind(EmailHandler::class, function () {
            $mailer = $this->app->make(Mailer::class);
            $cache = $this->app->make(CacheManager::class);
            $config = $this->app->make(Repository::class);
            return new EmailHandler($mailer, $cache, $config);
        });
        $this->app->bind('logger', function () {
            return $this->app->make(\NZTim\Logger\Logger::class);
        });
    }
}
