<?php namespace NZTim\Logger\Handlers;

use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use NZTim\Logger\Entry;
use InvalidArgumentException;
use Monolog\Logger as MonologLogger;

class EmailHandler implements Handler
{
    const CACHE_KEY = 'logger-email';

    public function write(Entry $entry)
    {
        if (!config('logger.email.send') || Cache::has(static::CACHE_KEY) || !$this->isTriggered($entry)) {
            return;
        }
        $recipient = config('logger.email.recipient', false);
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('No email recipient supplied');
        }
        Mail::send('logger::notify', ['entry' => $entry], function (Message $message) use ($recipient) {
            $message->to($recipient)->subject('Log notification from ' . config('logger.email.name'));
        });
        Cache::put(static::CACHE_KEY, true, 10);
    }

    protected function isTriggered(Entry $entry)
    {
        $emailLevel = config('logger.email.level', false);
        if (!$emailLevel || config('app.debug')) {
            return false;
        }
        $emailLevelCode = MonologLogger::getLevels()[$emailLevel];
        return $entry->getCode() >= $emailLevelCode;
    }
}
