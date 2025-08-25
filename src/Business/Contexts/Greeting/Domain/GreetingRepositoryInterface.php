<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Domain;

interface GreetingRepositoryInterface
{
    public function add(Greeting $greeting): void;
}
