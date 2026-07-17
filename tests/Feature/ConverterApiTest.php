<?php

declare(strict_types=1);

use App\Enums\ConversionStatus;
use App\Models\AudioFile;
use App\Models\ConversionPreset;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('validates supported audio upload mime types', function (): void {
    Storage::fake();
    $this->seed(RbacSeeder::class);
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole('user');
    $preset = ConversionPreset::query()->create(['name' => '2.3x', 'speed' => 2.3, 'amplify_db' => -4, 'is_default' => true]);

    $response = $this->actingAs($user)->postJson('/api/converter/upload', [
        'file' => UploadedFile::fake()->create('beat.ogg', 100, 'audio/ogg'),
        'preset_id' => $preset->id,
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.status', ConversionStatus::Uploaded->value);
});

it('returns converter status resource', function (): void {
    $this->seed(RbacSeeder::class);
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole('user');
    $preset = ConversionPreset::query()->create(['name' => '2.3x', 'speed' => 2.3, 'amplify_db' => -4, 'is_default' => true]);
    $audioFile = AudioFile::query()->create([
        'user_id' => $user->id,
        'conversion_preset_id' => $preset->id,
        'original_name' => 'beat.ogg',
        'mime_type' => 'audio/ogg',
        'extension' => 'ogg',
        'original_path' => 'converter/originals/beat.ogg',
        'original_size' => 1024,
        'speed' => 2.3,
        'amplify_db' => -4,
        'status' => ConversionStatus::Uploaded,
        'progress' => ConversionStatus::Uploaded->progress(),
    ]);

    $this->actingAs($user)->getJson("/api/converter/status/{$audioFile->id}")
        ->assertOk()
        ->assertJsonPath('data.file_name', 'beat.ogg');
});

it('prevents users from viewing another users conversion', function (): void {
    $this->seed(RbacSeeder::class);
    $owner = User::factory()->create(['email_verified_at' => now()]);
    $other = User::factory()->create(['email_verified_at' => now()]);
    $owner->assignRole('user');
    $other->assignRole('user');
    $preset = ConversionPreset::query()->create(['name' => '2.3x', 'speed' => 2.3, 'amplify_db' => -4, 'is_default' => true]);
    $audioFile = AudioFile::query()->create([
        'user_id' => $owner->id,
        'conversion_preset_id' => $preset->id,
        'original_name' => 'private.ogg',
        'mime_type' => 'audio/ogg',
        'extension' => 'ogg',
        'original_path' => 'converter/originals/private.ogg',
        'original_size' => 1024,
        'speed' => 2.3,
        'amplify_db' => -4,
        'status' => ConversionStatus::Uploaded,
        'progress' => ConversionStatus::Uploaded->progress(),
    ]);

    $this->actingAs($other)->getJson("/api/converter/status/{$audioFile->id}")->assertForbidden();
});
