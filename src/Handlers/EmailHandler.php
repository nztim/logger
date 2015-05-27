<?php namespace NZTim\Logger\Handlers;

use NZTim\Logger\Entry;
use InvalidArgumentException;
use Illuminate\Mail\Mailer;

class EmailHandler implements Handler
{
    protected $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param Entry $entry
     * @return null
     */
    public function write(Entry $entry)
    {
        if(!$entry->isTriggered(env('LOGGER_EMAIL_LEVEL', false))) {
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
    }
}