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
        $this->channel = substr(str_slug($channel), 0, 10);
        if (strlen($this->channel) == 0) {
            throw new InvalidArgumentException('Channel name must be set');
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

    public function getChannel()
    {
        return $this->channel;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getContext()
    {
        return $this->context;
    }
}
