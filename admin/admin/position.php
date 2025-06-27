<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../db.php';

$categories = [
    'festmenyek' => 'Festmények',
    'ekszerek' => 'Ékszerek',
    'tuzzomanc' => 'Tűzzománc',
    'szakralis' => 'Szakrális'
];

$category = $_GET['category'] ?? 'festmenyek';
if (!array_key_exists($category, $categories)) {
    $category = 'festmenyek';
}

$stmt = $pdo->prepare("SELECT * FROM images WHERE category = ? AND visible = 1 ORDER BY position ASC, id ASC");
$stmt->execute([$category]);
$images = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <title>Sorrend beállítása - <?= htmlspecialchars($category) ?></title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/grid.css">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/arts.css">
    <link rel="stylesheet" href="../css/gallery.css">
    <link rel="stylesheet" href="../css/loadingscreen.css">
    <link rel="stylesheet" href="../css/header.css">
    
    <link rel="icon" href="../images/icon.png" type="image/png">
    <style>
        body{
            color:white; !important
        }

        /* A sorrendezhető lista konténere */
        ul#sortable {
          list-style: none;
          padding: 40px;
          max-width: 900px;
          margin: 120px auto 40px auto;
        }

        /* Egy kép blokk */
        ul#sortable li {
          background: rgba(255, 255, 255, 0.95);
          color: #111;
          border: 1px solid #ccc;
          padding: 16px;
          margin-bottom: 16px;
          display: flex;
          align-items: center; /* függőleges közép */
          gap: 30px;
          border-radius: 8px;
          box-shadow: 0 4px 8px rgba(0,0,0,0.2);
          cursor: grab;
          transition: transform 0.2s ease;
          max-height: 400px; /* minimum magasság, hogy egységes legyen */
        }

        ul#sortable li:hover {
          transform: scale(1.01);
        }

        /* Kép megjelenítése */
        ul#sortable img {
          max-height: 250px; 
          max-width: 600px;       
          border-radius: 6px;
          border: 1px solid #999;
          object-fit: cover;   /* hogy kitöltse a 400px-t, ha kell */
        }
        /* Leírás blokk */
        ul#sortable li > div {
          font-size: 1.1rem;
          line-height: 1.4;
          color: #111;
          flex: 1; /* kitölti a maradék helyet */
          display: flex;
          flex-direction: column;
          justify-content: center; /* függőleges közép */
          padding-right: 20px;
        }
    </style>
</head>
<body class="drag-admin fade-in">

<header>
  <nav>
    <?php foreach ($categories as $key => $label): ?>
      <a href="?category=<?= $key ?>" class="<?= $key === $category ? 'active' : '' ?>">
        <?= $label ?>
      </a>
    <?php endforeach; ?>
  </nav>
  <div class="buttons-right">
    <button class="save-order">Sorrend mentése</button>
    <button id="backBtn">Vissza</button>
  </div>
</header>

<main>
  <h2><?= htmlspecialchars($categories[$category]) ?> sorrend módosítása</h2>

  <ul id="sortable" class="image-list">
    <?php foreach ($images as $img): ?>
      <li data-id="<?= $img['id'] ?>">
        <img src="../uploads/<?= $category ?>/<?= htmlspecialchars($img['filename']) ?>" alt="">
        <div>
          <strong><?= htmlspecialchars($img['description']) ?></strong><br>
          <?= htmlspecialchars($img['size']) ?> — <?= htmlspecialchars($img['technique']) ?>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
  $(function() {
    $("#sortable").sortable();
    $(".save-order").click(function() {
      let order = [];
      $("#sortable li").each(function(index) {
        order.push({ id: $(this).data("id"), position: index + 1 });
      });

      $.ajax({
        url: "save_order.php",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(order),
        success: function(res) {
          alert("Sorrend mentve!");
        },
        error: function(err) {
          alert("Hiba a mentés során!");
          console.error(err);
        }
      });
    });
  });

  document.getElementById('backBtn').addEventListener('click', () => {
  window.location.href = 'index.php';
});
</script>

</body>
</html>
