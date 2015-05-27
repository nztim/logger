#Logger

Logger complements or replaces standard Laravel logging  
It logs to local files and additionally handles email alerts and remote log aggregators.  
Additionally, it can capture messages sent via the Laravel Log facade and exceptions.

###Installation

Register the service provider:  
`'NZTim\Logger\LoggerServiceProvider',`

Add facade reference:  
`'Logger' => 'NZTim\Logger\LoggerFacade',`

###Usage

Use the facade to write to custom log files. Files are stored in `storage/logs/custom`:

```
Logger::info('auth', 'User x logged in from IP: 1.2.3.4');
Logger::warning('audit', 'A record was updated');
Logger::error('exceptions', 'Fatal exception: ');  
```

Fatal errors caused by the logging process are stored in `storage/logs/fatal-logger-errors.log`

### Configuration

Configuration is via the .env file, no entries are required for base functionality.

**Hook into Laravel Log facade messages:**  

```
// .env
LOGGER_LOG=true
```

**Hook into Laravel exception messages:**    

```
// app/Exceptions/Handler.php
use Logger;

public function report(Exception $e)
{
    $class = (new \ReflectionClass($e))->getShortName();
    Logger::error('exceptions', "Exception {$class} | {$e->getMessage()}");
    return parent::report($e);
}
```

###External Services

External services are only triggered if debug is false and the relevant error level meets the requirement.  

**Email alerts**

The Laravel mail system must be configured for emails to function.   
The 'from' address must be st in `config/mail.php`.
```
LOGGER_APP_NAME=MyApp
LOGGER_EMAIL_LEVEL=WARNING
LOGGER_EMAIL_TO=recipient@domain.com
```

**Papertrail**

Send log entries to Papertrail

```
LOGGER_PAPERTRAIL_LEVEL=DEBUG
LOGGER_PAPERTRAIL_HOST=<xyz>.papertrailapp.com
LOGGER_PAPERTRAIL_PORT=12345
```

### Summary of .env entries:
```
LOGGER_LOG=true
LOGGER_APP_NAME=MyAppName
LOGGER_EMAIL_LEVEL=ERROR
LOGGER_EMAIL_TO=recipient@domain.com
LOGGER_PAPERTRAIL_LEVEL=DEBUG
LOGGER_PAPERTRAIL_HOST=<xyz>.papertrailapp.com
LOGGER_PAPERTRAIL_PORT=12345
```