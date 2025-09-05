<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Domain\Event;

use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use App\Business\Shared\Domain\Event\AbstractDomainEvent;
use App\Business\Shared\Domain\ValueObject\EventId;

/**
 * Événement de domaine levé lorsqu'un Greeting est crée.
 *
 * @extends AbstractDomainEvent<self>
 */
final readonly class GreetingWasCreated extends AbstractDomainEvent
{
    /**
     * @param GreetingId         $greetingId L'ID de l'agrégat créé
     * @param string             $message    la donnée métier
     * @param \DateTimeImmutable $createdAt  la date métier (quand le greeting a été créé)
     * @param \DateTimeImmutable $occurredOn la date système (quand l'événement a été levé)
     */
    public function __construct(
        EventId $eventId,
        public GreetingId $greetingId,
        public string $message,
        public \DateTimeImmutable $createdAt,
        \DateTimeImmutable $occurredOn,
    ) {
        parent::__construct($eventId, $greetingId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'greeting.created';
    }
}
