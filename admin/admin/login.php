<?php
session_start();
require_once '../db.php'; // fontos: a PDO kapcsolat miatt

$hiba = '';
$ip = $_SERVER['REMOTE_ADDR'];

// ⏱ korlátozás: max. 5 próbálkozás 15 perc alatt
$stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND attempt_time > (NOW() - INTERVAL 15 MINUTE)");
$stmt->execute([$ip]);
$attempts = $stmt->fetchColumn();

if ($attempts >= 5) {
    $hiba = 'Túl sok próbálkozás. Próbáld meg 15 perc múlva újra.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $felhasznalo = $_POST['username'] ?? '';
    $jelszo = $_POST['password'] ?? '';

    // Itt egy példa jelszó hash-elt formában (generáld le a sajátodat előtte)
    $engedelyezett_felhasznalok = [
        'admin' => '$2y$10$9WsCJ1oiEeOl7pHVgbHoFe86KHPuUjsWk4Dg4MUHsNCgyEi/XSRkC' // jelszó: titkos123
    ];

    if (isset($engedelyezett_felhasznalok[$felhasznalo]) && password_verify($jelszo, $engedelyezett_felhasznalok[$felhasznalo])) {
        session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $felhasznalo;
        header('Location: index.php');
        exit;
    } else {
        // Hiba esetén: naplózás
        $stmt = $pdo->prepare("INSERT INTO login_attempts (ip, attempt_time) VALUES (?, NOW())");
        $stmt->execute([$ip]);

        $hiba = 'Hibás felhasználónév vagy jelszó.';
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <title>Admin belépés</title>
  <style>
    body {
      background: #111;
      color: #fff;
      font-family: sans-serif;
      display: flex;
      height: 100vh;
      justify-content: center;
      align-items: center;
    }
    .login-box {
      background: #222;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(255,255,255,0.1);
      width: 300px;
    }
    input {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border: none;
      border-radius: 4px;
    }
    button {
      width: 100%;
      margin-top: 20px;
      padding: 10px;
      background: #444;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .error {
      color: #f88;
      margin-top: 10px;
    }
  </style>
</head>
<body>

<div class="login-box">
  <h2>Admin belépés</h2>
  <?php if ($hiba): ?>
    <div class="error"><?= htmlspecialchars($hiba) ?></div>
  <?php endif; ?>
  <form method="post">
    <input type="text" name="username" placeholder="Felhasználónév" required>
    <input type="password" name="password" placeholder="Jelszó" required>
    <button type="submit">Belépés</button>
  </form>
</div>

</body>
</html>