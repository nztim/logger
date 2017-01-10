<?php namespace NZTim\Logger;

use Illuminate\Support\Facades\Auth;
use NZTim\Logger\Handlers\DatabaseHandler;
use NZTim\Logger\Handlers\EmailHandler;
use NZTim\Logger\Handlers\FileHandler;
use NZTim\Logger\Handlers\Handler;
use Throwable;

class Logger
{
    protected $handlers = [
        FileHandler::class,
        EmailHandler::class,
        DatabaseHandler::class,
    ];

    public function info(string $channel, string $message, array $context = [])
    {
        $this->add($channel, 'INFO', $message, $context);
    }

    public function warning(string $channel, string $message, array $context = [])
    {
        $this->add($channel, 'WARNING', $message, $context);
    }

    public function error(string $channel, string $message, array $context = [])
    {
        $this->add($channel, 'ERROR', $message, $context);
    }

    public function add(string $channel, string $level, string $message, array $context = [])
    {
        $entry = new Entry($channel, $level, $message, $context);
        foreach ($this->handlers as $handlerName) {
            $handler = app($handlerName); /** @var Handler $handler */
            try {
                $handler->write($entry);
            } catch (Throwable $e) {
                $this->writeExceptionMessage(get_class($handler), $e);
            }
        }
    }

    protected function writeExceptionMessage(string $handlerName, Throwable $e)
    {
        $message = date('c')." Exception processing handler {$handlerName}: {$e->getMessage()}\n";
        $ds = DIRECTORY_SEPARATOR;
        $filename = storage_path() . "{$ds}logs{$ds}fatal-logger-errors.log";
        file_put_contents($filename, $message, FILE_APPEND);
    }

    public function requestInfo(): array
    {
        $info = [];
        $info['ip'] = request()->getClientIp();
        $info['method'] = request()->server('REQUEST_METHOD');
        $info['url'] = request()->url();
        if (Auth::check()) {
            $info['userid'] = Auth::user()->id;
        }
        $input = request()->all();
        $remove = ['password', 'password_confirmation', '_token'];
        foreach ($remove as $item) {
            if (isset($input[$item])) {
                unset($input[$item]);
            }
        }
        $info['input'] = $input;
        return $info;
    }
}
