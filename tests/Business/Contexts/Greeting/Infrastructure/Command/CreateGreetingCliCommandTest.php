<?php

declare(strict_types=1);

namespace App\Tests\Business\Contexts\Greeting\Infrastructure\Command;

use App\Business\Contexts\Greeting\Application\Command\CreateGreetingCommand;
use App\Business\Contexts\Greeting\Infrastructure\Command\CreateGreetingCliCommand;
use App\Business\Shared\Domain\Exception\ValidationException;
use App\Kernel\Bus\CommandBusInterface;
use PHPUnit\Framework\Attributes\{CoversClass, Group};
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
        $message = 'Hello from a unit test!';
        $email = 'test@example.com';

        $this->commandBusMock
            ->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(CreateGreetingCommand::class));

        // 2. Act : On exécute la commande avec un message.
        $this->commandTester->execute([
            'message' => $message,
            'author' => $email,
        ]);

        // 3. Assert : On vérifie que la commande s'est bien terminée et a affiché le bon message.
        self::assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Le message de salutation a été créé avec succès !', $output);
    }

    public function testExecuteHandlesValidationException(): void
    {
        // 1. Arrange : On simule une ValidationException venant du bus.
        // On y imbrique une InvalidArgumentException pour tester la récupération du message de l'exception précédente.
        $previousException = new \InvalidArgumentException('L\'email fourni n\'est pas valide.');
        $validationException = new ValidationException('La commande n\'est pas valide.', 0, $previousException);

        $this->commandBusMock
            ->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(CreateGreetingCommand::class))
            ->willThrowException($validationException);

        // 2. Act
        $this->commandTester->execute([
            'message' => 'Un message valide',
            'author' => 'email-invalide',
        ]);

        // 3. Assert
        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Erreur de validation : L\'email fourni n\'est pas valide.', $output);
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
        $this->commandTester->execute([
            'message' => 'un message qui va échouer',
            'author' => 'fail@example.com',
        ]);

        // Assert: On vérifie que la commande a bien échoué et affiché le bon message.
        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());

        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Une erreur est survenue : Bus en maintenance !', $output);
    }
}
