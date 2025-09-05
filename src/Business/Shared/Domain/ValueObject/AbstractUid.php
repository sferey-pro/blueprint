<?php

declare(strict_types=1);

namespace App\Business\Shared\Domain\ValueObject;

/**
 * Classe de base abstraite pour les identifiants uniques (UID).
 *
 * Cette classe encapsule un objet Uid de Symfony, fournissant une base solide
 * pour créer des identifiants typés (comme EventId, UserId, etc.) tout en centralisant
 * la logique de génération et de comparaison.
 */
abstract readonly class AbstractUid implements ValueObjectInterface
{
    public function __construct(
        public UuidInterface $value,
    ) {
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof static && $this->value->equals($other->value());
    }

    public function value(): UuidInterface
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value->__toString();
    }

    public function __toArray(): array
    {
        return ['value' => $this->value->__toString()];
    }

    public static function fromArray(array $data): object
    {
        throw new \LogicException(\sprintf('%s cannot be created from an array directly. Use a factory.', static::class));
    }
}
