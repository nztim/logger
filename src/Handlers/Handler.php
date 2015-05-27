<?php namespace NZTim\Logger\Handlers;

use NZTim\Logger\Entry;

interface Handler
{
    /**
     * @param Entry $entry
     * @return mixed
     */
    public function write(Entry $entry);
}