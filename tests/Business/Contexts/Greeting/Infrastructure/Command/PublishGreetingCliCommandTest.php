<?php

declare(strict_types=1);

namespace App\Tests\Business\Contexts\Greeting\Infrastructure\Command;

use App\Business\Contexts\Greeting\Application\Command\PublishGreetingCommand;
use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use App\Business\Contexts\Greeting\Infrastructure\Command\PublishGreetingCliCommand;
use App\Business\Shared\Domain\Port\UuidFactoryInterface;
use App\Kernel\Bus\CommandBusInterface;
use App\Tests\Faker\FakerUuidFactory;
use PHPUnit\Framework\Attributes\{CoversClass, Group};
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

#[Group('unit')]
#[Group('greeting')]
#[CoversClass(PublishGreetingCliCommand::class)]
final class PublishGreetingCliCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private CommandBusInterface&MockObject $commandBusMock;
    private UuidFactoryInterface $uuidFactory;

    protected function setUp(): void
    {
        $this->uuidFactory = new FakerUuidFactory();
        $this->commandBusMock = $this->createMock(CommandBusInterface::class);
        $command = new PublishGreetingCliCommand($this->commandBusMock, $this->uuidFactory);

        $application = new Application();
        $application->add($command);
        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteSuccess(): void
    {
        // 1. Arrange
        $greetingId = $this->uuidFactory->generate(GreetingId::class);

        // On s'attend à ce que le bus soit appelé 1x avec la bonne commande
        $this->commandBusMock
            ->expects(self::once())
            ->method('dispatch')
            ->with(self::callback(
                fn (PublishGreetingCommand $command) => $command->greetingId->equals($greetingId)
            ));

        // 2. Act
        $this->commandTester->execute(['id' => (string) $greetingId]);

        // 3. Assert
        $this->commandTester->assertCommandIsSuccessful();
        self::assertStringContainsString('Le message de salutation a été publié avec succès !', $this->commandTester->getDisplay());
    }

    public function testExecuteHandlesBusErrorsGracefully(): void
    {
        // 1. Arrange
        $greetingId = $this->uuidFactory->generate(GreetingId::class);
        $this->commandBusMock
            ->expects(self::once())
            ->method('dispatch')
            ->willThrowException(new \RuntimeException('Workflow en panne.'));

        // 2. Act
        $this->commandTester->execute(['id' => (string) $greetingId]);

        // 3. Assert
        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        self::assertStringContainsString('Une erreur est survenue : Workflow en panne.', $this->commandTester->getDisplay());
    }

    public function testExecuteFailsWithInvalidUuid(): void
    {
        // 1. Arrange
        $invalidId = 'ceci-nest-pas-un-uuid';

        // Le bus ne doit jamais être appelé si l'ID est invalide en amont
        $this->commandBusMock->expects(self::never())->method('dispatch');

        // 2. Act
        $this->commandTester->execute(['id' => $invalidId]);

        // 3. Assert
        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        self::assertStringContainsString('Invalid UUID: "ceci-nest-pas-un-uuid"', $this->commandTester->getDisplay());
    }
}
