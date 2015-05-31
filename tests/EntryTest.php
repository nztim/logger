<?php namespace NZTim\Logger;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class EntryTest extends PHPUnit_Framework_TestCase
{
    public function testInInstantiable()
    {
        $this->assertTrue(class_exists('NZTim\Logger\Entry'));
    }

    /**
     * @test
     */
    public function normalConstructionAndAccessors()
    {
        $entry = new Entry('test', 'INFO', 'Test message', ['abc' => 123]);
        $this->assertTrue($entry instanceof Entry);
        $this->assertEquals('test', $entry->getChannel());
        $this->assertEquals('INFO', $entry->getLevel());
        $this->assertEquals(200, $entry->getCode());
        $this->assertEquals('Test message', $entry->getMessage());
        $this->assertEquals(['abc' => 123], $entry->getContext());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function testInvalidConstructionChannelName()
    {
        $entry = new Entry('', 'INFO', '', ['abc' => 123]);
        $entry = new Entry('$$$', 'INFO', '', ['abc' => 123]);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function testInvalidConstructionLevel()
    {
        $entry = new Entry('test', 'NotALevel', 'Test message', ['abc' => 123]);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function testInvalidConstructionMessage()
    {
        $entry = new Entry('test', 'NotALevel', '', ['abc' => 123]);
    }
}

function str_slug($string) {
    return strtolower($string);
}