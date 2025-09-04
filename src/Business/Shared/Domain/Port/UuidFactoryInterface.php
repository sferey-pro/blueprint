<?php

declare(strict_types=1);

namespace App\Business\Shared\Domain\Port;

use App\Business\Shared\Domain\ValueObject\AbstractUid;

/**
 * Port pour la création de Value Objects d'identifiants.
 *
 * @template T of AbstractUid
 */
interface UuidFactoryInterface
{
    /**
     * Crée une instance d'un Value Object d'identifiant basé sur UUIDv7.
     *
     * @param class-string<T> $uidClass Le FQCN du Value Object à créer (ex: GreetingId::class)
     *
     * @return T L'instance du Value Object créé
     */
    public function generate(string $uidClass): AbstractUid;

    /**
     * Crée une instance d'un Value Object d'identifiant à partir d'une string.
     *
     * @param class-string<T> $uidClass Le FQCN du Value Object à créer
     * @param string          $uuid     La chaîne de l'UUID à valider et utiliser
     *
     * @return T L'instance du Value Object créé
     */
    public function fromString(string $uidClass, string $uuid): AbstractUid;
}
