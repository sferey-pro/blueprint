<?php

declare(strict_types=1);

namespace App\Business\Contexts\Greeting\Application\Query;

interface GreetingFinderInterface
{
    /**
     * @return list<GreetingView>
     */
    public function findAllAsView(): array;
}
