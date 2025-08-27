<?php

declare(strict_types=1);

namespace App\Tests\Business\Contexts\Greeting\Application\Query;

use App\Business\Contexts\Greeting\Application\Query\GreetingView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[Group('greeting')]
#[CoversClass(GreetingView::class)]
final class GreetingViewTest extends TestCase
{
    public function testConstructorFormatsDateCorrectly(): void
    {
        // 1. Arrange
        $id = 'a72855f3-3361-41d3-8515-0181792b0efc';
        $message = 'Hello, DTO!';
        $createdAt = new \DateTimeImmutable('2023-10-27 10:30:45');

        // 2. Act
        $view = new GreetingView($id, $message, $createdAt);

        // 3. Assert
        self::assertSame($id, $view->id);
        self::assertSame($message, $view->message);
        self::assertSame('2023-10-27 10:30:45', $view->createdAt);
    }
}
