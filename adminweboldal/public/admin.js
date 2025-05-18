document.getElementById("logoutBtn").addEventListener("click", () => {
    fetch("/logout")
      .then(() => {
        window.location.href = "/";
      });
  });

  let timeLeft = 30 * 60; // másodpercekben

const countdownEl = document.getElementById("countdown");

function updateCountdown() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    countdownEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    if (timeLeft > 0) {
        timeLeft--;
    } else {
        // Idő lejárt, automatikus kijelentkezés
        fetch("/logout").then(() => {
            window.location.href = "/";
        });
    }
}

// Indítsuk el másodpercenként frissítve
setInterval(updateCountdown, 1000);