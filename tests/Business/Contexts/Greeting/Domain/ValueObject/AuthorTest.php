<?php

declare(strict_types=1);

namespace App\Tests\Business\Contexts\Greeting\Domain\ValueObject;

use App\Business\Contexts\Greeting\Domain\ValueObject\Author;
use App\Business\Shared\Domain\ValueObject\Email;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[Group('greeting')]
#[CoversClass(Author::class)]
final class AuthorTest extends TestCase
{
    public function testCreateAndValue(): void
    {
        $email = Email::fromValidatedValue('author@example.com');
        $author = Author::create($email);

        self::assertInstanceOf(Author::class, $author);
        self::assertSame($email, $author->value());
        self::assertSame('author@example.com', (string) $author);
    }

    public function testEquals(): void
    {
        $email1 = Email::fromValidatedValue('author@example.com');
        $author1 = Author::create($email1);

        $email2 = Email::fromValidatedValue('author@example.com');
        $author2 = Author::create($email2);

        $email3 = Email::fromValidatedValue('another@example.com');
        $author3 = Author::create($email3);

        self::assertTrue($author1->equals($author2));
        self::assertFalse($author1->equals($author3));
    }

    public function testToArrayAndFromArray(): void
    {
        $email = Email::fromValidatedValue('author@example.com');
        $author = Author::create($email);

        $expectedArray = ['email' => 'author@example.com'];
        self::assertSame($expectedArray, $author->__toArray());

        $recreatedAuthor = Author::fromArray($expectedArray);
        self::assertTrue($author->equals($recreatedAuthor));
    }

    public function testFromArrayWithMissingDataThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required field: email');

        Author::fromArray([]);
    }
}
