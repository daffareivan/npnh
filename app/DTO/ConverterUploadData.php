<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Http\UploadedFile;

final readonly class ConverterUploadData
{
    public function __construct(
        public UploadedFile $file,
        public int $presetId,
        public ?int $userId,
    ) {}
}
