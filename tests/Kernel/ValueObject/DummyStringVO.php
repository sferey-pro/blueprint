<?php

declare(strict_types=1);

namespace App\Tests\Kernel\ValueObject;

use App\Kernel\Exception\InvalidValueObjectDataException;
use App\Kernel\ValueObject\AbstractStringValueObject;

/**
 * @template-extends AbstractStringValueObject<string>
 */
final readonly class DummyStringVO extends AbstractStringValueObject
{
    protected static function validateString(string $value): void
    {
        if ('' === $value) {
            throw InvalidValueObjectDataException::because('value cannot be empty.');
        }
    }
}
