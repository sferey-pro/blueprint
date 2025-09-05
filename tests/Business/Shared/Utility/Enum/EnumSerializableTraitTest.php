<?php

declare(strict_types=1);

namespace App\Tests\Business\Shared\Utility\Enum;

use App\Business\Shared\Utility\Enum\EnumArraySerializableTrait;
use App\Business\Shared\Utility\Enum\EnumJsonSerializableTrait;
use App\Business\Shared\Utility\Enum\EnumNamesTrait;
use App\Business\Shared\Utility\Enum\EnumValuesTrait;
use App\Tests\Business\Shared\Utility\Enum\Fixtures\DummyBackedEnum;
use App\Tests\Business\Shared\Utility\Enum\Fixtures\DummyUnitEnum;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[Group('kernel')]
#[CoversTrait(EnumNamesTrait::class)]
#[CoversTrait(EnumValuesTrait::class)]
#[CoversTrait(EnumArraySerializableTrait::class)]
#[CoversTrait(EnumJsonSerializableTrait::class)]
final class EnumSerializableTraitTest extends TestCase
{
    public function testNames(): void
    {
        // Test pour EnumNamesTrait
        $expected = ['Pending', 'Active', 'Inactive'];
        self::assertSame($expected, DummyUnitEnum::names());

        $expectedBacked = ['Hearts', 'Diamonds', 'Clubs', 'Spades'];
        self::assertSame($expectedBacked, DummyBackedEnum::names());
    }

    public function testValues(): void
    {
        // Test pour EnumValuesTrait
        $expected = ['H', 'D', 'C', 'S'];
        self::assertSame($expected, DummyBackedEnum::values());
    }

    public function testArray(): void
    {
        // Test pour EnumArraySerializableTrait
        $expected = [
            'Hearts' => 'H',
            'Diamonds' => 'D',
            'Clubs' => 'C',
            'Spades' => 'S',
        ];
        self::assertSame($expected, DummyBackedEnum::array());
    }

    public function testJsonSerialize(): void
    {
        // Test pour EnumJsonSerializableTrait
        $expectedJson = '{"Hearts":"H","Diamonds":"D","Clubs":"C","Spades":"S"}';
        self::assertSame($expectedJson, DummyBackedEnum::jsonSerialize());
    }
}
