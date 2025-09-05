<?php

declare(strict_types=1);

namespace App\Tests\Business\Shared\Utility\Enum\Fixtures;

use App\Business\Shared\Utility\Enum\EnumNamesTrait;

enum DummyUnitEnum
{
    use EnumNamesTrait;

    case Pending;
    case Active;
    case Inactive;
}
