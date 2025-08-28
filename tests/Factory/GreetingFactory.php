<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Business\Contexts\Greeting\Domain\Greeting;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\Clock;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Greeting>
 */
final class GreetingFactory extends PersistentObjectFactory
{
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

    public function withClock(?ClockInterface $clock = null): self
    {
        return $this->with(function () use ($clock) {
            return ['clock' => $clock ?? self::faker()->dateTime()];
        });
    }

    protected function initialize(): static
    {
        return $this
            ->instantiateWith(fn (array $attributes): Greeting => Greeting::create(
                $attributes['message'],
                $attributes['createdAt'],
                $attributes['clock'] ?? Clock::get(),
            ));
    }
}
