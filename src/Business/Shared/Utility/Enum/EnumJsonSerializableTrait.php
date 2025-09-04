<?php

declare(strict_types=1);

namespace App\Business\Shared\Utility\Enum;

trait EnumJsonSerializableTrait
{
    use EnumArraySerializableTrait;

    public static function jsonSerialize(): string
    {
        return json_encode(static::array());
    }
}
