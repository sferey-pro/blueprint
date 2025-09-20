<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Application\Query;

use App\Business\Contexts\Greeting\Domain\GreetingStatus;

/**
 * Modèle de lecture (DTO) pour un Greeting.
 * C'est une structure de données simple destinée à l'affichage.
 */
final readonly class GreetingView
{
    public function __construct(
        public string $id,
        public string $message,
        public GreetingStatus $status,
        public \DateTimeImmutable $createdAt,
    ) {
    }
}
