#Logger

Logger complements or replaces standard Laravel logging.
It logs to local files and optionally provides email alerts.  

###Installation

Register the service provider:  
`NZTim\Logger\LoggerServiceProvider::class,`

Add facade reference:  
`'Logger' => NZTim\Logger\LoggerFacade::class,`

### Configuration

Publish the configuration file with: `php artisan vendor:publish --provider=NZTim\Logger\LoggerServiceProvider`.

* `'laravel' => true,` captures Laravel log facade messages
* `'folder' => 'custom'` sets the default subfolder for custom logs
* `'email.send' => false` turns sending of error emails on/off
* `'email.level' => 'ERROR'` sets the level at which email notifications are sent
* `'email.recipient' => 'recipient@example.com',` sets the email recipient

### Usage

Use the facade to write to custom log files. By default files are stored in `storage/logs/custom`:

```
Logger::info('auth', 'User login from 1.2.3.4');
Logger::warning('audit', 'A record was updated');
Logger::error('exceptions', 'Fatal exception: ');  
```

If you wish to store a logfile in a subfolder, use dot notation:

```
Logger::info('audit.auth', 'User login from 1.2.3.4'); // Logs to `storage/logs/custom/audit/auth.log`
```

A `requestInfo()` method is available to provide context for the message, including IP address, request type/URL, `Auth::user()->id` and input data

```
Logger::warning('audit', 'An unusual request', Logger::requestInfo());
```

Fatal errors occurring during the logging process, are stored in `storage/logs/fatal-logger-errors.log`.
For example, a message will be logged here when the system is unable to send an error notification.

### Hook into Laravel exceptions:

Make sure the `logger.laravel` configuration option is set to true, and update your error handler:

```
// app/Exceptions/Handler.php
use Logger;

public function report(Exception $e)
{
    $class = (new \ReflectionClass($e))->getShortName();
    Logger::error('exceptions', "Exception {$class} | {$e->getMessage()}", Logger::requestInfo());
    return parent::report($e);
}
```

### Email alerts

Emails are only triggered if email sending is turned on, `app.debug` is false and the relevant error level meets the requirement. 
The Laravel mail system must be configured for emails to function.   

### Database handler

* Add the table using `php artisan logger:migration` and `php artisan migrate`
* Use the config file to set which channels should be logged to the database
* You may now query the database using the Eloquent model `DbEntry`

### Changelog

* v0.5: Enable configuration of base folder and dot notation for subfolders
  * Upgrade: add new config file entry: `'folder' => 'custom',`
* v0.4: Add database handler.
  * Upgrade: add new config file entries. To log entries to the database, add and run the migration, then update config file settings accordingly. 
* v0.3: Remove Papertrail handler, use config file instead of .env, require PHP7.
  * Upgrade: publish and update the config file, make sure Papertrail handler is not required.
