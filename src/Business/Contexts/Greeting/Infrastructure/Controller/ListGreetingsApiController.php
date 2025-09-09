<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Infrastructure\Controller;

use App\Business\Contexts\Greeting\Application\Query\ListGreetingsQuery;
use App\Kernel\Bus\QueryBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
final class ListGreetingsApiController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route('/api/greetings', name: 'api_greetings_list', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $greetings = $this->queryBus->ask(new ListGreetingsQuery());

        return new JsonResponse(
            $this->serializer->serialize($greetings, 'json'),
            json: true
        );
    }
}
