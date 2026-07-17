<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConversionPreset extends Model
{
    protected $fillable = ['name', 'speed', 'amplify_db', 'is_default'];

    protected function casts(): array
    {
        return [
            'speed' => 'decimal:1',
            'amplify_db' => 'integer',
            'is_default' => 'boolean',
        ];
    }

    public function audioFiles(): HasMany
    {
        return $this->hasMany(AudioFile::class);
    }
}
