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
        $filename = 'logs' . $ds;
        $filename .= config('logger.folder') ? config('logger.folder') . $ds : '';
        $filename = str_replace('.', $ds, $filename . $channel) . '.log';
        $filename = storage_path($filename);
        $folder = pathinfo($filename)['dirname'];
        if (!is_dir($folder) && !file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
        return $filename;
    }
}
