<?php namespace NZTim\Logger\Handlers;

use Illuminate\Database\Eloquent\Model;
use NZTim\Logger\Entry;

/**
 * @method static DbEntry find($id)
 * @method static DbEntry first()
 */

class DbEntry extends Model
{
    // Eloquent ---------------------------------------------------------------

    protected $table = 'logger';

    // Accessors and mutators -------------------------------------------------

    public function getContextAttribute($value)
    {
        return unserialize(base64_decode($value));
    }

    public function setContextAttribute($value)
    {
        $this->attributes['context'] = base64_encode(serialize($value));
    }

    // CRUD -----------------------------------------------------------------00

    public static function storeEntry(Entry $entry): DbEntry
    {
        $dbEntry = new static;
        $dbEntry->channel = $entry->channel();
        $dbEntry->level = $entry->level();
        $dbEntry->message = $entry->message();
        $dbEntry->context = $entry->context();
        $dbEntry->save();
        return $dbEntry;
    }

    // Instance ---------------------------------------------------------------

    public function channel()
    {
        return $this->channel;
    }

    public function level()
    {
        return $this->level;
    }

    public function code()
    {
        return $this->code;
    }

    public function message()
    {
        return $this->message;
    }

    public function context()
    {
        return $this->context;
    }
}
