<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Infrastructure\Persistence\Doctrine\Repository;

use App\Business\Contexts\Greeting\Application\Query\GreetingFinderInterface;
use App\Business\Contexts\Greeting\Application\Query\GreetingView;
use App\Business\Contexts\Greeting\Domain\Greeting;
use App\Business\Contexts\Greeting\Domain\GreetingRepositoryInterface;
use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Greeting>
 */
final class DoctrineGreetingRepository extends ServiceEntityRepository implements GreetingRepositoryInterface, GreetingFinderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Greeting::class);
    }

    public function add(Greeting $greeting): void
    {
        $this->getEntityManager()->persist($greeting);
    }

    public function ofId(GreetingId $id): ?Greeting
    {
        $greeting = $this->find($id);

        return $greeting;
    }

    public function findAllAsView(): array
    {
        $dql = \sprintf(
            'SELECT NEW %s(g.id, g.message, g.status, g.createdAt) FROM %s g ORDER BY g.createdAt DESC',
            GreetingView::class,
            Greeting::class
        );

        $query = $this->getEntityManager()->createQuery($dql);

        /** @var list<GreetingView> $result */
        $result = $query->getResult();

        return $result;
    }
}
