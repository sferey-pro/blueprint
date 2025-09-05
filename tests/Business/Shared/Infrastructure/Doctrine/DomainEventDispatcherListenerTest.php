<?php

declare(strict_types=1);

namespace App\Tests\Business\Shared\Infrastructure\Doctrine;

use App\Business\Shared\Domain\Aggregate\{AggregateRoot};
use App\Business\Shared\Infrastructure\Doctrine\DomainEventDispatcherListener;
use App\Kernel\Bus\EventBusInterface;
use Doctrine\ORM\{EntityManagerInterface, UnitOfWork};
use Doctrine\ORM\Event\{OnFlushEventArgs, PostFlushEventArgs};
use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[Group('shared')]
#[CoversClass(DomainEventDispatcherListener::class)]
final class DomainEventDispatcherListenerTest extends TestCase
{
    private EventBusInterface&MockObject $eventBusMock;
    private EntityManagerInterface&MockObject $entityManagerMock;
    private UnitOfWork&MockObject $unitOfWorkMock;
    private DomainEventDispatcherListener $listener;

    protected function setUp(): void
    {
        $this->eventBusMock = $this->createMock(EventBusInterface::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->unitOfWorkMock = $this->createMock(UnitOfWork::class);

        $this->entityManagerMock->method('getUnitOfWork')->willReturn($this->unitOfWorkMock);

        $this->listener = new DomainEventDispatcherListener($this->eventBusMock);
    }

    public function testEventsAreDispatchedAfterFlush(): void
    {
        // 1. Arrange
        $aggregateRoot = new class extends AggregateRoot {
            public function __construct()
            {
                $this->raise(new \stdClass());
                $this->raise(new \stdClass());
            }
        };

        $this->unitOfWorkMock
            ->method('getScheduledEntityInsertions')
            ->willReturn([$aggregateRoot]);

        $this->unitOfWorkMock
            ->method('getScheduledEntityUpdates')
            ->willReturn([]);

        // On s'attend à ce que le bus soit appelé 2 fois
        $this->eventBusMock->expects(self::exactly(2))->method('dispatch');

        // 2. Act
        $this->listener->onFlush(new OnFlushEventArgs($this->entityManagerMock));
        $this->listener->postFlush(new PostFlushEventArgs($this->entityManagerMock));

        // 3. Assert
        // Les assertions sont faites via les mocks.
        // On vérifie également que les événements ont été purgés de l'agrégat.
        self::assertCount(0, $aggregateRoot->pullDomainEvents());
    }

    public function testNoEventsAreDispatchedWhenNoAggregatesAreFlushed(): void
    {
        // 1. Arrange
        $this->unitOfWorkMock->method('getScheduledEntityInsertions')->willReturn([]);
        $this->unitOfWorkMock->method('getScheduledEntityUpdates')->willReturn([]);

        // On s'attend à ce que le bus ne soit JAMAIS appelé
        $this->eventBusMock->expects(self::never())->method('dispatch');

        // 2. Act
        $this->listener->onFlush(new OnFlushEventArgs($this->entityManagerMock));
        $this->listener->postFlush(new PostFlushEventArgs($this->entityManagerMock));
    }

    public function testListenerStateIsClearedAfterPostFlush(): void
    {
        // 1. Arrange
        $aggregateRoot = new class extends AggregateRoot {
            public function __construct()
            {
                $this->raise(new \stdClass());
            }
        };
        $this->unitOfWorkMock->method('getScheduledEntityInsertions')->willReturn([$aggregateRoot]);
        $this->unitOfWorkMock->method('getScheduledEntityUpdates')->willReturn([]);

        // On s'attend à ce que le bus soit appelé une seule fois pour le premier flush
        $this->eventBusMock->expects(self::once())->method('dispatch');

        // 2. Act
        // Premier cycle de flush
        $this->listener->onFlush(new OnFlushEventArgs($this->entityManagerMock));
        $this->listener->postFlush(new PostFlushEventArgs($this->entityManagerMock));

        // Deuxième cycle de flush (sans aucune entité)
        $this->unitOfWorkMock->method('getScheduledEntityInsertions')->willReturn([]);
        $this->listener->onFlush(new OnFlushEventArgs($this->entityManagerMock));
        $this->listener->postFlush(new PostFlushEventArgs($this->entityManagerMock));

        // 3. Assert : Le test réussit si le mock n'est appelé qu'une seule fois.
    }
}
