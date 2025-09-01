<?php

declare(strict_types=1);

namespace App\Tests\Kernel\Enum\Fixtures;

use App\Kernel\Enum\EnumArraySerializableTrait;
use App\Kernel\Enum\EnumJsonSerializableTrait;

enum DummyBackedEnum: string
{
    use EnumArraySerializableTrait;
    use EnumJsonSerializableTrait;

    case Hearts = 'H';
    case Diamonds = 'D';
    case Clubs = 'C';
    case Spades = 'S';
}
