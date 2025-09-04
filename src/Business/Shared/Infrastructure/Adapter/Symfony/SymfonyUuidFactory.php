<?php

declare(strict_types=1);

namespace App\Business\Shared\Infrastructure\Adapter\Symfony;

use App\Business\Shared\Domain\Port\UuidFactoryInterface;
use App\Business\Shared\Domain\ValueObject\AbstractUid;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

final readonly class SymfonyUuidFactory implements UuidFactoryInterface
{
    public function generate(string $uidClass): AbstractUid
    {
        // 1. On utilise Symfony pour générer l'objet Uuid
        $symfonyUuid = Uuid::v7();

        // 2. On l'encapsule dans notre adaptateur d'infrastructure
        $domainUuid = new SymfonyUuid($symfonyUuid);

        // 3. On instancie la classe de Value Object du Domaine demandée (ex: GreetingId)
        //    en lui passant notre adaptateur qui respecte l'UuidInterface.
        return new $uidClass($domainUuid);
    }

    public function fromString(string $uidClass, string $uuid): AbstractUid
    {
        // 1. On utilise Symfony pour valider et créer l'objet Uuid
        $symfonyUuid = Uuid::fromString($uuid);

        // 2. On l'encapsule dans notre adaptateur
        $domainCompatibleUuid = new SymfonyUuid($symfonyUuid);

        // 3. On instancie la classe de Value Object du Domaine demandée
        Assert::isAOf($uidClass, AbstractUid::class);

        return new $uidClass($domainCompatibleUuid);
    }
}
