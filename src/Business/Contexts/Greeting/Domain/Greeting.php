<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Domain;

use App\Business\Contexts\Greeting\Domain\Event\GreetingWasCreated;
use App\Business\Contexts\Greeting\Domain\Event\GreetingWasPublished;
use App\Business\Contexts\Greeting\Domain\ValueObject\Author;
use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use App\Business\Shared\Domain\Aggregate\AggregateRoot;
use App\Business\Shared\Domain\Port\UuidFactoryInterface;
use App\Business\Shared\Domain\ValueObject\EventId;
use Psr\Clock\ClockInterface;

class Greeting extends AggregateRoot
{
    public private(set) GreetingId $id;
    public private(set) string $message;
    public private(set) \DateTimeImmutable $createdAt;
    public private(set) GreetingStatus $status;
    public private(set) Author $author;

    private function __construct(GreetingId $id, string $message, Author $author, \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->message = $message;
        $this->author = $author;
        $this->createdAt = $createdAt;

        $this->status = GreetingStatus::DRAFT; // Un nouveau Greeting est toujours un brouillon.
    }

    public static function create(
        string $message,
        Author $author,
        \DateTimeImmutable $createdAt,
        UuidFactoryInterface $uuidFactory,
        ClockInterface $clock,
    ): self {
        $id = $uuidFactory->generate(GreetingId::class);

        $greeting = new self($id, $message, $author, $createdAt);

        $eventId = $uuidFactory->generate(EventId::class);

        $greeting->raise(new GreetingWasCreated(
            $eventId,
            $greeting->id,
            $greeting->message,
            $greeting->createdAt,
            $clock->now()
        ));

        return $greeting;
    }

    public function publish(UuidFactoryInterface $uuidFactory, ClockInterface $clock): void
    {
        $eventId = $uuidFactory->generate(EventId::class);
        $this->status = GreetingStatus::PUBLISHED;

        $this->raise(new GreetingWasPublished(
            $eventId,
            $this->id,
            $clock->now()
        ));
    }

    public function getStatus(): string
    {
        return $this->status->value;
    }
}
