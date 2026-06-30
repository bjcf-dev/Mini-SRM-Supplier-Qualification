<?php

declare(strict_types=1);

namespace App\Qualification\Application\Create;

use App\Qualification\Domain\Model\AuditorId;
use App\Qualification\Domain\Model\Qualification;
use App\Qualification\Domain\Model\QualificationId;
use App\Qualification\Domain\Model\QualificationScore;
use App\Qualification\Domain\Model\SupplierId;
use App\Qualification\Domain\Repository\QualificationRepositoryInterface;

readonly class CreateQualificationCommandHandler
{
    public function __construct(
        private QualificationRepositoryInterface $repository,
    ) {}

    public function __invoke(CreateQualificationCommand $command): QualificationId
    {
        $id = new QualificationId($this->generateUuid());
        $supplierId = new SupplierId($command->supplierId);
        $auditorId = new AuditorId($command->auditorId);
        $score = new QualificationScore($command->score);

        $qualification = Qualification::create(
            id: $id,
            supplierId: $supplierId,
            auditorId: $auditorId,
            score: $score,
            comments: $command->comments,
        );

        $this->repository->save($qualification);

        return $id;
    }

    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        );
    }
}
