<?php

declare(strict_types=1);

namespace App\Qualification\Application\Create;

readonly class CreateQualificationCommand
{
    public function __construct(
        public string $supplierId,
        public string $auditorId,
        public int $score,
        public string $comments = '',
    ) {}
}
