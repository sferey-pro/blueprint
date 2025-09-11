<?php

declare(strict_types=1);

use App\Business\Shared\Domain\Event\DomainEvent;
use App\Kernel\Bus\Message\Command;
use App\Kernel\Bus\Message\Query;
use Symfony\Config\FrameworkConfig;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (FrameworkConfig $framework): void {
    $messenger = $framework->messenger();

    $messenger->defaultBus('command.bus');

    $bus = $messenger->bus('command.bus');
    $bus->middleware()->id('doctrine_transaction');

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

    $messenger->transport('async')
        ->dsn(env('MESSENGER_TRANSPORT_DSN'));

    $messenger->transport('failed_default')
        ->dsn('doctrine://default?queue_name=failed_default');

    $messenger->routing(Query::class)->senders(['sync']);
    $messenger->routing(Command::class)->senders(['sync']);
    $messenger->routing(DomainEvent::class)->senders(['async']);
};
