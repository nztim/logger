<?php namespace NZTim\Logger\Handlers;

use NZTim\Logger\Entry;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;

class FileHandler implements Handler
{
    public function write(Entry $entry)
    {
        $log = new MonologLogger($entry->channel());
        $log->pushHandler(new StreamHandler($this->getPath($entry->channel()), $entry->code()));
        $log->addRecord($entry->code(), $entry->message(), $entry->context());
    }

    protected function getPath(string $channel): string
    {
        $ds = DIRECTORY_SEPARATOR;
        $path = storage_path() . "{$ds}logs{$ds}custom";
        if (!is_dir($path) && !file_exists($path)) {
            mkdir($path, 0755);
        }
        return "{$path}{$ds}{$channel}.log";
    }
}
