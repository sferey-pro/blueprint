<?php

declare(strict_types=1);

namespace App\Business\Shared\Domain\ValueObject;

use App\Business\Shared\Domain\Exception\ValidationException;
use App\Business\Shared\Utility\Result;
use Webmozart\Assert\InvalidArgumentException;

/**
 * Trait pour simplifier la création de ValueObjects validés.
 * Utilise l'objet Result pour une gestion d'erreur fonctionnelle.
 */
trait ValidatedValueObjectTrait
{
    /**
     * Template method pour la validation.
     *
     * @return Result<static, ValidationException>
     */
    public static function create(...$args): Result
    {
        try {
            static::validate(...$args);

            return Result::success(new static(...$args));
        } catch (InvalidArgumentException $e) {
            return Result::failure(
                new ValidationException(
                    \sprintf('%s validation failed: %s', static::class, $e->getMessage()),
                    previous: $e
                )
            );
        }
    }

    /**
     * Factory method qui throw directement.
     * Utile dans les contextes où on est sûr de la validité.
     *
     * @throws ValidationException
     */
    public static function fromValidatedValue(...$args): static
    {
        return static::create(...$args)->valueOrThrow();
    }

    /**
     * À implémenter dans chaque ValueObject.
     *
     * @throws InvalidArgumentException
     */
    abstract protected static function validate(...$args): void;
}
