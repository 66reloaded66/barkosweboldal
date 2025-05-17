// Bejelentkező űrlap elküldésekor lefutó funkció
document.getElementById('login-form').addEventListener('submit', async (e) => {
  e.preventDefault(); // Megakadályozzuk az oldal újratöltését

  // Beolvassuk a felhasználó által megadott adatokat
  const username = document.getElementById('username').value;
  const password = document.getElementById('password').value;

  try {
    // POST kérést küldünk a /login végpontra JSON formátumban
    const response = await fetch('/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, password })
    });

    if (response.ok) {
      // Sikeres bejelentkezés esetén átirányítunk az admin felületre
      window.location.href = '/admin.html';
    } else {
      // Sikertelen bejelentkezésnél a hibaüzenetet kiírjuk
      const data = await response.json();
      showError(data.message || 'Hibás bejelentkezés');
    }
  } catch (error) {
    // Hálózati hiba esetén is jelzünk
    showError('Hálózati hiba történt');
  }
});

// Hibák megjelenítése a login.html oldalán lévő div-ben
function showError(msg) {
  const errorDiv = document.getElementById('error-message');
  errorDiv.textContent = msg;
  errorDiv.style.display = 'block';
}