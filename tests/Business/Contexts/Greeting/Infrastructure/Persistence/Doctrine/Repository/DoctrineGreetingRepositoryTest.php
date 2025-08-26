<?php

declare(strict_types=1);

namespace App\Tests\Business\Contexts\Greeting\Infrastructure\Persistence\Doctrine\Repository;

use App\Business\Contexts\Greeting\Domain\Greeting;
use App\Business\Contexts\Greeting\Domain\GreetingRepositoryInterface;
use App\Business\Contexts\Greeting\Infrastructure\Persistence\Doctrine\Repository\DoctrineGreetingRepository;
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

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->repository = $container->get(GreetingRepositoryInterface::class);
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

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
