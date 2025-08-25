<?php

declare(strict_types=1);

namespace App\Kernel\ValueObject;

use App\Kernel\Exception\InvalidValueObjectDataException;
use Assert\Assert;

/**
 * Classe de base pour les Value Objects qui ne sont qu'un wrapper
 * autour d'une unique valeur de type string.
 *
 * @template T of string
 */
abstract readonly class AbstractStringValueObject implements ValueObjectInterface, \Stringable
{
    use ValidatedValueObjectTrait;

    /**
     * Le constructeur est protégé pour forcer la création via une factory
     * nommée (comme `create()` ou `fromString()`) dans les classes enfants,
     * qui contiendra la logique de validation.
     */
    protected function __construct(public string $value)
    {
    }

    /**
     * Implémente la méthode générique du trait.
     * Son rôle est de valider les arguments et de déléguer à une méthode typée.
     */
    final protected static function validate(...$args): void
    {
        Assert::that($args)->count(1, '%s expects a single string argument.', static::class);
        Assert::that($args[0])->string('%s expects a single string argument, got %%s.', static::class);

        static::validateString($args[0]);
    }

    /**
     * Méthode de validation spécifique à la chaîne de caractères.
     * À implémenter dans chaque ValueObject concret.
     */
    abstract protected static function validateString(string $value): void;

    /**
     * @return T
     */
    public function value(): mixed
    {
        return $this->value;
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof static && $this->value === $other->value();
    }

    public function __toArray(): array
    {
        return ['value' => $this->value];
    }

    public static function fromArray(array $data): object
    {
        if (!isset($data['value'])) {
            throw InvalidValueObjectDataException::because('Missing required fields: value');
        }

        return static::create($data['value'])->value();
    }

    #[\Override]
    public function __toString(): string
    {
        return (string) $this->value;
    }
}
