<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class ShowGreetingsPageController extends AbstractController
{
    #[Route('/greetings', name: 'app_greetings_show')]
    public function __invoke(): Response
    {
        return $this->render('greeting/show.html.twig');
    }
}
