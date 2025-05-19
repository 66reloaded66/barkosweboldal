document.getElementById('login-form').addEventListener('submit', async (e) => {
  e.preventDefault();

  const username = document.getElementById('username').value;
  const password = document.getElementById('password').value;

  try {
    const response = await fetch('/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, password })
    });

    if (response.ok) {
      window.location.href = '/admin.html';
    } else {
      const data = await response.json();
      showError(data.message || 'Hibás bejelentkezés');
    }
  } catch (error) {
    console.error('Fetch error:', error);
    showError('Hálózati hiba történt');
  }
});

function showError(msg) {
  const errorDiv = document.getElementById('error-message');
  if (!errorDiv) return;
  errorDiv.textContent = msg;
  errorDiv.style.display = 'block';
}
