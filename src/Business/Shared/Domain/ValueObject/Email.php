<?php

declare(strict_types=1);

namespace App\Business\Shared\Domain\ValueObject;

use App\Kernel\Exception\InvalidValueObjectDataException;
use App\Kernel\ValueObject\AbstractStringValueObject;
use Assert\Assert;

/**
 * @template-extends AbstractStringValueObject<string>
 */
final readonly class Email extends AbstractStringValueObject
{
    private const int MAX_LENGTH = 180;

    /**
     * Le constructeur est redéfini pour normaliser la valeur (en minuscules).
     */
    protected function __construct(string $value)
    {
        parent::__construct(mb_strtolower($value));
    }

    /**
     * Valide que la chaîne fournie est un email valide.
     *
     * @throws InvalidValueObjectDataException
     */
    protected static function validateString(string $value): void
    {
        $email = $value;
        $normalizedEmail = mb_strtolower(mb_trim($email));

        Assert::that($normalizedEmail)
            ->notEmpty('Email address cannot be empty')
            ->maxLength(self::MAX_LENGTH, \sprintf(
                'Email address cannot exceed %d characters',
                self::MAX_LENGTH
            ))
            ->email('"%s" is not a valid email address');
    }
}
