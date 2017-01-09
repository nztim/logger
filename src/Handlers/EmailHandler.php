<?php namespace NZTim\Logger\Handlers;

use Illuminate\Mail\Message;
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
        $this->cache = $cache; /** @var Cache $cache */
        $this->config = $config;
    }

    public function write(Entry $entry)
    {
        if ($this->cache->has('logger-email') || !$this->isTriggered($entry)) {
            return;
        }
        $recipient = config('logger.email.recipient', false);
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('No email recipient supplied');
        }
        $this->mailer->send('logger::notify', ['entry' => $entry], function (Message $message) use ($recipient) {
            $message->to($recipient)->subject('Log notification from ' . config('logger.email.name'));
        });
        $this->cache->put('logger-email', true, 10);
    }

    protected function isTriggered(Entry $entry)
    {
        $emailLevel = config('logger.email.level', false);
        if (!$emailLevel || $this->config->get('app.debug')) {
            return false;
        }
        $emailLevelCode = MonologLogger::getLevels()[$emailLevel];
        return $entry->getCode() >= $emailLevelCode;
    }
}
