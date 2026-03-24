import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

console.log('heloo');

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: '90f47145550aa808defc', // APP_KEY من Pusher dashboard
    cluster: 'mt1',               // APP_CLUSTER من Pusher dashboard
    forceTLS: false,               // localhost
});

console.log('Echo loaded:', window.Echo);
