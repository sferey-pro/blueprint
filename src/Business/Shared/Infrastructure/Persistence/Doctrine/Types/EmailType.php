<?php

declare(strict_types=1);

namespace App\Business\Shared\Infrastructure\Persistence\Doctrine\Types;

use App\Business\Shared\Domain\ValueObject\Email;
use App\Kernel\Persistence\Adapter\Doctrine\Types\AbstractValueObjectStringType;

final class EmailType extends AbstractValueObjectStringType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const string NAME = 'email';

    public function getTypeName(): string
    {
        return Email::class;
    }

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return Email::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
