<?php namespace NZTim\Logger\Handlers;

use NZTim\Logger\Entry;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;

class FileHandler implements Handler
{
    /**
     * @param Entry $entry
     * @return null
     */
    public function write(Entry $entry)
    {
        $log = new MonologLogger($entry->getChannel());
        $log->pushHandler(new StreamHandler($this->getPath($entry->getChannel()), $entry->getCode()));
        $log->addRecord($entry->getCode(), $entry->getMessage(), $entry->getContext());
    }

    /**
     * @param string $channel
     * @return string
     */
    protected function getPath($channel)
    {
        $ds = DIRECTORY_SEPARATOR;
        $path = storage_path() . "{$ds}logs{$ds}custom";
        if (!file_exists($path)) {
            mkdir($path, 0755);
        }
        return "{$path}{$ds}{$channel}.log";
    }
}