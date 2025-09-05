<?php

declare(strict_types=1);

namespace App\Tests\Faker;

use App\Business\Shared\Domain\Port\UuidFactoryInterface;
use App\Business\Shared\Domain\ValueObject\AbstractUid;
use App\Business\Shared\Infrastructure\Adapter\Symfony\SymfonyUuid;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\Uid\Uuid;

#[When(env: 'test')]
final class FakerUuidFactory implements UuidFactoryInterface
{
    private static int $sequence = 0;

    public function generate(string $uidClass): AbstractUid
    {
        ++self::$sequence;
        $uuid = Uuid::fromString(\sprintf('00000000-0000-0000-0000-%012d', self::$sequence++));

        return new $uidClass(new SymfonyUuid($uuid));
    }

    public function fromString(string $uidClass, string $uuid): AbstractUid
    {
        return new $uidClass(new SymfonyUuid(Uuid::fromString($uuid)));
    }

    public static function reset(): void
    {
        self::$sequence = 0;
    }
}
