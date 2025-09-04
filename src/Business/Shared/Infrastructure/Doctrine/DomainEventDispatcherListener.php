<?php

declare(strict_types=1);

namespace App\Business\Shared\Infrastructure\Doctrine;

use App\Business\Shared\Domain\Aggregate\AggregateRoot;
use App\Kernel\Bus\EventBusInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;

/**
 * Écouteur Doctrine qui collecte et dispatche les événements de domaine
 * après que les changements aient été persistés en base de données.
 */
#[AsDoctrineListener(event: Events::onFlush, priority: 100)]
#[AsDoctrineListener(event: Events::postFlush, priority: 100)]
final class DomainEventDispatcherListener
{
    /** @var list<AggregateRoot> */
    private array $aggregateRoots = [];

    public function __construct(
        private readonly EventBusInterface $eventBus,
    ) {
    }

    /**
     * Collecte tous les agrégats modifiés pendant l'opération de flush.
     */
    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $unitOfWork = $eventArgs->getObjectManager()->getUnitOfWork();

        // Concatène les entités à insérer et à mettre à jour
        $entities = array_merge(
            $unitOfWork->getScheduledEntityInsertions(),
            $unitOfWork->getScheduledEntityUpdates()
        );

        foreach ($entities as $entity) {
            if ($entity instanceof AggregateRoot) {
                $this->aggregateRoots[] = $entity;
            }
        }
    }

    /**
     * Dispatche les événements collectés une fois que le flush a réussi.
     */
    public function postFlush(PostFlushEventArgs $eventArgs): void
    {
        // On s'assure de ne traiter qu'une seule fois les agrégats uniques
        foreach (array_unique($this->aggregateRoots, \SORT_REGULAR) as $aggregateRoot) {
            foreach ($aggregateRoot->pullDomainEvents() as $domainEvent) {
                $this->eventBus->dispatch($domainEvent);
            }
        }

        // On vide la liste pour la prochaine opération de flush
        $this->aggregateRoots = [];
    }
}
