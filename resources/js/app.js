// resources/js/app.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

/* Pusher.logToConsole = true;
 */
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    wsHost: import.meta.env.VITE_PUSHER_HOST,
    wsPort: import.meta.env.VITE_PUSHER_PORT,
    wssPort: import.meta.env.VITE_PUSHER_PORT,
    forceTLS: import.meta.env.VITE_PUSHER_SCHEME === 'https',
    enabledTransports: ['ws', 'wss'],
    wsPath: import.meta.env.VITE_PUSHER_PATH, // 👈 THIS IS CRUCIAL
    disableStats: true // Optional: reduces other background traffic

});