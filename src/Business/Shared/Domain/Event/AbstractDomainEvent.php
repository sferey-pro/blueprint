<?php

declare(strict_types=1);

namespace App\Business\Shared\Domain\Event;

use App\Business\Shared\Domain\ValueObject\AggregateRootId;
use App\Business\Shared\Domain\ValueObject\EventId;

/**
 * Classe de base pour tous les événements de domaine, fournissant une structure commune.
 *
 * @template T
 *
 * @implements DomainEvent<T>
 */
abstract readonly class AbstractDomainEvent implements DomainEvent
{
    /**
     * @param EventId            $eventId     L'ID de l'événement
     * @param AggregateRootId    $aggregateId L'ID de l'agrégat qui a levé l'événement
     * @param \DateTimeImmutable $occurredOn  le moment où l'événement s'est produit
     */
    public function __construct(
        public private(set) EventId $eventId,
        public private(set) AggregateRootId $aggregateId,
        public private(set) \DateTimeImmutable $occurredOn,
    ) {
    }

    /**
     * Doit retourner un nom unique et lisible pour l'événement.
     * Utile pour la journalisation, le monitoring ou le routage.
     * e.g., 'greeting.greeting_was_created'.
     */
    abstract public static function eventName(): string;
}
