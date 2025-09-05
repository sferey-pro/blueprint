<?php

declare(strict_types=1);

namespace App\Business\Shared\Domain\ValueObject;

interface UuidInterface extends \Stringable
{
    public function equals(self $other): bool;
}
