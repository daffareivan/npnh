<?php

declare(strict_types=1);

namespace App\Enums;

enum ConversionStatus: string
{
    case Pending = 'pending';
    case Uploaded = 'uploaded';
    case Uploading = 'uploading';
    case Analyzing = 'analyzing';
    case Converting = 'converting';
    case Encoding = 'encoding';
    case Finished = 'finished';
    case Failed = 'failed';
    case Deleted = 'deleted';

    public function progress(): int
    {
        return match ($this) {
            self::Uploading => 15,
            self::Uploaded => 25,
            self::Pending => 30,
            self::Analyzing => 40,
            self::Converting => 65,
            self::Encoding => 85,
            self::Finished => 100,
            self::Failed, self::Deleted => 0,
        };
    }
}
