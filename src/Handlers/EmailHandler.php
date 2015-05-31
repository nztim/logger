<?php namespace NZTim\Logger\Handlers;

use NZTim\Logger\Entry;
use InvalidArgumentException;
use Illuminate\Config\Repository;
use Illuminate\Mail\Mailer;
use Illuminate\Cache\CacheManager as Cache;
use Monolog\Logger as MonologLogger;

class EmailHandler implements Handler
{
    protected $mailer;
    protected $cache;
    protected $config;

    public function __construct(Mailer $mailer, Cache $cache, Repository $config)
    {
        $this->mailer = $mailer;
        /** @var Cache $cache */
        $this->cache = $cache;
        $this->config = $config;
    }

    /**
     * @param Entry $entry
     * @return null
     */
    public function write(Entry $entry)
    {
        if ($this->cache->has('logger-email') || !$this->isTriggered($entry)) {
            return;
        }
        $recipient = env('LOGGER_EMAIL_TO', false);
        if(!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('No email recipient supplied');
        }
        $this->mailer->send('logger::notify', compact('entry'), function($message) use ($recipient)
        {
            $message->to($recipient)
                ->subject('Log notification from ' . env('LOGGER_APP_NAME', 'Laravel'));
        });
        $this->cache->put('logger-email', true, 10);
    }

    protected function isTriggered(Entry $entry)
    {
        $emailLevel = env('LOGGER_EMAIL_LEVEL', false);
        if(!$emailLevel || $this->config->get('debug')) {
            return false;
        }
        $emailLevelCode = MonologLogger::getLevels()[$emailLevel];
        return $entry->getCode() >= $emailLevelCode;
    }
}