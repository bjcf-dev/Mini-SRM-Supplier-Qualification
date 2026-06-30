<?php

declare(strict_types=1);

namespace App\Tests\Unit\Qualification\Domain\Model;

use App\Qualification\Domain\Model\QualificationScore;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(QualificationScore::class)]
final class QualificationScoreTest extends TestCase
{
    #[DataProvider('validScoresProvider')]
    public function test_valid_scores(int $value): void
    {
        $score = new QualificationScore($value);
        $this->assertSame($value, $score->value());
    }

    public static function validScoresProvider(): array
    {
        return [[0], [1], [50], [59], [60], [99], [100]];
    }

    #[DataProvider('invalidScoresProvider')]
    public function test_invalid_scores_throw(int $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new QualificationScore($value);
    }

    public static function invalidScoresProvider(): array
    {
        return [[-1], [-100], [101], [200]];
    }

    public function test_is_approved_when_score_is_60_or_higher(): void
    {
        $this->assertTrue((new QualificationScore(60))->isApproved());
        $this->assertTrue((new QualificationScore(100))->isApproved());
    }

    public function test_is_rejected_when_score_is_below_60(): void
    {
        $this->assertTrue((new QualificationScore(0))->isRejected());
        $this->assertTrue((new QualificationScore(59))->isRejected());
    }

    public function test_boundary_59_is_rejected(): void
    {
        $score = new QualificationScore(59);
        $this->assertTrue($score->isRejected());
        $this->assertFalse($score->isApproved());
    }

    public function test_boundary_60_is_approved(): void
    {
        $score = new QualificationScore(60);
        $this->assertTrue($score->isApproved());
        $this->assertFalse($score->isRejected());
    }
}
