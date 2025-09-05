<?php

declare(strict_types=1);

namespace App\Tests\Business\Shared\Infrastructure\Doctrine;

use App\Business\Shared\Domain\Port\UuidFactoryInterface;
use App\Business\Shared\Domain\ValueObject\AbstractUid;
use App\Business\Shared\Infrastructure\Doctrine\UuidTypeDependencyInjectorMiddleware;
use App\Business\Shared\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectIdType;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[Group('shared')]
#[CoversClass(UuidTypeDependencyInjectorMiddleware::class)]
final class UuidTypeDependencyInjectorMiddlewareTest extends TestCase
{
    public function testWrapInjectsFactoryIntoCustomTypes(): void
    {
        // 1. Arrange
        $uuidFactory = $this->createMock(UuidFactoryInterface::class);
        $driver = $this->createMock(Driver::class);

        // On s'assure que notre type de test est bien enregistré avant de lancer le middleware
        if (!Type::hasType(DummyIdTypeForMiddlewareTest::NAME)) {
            Type::addType(DummyIdTypeForMiddlewareTest::NAME, DummyIdTypeForMiddlewareTest::class);
        }

        // Le middleware recevra un itérable contenant la classe de notre type de test
        $customTypeClasses = [DummyIdTypeForMiddlewareTest::class];

        // 2. Act
        $middleware = new UuidTypeDependencyInjectorMiddleware($uuidFactory, $customTypeClasses);
        $wrappedDriver = $middleware->wrap($driver);

        // 3. Assert
        $typeInstance = Type::getType(DummyIdTypeForMiddlewareTest::NAME);
        self::assertInstanceOf(DummyIdTypeForMiddlewareTest::class, $typeInstance);

        // On vérifie que la factory a bien été injectée
        $reflection = new \ReflectionProperty(AbstractValueObjectIdType::class, 'uuidFactory');
        $reflection->setAccessible(true);
        self::assertSame($uuidFactory, $reflection->getValue($typeInstance));

        self::assertSame($driver, $wrappedDriver, 'The middleware should return the original driver instance.');
    }
}

/**
 * Classe "Stub" pour les besoins du test.
 * C'est une implémentation concrète et minimale de notre classe abstraite.
 *
 * @internal
 */
final class DummyIdTypeForMiddlewareTest extends AbstractValueObjectIdType
{
    public const NAME = 'dummy_type_for_middleware_test';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function getValueObjectClass(): string
    {
        return AbstractUid::class; // Le type exact n'importe pas pour ce test
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return ''; // Non pertinent pour ce test
    }
}
