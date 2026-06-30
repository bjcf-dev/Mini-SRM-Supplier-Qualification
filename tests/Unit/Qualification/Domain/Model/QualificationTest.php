<?php

declare(strict_types=1);

namespace App\Tests\Unit\Qualification\Domain\Model;

use App\Qualification\Domain\Model\AuditorId;
use App\Qualification\Domain\Model\Qualification;
use App\Qualification\Domain\Model\QualificationId;
use App\Qualification\Domain\Model\QualificationScore;
use App\Qualification\Domain\Model\QualificationStatus;
use App\Qualification\Domain\Model\SupplierId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Qualification::class)]
final class QualificationTest extends TestCase
{
    private const VALID_UUID = '550e8400-e29b-41d4-a716-446655440000';

    public function test_create_approved(): void
    {
        $q = Qualification::create(
            new QualificationId(self::VALID_UUID),
            new SupplierId(self::VALID_UUID),
            new AuditorId(self::VALID_UUID),
            new QualificationScore(85),
            'Todo conforme',
        );

        $this->assertSame(QualificationStatus::APPROVED, $q->status());
        $this->assertSame(85, $q->score()->value());
        $this->assertSame('Todo conforme', $q->comments());
    }

    public function test_create_rejected_when_score_below_60(): void
    {
        $q = Qualification::create(
            new QualificationId(self::VALID_UUID),
            new SupplierId(self::VALID_UUID),
            new AuditorId(self::VALID_UUID),
            new QualificationScore(45),
        );

        $this->assertSame(QualificationStatus::REJECTED, $q->status());
    }

    public function test_create_boundary_59_is_rejected(): void
    {
        $q = Qualification::create(
            new QualificationId(self::VALID_UUID),
            new SupplierId(self::VALID_UUID),
            new AuditorId(self::VALID_UUID),
            new QualificationScore(59),
        );

        $this->assertSame(QualificationStatus::REJECTED, $q->status());
    }

    public function test_create_boundary_60_is_approved(): void
    {
        $q = Qualification::create(
            new QualificationId(self::VALID_UUID),
            new SupplierId(self::VALID_UUID),
            new AuditorId(self::VALID_UUID),
            new QualificationScore(60),
        );

        $this->assertSame(QualificationStatus::APPROVED, $q->status());
    }

    public function test_default_expires_at_is_12_months(): void
    {
        $createdAt = new \DateTimeImmutable('2025-01-15T10:00:00');
        $q = Qualification::create(
            new QualificationId(self::VALID_UUID),
            new SupplierId(self::VALID_UUID),
            new AuditorId(self::VALID_UUID),
            new QualificationScore(75),
            '',
            $createdAt,
        );

        $this->assertSame('2026-01-15', $q->expiresAt()->format('Y-m-d'));
    }

    public function test_custom_expires_at(): void
    {
        $createdAt = new \DateTimeImmutable('2025-01-15T10:00:00');
        $expiresAt = new \DateTimeImmutable('2025-06-15T10:00:00');

        $q = Qualification::create(
            new QualificationId(self::VALID_UUID),
            new SupplierId(self::VALID_UUID),
            new AuditorId(self::VALID_UUID),
            new QualificationScore(75),
            '',
            $createdAt,
            $expiresAt,
        );

        $this->assertSame('2025-06-15', $q->expiresAt()->format('Y-m-d'));
    }

    public function test_expires_at_must_be_strictly_after_created_at(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Qualification(
            new QualificationId(self::VALID_UUID),
            new SupplierId(self::VALID_UUID),
            new AuditorId(self::VALID_UUID),
            new QualificationScore(75),
            QualificationStatus::APPROVED,
            '',
            new \DateTimeImmutable('2025-01-15'),
            new \DateTimeImmutable('2025-01-15'),
        );
    }

    public function test_default_comments_is_empty_string(): void
    {
        $q = Qualification::create(
            new QualificationId(self::VALID_UUID),
            new SupplierId(self::VALID_UUID),
            new AuditorId(self::VALID_UUID),
            new QualificationScore(80),
        );

        $this->assertSame('', $q->comments());
    }

    public function test_getters_return_expected_values(): void
    {
        $id = new QualificationId(self::VALID_UUID);
        $supplierId = new SupplierId(self::VALID_UUID);
        $auditorId = new AuditorId(self::VALID_UUID);
        $score = new QualificationScore(85);
        $createdAt = new \DateTimeImmutable('2025-01-15');
        $expiresAt = new \DateTimeImmutable('2026-01-15');

        $q = Qualification::create($id, $supplierId, $auditorId, $score, 'Ok', $createdAt, $expiresAt);

        $this->assertSame($id, $q->id());
        $this->assertSame($supplierId, $q->supplierId());
        $this->assertSame($auditorId, $q->auditorId());
        $this->assertSame($score, $q->score());
        $this->assertSame($createdAt, $q->createdAt());
        $this->assertSame($expiresAt, $q->expiresAt());
    }
}
