<?php

declare(strict_types=1);

namespace App\Business\Shared\Domain\ValueObject;

use App\Kernel\Exception\InvalidValueObjectDataException;
use App\Kernel\ValueObject\AbstractStringValueObject;
use Webmozart\Assert\Assert;

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

        Assert::notEmpty($normalizedEmail, 'Email address cannot be empty');
        Assert::maxLength($normalizedEmail, self::MAX_LENGTH, 'Email address cannot exceed %2$d characters. Got %s');
        Assert::email($normalizedEmail, '%s is not a valid email address');
    }
}
