<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Live Provider Map') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                {{-- Map Container --}}
                <div id="map" style="height: 600px;" class="w-full"></div>
                {{-- Loading indicator or message --}}
                <div id="map-loading" class="p-4 text-center text-gray-500">Loading map...</div>
            </div>
        </div>
    </div>

@push('scripts')
{{-- Include Leaflet CSS and JS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    document.addEventListener('livewire:init', () => { // OrDOMContentLoaded if not using Livewire here

        const mapElement = document.getElementById('map');
        const loadingElement = document.getElementById('map-loading');

        if (mapElement) {
            // Initialize Leaflet Map (Set initial view, e.g., South Africa)
            const map = L.map('map').setView([-29, 24], 5); // Centered roughly on SA

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            loadingElement.style.display = 'none'; // Hide loading message

            let providerMarkers = {}; // To store markers by provider ID

            // --- Echo Integration for Live Updates ---
            if (window.Echo) {
                 console.log('Map: Setting up Echo listeners...');

                 // Listen on the presence channel for initial online users and joining/leaving events
                 window.Echo.join('providers')
                    .here((users) => {
                        console.log('Map: Initial online providers:', users);
                        users.forEach(user => {
                            // TODO: Need provider's *current* location here.
                            // This requires providers to broadcast their location separately.
                            // For now, just log that they are online.
                            console.log(`Provider ${user.name} (${user.id}) is online.`);
                            // Example marker creation (needs lat/lng):
                            // if (user.latitude && user.longitude) {
                            //     providerMarkers[user.id] = L.marker([user.latitude, user.longitude])
                            //         .addTo(map)
                            //         .bindPopup(`<b>${user.name}</b><br>Online`);
                            // }
                        });
                    })
                    .joining((user) => {
                        console.log('Map: Provider joining:', user.name);
                        // TODO: Add marker when location is received.
                    })
                    .leaving((user) => {
                        console.log('Map: Provider leaving:', user.name);
                        if (providerMarkers[user.id]) {
                            map.removeLayer(providerMarkers[user.id]);
                            delete providerMarkers[user.id];
                        }
                    })
                    .error((error) => {
                         console.error('Map: Presence Channel Error:', error);
                     });

                 // Listen for custom location update events on the public channel
                 window.Echo.channel('provider-locations')
                    .listen('ProviderLocationUpdated', (e) => {
                        console.log('Map: Location Update Received:', e);
                        // The event payload directly contains providerId, latitude, longitude
                        const providerId = e.providerId;
                        const lat = e.latitude;
                        const lng = e.longitude;

                        if (!lat || !lng) return; // Ignore if invalid coordinates

                        if (providerMarkers[providerId]) {
                            // Update existing marker position
                            providerMarkers[providerId].setLatLng([lat, lng]);
                            console.log(`Map: Updated marker for provider ${providerId}`);
                        } else {
                            // Create new marker if provider wasn't on map (e.g., came online after page load)
                         // Use providerName from the event payload
                         providerMarkers[providerId] = L.marker([lat, lng])
                             .addTo(map)
                             .bindPopup(`<b>${e.providerName}</b><br>ID: ${providerId}`); // Use name in popup
                          console.log(`Map: Created marker for provider ${e.providerName} (${providerId})`);
                        }
                    });
                 console.log("Map: Listening on 'provider-locations' channel.");

            } else {
                console.error('Map: Laravel Echo not found. Real-time updates disabled.');
            }

        } else {
            console.error('Map container element not found.');
            loadingElement.textContent = 'Error loading map container.';
        }
    });
</script>
@endpush
</x-app-layout>
