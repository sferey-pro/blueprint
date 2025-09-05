<?php

declare(strict_types=1);

namespace App\Business\Shared\Domain\Exception;

/**
 * Exception levée lorsqu'une donnée fournie pour la création d'un Value Object
 * ne respecte pas les règles de validation de ce dernier.
 */
final class InvalidValueObjectDataException extends \InvalidArgumentException
{
    /**
     * Le constructeur est privé pour forcer l'utilisation des factories statiques,
     * garantissant ainsi des messages d'erreur clairs et standardisés.
     */
    private function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Factory pour créer une exception avec une raison générique.
     */
    public static function because(string $reason): self
    {
        return new self($reason);
    }

    /**
     * Factory plus spécifique pour un format invalide.
     */
    public static function forInvalidFormat(string $value, string $expectedFormat): self
    {
        $message = \sprintf(
            'The provided value "%s" does not match the expected format "%s".',
            $value,
            $expectedFormat
        );

        return new self($message);
    }
}
