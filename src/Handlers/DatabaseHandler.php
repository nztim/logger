<?php namespace NZTim\Logger\Handlers;

use NZTim\Logger\Entry;

class DatabaseHandler implements Handler
{
    public function write(Entry $entry)
    {
        if ($this->entryIsStoredInDb($entry)) {
            DbEntry::storeEntry($entry);
        }
    }

    protected function entryIsStoredInDb(Entry $entry): bool
    {
        $channels = config('logger.database.channels');
        if ($channels == ['*']) {
            return true;
        }
        return in_array($entry->channel(), $channels);
    }
}
