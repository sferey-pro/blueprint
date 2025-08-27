<?php

declare(strict_types=1);

namespace App\Tests\Business\Contexts\Greeting\Application\Query;

use App\Business\Contexts\Greeting\Application\Query\GreetingFinderInterface;
use App\Business\Contexts\Greeting\Application\Query\ListGreetingsHandler;
use App\Business\Contexts\Greeting\Application\Query\ListGreetingsQuery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[Group('greeting')]
#[CoversClass(ListGreetingsHandler::class)]
final class ListGreetingsHandlerTest extends TestCase
{
    public function testInvoke(): void
    {
        // 1. Arrange: On mock le Finder.
        $finderMock = $this->createMock(GreetingFinderInterface::class);
        $finderMock
            ->expects(self::once()) // On s'attend à ce que la méthode "findAllAsView" soit appelée.
            ->method('findAllAsView')
            ->willReturn(['...données mockées...']); // Et on définit ce qu'elle doit retourner.

        // 2. Act: On injecte le mock dans le handler.
        $handler = new ListGreetingsHandler($finderMock);
        $result = $handler(new ListGreetingsQuery());

        // 3. Assert: On vérifie que le handler retourne bien ce que le finder lui a donné.
        self::assertSame(['...données mockées...'], $result);
    }
}
