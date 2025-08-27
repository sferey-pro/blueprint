<?php

declare(strict_types=1);

namespace App\Tests\Business\Contexts\Greeting\Infrastructure\Command;

use App\Business\Contexts\Greeting\Application\Command\CreateGreetingCommand;
use App\Business\Contexts\Greeting\Infrastructure\Command\CreateGreetingCliCommand;
use App\Kernel\Bus\CommandBusInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

#[Group('unit')]
#[Group('greeting')]
#[CoversClass(CreateGreetingCliCommand::class)]
final class CreateGreetingCliCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private CommandBusInterface&MockObject $commandBusMock;

    protected function setUp(): void
    {
        // 1. Arrange : On mock le CommandBus, notre seule dépendance.
        $this->commandBusMock = $this->createMock(CommandBusInterface::class);

        // 2. On instancie la commande manuellement avec son mock.
        $command = new CreateGreetingCliCommand($this->commandBusMock);

        // 3. On prépare le CommandTester de Symfony.
        $application = new Application();
        $application->add($command);
        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteSuccess(): void
    {
        // 1. Arrange : On s'attend à ce que la méthode "dispatch" soit appelée une seule fois.
        // C'est notre principale assertion sur l'interaction.
        $this->commandBusMock
            ->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(CreateGreetingCommand::class));

        // 2. Act : On exécute la commande avec un message.
        $this->commandTester->execute(['message' => 'Hello from a unit test!']);

        // 3. Assert : On vérifie que la commande s'est bien terminée et a affiché le bon message.
        self::assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Le message de salutation a été créé avec succès !', $output);
    }

    public function testExecuteHandlesErrorsGracefully(): void
    {
        // Arrange: On configure le mock pour qu'il lance une exception
        // lorsqu'il reçoit n'importe quel objet CreateGreetingCommand.
        $this->commandBusMock
            ->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(CreateGreetingCommand::class))
            ->willThrowException(new \RuntimeException('Bus en maintenance !'));

        // Act: On exécute la commande via le tester.
        $this->commandTester->execute(['message' => 'un message qui va échouer']);

        // Assert: On vérifie que la commande a bien échoué et affiché le bon message.
        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Une erreur est survenue : Bus en maintenance !', $output);
    }
}
