<?php

declare(strict_types=1);

namespace App\Tests\E2E\Greeting;

use App\Business\Contexts\Greeting\Infrastructure\Command\ListGreetingsCliCommand;
use App\Tests\Factory\GreetingFactory;
use App\Tests\Helper\Command\AbstractCommandTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Zenstruck\Foundry\Test\ResetDatabase;

#[Group('e2e')]
#[Group('greeting')]
#[CoversClass(ListGreetingsCliCommand::class)]
final class ListGreetingsCommandTest extends AbstractCommandTestCase
{
    use ResetDatabase;

    public function testExecuteWithGreetings(): void
    {
        // Arrange: Crée 3 greetings en base de données
        GreetingFactory::createMany(3);

        // Act: Exécute la commande
        $commandTester = $this->executeCommand([]);

        // Assert: Vérifie la sortie
        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Message', $output);
        // On vérifie qu'il y a bien 3 lignes de données (+ l'en-tête + separateur)
        self::assertCount(5, explode("\n", mb_trim($output, " \n+-")));
    }

    public function testExecuteWithNoGreetings(): void
    {
        // Arrange: La base est vide grâce à ResetDatabase

        // Act
        $commandTester = $this->executeCommand([]);

        // Assert
        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Aucun message de salutation à afficher.', $output);
    }

    protected function getCommandName(): string
    {
        return 'greeting:list';
    }
}
