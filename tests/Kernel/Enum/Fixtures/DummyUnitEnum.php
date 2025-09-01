<?php

declare(strict_types=1);

namespace App\Tests\Kernel\Enum\Fixtures;

use App\Kernel\Enum\EnumNamesTrait;

enum DummyUnitEnum
{
    use EnumNamesTrait;

    case Pending;
    case Active;
    case Inactive;
}
