<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Domain\Event;

use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use App\Kernel\Bus\Message\AbstractDomainEvent;
use App\Kernel\Bus\Message\DomainEvent;

/**
 * Événement de domaine levé lorsqu'un Greeting est publié.
 *
 * @implements DomainEvent<self>
 */
final readonly class GreetingWasPublished extends AbstractDomainEvent
{
    /**
     * @param GreetingId         $greetingId  L'ID de l'agrégat publié
     * @param \DateTimeImmutable $occurredOn  la date système (quand l'événement a été levé)
     *
     * @return void
     */
    public function __construct(
        public GreetingId $greetingId,
        \DateTimeImmutable $occurredOn,
    ) {
        parent::__construct($greetingId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'greeting.published';
    }
}
