<?php

declare(strict_types=1);

namespace App\Qualification\Infrastructure\Persistence\Doctrine\Type;

use App\Qualification\Domain\Model\AuditorId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class AuditorIdType extends Type
{
    public const NAME = 'auditor_id';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getGuidTypeDeclarationSQL($column);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?AuditorId
    {
        if ($value === null || $value === '') {
            return null;
        }

        return new AuditorId($value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof AuditorId ? $value->value() : (string) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
