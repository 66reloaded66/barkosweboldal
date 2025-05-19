document.getElementById('login-form').addEventListener('submit', async (e) => {
  e.preventDefault();

  const username = document.getElementById('username').value;
  const password = document.getElementById('password').value;

  try {
    const response = await fetch('http://localhost:3000/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ username, password }),
    });

    if (response.ok) {
      window.location.href = '/admin.html';
    } else {
      const data = await response.json();
      showError(data.message || 'Hibás bejelentkezés');
    }
  } catch (error) {
    showError('Hálózati hiba történt');
  }
});

function showError(msg) {
  const errorDiv = document.getElementById('error-message');
  errorDiv.textContent = msg;
  errorDiv.style.display = 'block';
}