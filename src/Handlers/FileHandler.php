<?php namespace NZTim\Logger\Handlers;

use Monolog\Handler\RotatingFileHandler;
use NZTim\Logger\Entry;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;

class FileHandler implements Handler
{
    public function write(Entry $entry)
    {
        $log = new MonologLogger($entry->channel());
        if (in_array($entry->channel(), config('logger.daily'))) {
            $log->pushHandler(
                new RotatingFileHandler($this->getPath($entry->channel()), config('logger.max_daily'), $entry->code())
            );
        } else {
            $log->pushHandler(new StreamHandler($this->getPath($entry->channel()), $entry->code()));
        }
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
