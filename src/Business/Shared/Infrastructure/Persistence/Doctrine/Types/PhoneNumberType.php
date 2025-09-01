<?php

declare(strict_types=1);

namespace App\Business\Shared\Infrastructure\Persistence\Doctrine\Types;

use App\Business\Shared\Domain\ValueObject\PhoneNumber;
use App\Kernel\Persistence\Adapter\Doctrine\Types\AbstractValueObjectStringType;

final class PhoneNumberType extends AbstractValueObjectStringType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const string NAME = 'phone_number';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return PhoneNumber::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
