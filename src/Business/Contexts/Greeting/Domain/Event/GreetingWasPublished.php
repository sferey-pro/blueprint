<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Domain\Event;

use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use App\Business\Shared\Domain\Event\AbstractDomainEvent;
use App\Business\Shared\Domain\ValueObject\EventId;

/**
 * Événement de domaine levé lorsqu'un Greeting est publié.
 *
 * @extends AbstractDomainEvent<self>
 */
final readonly class GreetingWasPublished extends AbstractDomainEvent
{
    /**
     * @param GreetingId         $greetingId L'ID de l'agrégat publié
     * @param \DateTimeImmutable $occurredOn la date système (quand l'événement a été levé)
     *
     * @return void
     */
    public function __construct(
        EventId $eventId,
        GreetingId $greetingId,
        \DateTimeImmutable $occurredOn,
    ) {
        parent::__construct($eventId, $greetingId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'greeting.published';
    }
}
