<?php

declare(strict_types=1);

namespace App\Tests\Helper\Doctrine\Types;

use App\Business\Shared\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectStringType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class ValueObjectStringTypeTestCase extends TestCase
{
    private AbstractPlatform&MockObject $platform;
    private AbstractValueObjectStringType $type;

    abstract protected function getTypeClass(): string;

    abstract protected function getValueObjectClass(): string;

    abstract protected function getTypeName(): string;

    abstract protected function getValidValue(): string;

    public static function setUpBeforeClass(): void
    {
        // Sera surchargée pour enregistrer le type si nécessaire.
    }

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->type = Type::getType($this->getTypeName());
    }

    public function testGetNameReturnsCorrectName(): void
    {
        self::assertSame($this->getTypeName(), $this->type->getName());
    }

    public function testGetSQLDeclaration(): void
    {
        $this->platform->expects(self::once())
            ->method('getStringTypeDeclarationSQL')
            ->willReturn('VARCHAR(255)');

        self::assertSame('VARCHAR(255)', $this->type->getSQLDeclaration([], $this->platform));
    }

    public function testConvertToPHPValueWithValidString(): void
    {
        $voClass = $this->getValueObjectClass();
        $validValue = $this->getValidValue();

        $vo = $this->type->convertToPHPValue($validValue, $this->platform);

        self::assertInstanceOf($voClass, $vo);
        self::assertSame($validValue, (string) $vo);
    }

    public function testConvertToPHPValueWithNullReturnsNull(): void
    {
        self::assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testConvertToDatabaseValueWithValueObject(): void
    {
        $voClass = $this->getValueObjectClass();
        $validValue = $this->getValidValue();
        $vo = $voClass::fromValidatedValue($validValue);

        $databaseValue = $this->type->convertToDatabaseValue($vo, $this->platform);

        self::assertSame($validValue, $databaseValue);
    }
}
