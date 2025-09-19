<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Application\Query;

use ApiPlatform\Metadata\{ApiProperty, ApiResource, GetCollection};
use App\Business\Contexts\Greeting\Domain\GreetingStatus;
use App\Business\Contexts\Greeting\Infrastructure\ApiPlatform\GreetingCollectionProvider;

/**
 * Modèle de lecture (DTO) pour un Greeting.
 * C'est une structure de données simple destinée à l'affichage.
 */
#[ApiResource(
    operations: [
        new GetCollection(provider: GreetingCollectionProvider::class),
    ],
    shortName: 'Greeting',
)]
final readonly class GreetingView
{
    public string $createdAt;

    public function __construct(
        #[ApiProperty(identifier: true)]
        public string $id,
        public string $message,
        public GreetingStatus $status,
        \DateTimeImmutable $createdAt,
    ) {
        // Le DTO est maintenant responsable du formatage de la date pour l'affichage.
        $this->createdAt = $createdAt->format('Y-m-d H:i:s');
    }
}
