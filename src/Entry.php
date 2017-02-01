<?php namespace NZTim\Logger;

use InvalidArgumentException;
use Monolog\Logger as MonologLogger;

class Entry
{
    protected $channel;
    protected $level;
    protected $code;
    protected $message;
    protected $context;

    public function __construct(string $channel, string $level, string $message, array $context = [])
    {
        $this->channel = preg_replace("/[^a-zA-Z0-9\.]/", "", $channel); // Remove everything but a-z, A-Z and '.'
        if (strlen($this->channel) == 0) {
            throw new InvalidArgumentException('Invalid channel name: ' . $channel);
        }
        $this->level = trim(strtoupper($level));
        if (!isset(MonologLogger::getLevels()[$this->level])) {
            throw new InvalidArgumentException('Level must be a standard Monolog level');
        }
        $this->code = MonologLogger::getLevels()[$this->level];
        if (empty($message)) {
            throw new InvalidArgumentException('Log entry must contain a message');
        }
        $this->message = $message;
        $this->context = $context;
    }

    public function channel()
    {
        return $this->channel;
    }

    public function level()
    {
        return $this->level;
    }

    public function code()
    {
        return $this->code;
    }

    public function message()
    {
        return $this->message;
    }

    public function context()
    {
        return $this->context;
    }
}
