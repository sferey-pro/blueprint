<?php

declare(strict_types=1);

namespace App\Business\Shared\Infrastructure\Persistence\Doctrine\Types;

use App\Business\Shared\Domain\ValueObject\AbstractStringValueObject;
use App\Kernel\Persistence\Adapter\Doctrine\Types\DoctrineCustomTypeInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

abstract class AbstractValueObjectStringType extends Type implements DoctrineCustomTypeInterface
{
    /**
     * Doit retourner le nom unique du type Doctrine.
     * Ce nom sera utilisé dans les fichiers de mapping XML.
     */
    abstract public function getName(): string;

    /**
     * Doit retourner le FQCN (Fully Qualified Class Name) de la classe du Value Object.
     * Ex: SharedKernel\Domain\ValueObject\EmailAddress::class.
     */
    abstract protected function getValueObjectClass(): string;

    public function convertToPHPValue($value, AbstractPlatform $platform): ?AbstractStringValueObject
    {
        if (null === $value) {
            return null;
        }

        $voClass = $this->getValueObjectClass();
        $result = $voClass::create($value);

        if ($result->isFailure()) {
            // Une donnée invalide en BDD est une corruption, une exception est justifiée.
            throw new \RuntimeException(\sprintf('Failed to convert database value "%s" to Value Object %s: %s', $value, $voClass, $result->error()->getMessage()));
        }

        return $result->value();
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof AbstractStringValueObject) {
            throw new \InvalidArgumentException(\sprintf('Expected instance of %s, got %s', $this->getValueObjectClass(), get_debug_type($value)));
        }

        return $value->value;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }
}
