<?php

declare(strict_types=1);

namespace App\Tests\Business\Shared\Utility\Enum;

use App\Business\Shared\Utility\Enum\{EnumArraySerializableTrait, EnumJsonSerializableTrait, EnumNamesTrait, EnumValuesTrait};
use App\Tests\Business\Shared\Utility\Enum\Fixtures\{DummyBackedEnum, DummyUnitEnum};
use PHPUnit\Framework\Attributes\{CoversTrait, Group};
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
