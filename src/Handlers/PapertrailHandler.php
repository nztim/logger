<?php namespace NZTim\Logger\Handlers;

use NZTim\Logger\Entry;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\SyslogUdpHandler;

class PapertrailHandler implements Handler
{

    /**
     * @param Entry $entry
     * @return null
     */
    public function write(Entry $entry)
    {
        if(!$entry->isTriggered(env('LOGGER_PAPERTRAIL_LEVEL', false))) {
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
}