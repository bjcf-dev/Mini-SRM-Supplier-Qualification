<?php

declare(strict_types=1);

namespace App\Qualification\Domain\Model;

enum QualificationStatus: string
{
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
}
