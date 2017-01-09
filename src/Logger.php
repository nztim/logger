<?php namespace NZTim\Logger;

use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use NZTim\Logger\Handlers\EmailHandler;
use NZTim\Logger\Handlers\FileHandler;
use NZTim\Logger\Handlers\Handler;
use NZTim\Logger\Handlers\PapertrailHandler;
use Throwable;

class Logger
{
    protected $handlers = [
        FileHandler::class,
        EmailHandler::class,
        PapertrailHandler::class,
    ];

    protected $app;
    protected $request;
    protected $authManager;

    public function __construct(
        Application $app,
        Request $request,
        AuthManager $authManager)
    {
        $this->app = $app;
        $this->request = $request;
        $this->authManager = $authManager;
    }

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
            $handler = $this->app->make($handlerName); /** @var Handler $handler */
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

    public function requestInfo()
    {
        $info = [];
        $info['ip'] = $this->request->getClientIp();
        $info['method'] = $this->request->server('REQUEST_METHOD');
        $info['url'] = $this->request->url();
        if($this->authManager->check()) {
            $info['userid'] = $this->authManager->user()->id;
        }
        $input = $this->request->all();
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
