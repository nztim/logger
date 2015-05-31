<?php namespace NZTim\Logger;

use Exception;
use Mockery;
use PHPUnit_Framework_TestCase;

class LoggerTest extends PHPUnit_Framework_TestCase
{
    protected $app;
    protected $request;
    protected $authManager;
    protected $config;
    protected $fileHandler;
    protected $emailHandler;
    protected $papertrailHandler;

    /**
     * @test
     */
    public function isInstantiable()
    {
        $this->assertTrue(class_exists('NZTim\Logger\Logger'));
    }

    /**
     * @test
     */
    public function testAddSuccessful()
    {
        $logger = $this->instantiateWithMocks();
        $logger->add('test', 'ERROR', 'Test message', ['abc' => 123]);
    }

    protected $helpers = ['info', 'warning', 'error'];
    /**
     * @test
     */
    public function testHelpers()
    {
        foreach($this->helpers as $helper) {
            $logger = $this->instantiateWithMocks();
            $this->fileHandler->shouldReceive('write')->once();
            $this->emailHandler->shouldReceive('write')->once();
            $this->papertrailHandler->shouldReceive('write')->once();
            $logger->$helper('test', 'Test message');
        }
    }

    public function testAddWithException()
    {
        $logger = $this->instantiateWithMocks();
        $this->fileHandler->shouldReceive('write')->once();
        $this->emailHandler->shouldReceive('write')->once();
        $this->papertrailHandler->shouldReceive('write')->andThrow(new Exception);
        $logger->add('test', 'ERROR', 'Test message', ['abc' => 123]);
    }

    public function testRequestInfo()
    {
        $logger = $this->instantiateWithMocks();
        $this->request->shouldReceive('getClientIp', 'server', 'url', 'all')->once()->andReturn('test');
        $this->authManager->shouldReceive('check')->once()->andReturn(false);
        $output = $logger->requestInfo();
        $this->assertEquals(['ip' => 'test', 'method' => 'test', 'url' => 'test', 'input' => 'test'], $output);
    }

    protected function instantiateWithMocks()
    {
        $this->fileHandler = Mockery::mock('NZTim\Logger\Handlers\Handler');
        $this->emailHandler = Mockery::mock('NZTim\Logger\Handlers\Handler');
        $this->papertrailHandler = Mockery::mock('NZTim\Logger\Handlers\Handler');
        $this->app = Mockery::mock('Illuminate\Foundation\Application');
        $this->app->shouldReceive('make')->with('NZTim\Logger\Handlers\FileHandler')->andReturn($this->fileHandler);
        $this->app->shouldReceive('make')->with('NZTim\Logger\Handlers\EmailHandler')->andReturn($this->emailHandler);
        $this->app->shouldReceive('make')->with('NZTim\Logger\Handlers\PapertrailHandler')->andReturn($this->papertrailHandler);
        $this->request = Mockery::mock('Illuminate\Http\Request');
        $this->authManager = Mockery::mock('Illuminate\Auth\AuthManager');
        return new Logger($this->app, $this->request, $this->authManager);
    }
}

function storage_path() {
    return '/path/to/storage';
}

function file_put_contents($filename, $message, $flags) {
//    echo "Wrote exception to log\n";
    return true;
}
