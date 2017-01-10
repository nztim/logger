<?php namespace NZTim\Logger\Handlers;

use NZTim\Logger\Entry;

interface Handler
{
    public function write(Entry $entry);
}
