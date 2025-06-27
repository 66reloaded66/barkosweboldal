<?php
require_once '../authentication/auth.php'; // belépés ellenőrzése
require_once '../db.php';


// Képek frissítése
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
  foreach ($_POST['id'] as $index => $id) {
      $title = $_POST['title'][$index];
      $visible = isset($_POST['visible'][$index]) ? 1 : 0;
      $position = (int)$_POST['position'][$index];

      $stmt = $pdo->prepare("UPDATE images SET title = ?, visible = ?, position = ? WHERE id = ?");
      $stmt->execute([$title, $visible, $position, $id]);
  }
}

// Lekérdezzük az összes képet
$stmt = $pdo->query("SELECT * FROM images ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <title>Admin - Képek kezelése</title>
  <link rel="stylesheet" href="../css/admin.css">
  <style>
    .custom-dropdown {
  position: relative;
  width: 200px;
  user-select: none;
  perspective: 800px;
}

.selected {
  background-color: #333;
  color: white;
  padding: 10px;
  cursor: pointer;
  border-radius: 6px;
}

.dropdown-box {
  transform-origin: top center;
  transform: rotateX(-90deg);
  opacity: 0;
  max-height: 0;
  transition: transform 0.4s ease, opacity 0.3s ease;
  background: #444;
  position: absolute;
  top: 100%;
  width: 100%;
  z-index: 10;
  border-radius: 0 0 6px 6px;
  box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}

.custom-dropdown.open .dropdown-box {
  transform: rotateX(0deg);
  opacity: 1;
  max-height: 400px;
}

.dropdown-options {
  list-style: none;
  margin: 0;
  padding: 0;
}

.dropdown-options li {
  padding: 10px;
  color: white;
  cursor: pointer;
}

.dropdown-options li:hover {
  background-color: #555;
}

button {
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  background-color: #eee;
  cursor: pointer;
  color: white;
}

button[type="submit"] {
  background-color: #f44336;
  color: white;
}

button[type="submit"]:hover {
  background-color: #d32f2f;
  color: white;
}

.description-cell {
  white-space: pre-line;  /* sortörések engedélyezése */
  font-size: 14px;
  color: #ffffff;
  line-height: 1.5;
  padding: 8px 12px;
  border-radius: 6px;
}
.description-cell input[type="text"] {
  border: none;
  background: transparent;
  padding: 0;
  margin: 0;
  font-size: 14px;
  font-family: inherit;
  width: 100%;
  outline: none; /* hogy ne legyen kék keret fókuszáláskor */
  box-shadow: none;
}
.description-cell input[type="text"]:focus {
  outline: none;
  border-bottom: 1px solid #f44336; /* fókusz alatt pl. egy finom alsó vonal */
}
  </style>
</head>
<body>

<a href="logout.php" style="color: red;">Kijelentkezés</a>

<h2>Kép feltöltése</h2>

<form method="post" action="upload.php" enctype="multipart/form-data">
  <label>Kép: <input type="file" name="image" required></label><br><br>

  <label>Cím: <input type="text" name="description" required></label><br><br>

  <label>Méret: <input type="text" name="size" placeholder="pl. 30x40 cm"></label><br><br>

  <label>Technika/anyag: <input type="text" name="technique" placeholder="pl. olaj, vászon"></label><br><br>

  <label>Kategória:
    <select name="category" required>
      <option value="festmenyek">Festmények</option>
      <option value="ekszerek">Ékszerek</option>
      <option value="tuzzomanc">Tűzzománc</option>
      <option value="szakralis">Szakrális</option>
    </select>
  </label><br><br>

  <label>Alkotó neve: <input type="text" name="watermark" required></label><br><br>

  <button type="submit">Feltöltés</button>
</form>

<h2>Feltöltött képek</h2>

<form method="post" action="update_images.php">
  <table>
  <tr>
    <th>Kép</th>
    <th>Kategória</th>
    <th>Leírás</th>
    <th>Látható?</th>
    <th>Törlés</th>
  </tr>
    <?php foreach ($stmt as $image): ?>
      <tr>
  <td>
    <img src="../uploads/<?= htmlspecialchars($image['category']) ?>/<?= htmlspecialchars($image['filename']) ?>" alt="Kép">
  </td>

  <td>
    <div class="custom-dropdown" data-id="<?= $image['id'] ?>">
      <div class="selected"><?= htmlspecialchars(ucfirst($image['category'])) ?></div>
      <div class="dropdown-box">
        <ul class="dropdown-options">
          <li data-value="festmenyek">Festmények</li>
          <li data-value="ekszerek">Ékszerek</li>
          <li data-value="tuzzomanc">Tűzzománc</li>
          <li data-value="szakralis">Szakrális</li>
        </ul>
      </div>
      <input type="hidden" name="category[<?= $image['id'] ?>]" value="<?= htmlspecialchars($image['category']) ?>">
    </div>
  </td>

  <td class="description-cell">
    <input type="text" name="description[<?= $image['id'] ?>]" value="<?= htmlspecialchars($image['description']) ?>" placeholder="Leírás" style="display:block; width: 100%; margin-bottom: 6px;">
    <input type="text" name="size[<?= $image['id'] ?>]" value="<?= htmlspecialchars($image['size']) ?>" placeholder="Méret" style="display:block; width: 100%; margin-bottom: 6px;">
    <input type="text" name="technique[<?= $image['id'] ?>]" value="<?= htmlspecialchars($image['technique']) ?>" placeholder="Technika" style="display:block; width: 100%;">
  </td>

  <td>
    <input type="checkbox" name="visible[<?= $image['id'] ?>]" <?= $image['visible'] ? 'checked' : '' ?>>
  </td>

  <td>
    <button type="button" class="delete-button" 
      data-id="<?= $image['id'] ?>" 
      data-filename="<?= htmlspecialchars($image['filename']) ?>" 
      data-category="<?= htmlspecialchars($image['category']) ?>"
      style="color: red;">
      Törlés
    </button>
  </td>
</tr>

    <?php endforeach; ?>
  </table>

  <button type="submit" name="update">Módosítások mentése</button>
</form>
<!-- Törlés megerősítő modál -->
<div id="deleteModal" class="modal" style="display:none;">
  <div class="modal-content">
    <p>Biztosan törölni szeretnéd a képet?</p>
    <label><input type="checkbox" id="dontShowAgain"> Nem szeretném többet látni ezt az ablakot</label>
    <div class="modal-buttons">
      <button id="cancelBtn">Mégsem</button>
      <button id="confirmBtn">Törlés</button>
    </div>
  </div>
</div>
<a href="position.php">
  <button style="background-color: #2196F3; color: white; padding: 10px 20px; border: none; border-radius: 4px; margin-bottom: 20px; cursor: pointer;">
    Képek sorrendjének beállítása
  </button>
</a>
<style>
.modal {
  position: fixed; top:0; left:0; width:100%; height:100%;
  background: rgba(0,0,0,0.6);
  display: flex; justify-content: center; align-items: center;
  z-index: 9999;
}
.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  max-width: 300px;
  text-align: center;
}
.modal-buttons {
  margin-top: 15px;
}
.modal-buttons button {
  margin: 0 10px;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
#cancelBtn {
  background-color: #bbb;
}
#confirmBtn {
  background-color: #f44336;
  color: white;
}
#deleteModal {
  position: fixed;
  top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0,0,0,0.6);
  display: flex; justify-content: center; align-items: center;
  z-index: 9999;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('deleteModal');
  const dontShowCheckbox = document.getElementById('dontShowAgain');
  const cancelBtn = document.getElementById('cancelBtn');
  const confirmBtn = document.getElementById('confirmBtn');

  let currentButton = null;

  document.querySelectorAll('.delete-button').forEach(button => {
    button.addEventListener('click', e => {
      if (localStorage.getItem('skipDeleteConfirm') === 'true') {
        submitDeleteForm(button);
        return;
      }
      e.preventDefault();
      currentButton = button;
      modal.style.display = 'flex';
    });
  });

  cancelBtn.addEventListener('click', () => {
    modal.style.display = 'none';
    currentButton = null;
  });

  confirmBtn.addEventListener('click', () => {
    if (dontShowCheckbox.checked) {
      localStorage.setItem('skipDeleteConfirm', 'true');
    }
    modal.style.display = 'none';
    if (currentButton) {
      submitDeleteForm(currentButton);
    }
  });

  function submitDeleteForm(button) {
    const form = document.createElement('form');
    form.method = 'post';
    form.action = 'delete_image.php';

    ['id', 'filename', 'category'].forEach(field => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = field;
      input.value = button.dataset[field];
      form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
  }
});
</script>

<script>
document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
  const selected = dropdown.querySelector('.selected');
  const box = dropdown.querySelector('.dropdown-box');
  const options = dropdown.querySelectorAll('.dropdown-options li');
  const hiddenInput = dropdown.querySelector('input[type="hidden"]');

  selected.addEventListener('click', () => {
    dropdown.classList.toggle('open');
  });

  options.forEach(option => {
    option.addEventListener('click', () => {
      selected.textContent = option.textContent;
      hiddenInput.value = option.getAttribute('data-value');
      dropdown.classList.remove('open');
    });
  });

  document.addEventListener('click', e => {
    if (!dropdown.contains(e.target)) {
      dropdown.classList.remove('open');
    }
  });
});
</script>
</body>
</html>
