<?php

declare(strict_types=1);

namespace App\Qualification\Domain\Model;

class Qualification
{
    public function __construct(
        private QualificationId $id,
        private SupplierId $supplierId,
        private AuditorId $auditorId,
        private QualificationScore $score,
        private QualificationStatus $status,
        private string $comments,
        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $expiresAt,
    ) {
        if ($this->expiresAt <= $this->createdAt) {
            throw new \InvalidArgumentException('expiresAt must be strictly after createdAt.');
        }
    }

    public static function create(
        QualificationId $id,
        SupplierId $supplierId,
        AuditorId $auditorId,
        QualificationScore $score,
        string $comments = '',
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $expiresAt = null,
    ): self {
        $createdAt ??= new \DateTimeImmutable();
        $status = $score->value() < 60 ? QualificationStatus::REJECTED : QualificationStatus::APPROVED;
        $expiresAt ??= $createdAt->modify('+12 months');

        return new self($id, $supplierId, $auditorId, $score, $status, $comments, $createdAt, $expiresAt);
    }

    public function id(): QualificationId
    {
        return $this->id;
    }

    public function supplierId(): SupplierId
    {
        return $this->supplierId;
    }

    public function auditorId(): AuditorId
    {
        return $this->auditorId;
    }

    public function score(): QualificationScore
    {
        return $this->score;
    }

    public function status(): QualificationStatus
    {
        return $this->status;
    }

    public function comments(): string
    {
        return $this->comments;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function expiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }
}
