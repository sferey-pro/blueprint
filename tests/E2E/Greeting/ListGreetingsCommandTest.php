<?php

declare(strict_types=1);

namespace App\Tests\E2E\Greeting;

use App\Business\Contexts\Greeting\Infrastructure\Command\ListGreetingsCliCommand;
use App\Tests\Factory\GreetingFactory;
use App\Tests\Helper\Command\CommandTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Psr\Clock\ClockInterface;
use Zenstruck\Foundry\Test\ResetDatabase;

#[Group('e2e')]
#[Group('greeting')]
#[CoversClass(ListGreetingsCliCommand::class)]
final class ListGreetingsCommandTest extends CommandTestCase
{
    use ResetDatabase;

    public function testExecuteWithGreetings(): void
    {
        // 1. Arrange: Crée 3 greetings en base de données
        GreetingFactory::new()
            ->withClock(self::getContainer()->get(ClockInterface::class))
            ->many(3)
            ->create();

        // 2. Act: Exécute la commande
        $commandTester = $this->executeCommand([]);

        // 3. Assert
        // a) On vérifie la sortie de la commande
        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Message', $output);

        // b) On vérifie qu'il y a bien 3 lignes de données (+ l'en-tête + separateur)
        self::assertCount(5, explode("\n", mb_trim($output, " \n+-")));
    }

    public function testExecuteWithNoGreetings(): void
    {
        // 1. Arrange: La base est vide grâce à ResetDatabase

        // 2. Act
        $commandTester = $this->executeCommand([]);

        // 3. Assert
        // a) On vérifie la sortie de la commande
        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();

        // b) On vérifie le message d'information
        self::assertStringContainsString('Aucun message de salutation à afficher.', $output);
    }

    protected function getCommandName(): string
    {
        return 'greeting:list';
    }
}
