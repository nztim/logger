<?php namespace NZTim\Logger\Commands;

use Illuminate\Console\Command;

class AddMigration extends Command
{
    protected $name = 'logger:migration';
    protected $description = 'Add database migration for logger table';

    public function handle()
    {
        $name = 'create_logger_table';
        $ds = DIRECTORY_SEPARATOR;
        $path = database_path().$ds.'migrations';
        $filename = app('migration.creator')->create($name, $path);
        $content = file_get_contents(__DIR__.'/migration.stub');
        file_put_contents($filename, $content);
    }
}
