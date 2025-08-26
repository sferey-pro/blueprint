<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Domain;

use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use App\Business\Contexts\Greeting\Infrastructure\Persistence\Doctrine\Repository\DoctrineGreetingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrineGreetingRepository::class)]
#[ORM\Table(name: 'greetings')]
class Greeting
{
    #[ORM\Id]
    #[ORM\Column(type: 'greeting_id', length: 36, unique: true)]
    public private(set) GreetingId $id;

    #[ORM\Column(type: 'text')]
    private string $message;

    #[ORM\Column(type: 'datetime_immutable')]
    public private(set) \DateTimeImmutable $createdAt;

    private function __construct(GreetingId $id, string $message, \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->message = $message;
        $this->createdAt = $createdAt;
    }

    public static function create(string $message, \DateTimeImmutable $createdAt): self
    {
        return new self(GreetingId::generate(), $message, $createdAt);
    }

    public function message(): string
    {
        return $this->message;
    }
}
