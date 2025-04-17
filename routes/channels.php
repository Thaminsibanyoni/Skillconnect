<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Default user channel (Private) - Already present
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Order specific channel (Private)
// Only the seeker or provider associated with the order can listen
Broadcast::channel('order.{orderId}', function (User $user, int $orderId) {
    $order = Order::find($orderId);
    return $order && ($user->id === $order->user_id || $user->id === $order->provider_id);
});

// Provider presence channel (Presence)
// Used for tracking online status and potentially location later.
// Only authenticated users with the 'provider' role can join.
// Returns user data to be shared with other channel members.
Broadcast::channel('providers', function (User $user) {
    if ($user->role === 'provider' && $user->status === 'approved') { // Only approved providers
        return ['id' => $user->id, 'name' => $user->name]; // Data available to other presence members
    }
});

// Public channel example (no auth needed) - If needed later
// Broadcast::channel('public-notifications', function () {
//     return true;
// });
