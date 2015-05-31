<?php namespace NZTim\Logger\Handlers;

use Illuminate\Config\Repository;
use NZTim\Logger\Entry;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\SyslogUdpHandler;

class PapertrailHandler implements Handler
{
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function write(Entry $entry)
    {
        if (!$this->isTriggered($entry)) {
            return;
        }
        $output = "%channel%.%level_name%: %message%";
        $formatter = new LineFormatter($output);
        $name = env('LOGGER_APP_NAME') . '-' . $entry->getChannel();
        $log = new MonologLogger($name);
        $syslogHandler = new SyslogUdpHandler(env('LOGGER_PAPERTRAIL_HOST'), env('LOGGER_PAPERTRAIL_PORT'));
        $syslogHandler->setFormatter($formatter);
        $log->pushHandler($syslogHandler);
        $log->addRecord($entry->getCode(), $entry->getMessage(), $entry->getContext());
    }

    protected function isTriggered(Entry $entry)
    {
        $papertrailLevel = env('LOGGER_PAPERTRAIL_LEVEL', false);
        if (!$papertrailLevel || $this->config->get('debug')) {
            return false;
        }
        $papertrailLevelCode = MonologLogger::getLevels()[$papertrailLevel];
        return $entry->getCode() >= $papertrailLevelCode;
    }
}