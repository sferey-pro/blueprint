<?php

declare(strict_types=1);

namespace App\Tests\Kernel\ValueObject;

use App\Kernel\ValueObject\AbstractStringValueObject;
use Webmozart\Assert\Assert;

/**
 * @template-extends AbstractStringValueObject<string>
 */
final readonly class DummyStringVO extends AbstractStringValueObject
{
    protected static function validateString(string $value): void
    {
        Assert::notEmpty($value, 'value cannot be empty.');
    }
}
