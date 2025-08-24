<?php

declare(strict_types=1);

use App\Kernel\Bus\Message\Command;
use App\Kernel\Bus\Message\DomainEvent;
use App\Kernel\Bus\Message\Query;
use Symfony\Config\FrameworkConfig;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (FrameworkConfig $framework): void {
    $messenger = $framework->messenger();

    $messenger->defaultBus('command.bus');

    $messenger->bus('command.bus');
    $messenger->bus('query.bus');

    $messenger->bus('event.bus')
        ->defaultMiddleware()
        ->enabled(true)
        ->allowNoHandlers(true)
        ->allowNoSenders(true)
    ;

    $messenger->transport('sync')
        ->dsn('sync://')
    ;

    $messenger->failureTransport('failed_default');

    $messenger->transport('async_priority_high')
        ->dsn(env('MESSENGER_TRANSPORT_DSN'))
        ->failureTransport('failed_high_priority');

    $messenger->transport('async_events')
        ->dsn(env('MESSENGER_TRANSPORT_DSN'))
        ->retryStrategy()
            ->maxRetries(0)
    ;

    $messenger->transport('saga_internal')
        ->dsn(env('MESSENGER_TRANSPORT_DSN'))
        ->failureTransport('failed_high_priority')
        ->retryStrategy()
            ->maxRetries(0)
    ;

    $messenger->transport('async_priority_low')
        ->dsn(env('MESSENGER_TRANSPORT_DSN'));

    $messenger->transport('failed_default')
        ->dsn('doctrine://default?queue_name=failed_default');

    $messenger->transport('failed_high_priority')
        ->dsn('doctrine://default?queue_name=failed_high_priority');

    $messenger->routing(Query::class)->senders(['sync']);
    $messenger->routing(Command::class)->senders(['sync']);
    $messenger->routing(DomainEvent::class)->senders(['async_events']);
};
