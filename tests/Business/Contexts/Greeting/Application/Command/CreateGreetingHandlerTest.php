<?php

declare(strict_types=1);

namespace App\Tests\Business\Contexts\Greeting\Application\Command;

use App\Business\Contexts\Greeting\Application\Command\CreateGreetingCommand;
use App\Business\Contexts\Greeting\Application\Command\CreateGreetingHandler;
use App\Business\Contexts\Greeting\Domain\Event\GreetingWasCreated;
use App\Business\Contexts\Greeting\Domain\Greeting;
use App\Business\Contexts\Greeting\Domain\GreetingRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;

#[Group('unit')]
#[Group('greeting')]
#[CoversClass(CreateGreetingHandler::class)]
final class CreateGreetingHandlerTest extends TestCase
{
    private GreetingRepositoryInterface&MockObject $repositoryMock;
    private ClockInterface&MockClock $clock;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(GreetingRepositoryInterface::class);
        $this->clock = new MockClock();
    }

    public function testInvokeUsesClockWhenDateIsNotProvided(): void
    {
        // 1. Arrange
        $command = new CreateGreetingCommand('Hello from test!', 'test@example.com');
        $now = $this->clock->now();

        // On prépare une variable pour "capturer" l'argument passé à la méthode add()
        /** @var ?Greeting $greetingCaptor */
        $greetingCaptor = null;

        $this->repositoryMock
            ->expects(self::once())
            ->method('add')
            ->with(self::callback(function (Greeting $greeting) use (&$greetingCaptor) {
                $greetingCaptor = $greeting; // On capture l'argument

                return true; // On valide toujours l'appel
            }));

        // 2. Act
        $handler = new CreateGreetingHandler($this->repositoryMock, $this->clock);
        $handler($command);

        // 3. Assert
        // On fait maintenant des assertions claires sur l'objet capturé
        self::assertInstanceOf(Greeting::class, $greetingCaptor);
        self::assertEquals($now, $greetingCaptor->createdAt);
    }

    public function testInvokeWithSpecificDateUsesCorrectTimestamps(): void
    {
        // 1. Arrange
        $businessDate = new \DateTimeImmutable('2024-12-24 18:00:00'); // Une date métier spécifique
        $systemDate = $this->clock->now(); // L'heure "actuelle" de l'action
        $command = new CreateGreetingCommand('Hello for Christmas!', 'test@example.com', $businessDate);

        $this->repositoryMock
            ->expects(self::once())
            ->method('add')
            ->with(self::callback(function (Greeting $greeting) use ($businessDate, $systemDate) {
                // Assertion 1: La date de création de l'agrégat doit être la date métier.
                self::assertEquals($businessDate, $greeting->createdAt);

                // Assertion 2: L'événement doit être horodaté avec l'heure système.
                $events = $greeting->pullDomainEvents();
                self::assertCount(1, $events);
                self::assertInstanceOf(GreetingWasCreated::class, $events[0]);
                self::assertEquals($systemDate, $events[0]->occurredOn);

                return true; // Le callback doit retourner true pour que le test passe
            }));

        // 2. Act
        $handler = new CreateGreetingHandler($this->repositoryMock, $this->clock);
        $handler($command);
    }
}
