<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncRobloxProfileJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly int $robloxAccountId) {}

    public function handle(): void
    {
        // Reserved for official Roblox profile synchronization.
    }
}
