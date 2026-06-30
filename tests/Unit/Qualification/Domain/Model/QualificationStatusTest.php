<?php

declare(strict_types=1);

namespace App\Tests\Unit\Qualification\Domain\Model;

use App\Qualification\Domain\Model\QualificationStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(QualificationStatus::class)]
final class QualificationStatusTest extends TestCase
{
    public function test_approved_case_exists(): void
    {
        $this->assertSame('APPROVED', QualificationStatus::APPROVED->value);
    }

    public function test_rejected_case_exists(): void
    {
        $this->assertSame('REJECTED', QualificationStatus::REJECTED->value);
    }

    public function test_from_string(): void
    {
        $this->assertSame(QualificationStatus::APPROVED, QualificationStatus::from('APPROVED'));
        $this->assertSame(QualificationStatus::REJECTED, QualificationStatus::from('REJECTED'));
    }

    public function test_invalid_string_throws(): void
    {
        $this->expectException(\ValueError::class);
        QualificationStatus::from('INVALID');
    }
}
