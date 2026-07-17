<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConverterUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxKb = config('converter.max_upload_size_mb') * 1024;

        return [
            'file' => [
                'required',
                'file',
                "max:{$maxKb}",
                'mimetypes:audio/ogg,application/ogg,audio/mpeg,audio/wav,audio/x-wav,audio/mp4,audio/x-m4a,audio/aac',
            ],
            'preset_id' => ['required', 'integer', Rule::exists('conversion_presets', 'id')],
        ];
    }
}
