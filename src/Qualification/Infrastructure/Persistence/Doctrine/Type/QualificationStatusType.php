<?php

declare(strict_types=1);

namespace App\Qualification\Infrastructure\Persistence\Doctrine\Type;

use App\Qualification\Domain\Model\QualificationStatus;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class QualificationStatusType extends Type
{
    public const NAME = 'qualification_status';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?QualificationStatus
    {
        if ($value === null) {
            return null;
        }

        return QualificationStatus::from($value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof QualificationStatus ? $value->value : (string) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
