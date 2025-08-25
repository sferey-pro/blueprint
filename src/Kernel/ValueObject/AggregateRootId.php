<?php

declare(strict_types=1);

namespace App\Kernel\ValueObject;

/**
 * Classe de base abstraite pour tous les identifiants d'agrégat.
 * Fournit une base commune et permet le typage polymorphique.
 * Un UserId, un OrderId, etc. doivent hériter de cette classe.
 */
abstract readonly class AggregateRootId extends AbstractUid
{
}
