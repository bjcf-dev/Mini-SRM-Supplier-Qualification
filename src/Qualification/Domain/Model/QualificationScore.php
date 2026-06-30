<?php

declare(strict_types=1);

namespace App\Qualification\Domain\Model;

readonly class QualificationScore
{
    public function __construct(private int $value)
    {
        if ($value < 0 || $value > 100) {
            throw new \InvalidArgumentException('QualificationScore must be between 0 and 100.');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function isApproved(): bool
    {
        return $this->value >= 60;
    }

    public function isRejected(): bool
    {
        return $this->value < 60;
    }
}
