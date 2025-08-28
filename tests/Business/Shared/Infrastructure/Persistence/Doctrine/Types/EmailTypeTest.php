<?php

declare(strict_types=1);

namespace App\Tests\Business\Shared\Infrastructure\Persistence\Doctrine\Types;

use App\Business\Shared\Domain\ValueObject\Email;
use App\Business\Shared\Infrastructure\Persistence\Doctrine\Types\EmailType;
use App\Tests\Helper\Doctrine\Types\ValueObjectStringTypeTestCase;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

#[Group('unit')]
#[Group('greeting')]
#[CoversClass(EmailType::class)]
final class EmailTypeTest extends ValueObjectStringTypeTestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!Type::hasType(EmailType::NAME)) {
            Type::addType(EmailType::NAME, EmailType::class);
        }
    }

    protected function getTypeClass(): string
    {
        return EmailType::class;
    }

    protected function getValueObjectClass(): string
    {
        return Email::class;
    }

    protected function getTypeName(): string
    {
        return EmailType::NAME;
    }

    protected function getValidValue(): string
    {
        return 'test@example.com';
    }
}
