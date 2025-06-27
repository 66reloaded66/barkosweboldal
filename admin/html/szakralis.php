<?php
require_once '../db.php';

$category = 'szakralis'; // ← ezt cseréld a megfelelő kategóriára

$stmt = $pdo->prepare("SELECT * FROM images WHERE category = ? AND visible = 1 ORDER BY id DESC");
$stmt->execute([$category]);
$images = $stmt->fetchAll();
?>


<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="index, follow">
<meta name="author" content="Barkos Beáta">
<meta name="keywords" content="galéria, művészet, festmény, zománc, Barkos, Beáta, Bea, alkotások, vallás, vallási, mű, ékszerek">

<title>Barkos Beáta</title>


<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/grid.css">
<link rel="stylesheet" href="../css/menu.css">
<link rel="stylesheet" href="../css/arts.css">
<link rel="stylesheet" href="../css/gallery.css">
<link rel="stylesheet" href="../css/loadingscreen.css">

<link rel="icon" href="../images/icon.png" type="image/png">

<body>

  <div id="loader-wrapper">
    <div class="spinner">
      <div class="bar bar1"></div>
      <div class="bar bar2"></div>
      <div class="bar bar3"></div>
      <div class="bar bar4"></div>
      <div class="bar bar5"></div>
      <div class="bar bar6"></div>
      <div class="bar bar7"></div>
      <div class="bar bar8"></div>
      <div class="bar bar9"></div>
      <div class="bar bar10"></div>
      <div class="bar bar11"></div>
      <div class="bar bar12"></div>
    </div>
  </div>
  <script src="../scripts/loadingscreen.js"></script>

  <div class="holder">
    <table>
      <?php foreach ($images as $img): ?>
    <tr>
      <td>
        <a href="../uploads/<?= $category ?>/<?= $img['filename'] ?>" class="lightbox-trigger">
          <img src="../uploads/szakralis/<?= htmlspecialchars($img['filename']) ?>" alt="<?= htmlspecialchars($img['description']) ?>">
        </a>
      </td>
      <td>
        <h2><?= htmlspecialchars($img['description']) ?></h2>
        <h3><?= htmlspecialchars($img['size']) ?></h3>
        <h3><?= htmlspecialchars($img['technique']) ?></h3>
      </td>
    </tr>
      <?php endforeach; ?>
    </table>
  </div>
  
  <script src="../scripts/animations.js"></script>
  <script src="../scripts/gallery.js"></script>
</body>


