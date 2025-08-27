<?php

declare(strict_types=1);

namespace App\Tests\E2E\Greeting;

use App\Business\Contexts\Greeting\Infrastructure\Command\CreateGreetingCliCommand;
use App\Tests\Factory\GreetingFactory;
use App\Tests\Helper\Command\AbstractCommandTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Zenstruck\Foundry\Test\ResetDatabase;

#[Group('e2e')]
#[Group('greeting')]
#[CoversClass(CreateGreetingCliCommand::class)]
final class CreateGreetingCommandTest extends AbstractCommandTestCase
{
    use ResetDatabase;

    public function testExecute(): void
    {
        // 1. Arrange
        $message = 'Hello from a Foundry-powered E2E test!';

        // 2. Act
        $commandTester = $this->executeCommand([
            'message' => $message,
        ]);

        // 3. Assert
        // a) On vérifie la sortie de la commande
        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Le message de salutation a été créé avec succès !', $output);

        // b) On vérifie la persistance en base de données avec la syntaxe expressive de Foundry
        GreetingFactory::assert()->count(1, [
            'message' => $message,
        ]);
    }

    protected function getCommandName(): string
    {
        return 'greeting:create';
    }
}
