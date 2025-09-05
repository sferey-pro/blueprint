<?php

declare(strict_types=1);

namespace App\Tests\Business\Contexts\Greeting\Domain;

use App\Business\Contexts\Greeting\Domain\GreetingStatus;
use PHPUnit\Framework\Attributes\{CoversClass, DataProvider, Group};
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[Group('greeting')]
#[CoversClass(GreetingStatus::class)]
final class GreetingStatusTest extends TestCase
{
    /**
     * @param non-empty-string $expectedLabel
     */
    #[DataProvider('provideStatusLabels')]
    public function testGetLabel(GreetingStatus $status, string $expectedLabel): void
    {
        self::assertSame($expectedLabel, $status->getLabel());
    }

    public static function provideStatusLabels(): \Generator
    {
        yield 'Draft status' => [GreetingStatus::DRAFT, 'Brouillon'];
        yield 'Published status' => [GreetingStatus::PUBLISHED, 'Publié'];
        yield 'Archived status' => [GreetingStatus::ARCHIVED, 'Archivé'];
    }

    public function testEquals(): void
    {
        self::assertTrue(GreetingStatus::DRAFT->equals(GreetingStatus::DRAFT));
        self::assertFalse(GreetingStatus::DRAFT->equals(GreetingStatus::PUBLISHED));
    }

    public function testJsonSerialize(): void
    {
        $expectedJson = '{"DRAFT":"draft","PUBLISHED":"published","ARCHIVED":"archived"}';
        self::assertSame($expectedJson, GreetingStatus::jsonSerialize());
    }
}
