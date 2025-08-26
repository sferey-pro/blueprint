<?php

declare(strict_types=1);

namespace App\Tests\Business\Contexts\Greeting\Application\Command;

use App\Business\Contexts\Greeting\Application\Command\CreateGreetingCommand;
use App\Business\Contexts\Greeting\Application\Command\CreateGreetingHandler;
use App\Business\Contexts\Greeting\Domain\Greeting;
use App\Business\Contexts\Greeting\Domain\GreetingRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

#[Group('unit')]
#[Group('greeting')]
#[CoversClass(CreateGreetingHandler::class)]
final class CreateGreetingHandlerTest extends TestCase
{
    public function testInvoke(): void
    {
        // 1. Préparation (Arrange)
        $command = new CreateGreetingCommand('Hello from test!');
        $now = new \DateTimeImmutable();

        // Création du Mock pour le Repository
        $repositoryMock = $this->createMock(GreetingRepositoryInterface::class);
        $repositoryMock
            ->expects(self::once()) // On s'attend à ce que la méthode "add" soit appelée une seule fois
            ->method('add')
            ->with(self::isInstanceOf(Greeting::class)); // Avec un argument qui est une instance de Greeting

        // Création du Mock pour l'Horloge
        $clockMock = $this->createMock(ClockInterface::class);
        $clockMock
            ->expects(self::once())
            ->method('now')
            ->willReturn($now);

        // 2. Action (Act)
        $handler = new CreateGreetingHandler($repositoryMock, $clockMock);
        $handler($command);

        // 3. Assertion (Assert)
        // Les assertions sont définies dans la configuration des mocks (expects, with).
        // Si la méthode "add" n'est pas appelée, ou pas avec le bon type d'objet, le test échouera.
    }
}
