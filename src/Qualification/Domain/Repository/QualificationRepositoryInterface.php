<?php

declare(strict_types=1);

namespace App\Qualification\Domain\Repository;

use App\Qualification\Domain\Model\Qualification;
use App\Qualification\Domain\Model\QualificationId;

interface QualificationRepositoryInterface
{
    public function save(Qualification $qualification): void;

    /** @return Qualification[] */
    public function findAll(): array;

    public function deleteById(QualificationId $id): void;

    public function deleteAll(): void;
}
