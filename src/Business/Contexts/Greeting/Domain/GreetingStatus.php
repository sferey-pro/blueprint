<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Domain;

use App\Kernel\Enum\EnumJsonSerializableTrait;

/**
 * Représente le cycle de vie d'un Greeting.
 * L'utilisation d'un Backed Enum garantit la validité des statuts et améliore la lisibilité.
 */
enum GreetingStatus: string
{
    use EnumJsonSerializableTrait;

    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Brouillon',
            self::PUBLISHED => 'Publié',
            self::ARCHIVED => 'Archivé',
        };
    }

    public function equals(self $other): bool
    {
        return $this === $other;
    }
}
