<?php

declare(strict_types=1);

namespace App\Tests\Functional\Greeting;

use App\Business\Contexts\Greeting\Application\Command\CreateGreetingCommand;
use App\Kernel\Bus\CommandBusInterface;
use App\Tests\Factory\GreetingFactory;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

#[Group('functional')]
#[Group('greeting')]
final class CreateGreetingTest extends KernelTestCase
{
    use ResetDatabase; // On s'assure que la BDD est propre

    public function testCreateGreetingCommandPersistsData(): void
    {
        // 1. Arrange
        self::bootKernel();
        $container = self::getContainer();

        /** @var CommandBusInterface $commandBus */
        $commandBus = $container->get(CommandBusInterface::class);
        $message = 'Hello, functional test!';
        $command = new CreateGreetingCommand($message);

        // 2. Act
        $commandBus->dispatch($command);

        // 3. Assert
        // On utilise Foundry pour affirmer qu'une entité correspondant
        // à nos critères existe maintenant en base de données.
        GreetingFactory::assert()->exists([
            'message' => $message,
        ]);
    }
}
