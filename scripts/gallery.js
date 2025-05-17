document.addEventListener("DOMContentLoaded", () => {
    const galleries = document.querySelectorAll(".gallery");
  
    galleries.forEach((gallery, index) => {
      const images = gallery.querySelectorAll(".gallery-img");
      let current = 0;
  
      
      const startDelay = index * 2000;
  
      setTimeout(() => {
        setInterval(() => {
          images[current].classList.remove("active");
          current = (current + 1) % images.length;
          images[current].classList.add("active");
        }, 5000);
      }, startDelay);
    });
  });
  