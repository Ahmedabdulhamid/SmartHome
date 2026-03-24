import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: '1472d2cbdd35f4be5341',
    cluster: 'mt1',
    forceTLS: false,

});

