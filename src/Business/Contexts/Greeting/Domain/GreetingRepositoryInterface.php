<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Domain;

use App\Business\Contexts\Greeting\Domain\ValueObject\GreetingId;

interface GreetingRepositoryInterface
{
    public function add(Greeting $greeting): void;

    public function ofId(GreetingId $id): ?Greeting;
}
