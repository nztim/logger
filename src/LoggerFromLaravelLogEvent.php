<?php namespace NZTim\Logger;

use Exception;
use ReflectionClass;

class LoggerFromLaravelLogEvent
{
    public function handle(array $args)
    {
        [$level, $message, $context] = count($args) === 1 ? $this->laravel54($args) : $args;
        $message = empty($message) ? $this->createMessage($context) : $message;
        app(Logger::class)->add('laravel', $level, $message, $context);
    }

    protected function laravel54(array $args): array
    {
        // Laravel 5.4 provides \Illuminate\Log\Events\MessageLogged object
        return [$args[0]->level, $args[0]->message, $args[0]->context];
    }

    protected function createMessage(array $context): string
    {
        $exception = $context['exception'] ?? null;
        if ($exception instanceof Exception) {
            $class = (new ReflectionClass($context['exception']))->getShortName();
            return "Exception {$class} | {$exception->getMessage()}";
        }
        return '(No message)';
    }
}
