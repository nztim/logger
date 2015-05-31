<?php namespace NZTim\Logger;

use App;
use Exception;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use NZTim\Logger\Handlers\Handler;

class Logger
{
    protected $handlers = [
        'NZTim\Logger\Handlers\FileHandler',
        'NZTim\Logger\Handlers\EmailHandler',
        'NZTim\Logger\Handlers\PapertrailHandler',
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

    /**
     * @param string $channel
     * @param string $message
     * @param array $context
     */
    public function info($channel, $message, $context = [])
    {
        $this->add($channel, 'INFO', $message, $context);
    }

    /**
     * @param string $channel
     * @param string $message
     * @param array $context
     */
    public function warning($channel, $message, $context = [])
    {
        $this->add($channel, 'WARNING', $message, $context);
    }

    /**
     * @param string $channel
     * @param string $message
     * @param array $context
     */
    public function error($channel, $message, $context = [])
    {
        $this->add($channel, 'ERROR', $message, $context);
    }

    /**
     * @param string $channel
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function add($channel, $level, $message, $context = [])
    {
        $entry = new Entry($channel, $level, $message, $context);
        foreach ($this->handlers as $handlerName) {
            $handler = $this->app->make($handlerName);
            try {
                /** @var Handler $handler */
                $handler->write($entry);
            } catch (Exception $e) {
                $this->writeExceptionMessage(get_class($handler), $e);
            }
        }
    }

    /**
     * @param string $handlerName
     */
    protected function writeExceptionMessage($handlerName, Exception $e)
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
