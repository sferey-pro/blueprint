<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Domain\Event;

use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use App\Kernel\Bus\Message\AbstractDomainEvent;
use App\Kernel\Bus\Message\DomainEvent;

/**
 * Événement de domaine levé lorsqu'un Greeting est crée.
 *
 * @implements DomainEvent<self>
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
        public GreetingId $greetingId,
        public string $message,
        public \DateTimeImmutable $createdAt,
        \DateTimeImmutable $occurredOn,
    ) {
        parent::__construct($greetingId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'greeting.created';
    }
}
