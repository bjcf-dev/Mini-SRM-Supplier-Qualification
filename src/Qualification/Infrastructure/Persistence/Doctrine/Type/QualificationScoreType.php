<?php

declare(strict_types=1);

namespace App\Qualification\Infrastructure\Persistence\Doctrine\Type;

use App\Qualification\Domain\Model\QualificationScore;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class QualificationScoreType extends Type
{
    public const NAME = 'qualification_score';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?QualificationScore
    {
        if ($value === null) {
            return null;
        }

        return new QualificationScore((int) $value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?int
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof QualificationScore ? $value->value() : (int) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
