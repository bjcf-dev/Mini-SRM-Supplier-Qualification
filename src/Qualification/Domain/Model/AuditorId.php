<?php

declare(strict_types=1);

namespace App\Qualification\Domain\Model;

readonly class AuditorId
{
    public function __construct(private string $value)
    {
        if (!self::isValidUuid($value)) {
            throw new \InvalidArgumentException('Invalid UUID v4 format for AuditorId.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    private static function isValidUuid(string $uuid): bool
    {
        return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid);
    }
}
