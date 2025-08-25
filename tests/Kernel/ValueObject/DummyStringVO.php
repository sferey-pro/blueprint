<?php

declare(strict_types=1);

namespace App\Tests\Kernel\ValueObject;

use App\Kernel\Exception\InvalidValueObjectDataException;
use App\Kernel\ValueObject\AbstractStringValueObject;

/**
 * @internal
 *
 * @template-extends AbstractStringValueObject<string>
 */
final readonly class DummyStringVO extends AbstractStringValueObject
{
    protected static function validate(...$args): void
    {
        $value = $args[0];
        if ('' === $value) {
            throw InvalidValueObjectDataException::because('value cannot be empty.');
        }
    }
}
