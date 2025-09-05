<?php

declare(strict_types=1);

namespace App\Business\Shared\Domain\ValueObject;

use Webmozart\Assert\Assert;

/**
 * @template-extends AbstractStringValueObject<string>
 */
final readonly class PhoneNumber extends AbstractStringValueObject
{
    /**
     * Le constructeur est redéfini pour normaliser la valeur.
     */
    protected function __construct(string $value)
    {
        parent::__construct(self::normalize($value));
    }

    /**
     * Valide que la chaîne fournie est un phone valide.
     */
    protected static function validateString(string $value): void
    {
        $phone = mb_trim($value);

        // Étape 1 : Valider le format de l'entrée BRUTE
        Assert::notEmpty($phone, 'Phone number cannot be empty.');
        Assert::regex($phone, '/^\+?[0-9\s\-()]+$/', 'Phone number %s is not valid. It should contain only digits, spaces, dashes, parentheses, and an optional leading plus sign.');

        // Étape 2 : Valider les propriétés de la version NORMALISÉE
        $normalizedPhone = self::normalize($phone);
        Assert::maxLength($normalizedPhone, 15, 'Phone number cannot exceed 15 characters.');
    }

    private static function normalize(string $rawPhoneNumber): string
    {
        $trimmed = mb_trim($rawPhoneNumber);

        // Supprime tout ce qui n'est pas un chiffre
        $digitsOnly = preg_replace('/[^\d]/', '', $trimmed);

        // Si l'original commençait par '+', on s'assure que le résultat le fait aussi.
        return str_starts_with($trimmed, '+') ? '+'.$digitsOnly : $digitsOnly;
    }
}
