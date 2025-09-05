<?php

declare(strict_types=1);

namespace App\Tests\Business\Shared\Utility\Enum\Fixtures;

use App\Business\Shared\Utility\Enum\{EnumArraySerializableTrait, EnumJsonSerializableTrait};

enum DummyBackedEnum: string
{
    use EnumArraySerializableTrait;
    use EnumJsonSerializableTrait;

    case Hearts = 'H';
    case Diamonds = 'D';
    case Clubs = 'C';
    case Spades = 'S';
}
