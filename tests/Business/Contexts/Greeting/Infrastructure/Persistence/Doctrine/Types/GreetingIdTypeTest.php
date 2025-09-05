<?php

declare(strict_types=1);

namespace App\Tests\Business\Contexts\Greeting\Infrastructure\Persistence\Doctrine\Types;

use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use App\Business\Contexts\Greeting\Infrastructure\Persistence\Doctrine\Types\GreetingIdType;
use App\Tests\Helper\Doctrine\Types\ValueObjectIdTypeTestCase;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\{CoversClass, Group};

#[Group('unit')]
#[Group('greeting')]
#[CoversClass(GreetingIdType::class)]
final class GreetingIdTypeTest extends ValueObjectIdTypeTestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!Type::hasType(self::getTypeNameStatic())) {
            Type::addType(self::getTypeNameStatic(), self::getTypeClassStatic());
        }
    }

    protected function getTypeClass(): string
    {
        return self::getTypeClassStatic();
    }

    protected function getValueObjectClass(): string
    {
        return GreetingId::class;
    }

    protected function getTypeName(): string
    {
        return self::getTypeNameStatic();
    }

    private static function getTypeClassStatic(): string
    {
        return GreetingIdType::class;
    }

    private static function getTypeNameStatic(): string
    {
        return GreetingIdType::NAME;
    }
}
