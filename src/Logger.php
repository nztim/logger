<?php namespace NZTim\Logger;

use App;
use Auth;
use NZTim\Logger\Handlers\Handler;
use Exception;

class Logger
{
    protected $handlers = [];
    protected $request;

    public function __construct()
    {
        $this->handlers[] = App::make('NZTim\Logger\Handlers\FileHandler');
        $this->handlers[] = App::make('NZTim\Logger\Handlers\EmailHandler');
        $this->handlers[] = App::make('NZTim\Logger\Handlers\PapertrailHandler');
        $this->request = App::make('Illuminate\Http\Request');
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
        foreach ($this->handlers as $handler) {
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
        if(Auth::check()) {
            $info['userid'] = Auth::user()->id;
        }
        $input = $this->request->all();
        if(isset($input['password'])) {
            unset($input['password']);
        }
        if(isset($input['password_confirmation'])) {
            unset($input['password_confirmation']);
        }
        $info['input'] = $input;
        return $info;
    }
}
