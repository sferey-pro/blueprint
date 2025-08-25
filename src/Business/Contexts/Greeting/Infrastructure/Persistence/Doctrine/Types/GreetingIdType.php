<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Infrastructure\Persistence\Doctrine\Types;

use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;
use App\Kernel\Persistence\Adapter\Doctrine\Types\AbstractValueObjectIdType;

/**
 * Classe de type Doctrine pour le ValueObject GreetingId.
 * Permet à Doctrine de comprendre comment stocker et récupérer cet objet.
 */
final class GreetingIdType extends AbstractValueObjectIdType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const string NAME = 'greeting_id';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return GreetingId::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
