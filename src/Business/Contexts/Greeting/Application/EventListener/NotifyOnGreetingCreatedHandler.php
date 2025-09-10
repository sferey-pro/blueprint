<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Application\EventListener;

use App\Business\Contexts\Greeting\Domain\Event\GreetingWasCreated;
use App\Business\Contexts\Greeting\Domain\GreetingStatus;
use App\Kernel\Attribute\AsEventListener;
use Symfony\Component\Mercure\{HubInterface, Update};
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Ce handler écoute la création d'un Greeting et publie une mise à jour Mercure.
 */
#[AsEventListener]
final readonly class NotifyOnGreetingCreatedHandler
{
    public function __construct(
        private HubInterface $hub,
        private SerializerInterface $serializer,
    ) {
    }

    public function __invoke(GreetingWasCreated $event): void
    {
        // 1. Nous sérialisons l'objet Greeting (ou un DTO) en JSON.
        // Pour cela, nous devons récupérer l'objet complet.
        // NOTE : Pour une architecture parfaite, l'événement contiendrait déjà un DTO sérialisable.
        // Ici, nous faisons un compromis en ne transmettant que les données nécessaires.
        $payload = [
            'id' => (string) $event->greetingId,
            'message' => $event->message,
            'status' => GreetingStatus::DRAFT->value, // Un nouveau greeting est toujours en DRAFT
            'createdAt' => $event->createdAt->format('Y-m-d H:i:s'),
            // On pourrait ajouter l'auteur si nécessaire
        ];

        $jsonPayload = $this->serializer->serialize($payload, 'json');

        // 2. Nous créons une mise à jour Mercure.
        // Le premier argument est le "topic" : un nom de canal unique.
        // Une bonne pratique est d'utiliser une URL qui peut correspondre à une ressource API.
        $update = new Update(
            'https://localhost/greetings-notify', // Topic auquel les clients vont s'abonner
            $jsonPayload,
            false // `private` à true si vous voulez gérer les droits d'accès plus tard
        );

        // 3. Nous publions la mise à jour sur le Hub.
        $this->hub->publish($update);
    }
}
