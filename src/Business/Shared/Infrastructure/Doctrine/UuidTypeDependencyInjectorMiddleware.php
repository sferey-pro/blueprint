<?php

declare(strict_types=1);

namespace App\Business\Shared\Infrastructure\Doctrine;

use App\Business\Shared\Domain\Port\UuidFactoryInterface;
use App\Business\Shared\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectIdType;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsMiddleware;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

#[AsMiddleware(priority: 10)]
final readonly class UuidTypeDependencyInjectorMiddleware implements Middleware
{
    /**
     * @param UuidFactoryInterface $uuidFactory La dépendance que nous voulons injecter
     */
    public function __construct(
        private UuidFactoryInterface $uuidFactory,
        #[AutowireIterator('doctrine.custom_type')]
        private iterable $customTypeNames,
    ) {
    }

    public function wrap(Driver $driver): Driver
    {
        foreach ($this->customTypeNames as $typeName) {
            if (Type::hasType($typeName::NAME)) {
                $type = Type::getType($typeName::NAME);
                if ($type instanceof AbstractValueObjectIdType) {
                    $type->setFactory($this->uuidFactory);
                }
            }
        }

        return $driver;
    }
}
