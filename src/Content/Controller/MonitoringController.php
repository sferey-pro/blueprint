<?php

declare(strict_types=1);

namespace App\Content\Controller;

use App\Business\Contexts\Greeting\Application\Query\{GetGreetingStatisticsQuery, GreetingStatisticsView};
use App\Kernel\Bus\QueryBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MonitoringController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    #[Route('/monitoring', name: 'app_monitoring')]
    public function __invoke(): Response
    {
        /** @var GreetingStatisticsView $greetingStats */
        $greetingStats = $this->queryBus->ask(new GetGreetingStatisticsQuery());

        return $this->render('content/monitoring/index.html.twig', [
            'greetingStats' => $greetingStats,
        ]);
    }
}
