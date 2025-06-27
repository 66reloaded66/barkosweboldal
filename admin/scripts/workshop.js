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

window.addEventListener('load', () => {
    const holder = document.querySelector('.holder');
    const rowHeight = parseInt(getComputedStyle(holder).getPropertyValue('grid-auto-rows'));
    const rowGap = parseInt(getComputedStyle(holder).getPropertyValue('gap'));

    holder.querySelectorAll('.holder-item').forEach(item => {
        const img = item.querySelector('img');

        function setSpan() {
            const height = img.getBoundingClientRect().height;
            const span = Math.ceil((height + rowGap) / (rowHeight + rowGap));
            item.style.setProperty('--row-span', span);
        }

        if (img.complete) {
            setSpan();
        } else {
            img.addEventListener('load', setSpan);
        }
    });
});