<?php

declare(strict_types=1);

namespace App\Tests\Business\Contexts\Greeting\Application\Command;

use App\Business\Contexts\Greeting\Application\Command\PublishGreetingCommand;
use App\Business\Contexts\Greeting\Application\Command\PublishGreetingHandler;
use App\Business\Contexts\Greeting\Domain\Event\GreetingWasPublished;
use App\Business\Contexts\Greeting\Domain\Greeting;
use App\Business\Contexts\Greeting\Domain\GreetingRepositoryInterface;
use App\Business\Contexts\Greeting\Domain\GreetingStatus;
use App\Business\Contexts\Greeting\Domain\ValueObject\Author;
use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use App\Business\Shared\Domain\Port\UuidFactoryInterface;
use App\Business\Shared\Domain\ValueObject\Email;
use App\Tests\Faker\FakerUuidFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Workflow\WorkflowInterface;

#[Group('unit')]
#[Group('greeting')]
#[CoversClass(PublishGreetingHandler::class)]
final class PublishGreetingHandlerTest extends TestCase
{
    private GreetingRepositoryInterface&MockObject $repositoryMock;
    private WorkflowInterface&MockObject $workflowMock;
    private ClockInterface $clock;
    private UuidFactoryInterface $uuidFactory;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(GreetingRepositoryInterface::class);
        $this->workflowMock = $this->createMock(WorkflowInterface::class);
        $this->clock = new MockClock(); // Utilisation d'une horloge contrôlable pour les tests
        $this->uuidFactory = new FakerUuidFactory();
    }

    public function testInvokeCallsPublishOnAggregateWhenTransitionIsAllowed(): void
    {
        // 1. Arrange
        $greetingId = $this->uuidFactory->generate(GreetingId::class);
        $command = new PublishGreetingCommand($greetingId);

        // On crée une vraie instance de l'agrégat pour le test.
        $greeting = Greeting::create(
            'test message',
            Author::create(Email::fromValidatedValue('test@example.com')),
            $this->clock->now(),
            $this->uuidFactory,
            $this->clock
        );

        // On configure les mocks pour simuler le "happy path"
        $this->repositoryMock->method('ofId')->with($greetingId)->willReturn($greeting);
        $this->workflowMock->method('can')->with($greeting, 'publish')->willReturn(true);

        // On vide les événements générés durant la phase de préparation.
        $greeting->pullDomainEvents();

        // 2. Act
        $handler = new PublishGreetingHandler($this->uuidFactory, $this->clock, $this->repositoryMock, $this->workflowMock);
        $handler($command);

        // 3. Assert
        // On vérifie que l'état de l'agrégat a bien changé en conséquence de l'appel à `publish`.
        self::assertSame(GreetingStatus::PUBLISHED, $greeting->status);

        // On vérifie que l'agrégat a bien enregistré l'événement attendu.
        $events = $greeting->pullDomainEvents();
        self::assertCount(1, $events, 'Only one event (GreetingWasPublished) should have been raised.');
        self::assertInstanceOf(GreetingWasPublished::class, $events[0]);
    }

    public function testInvokeThrowsExceptionWhenTransitionIsNotAllowed(): void
    {
        // 3. Assert - On s'attend à une exception
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot publish this greeting.');

        // 1. Arrange
        $greetingId = $this->uuidFactory->generate(GreetingId::class);
        $command = new PublishGreetingCommand($greetingId);
        $greeting = Greeting::create(
            'test message',
            Author::create(Email::fromValidatedValue('test@example.com')),
            $this->clock->now(),
            $this->uuidFactory,
            $this->clock
        );

        $this->repositoryMock->method('ofId')->willReturn($greeting);
        // On configure le mock du workflow pour qu'il refuse la transition.
        $this->workflowMock->method('can')->with($greeting, 'publish')->willReturn(false);

        // 2. Act
        $handler = new PublishGreetingHandler($this->uuidFactory, $this->clock, $this->repositoryMock, $this->workflowMock);
        $handler($command);
    }

    public function testInvokeThrowsExceptionWhenGreetingIsNotFound(): void
    {
        // 3. Assert - On s'attend à une exception
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Greeting not found.');

        // 1. Arrange
        $greetingId = $this->uuidFactory->generate(GreetingId::class);
        $command = new PublishGreetingCommand($greetingId);

        // On configure le mock du repository pour qu'il retourne null,
        // simulant ainsi que le Greeting n'a pas été trouvé.
        $this->repositoryMock->method('ofId')->with($greetingId)->willReturn(null);

        // Le workflow ne sera jamais appelé, donc pas besoin de le mocker pour ce cas.
        $this->workflowMock->expects(self::never())->method('can');
        $this->workflowMock->expects(self::never())->method('apply');

        // 2. Act
        $handler = new PublishGreetingHandler($this->uuidFactory, $this->clock, $this->repositoryMock, $this->workflowMock);
        $handler($command);
    }
}
