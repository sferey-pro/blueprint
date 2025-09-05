<?php

declare(strict_types=1);

namespace App\Tests\Helper\Doctrine\Types;

use App\Business\Shared\Domain\Port\UuidFactoryInterface;
use App\Business\Shared\Domain\ValueObject\AggregateRootId;
use App\Business\Shared\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectIdType;
use App\Tests\Faker\FakerUuidFactory;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Classe de base pour tester nos types Doctrine personnalisés qui gèrent des Value Objects d'ID.
 * Inspirée par les tests internes de Doctrine DBAL.
 */
abstract class ValueObjectIdTypeTestCase extends TestCase
{
    private AbstractPlatform&MockObject $platform;
    private AbstractValueObjectIdType $type;
    private UuidFactoryInterface $uuidFactory;

    /**
     * Doit retourner le FQCN (nom complet) de la classe de Type à tester.
     */
    abstract protected function getTypeClass(): string;

    /**
     * Doit retourner le FQCN du Value Object d'ID correspondant.
     */
    abstract protected function getValueObjectClass(): string;

    /**
     * Doit retourner le nom du type enregistré dans Doctrine.
     */
    abstract protected function getTypeName(): string;

    public static function setUpBeforeClass(): void
    {
        // Cette méthode sera surchargée dans la classe enfant pour enregistrer le type.
    }

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->type = Type::getType($this->getTypeName());
        $this->uuidFactory = new FakerUuidFactory();

        $this->type->setFactory($this->uuidFactory);
    }

    public function testGetNameReturnsCorrectName(): void
    {
        self::assertSame($this->getTypeName(), $this->type->getName());
    }

    public function testGetSQLDeclaration(): void
    {
        // On s'assure que notre type demande bien à la plateforme de lui fournir
        // la déclaration SQL pour un type GUID.
        $this->platform->expects(self::once())
            ->method('getGuidTypeDeclarationSQL')
            ->with(['name' => 'id', 'type' => $this->getTypeName()])
            ->willReturn('UUID');

        self::assertSame('UUID', $this->type->getSQLDeclaration(['name' => 'id', 'type' => $this->getTypeName()], $this->platform));
    }

    public function testConvertToPHPValueWithValidUuidString(): void
    {
        $voClass = $this->getValueObjectClass();
        $uuid = $this->uuidFactory->generate($voClass);
        $uuidString = (string) $uuid;

        $id = $this->type->convertToPHPValue($uuidString, $this->platform);

        self::assertInstanceOf($voClass, $id);
        self::assertSame($uuidString, (string) $id);
    }

    public function testConvertToPHPValueWithNullReturnsNull(): void
    {
        self::assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testConvertToPHPValueWithObjectInstanceReturnsSameInstance(): void
    {
        $voClass = $this->getValueObjectClass();

        $id = $this->uuidFactory->generate($voClass);

        $result = $this->type->convertToPHPValue($id, $this->platform);

        self::assertSame($id, $result);
    }

    public function testConvertToDatabaseValueAsStringForNativeGuidPlatform(): void
    {
        // 1. Arrange : Simule une plateforme qui supporte les UUID (ex: PostgreSQL)
        $this->platform->method('getGuidTypeDeclarationSQL')->willReturn('UUID');
        $this->platform->method('getStringTypeDeclarationSQL')->willReturn('CHAR(36)');

        $voClass = $this->getValueObjectClass();

        /** @var AggregateRootId $id */
        $id = $this->uuidFactory->generate($voClass);

        // 2. Act
        $databaseValue = $this->type->convertToDatabaseValue($id, $this->platform);

        // 3. Assert
        self::assertSame($id->value()->toRfc4122(), $databaseValue);
    }

    public function testConvertToDatabaseValueAsBinaryForNonNativeGuidPlatform(): void
    {
        // 1. Arrange : Simule une plateforme qui ne supporte PAS les UUID (ex: SQLite)
        $this->platform->method('getGuidTypeDeclarationSQL')->willReturn('CHAR(36)');
        $this->platform->method('getStringTypeDeclarationSQL')->willReturn('CHAR(36)');

        $voClass = $this->getValueObjectClass();

        /** @var AggregateRootId $id */
        $id = $this->uuidFactory->generate($voClass);

        // 2. Act
        $databaseValue = $this->type->convertToDatabaseValue($id, $this->platform);

        // 3. Assert
        self::assertSame($id->value()->toBinary(), $databaseValue);
    }

    public function testConvertToDatabaseValueWithNullReturnsNull(): void
    {
        self::assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }
}
