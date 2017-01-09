#Logger

Logger complements or replaces standard Laravel logging  
It logs to local files and additionally handles email alerts and remote log aggregators.  
Additionally, it can capture messages sent via the Laravel Log facade and exceptions.

###Installation

Register the service provider:  
`NZTim\Logger\LoggerServiceProvider::class,`

Add facade reference:  
`'Logger' => NZTim\Logger\LoggerFacade::class,`

###Usage

Use the facade to write to custom log files. Files are stored in `storage/logs/custom`:

```
Logger::info('auth', 'User login from 1.2.3.4');
Logger::warning('audit', 'A record was updated');
Logger::error('exceptions', 'Fatal exception: ');  
```

A `requestInfo()` method is available to provide context for the message, including IP address, request type/URL, `Auth::user()->id` and input data

```
Logger::warning('audit', 'An unusual request', Logger::requestInfo());
```

Fatal errors occurring during the logging process, are stored in `storage/logs/fatal-logger-errors.log`.
For example, a message will be logged here when the system is unable to send an error notification.

### Configuration

Publish the configuration file with: `php artisan vendor:publish --provider=NZTim\Logger\LoggerServiceProvider`.

* `'laravel' => true,` captures Laravel log facade messages
* `'email.level' => 'ERROR'` sets the level at which email notifications are sent
* `'email.recipient' => 'recipient@example.com',` sets the email recipient


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

Emails are only triggered if `app.debug` is false and the relevant error level meets the requirement. 
The Laravel mail system must be configured for emails to function.   

### Changelog

* v0.4: Remove Papertrail handler, use config file instead of .env, require PHP7.
  * Upgrade: publish and update the config file, make sure Papertrail handler is not required.
