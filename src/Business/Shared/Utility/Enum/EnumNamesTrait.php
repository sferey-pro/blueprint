<?php

declare(strict_types=1);

namespace App\Business\Shared\Utility\Enum;

trait EnumNamesTrait
{
    abstract public static function cases(): array;

    public static function names(): array
    {
        return array_map(fn (\UnitEnum $enum): string => $enum->name, static::cases());
    }
}
