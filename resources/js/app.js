import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Defer start until DOMContentLoaded so all module scripts — including
// @fluxScripts (which registers Flux's Alpine plugins) — have run first.
document.addEventListener('DOMContentLoaded', () => Alpine.start());