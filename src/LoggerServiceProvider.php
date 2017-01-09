<?php namespace NZTim\Logger;

use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Illuminate\Mail\Mailer;
use Illuminate\Support\ServiceProvider;
use Log;
use NZTim\Logger\Handlers\EmailHandler;

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
            Log::listen(function ($level, $message, $context) {
                LoggerFacade::add('laravel', $level, $message, $context);
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
            return $this->app->make(Logger::class);
        });
        $this->mergeConfigFrom(
            __DIR__.'/config/logger.php', 'logger'
        );
    }
}
