import './bootstrap';

import Alpine from 'alpinejs';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/thumbs';
import { Swiper, SwiperSlide } from 'swiper/vue';
import SwiperCore, { Navigation, Thumbs } from 'swiper';

SwiperCore.use([Navigation, Thumbs]);

window.Alpine = Alpine;

Alpine.start();
