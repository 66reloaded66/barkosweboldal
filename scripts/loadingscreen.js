window.addEventListener("load", function () {
    const loaderWrapper = document.getElementById("loader-wrapper");
    loaderWrapper.style.opacity = "0";
    loaderWrapper.style.pointerEvents = "none";
    setTimeout(() => loaderWrapper.remove(), 500); // teljes eltávolítás kis idő után
  });