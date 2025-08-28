<?php

declare(strict_types=1);

namespace App\Tests\Business\Contexts\Greeting\Infrastructure\Persistence\Doctrine\Repository;

use App\Business\Contexts\Greeting\Application\Query\GreetingFinderInterface;
use App\Business\Contexts\Greeting\Application\Query\GreetingView;
use App\Business\Contexts\Greeting\Domain\Greeting;
use App\Business\Contexts\Greeting\Domain\GreetingRepositoryInterface;
use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use App\Business\Contexts\Greeting\Infrastructure\Persistence\Doctrine\Repository\DoctrineGreetingRepository;
use App\Tests\Factory\GreetingFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Psr\Clock\ClockInterface;
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
    private ?ClockInterface $clock;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->repository = $container->get(GreetingRepositoryInterface::class);
        $this->finder = $container->get(GreetingFinderInterface::class);
        $this->clock = $container->get(ClockInterface::class);
    }

    public function testAddPersistsGreeting(): void
    {
        // 1. Arrange
        $greeting = Greeting::create(
            'Hello from an integration test!',
            $this->clock->now(),
            $this->clock
        );

        // 2. Act
        $this->repository->add($greeting);
        $this->entityManager->flush();

        // 3. Assert
        GreetingFactory::assert()->exists([
            'id' => $greeting->id,
            'message' => 'Hello from an integration test!',
        ]);
    }

    public function testOfIdFindsExistingGreeting(): void
    {
        // 1. Arrange
        $greetingProxy = GreetingFactory::createOne();
        $greetingId = $greetingProxy->id;
        $this->entityManager->flush();
        $this->entityManager->clear();

        // 2. Act
        $foundGreeting = $this->repository->ofId($greetingId);

        // 3. Assert
        self::assertNotNull($foundGreeting);
        self::assertTrue($foundGreeting->id->equals($greetingId));
    }

    public function testOfIdReturnsNullForNonExistingGreeting(): void
    {
        // 1. Arrange
        $nonExistentId = GreetingId::generate();

        // 2. Act
        $foundGreeting = $this->repository->ofId($nonExistentId);

        // 3. Assert
        self::assertNull($foundGreeting);
    }

    public function testFindAllAsView(): void
    {
        // 1. Arrange
        GreetingFactory::createOne(['message' => 'Hello 1']);
        GreetingFactory::createOne(['message' => 'Hello 2']);
        GreetingFactory::createOne(['message' => 'Hello 3']);
        $this->entityManager->flush();
        $this->entityManager->clear();

        // 2. Act
        $greetingViews = $this->finder->findAllAsView();

        // 3. Assert
        self::assertCount(3, $greetingViews);
        self::assertContainsOnlyInstancesOf(GreetingView::class, $greetingViews);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
