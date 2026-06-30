<?php

declare(strict_types=1);

namespace App\Tests\Unit\Qualification\Domain\Model;

use App\Qualification\Domain\Model\AuditorId;
use App\Qualification\Domain\Model\QualificationId;
use App\Qualification\Domain\Model\SupplierId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(QualificationId::class)]
#[CoversClass(SupplierId::class)]
#[CoversClass(AuditorId::class)]
final class QualificationIdTest extends TestCase
{
    private const VALID_UUID = '550e8400-e29b-41d4-a716-446655440000';

    public function test_valid_qualification_id(): void
    {
        $id = new QualificationId(self::VALID_UUID);
        $this->assertSame(self::VALID_UUID, $id->value());
    }

    public function test_valid_supplier_id(): void
    {
        $id = new SupplierId(self::VALID_UUID);
        $this->assertSame(self::VALID_UUID, $id->value());
    }

    public function test_valid_auditor_id(): void
    {
        $id = new AuditorId(self::VALID_UUID);
        $this->assertSame(self::VALID_UUID, $id->value());
    }

    public function test_throws_on_invalid_uuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new QualificationId('not-a-uuid');
    }

    public function test_throws_on_invalid_uuid_variant(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new QualificationId('550e8400-e29b-41d4-c716-446655440000');
    }

    public function test_equality(): void
    {
        $a = new QualificationId(self::VALID_UUID);
        $b = new QualificationId(self::VALID_UUID);
        $c = new QualificationId('550e8400-e29b-41d4-a716-446655440001');

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }

    public function test_from_string(): void
    {
        $id = QualificationId::fromString(self::VALID_UUID);
        $this->assertInstanceOf(QualificationId::class, $id);
        $this->assertSame(self::VALID_UUID, $id->value());
    }

    public function test_string_cast(): void
    {
        $id = new QualificationId(self::VALID_UUID);
        $this->assertSame(self::VALID_UUID, (string) $id);
    }
}
