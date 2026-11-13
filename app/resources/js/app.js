import './bootstrap';

// Registrasi Service Worker untuk Progressive Web App (PWA)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then((reg) => {
                console.log('PWA ServiceWorker terdaftar:', reg.scope);
            })
            .catch((err) => {
                console.log('PWA ServiceWorker gagal daftar:', err);
            });
    });
}
