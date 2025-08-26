<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Business\Contexts\Greeting\Domain\Greeting;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Greeting>
 */
final class GreetingFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Greeting::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'message' => self::faker()->sentence(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->instantiateWith(static function (array $attributes): Greeting {
                return Greeting::create(
                    $attributes['message'],
                    $attributes['createdAt']
                );
            })
        ;
    }
}
