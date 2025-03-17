import './bootstrap';

import './bootstrap';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import moment from 'moment';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT ? import.meta.env.VITE_PUSHER_PORT : 6001,
    wssPort: import.meta.env.VITE_PUSHER_PORT ? import.meta.env.VITE_PUSHER_PORT : 6001,
    forceTLS: (import.meta.env.PUSHER_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    auth:{
        headers: {
            'X-CSRF-Token': document.head.querySelector('meta[name="csrf-token"]').content,
        },
    }
});