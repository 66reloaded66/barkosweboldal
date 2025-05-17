document.addEventListener("DOMContentLoaded", function () {
    document.body.classList.add('fade-in');
  });

document.querySelectorAll('#menu a').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      const href = this.getAttribute('href');
      document.body.classList.add('fade-out');
      setTimeout(() => {
        window.location.href = href;
      }, 800);
    });
  });