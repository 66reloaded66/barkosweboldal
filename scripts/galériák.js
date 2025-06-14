document.addEventListener('DOMContentLoaded', () => {
    // Lightbox elemet létrehozzuk és a body-ba tesszük
    const lightboxOverlay = document.createElement('div');
    lightboxOverlay.classList.add('lightbox-overlay');
    document.body.appendChild(lightboxOverlay);

    lightboxOverlay.addEventListener('click', () => {
        lightboxOverlay.classList.remove('active');
        lightboxOverlay.innerHTML = ''; // képtörlés
    });

    // Minden lightbox trigger linkre kattintás
    document.querySelectorAll('.lightbox-trigger').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const href = link.getAttribute('href');
            const img = document.createElement('img');
            img.src = href;

            // Lightbox tartalma
            lightboxOverlay.innerHTML = '';
            lightboxOverlay.appendChild(img);
            lightboxOverlay.classList.add('active');
        });
    });
});