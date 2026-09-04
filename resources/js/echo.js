import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const reverbPort = Number(
    import.meta.env.VITE_REVERB_PORT || 8080
);

const reverbScheme =
    import.meta.env.VITE_REVERB_SCHEME || 'http';

window.Echo = new Echo({
    broadcaster: 'reverb',

    key: import.meta.env.VITE_REVERB_APP_KEY,

    wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',

    wsPort: reverbPort,

    wssPort: reverbPort,

    forceTLS: reverbScheme === 'https',

    enabledTransports: [
        'ws',
        'wss',
    ],
});