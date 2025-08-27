<?php

declare(strict_types=1);

namespace App\Tests\Business\Contexts\Greeting\Infrastructure\Persistence\Doctrine\Repository;

use App\Business\Contexts\Greeting\Application\Query\GreetingFinderInterface;
use App\Business\Contexts\Greeting\Application\Query\GreetingView;
use App\Business\Contexts\Greeting\Domain\Greeting;
use App\Business\Contexts\Greeting\Domain\GreetingRepositoryInterface;
use App\Business\Contexts\Greeting\Infrastructure\Persistence\Doctrine\Repository\DoctrineGreetingRepository;
use App\Tests\Factory\GreetingFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

#[Group('integration')]
#[Group('greeting')]
#[CoversClass(DoctrineGreetingRepository::class)]
final class DoctrineGreetingRepositoryTest extends KernelTestCase
{
    use ResetDatabase;

    private ?EntityManagerInterface $entityManager;
    private ?GreetingRepositoryInterface $repository;
    private ?GreetingFinderInterface $finder;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->repository = $container->get(GreetingRepositoryInterface::class);
        $this->finder = $container->get(GreetingFinderInterface::class);
    }

    public function testAddAndFind(): void
    {
        // 1. Préparation (Arrange)
        $greeting = Greeting::create(
            'Hello from an integration test!',
            new \DateTimeImmutable()
        );
        $greetingId = $greeting->id;

        // 2. Action (Act)
        $this->repository->add($greeting);
        // Dans un test d'intégration, nous contrôlons manuellement la transaction.
        $this->entityManager->flush();
        // On vide l'entity manager pour s'assurer qu'on récupère l'entité depuis la BDD
        // et non depuis le cache de l'unité de travail.
        $this->entityManager->clear();

        // 3. Assertion (Assert)
        $foundGreeting = $this->repository->find($greetingId);

        self::assertNotNull($foundGreeting);
        self::assertTrue($foundGreeting->id->equals($greetingId));
        self::assertSame('Hello from an integration test!', $foundGreeting->message());
    }

    public function testFindAllAsView(): void
    {
        // 1. Arrange
        GreetingFactory::createOne(['message' => 'Hello 1', 'createdAt' => new \DateTimeImmutable('2025-01-01 10:00:00')]);
        GreetingFactory::createOne(['message' => 'Hello 2', 'createdAt' => new \DateTimeImmutable('2025-01-01 11:00:00')]);
        GreetingFactory::createOne(['message' => 'Hello 3', 'createdAt' => new \DateTimeImmutable('2025-01-01 12:00:00')]);

        // On s'assure que les données sont bien en base
        $this->entityManager->flush();
        $this->entityManager->clear();

        // 2. Act
        $greetingViews = $this->finder->findAllAsView();

        // 3. Assert
        self::assertCount(3, $greetingViews);
        self::assertContainsOnlyInstancesOf(GreetingView::class, $greetingViews);

        // On peut optionnellement vérifier le contenu d'un des DTOs
        self::assertSame('Hello 3', $greetingViews[0]->message); // Le tri est DESC par défaut
        self::assertSame('Hello 2', $greetingViews[1]->message);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
