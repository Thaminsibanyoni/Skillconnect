import './bootstrap';
import Alpine from 'alpinejs';
import { Chart, registerables } from 'chart.js'; // Import Chart.js

// Register all Chart.js components (or specific ones if preferred)
Chart.register(...registerables);

window.Alpine = Alpine;
window.Chart = Chart;

// --- Dark Mode Toggle Alpine Component ---
Alpine.data('darkModeToggle', () => ({
    isDark: false,
    init() {
        this.isDark = localStorage.getItem('darkMode') === 'true' ||
                      (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
        this.applyTheme();
        // Watch for system changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (!('darkMode' in localStorage)) { // Only apply if user hasn't set preference
                this.isDark = e.matches;
                this.applyTheme();
            }
        });
    },
    toggle() {
        this.isDark = !this.isDark;
        localStorage.setItem('darkMode', this.isDark);
        this.applyTheme();
    },
    applyTheme() {
        if (this.isDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}));

Alpine.start();

// --- Geolocation Sending Logic ---
let watchId = null;
let lastSentLat = null;
let lastSentLng = null;
const minDistanceChange = 50; // Minimum distance in meters before sending update

function sendLocationUpdate(latitude, longitude) {
    // Basic check to avoid sending identical coordinates repeatedly
    if (latitude === lastSentLat && longitude === lastSentLng) {
        // console.log('Location unchanged, skipping send.');
        return;
    }

    // More advanced check: calculate distance from last sent point
    if (lastSentLat !== null && lastSentLng !== null) {
        const distance = calculateDistance(lastSentLat, lastSentLng, latitude, longitude);
        if (distance < minDistanceChange) {
            // console.log(`Distance moved (${distance.toFixed(1)}m) less than threshold (${minDistanceChange}m), skipping send.`);
            return;
        }
    }

    console.log(`Sending location update: Lat: ${latitude}, Lng: ${longitude}`);
    window.axios.post('/api/provider/location', { // Use relative URL if API is on same domain
        latitude: latitude,
        longitude: longitude
    })
    .then(response => {
        console.log('Location updated successfully:', response.data);
        lastSentLat = latitude; // Update last sent coordinates
        lastSentLng = longitude;
    })
    .catch(error => {
        console.error('Error updating location:', error.response ? error.response.data : error.message);
        // Stop watching if there's an auth error?
        if (error.response && (error.response.status === 401 || error.response.status === 403)) {
            stopWatchingLocation();
        }
    });
}

// Haversine formula to calculate distance between two points on a sphere
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371e3; // metres
    const φ1 = lat1 * Math.PI/180; // φ, λ in radians
    const φ2 = lat2 * Math.PI/180;
    const Δφ = (lat2-lat1) * Math.PI/180;
    const Δλ = (lon2-lon1) * Math.PI/180;

    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ/2) * Math.sin(Δλ/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    return R * c; // in metres
}


function startWatchingLocation() {
    if (navigator.geolocation && watchId === null) {
        console.log('Starting geolocation watch...');
        watchId = navigator.geolocation.watchPosition(
            (position) => {
                sendLocationUpdate(position.coords.latitude, position.coords.longitude);
            },
            (error) => {
                console.error("Geolocation error:", error);
                // Handle errors (e.g., PERMISSION_DENIED, POSITION_UNAVAILABLE)
                stopWatchingLocation(); // Stop if there's an error
            },
            {
                enableHighAccuracy: true,
                timeout: 10000, // 10 seconds
                maximumAge: 0 // Force fresh position
            }
        );
    } else if (watchId !== null) {
         console.log('Geolocation watch already active.');
    } else {
        console.error("Geolocation is not supported by this browser.");
    }
}

function stopWatchingLocation() {
    if (navigator.geolocation && watchId !== null) {
        console.log('Stopping geolocation watch...');
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
        lastSentLat = null; // Reset last sent location
        lastSentLng = null;
    }
}

// Listen for Livewire event from StatusToggle component
document.addEventListener('livewire:init', () => {
    // Ensure Echo is initialized before setting up listeners that depend on it
    if (window.Echo) {
        Livewire.on('provider-status-changed', (event) => {
            console.log('JS received provider-status-changed:', event);
            if (event.online) {
                startWatchingLocation();
                // Echo join logic is now handled in echo.js
            } else {
                stopWatchingLocation();
                 // Echo leave logic is now handled in echo.js
            }
        });

        // Initial check might be needed if the component loads after the initial JS run
         // This requires coordination with the Livewire component's initial state.
         // Check if a global JS variable indicating initial online status exists.
         if (window.isProviderInitiallyOnline === true) {
            startWatchingLocation();
         }
    } else {
         console.error("Echo not initialized when setting up Livewire listener.");
    }
});
// Removed duplicate Alpine import and start

// --- Provider Location Tracking ---

// Check if the necessary Geolocation and Livewire/Auth context exists
if ('geolocation' in navigator && window.Livewire && window.userId) {

    let watchId = null;
    let lastSentTime = 0;
    const minSendInterval = 30000; // Minimum interval in ms (e.g., 30 seconds)

    const sendLocation = (position) => {
        const now = Date.now();
        // Throttle sending updates
        if (now - lastSentTime < minSendInterval) {
            // console.log('Location update throttled.');
            return;
        }

        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;

        console.log(`Provider Location: Lat=${latitude}, Lng=${longitude}`);

        // Send to backend API
        window.axios.post('/api/provider/location', {
            latitude: latitude,
            longitude: longitude
        })
        .then(response => {
            console.log('Location updated successfully:', response.data);
            lastSentTime = now;
        })
        .catch(error => {
            console.error('Error updating location:', error.response ? error.response.data : error.message);
            // Stop watching if there's a persistent error (e.g., auth issue)
            if (error.response && (error.response.status === 401 || error.response.status === 403)) {
                stopWatchingLocation();
            }
        });
    };

    const handleLocationError = (error) => {
        console.error(`Geolocation Error (${error.code}): ${error.message}`);
        // Potentially stop watching if error is permanent (e.g., PERMISSION_DENIED)
        if (error.code === error.PERMISSION_DENIED) {
            stopWatchingLocation();
            // Maybe notify the user they need to enable location services
        }
    };

    const startWatchingLocation = () => {
        if (watchId === null) {
            console.log('Starting location watch...');
            // High accuracy might drain battery faster, adjust as needed
            const options = { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 };
            watchId = navigator.geolocation.watchPosition(sendLocation, handleLocationError, options);
            // Send initial location immediately
            navigator.geolocation.getCurrentPosition(sendLocation, handleLocationError, options);
        }
    };

    const stopWatchingLocation = () => {
        if (watchId !== null) {
            console.log('Stopping location watch...');
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
            lastSentTime = 0; // Reset throttle timer
            // Optionally send a final "offline" update or clear location in DB via API?
        }
    };

    // Listen for Livewire event from StatusToggle component
    document.addEventListener('livewire:init', () => {
        // Check if the StatusToggle component exists on the page for the current user
        // This is a basic check; a more robust way might involve checking user role via JS variable
        const statusToggleExists = document.querySelector('[wire\\:id] [wire\\:click="toggleStatus"]');

        if (statusToggleExists) {
            console.log('StatusToggle found, setting up location listeners.');
            Livewire.on('provider-status-changed', (event) => {
                console.log('JS received provider-status-changed:', event);
                if (event.online) {
                    startWatchingLocation();
                } else {
                    stopWatchingLocation();
                }
            });

            // Check initial state if possible (requires StatusToggle to maybe dispatch state on mount)
            // Or check a global variable set in the layout if the user is a provider and initially online
            // Example: if (window.isProviderInitiallyOnline) { startWatchingLocation(); }

        } else {
             console.log('StatusToggle not found, location listeners not active.');
        }
    });

} else {
    console.log('Geolocation API or Livewire/Auth context not available for location tracking.');
}
