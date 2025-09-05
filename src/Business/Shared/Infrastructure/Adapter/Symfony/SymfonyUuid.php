<?php

declare(strict_types=1);

namespace App\Business\Shared\Infrastructure\Adapter\Symfony;

use App\Business\Shared\Domain\ValueObject\UuidInterface;
use Symfony\Component\Uid\Uuid;

final readonly class SymfonyUuid implements UuidInterface
{
    private Uuid $value;

    public function __construct(Uuid $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value->__toString();
    }

    public function toRfc4122(): string
    {
        return $this->value->toRfc4122();
    }

    public function toBinary(): string
    {
        return $this->value->toBinary();
    }

    public function equals(UuidInterface $other): bool
    {
        return $other instanceof self && $this->value->equals($other->value);
    }

    public static function fromString(string $uid): self
    {
        return new self(Uuid::fromString($uid));
    }

    public static function v7(): self
    {
        return new self(Uuid::v7());
    }
}
