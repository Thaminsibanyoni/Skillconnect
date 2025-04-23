<?php

namespace App\Http\Controllers\Api;

use App\Events\ProviderLocationUpdated; // We'll create this event next
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProviderLocationController extends Controller
{
    /**
     * Update the authenticated provider's location.
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'latitude' => ['required', 'numeric', 'between:-90,90'],
                'longitude' => ['required', 'numeric', 'between:-180,180'],
            ]);

            $provider = Auth::user();

            // Ensure user is an approved provider (already handled by middleware, but double-check)
            if (!$provider || $provider->role !== 'provider' || $provider->status !== 'approved') {
                return response()->json(['message' => 'Unauthorized or provider not approved.'], 403);
            }

            // Update location in the database
            $provider->update([
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                // Optionally update a 'last_seen_at' timestamp here too
            ]);

            // Broadcast the location update event, including the provider's name
            broadcast(new ProviderLocationUpdated(
                $provider->id,
                $validated['latitude'],
                $validated['longitude'],
                $provider->name // Pass the name
            ))->toOthers(); // Use toOthers() to prevent broadcasting back to the sender


            return response()->json(['message' => 'Location updated successfully.']);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Invalid location data.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Provider Location Update Error: '.$e->getMessage());
            return response()->json(['message' => 'Failed to update location.'], 500);
        }
    }
}
