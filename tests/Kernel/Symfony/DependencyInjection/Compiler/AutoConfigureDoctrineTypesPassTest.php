<?php

declare(strict_types=1);

namespace App\Tests\Kernel\Symfony\DependencyInjection\Compiler;

use App\Kernel\Persistence\Adapter\Doctrine\Types\DoctrineCustomTypeInterface;
use App\Kernel\Symfony\DependencyInjection\Compiler\AutoConfigureDoctrineTypesPass;
use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\{ContainerBuilder, Definition};

#[Group('unit')]
#[Group('kernel')]
#[CoversClass(AutoConfigureDoctrineTypesPass::class)]
final class AutoConfigureDoctrineTypesPassTest extends TestCase
{
    private AutoConfigureDoctrineTypesPass $compilerPass;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->compilerPass = new AutoConfigureDoctrineTypesPass();
        $this->container = new ContainerBuilder();
    }

    public function testProcessAddsTaggedTypesToDoctrine(): void
    {
        // 1. Arrange
        $definition = new Definition(ValidCustomType::class);
        $definition->addTag('doctrine.custom_type');
        $this->container->setDefinition('app.doctrine.valid_custom_type', $definition);

        // 2. Act
        $this->compilerPass->process($this->container);

        // 3. Assert
        self::assertTrue($this->container->hasParameter('doctrine.dbal.connection_factory.types'));
        $types = $this->container->getParameter('doctrine.dbal.connection_factory.types');
        self::assertArrayHasKey('valid_type_name', $types);
        self::assertSame(['class' => ValidCustomType::class], $types['valid_type_name']);
    }

    public function testProcessMergesWithExistingTypes(): void
    {
        // 1. Arrange
        // On simule un paramètre déjà existant dans le conteneur.
        $existingTypes = ['existing_type' => ['class' => 'App\ExistingType', 'commented' => false]];
        $this->container->setParameter('doctrine.dbal.connection_factory.types', $existingTypes);

        // On ajoute une nouvelle définition taguée.
        $definition = new Definition(ValidCustomType::class);
        $definition->addTag('doctrine.custom_type');
        $this->container->setDefinition('app.doctrine.valid_custom_type', $definition);

        // 2. Act
        $this->compilerPass->process($this->container);

        // 3. Assert
        $types = $this->container->getParameter('doctrine.dbal.connection_factory.types');

        // On vérifie que les types existants ET les nouveaux sont présents.
        self::assertCount(2, $types);
        self::assertArrayHasKey('existing_type', $types);
        self::assertArrayHasKey('valid_type_name', $types);
        self::assertSame(['class' => ValidCustomType::class], $types['valid_type_name']);
    }

    public function testProcessThrowsExceptionWhenNameConstantIsMissing(): void
    {
        // 3. Assert
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessageMatches('/must have a "NAME" constant/');

        // 1. Arrange
        $definition = new Definition(TypeWithoutNameConstant::class);
        $definition->addTag('doctrine.custom_type');
        $this->container->setDefinition('app.doctrine.invalid_type', $definition);

        // 2. Act
        $this->compilerPass->process($this->container);
    }

    public function testProcessThrowsExceptionWhenInterfaceIsMissing(): void
    {
        // 3. Assert
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessageMatches('/must implement ".*DoctrineCustomTypeInterface"/');

        // 1. Arrange
        $definition = new Definition(TypeWithoutInterface::class);
        $definition->addTag('doctrine.custom_type');
        $this->container->setDefinition('app.doctrine.invalid_type', $definition);

        // 2. Act
        $this->compilerPass->process($this->container);
    }

    public function testProcessDoesNothingWhenNoTypesAreTagged(): void
    {
        // 1. Arrange (aucune définition ajoutée)

        // 2. Act
        $this->compilerPass->process($this->container);

        // 3. Assert
        self::assertFalse($this->container->hasParameter('doctrine.dbal.connection_factory.types'));
    }
}

// --- Stubs (classes de test) ---

/** @internal */
final class ValidCustomType implements DoctrineCustomTypeInterface
{
    public const NAME = 'valid_type_name';
}

/** @internal */
final class TypeWithoutNameConstant implements DoctrineCustomTypeInterface
{
    // Pas de constante NAME
}

/** @internal */
final class TypeWithoutInterface
{
    public const NAME = 'invalid_type';
}
