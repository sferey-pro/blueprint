<?php

declare(strict_types=1);

namespace App\Tests\Functional\Greeting;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Business\Contexts\Greeting\Application\Query\GreetingView;
use App\Tests\Factory\GreetingFactory;
use PHPUnit\Framework\Attributes\Group;
use Zenstruck\Foundry\Test\{Factories, ResetDatabase};

#[Group('e2e')]
#[Group('greeting')]
final class GetGreetingsCollectionTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;
    protected static ?bool $alwaysBootKernel = false;

    public function testGetGreetingsCollection(): void
    {
        // 1. Arrange
        GreetingFactory::createMany(15);

        // 2. Act
        $response = static::createClient()->request('GET', '/api/greetings');

        // 3. Assert
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Greeting',
            '@id' => '/api/greetings',
            '@type' => 'Collection',
            'totalItems' => 15,
        ]);

        $this->assertCount(15, $response->toArray()['member']);

        $this->assertMatchesResourceCollectionJsonSchema(GreetingView::class);
    }
}
