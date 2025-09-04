<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Domain\ValueObject;

use App\Business\Shared\Domain\ValueObject\Email;
use App\Business\Shared\Domain\ValueObject\ValueObjectInterface;

final readonly class Author implements ValueObjectInterface
{
    public private(set) Email $email;

    private function __construct(Email $email)
    {
        $this->email = $email;
    }

    public static function create(Email $email): self
    {
        return new self($email);
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self && $this->email->equals($other->email);
    }

    public function value(): Email
    {
        return $this->email;
    }

    public function __toString(): string
    {
        return (string) $this->email;
    }

    public function __toArray(): array
    {
        return ['email' => (string) $this->email];
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['email'])) {
            throw new \InvalidArgumentException('Missing required field: email');
        }

        return self::create(Email::fromArray(['value' => $data['email']]));
    }
}
