import Alpine from 'alpinejs';

if (!window.Alpine) {
    window.Alpine = Alpine;
}

if (!window.Alpine.__mpbStarted) {
    window.Alpine.__mpbStarted = true;
    window.Alpine.start();
}
