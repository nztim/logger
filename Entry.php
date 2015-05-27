<?php namespace NZTim\Logger;

use Config;
use InvalidArgumentException;
use Monolog\Logger as MonologLogger;

class Entry
{
    protected $channel;
    protected $level;
    protected $code;
    protected $message;
    protected $context;

    public function __construct($channel, $level, $message, $context = [])
    {
        $this->channel = substr(str_slug($channel), 0, 10);
        $this->level = strtoupper($level);
        $this->code = MonologLogger::getLevels()[$this->level];
        if(empty($message)) {
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

    /**
     * @param string|boolean $triggerLevel
     * @return boolean
     */
    public function isTriggered($triggerLevel)
    {
        if(Config::get('app.debug') || $triggerLevel == false) {
            return false;
        }
        if(!isset(MonologLogger::getLevels()[$triggerLevel])) {
            throw new InvalidArgumentException('Invalid trigger level supplied to Entry::isTriggered()');
        }
        $triggerCode = MonologLogger::getLevels()[$triggerLevel];
        return $this->code >= $triggerCode;
    }
}