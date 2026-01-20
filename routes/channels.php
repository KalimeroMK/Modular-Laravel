<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| This file is for WebSocket/Broadcasting channel authorization.
| Currently not actively used, but kept for future real-time features.
|
| To enable broadcasting:
| 1. Set BROADCAST_DRIVER in .env (pusher, reverb, redis, etc.)
| 2. Configure broadcasting service in config/broadcasting.php
| 3. Implement ShouldBroadcast in your events
|
| Module-specific channels should be registered in module service providers.
|
*/

// Example: Private user channel (uncomment when broadcasting is enabled)
// Broadcast::channel('App.Models.User.{id}', fn ($user, $id) => (int) $user->id === (int) $id);
