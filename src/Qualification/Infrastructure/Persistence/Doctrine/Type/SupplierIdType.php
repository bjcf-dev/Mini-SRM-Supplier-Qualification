<?php

declare(strict_types=1);

namespace App\Qualification\Infrastructure\Persistence\Doctrine\Type;

use App\Qualification\Domain\Model\SupplierId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class SupplierIdType extends Type
{
    public const NAME = 'supplier_id';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getGuidTypeDeclarationSQL($column);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?SupplierId
    {
        if ($value === null || $value === '') {
            return null;
        }

        return new SupplierId($value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof SupplierId ? $value->value() : (string) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
