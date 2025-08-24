<?php

declare(strict_types=1);

namespace App\Kernel\Bus;

use App\Kernel\Bus\Message\Query;

interface QueryBusInterface
{
    /**
     * @template T
     *
     * @param Query<T> $query
     *
     * @return T
     */
    public function ask(Query $query): mixed;
}
