<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\RobloxAccount;
use Illuminate\Foundation\Events\Dispatchable;

class RobloxConnected
{
    use Dispatchable;

    public function __construct(public readonly RobloxAccount $account) {}
}
