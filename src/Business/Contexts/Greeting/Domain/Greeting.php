<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Domain;

use App\Business\Contexts\Greeting\Domain\Event\GreetingWasCreated;
use App\Business\Contexts\Greeting\Domain\Event\GreetingWasPublished;
use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use App\Business\Contexts\Greeting\Infrastructure\Persistence\Doctrine\Repository\DoctrineGreetingRepository;
use App\Business\Contexts\Greeting\Infrastructure\Persistence\Doctrine\Types\GreetingIdType;
use App\Business\Shared\Domain\Aggregate\AggregateRoot;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Psr\Clock\ClockInterface;

#[ORM\Entity(repositoryClass: DoctrineGreetingRepository::class)]
#[ORM\Table(name: 'greetings')]
class Greeting extends AggregateRoot
{
    #[ORM\Id]
    #[ORM\Column(type: GreetingIdType::NAME, length: 36, unique: true)]
    public private(set) GreetingId $id;

    #[ORM\Column(type: 'text')]
    public private(set) string $message;

    #[ORM\Column(type: 'datetime_immutable')]
    public private(set) \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 255, enumType: GreetingStatus::class)]
    public private(set) GreetingStatus $status;

    private function __construct(GreetingId $id, string $message, \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->message = $message;
        $this->createdAt = $createdAt;
        $this->status = GreetingStatus::DRAFT; // Un nouveau Greeting est toujours un brouillon.
    }

    public static function create(string $message, \DateTimeImmutable $createdAt, ClockInterface $clock): self
    {
        $greeting = new self(GreetingId::generate(), $message, $createdAt);

        $greeting->raise(new GreetingWasCreated(
            $greeting->id,
            $greeting->message,
            $greeting->createdAt,
            $clock->now()
        ));

        return $greeting;
    }

    public function publish(ClockInterface $clock): void
    {
        $this->status = GreetingStatus::PUBLISHED;

        $this->raise(
            new GreetingWasPublished($this->id, $clock->now())
        );
    }

    public function getStatus(): string
    {
        return $this->status->value;
    }
}
