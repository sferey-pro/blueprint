<?php

declare(strict_types=1);

namespace App\Business\Shared\Domain\Aggregate;

abstract class AggregateRoot
{
    use RecordsDomainEvents;
}
