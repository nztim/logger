<?php namespace NZTim\Logger\Handlers;

use Mockery;
use NZTim\Logger\Entry;
use PHPUnit_Framework_TestCase;

class EmailHandlerTest extends PHPUnit_Framework_TestCase
{
    protected $mailer;
    protected $cache;

    public function testInInstantiable()
    {
        $this->assertTrue(class_exists('NZTim\Logger\Handlers\EmailHandler'));
    }

    public function testNotTriggeredWhenDebugIsTrue()
    {
        $handler = $this->instantiateWithMocks(true);
        $this->cache->shouldReceive('has')->with('logger-email')->andReturn(false);
        $handler->write(new Entry('test', 'WARNING', "Test message", ['abc' => 123]));
    }

    public function testNotTriggeredWhenThrottleActive()
    {
        $handler = $this->instantiateWithMocks();
        $this->cache->shouldReceive('has')->with('logger-email')->andReturn(true);
        $handler->write(new Entry('test', 'WARNING', "Test message", ['abc' => 123]));
    }

    public function testNotTriggeredWhenLevelIsTooLow()
    {
        // LOGGER_EMAIL_LEVEL is set to WARNING
        $handler = $this->instantiateWithMocks();
        $this->cache->shouldReceive('has')->with('logger-email')->andReturn(false);
        $handler->write(new Entry('test', 'INFO', "Test message", ['abc' => 123]));
    }

    public function testEmailSentWhenLevelIsEqual()
    {
        $handler = $this->instantiateWithMocks();
        $this->cache->shouldReceive('has')->with('logger-email')->andReturn(false);
        $this->mailer->shouldReceive('send')->once();
        $this->cache->shouldReceive('put');
        $handler->write(new Entry('test', 'WARNING', "Test message", ['abc' => 123]));
    }

    public function testEmailSentWhenLevelIsAbove()
    {
        $handler = $this->instantiateWithMocks();
        $this->cache->shouldReceive('has')->with('logger-email')->andReturn(false);
        $this->mailer->shouldReceive('send')->once();
        $this->cache->shouldReceive('put');
        $handler->write(new Entry('test', 'ERROR', "Test message", ['abc' => 123]));
    }

    protected function instantiateWithMocks($debug = false)
    {
        $this->mailer = Mockery::mock('Illuminate\Mail\Mailer');
        $this->cache = Mockery::mock('Illuminate\Cache\CacheManager');
        $this->config = Mockery::mock('Illuminate\Config\Repository');
        $this->config->shouldReceive('get')->andReturn($debug);
        return new EmailHandler($this->mailer, $this->cache, $this->config);
    }
}

function env($variable, $default) {
    if($variable == 'LOGGER_EMAIL_LEVEL') {
        return 'WARNING';
    }
    if($variable == 'LOGGER_EMAIL_TO') {
        return 'test@nztm.net';
    }
    return $default;
}