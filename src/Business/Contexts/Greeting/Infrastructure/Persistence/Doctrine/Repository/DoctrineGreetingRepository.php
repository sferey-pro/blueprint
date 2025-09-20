<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Infrastructure\Persistence\Doctrine\Repository;

use App\Business\Contexts\Greeting\Application\Query\{GreetingFinderInterface, GreetingStatisticsView, GreetingView};
use App\Business\Contexts\Greeting\Domain\{Greeting, GreetingRepositoryInterface, GreetingStatus};
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

    public function get(GreetingId $id): ?GreetingView
    {
        $dql = \sprintf(
            'SELECT NEW %s(g.id, g.message, g.status, g.createdAt) FROM %s g WHERE g.id = :id',
            GreetingView::class,
            Greeting::class
        );

        $query = $this->getEntityManager()->createQuery($dql)
                ->setParameter('id', $id->value());

        /** @var ?GreetingView */
        $result = $query->getOneOrNullResult();

        return $result;
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

    public function getStatistics(): GreetingStatisticsView
    {
        $qb = $this->createQueryBuilder('g')
            ->select('
                COUNT(g.id) as total,
                SUM(CASE WHEN g.status = :draft THEN 1 ELSE 0 END) as draftCount,
                SUM(CASE WHEN g.status = :published THEN 1 ELSE 0 END) as publishedCount
            ')
            ->setParameter('draft', GreetingStatus::DRAFT->value)
            ->setParameter('published', GreetingStatus::PUBLISHED->value);

        $result = $qb->getQuery()->getSingleResult();

        return new GreetingStatisticsView(
            (int) $result['total'],
            (int) $result['draftCount'],
            (int) $result['publishedCount']
        );
    }
}
