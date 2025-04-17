import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    // Add authEndpoint if using private/presence channels with default auth
    // authEndpoint: '/broadcasting/auth',
});

// --- Real-time Event Listening ---

// Function to get user ID (replace with your actual method if not using Blade injection)
// This assumes you have access to the authenticated user's ID in your JS scope.
// Often achieved by embedding it in the main layout Blade file within a <script> tag.
// Example in layout: <script>window.userId = {{ auth()->id() }};</script>
const userId = window.userId; // Make sure window.userId is set in your main layout

if (userId) {
    // Listen for notifications on the user's private channel
    window.Echo.private(`App.Models.User.${userId}`)
        .notification((notification) => {
            console.log('Notification Received:', notification);
            // Example: Show a simple alert (replace with a proper toast notification system)
            // alert(`New Notification: ${notification.message}`);

            // Optionally, trigger Livewire component refresh if needed,
            // although the #[On] attribute should handle the count update.
            // Livewire.dispatch('refreshNotifications');
        });

    console.log(`Echo listening on private channel: App.Models.User.${userId}`);

    // --- Provider Presence Channel Logic ---
    let providerPresenceChannel = null;

    // Function to join the presence channel
    const joinProviderChannel = () => {
        if (!providerPresenceChannel) {
            providerPresenceChannel = window.Echo.join('providers')
                .here((users) => {
                    // Called when *you* first join the channel
                    console.log('Presence Channel: You joined. Online providers:', users);
                    // Update UI with online providers count/list if needed
                })
                .joining((user) => {
                    // Called when *another* user joins
                    console.log('Presence Channel: User joining:', user.name);
                    // Update UI
                })
                .leaving((user) => {
                    // Called when *another* user leaves
                    console.log('Presence Channel: User leaving:', user.name);
                    // Update UI
                })
                .error((error) => {
                    console.error('Presence Channel Error:', error);
                });
            console.log('Attempted to join providers presence channel.');
        }
    };

    // Function to leave the presence channel
    const leaveProviderChannel = () => {
        if (providerPresenceChannel) {
            window.Echo.leave('providers');
            providerPresenceChannel = null;
            console.log('Left providers presence channel.');
        }
    };

    // Listen for the Livewire event dispatched by StatusToggle component
    // Note: Ensure Livewire assets are loaded before this script runs.
    document.addEventListener('livewire:init', () => {
         Livewire.on('provider-status-changed', (event) => {
            console.log('provider-status-changed event received:', event);
             if (event.online) {
                 joinProviderChannel();
             } else {
                 leaveProviderChannel();
             }
         });

         // Check initial status (if user is already online when page loads)
         // This requires passing the initial 'isOnline' status from PHP to JS,
         // possibly via the StatusToggle component's initial state or another method.
         // Example (assuming window.isProviderOnline is set):
         // if (window.isProviderOnline) {
         //     joinProviderChannel();
         // }
    });


} else {
    console.log('Echo: User not authenticated. Private channels not joined.');
}
