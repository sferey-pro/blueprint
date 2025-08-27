<?php

declare(strict_types=1);

namespace App\Tests\Business\Contexts\Greeting\Infrastructure\Command;

use App\Business\Contexts\Greeting\Application\Query\GreetingView;
use App\Business\Contexts\Greeting\Application\Query\ListGreetingsQuery;
use App\Business\Contexts\Greeting\Infrastructure\Command\ListGreetingsCliCommand;
use App\Kernel\Bus\QueryBusInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

#[Group('unit')]
#[Group('greeting')]
#[CoversClass(ListGreetingsCliCommand::class)]
final class ListGreetingsCliCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private QueryBusInterface&MockObject $queryBusMock;

    protected function setUp(): void
    {
        $this->queryBusMock = $this->createMock(QueryBusInterface::class);
        $command = new ListGreetingsCliCommand($this->queryBusMock);

        $application = new Application();
        $application->add($command);
        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteWithGreetings(): void
    {
        // 1. Arrange : On configure le mock pour qu'il retourne une liste de DTOs.
        $greetings = [
            new GreetingView('id-1', 'Message 1', new \DateTimeImmutable()),
            new GreetingView('id-2', 'Message 2', new \DateTimeImmutable()),
        ];
        $this->queryBusMock
            ->expects(self::once())
            ->method('ask')
            ->with(self::isInstanceOf(ListGreetingsQuery::class))
            ->willReturn($greetings);

        // 2. Act
        $this->commandTester->execute([]);

        // 3. Assert
        $this->commandTester->assertCommandIsSuccessful();
        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Message 1', $output);
        self::assertStringContainsString('Message 2', $output);
    }

    public function testExecuteWithNoGreetings(): void
    {
        // 1. Arrange : On configure le mock pour qu'il retourne un tableau vide.
        $this->queryBusMock
            ->expects(self::once())
            ->method('ask')
            ->with(self::isInstanceOf(ListGreetingsQuery::class))
            ->willReturn([]);

        // 2. Act
        $this->commandTester->execute([]);

        // 3. Assert
        $this->commandTester->assertCommandIsSuccessful();
        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Aucun message de salutation à afficher.', $output);
    }

    public function testExecuteHandlesErrorsGracefully(): void
    {
        // 1. Arrange : On configure le mock pour qu'il lance une exception.
        $this->queryBusMock
            ->expects(self::once())
            ->method('ask')
            ->with(self::isInstanceOf(ListGreetingsQuery::class))
            ->willThrowException(new \RuntimeException('Source de données indisponible.'));

        // 2. Act
        $this->commandTester->execute([]);

        // 3. Assert
        self::assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        $output = $this->commandTester->getDisplay();
        self::assertStringContainsString('Une erreur est survenue : Source de données indisponible.', $output);
    }
}
