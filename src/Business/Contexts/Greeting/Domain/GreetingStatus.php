<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Domain;

/**
 * Représente le cycle de vie d'un Greeting.
 * L'utilisation d'un Backed Enum garantit la validité des statuts et améliore la lisibilité.
 */
enum GreetingStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}
