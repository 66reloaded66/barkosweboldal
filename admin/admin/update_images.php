<?php
require_once '../authentication/auth.php';
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Érvénytelen kérés.');
}

$allowedCategories = ['festmenyek', 'ekszerek', 'tuzzomanc', 'szakralis'];

foreach ($_POST['description'] as $id => $description) {
    $id = (int)$id;
    $description = trim($description);
    $size = trim($_POST['size'][$id] ?? '');
    $technique = trim($_POST['technique'][$id] ?? '');
    $category = $_POST['category'][$id] ?? '';
    $visible = isset($_POST['visible'][$id]) ? 1 : 0;

    if (!in_array($category, $allowedCategories)) {
        continue;
    }

    // Lekérjük az előző kategóriát és a fájlnevet az adott képhez
    $stmtOld = $pdo->prepare("SELECT filename, category FROM images WHERE id = ?");
    $stmtOld->execute([$id]);
    $oldImage = $stmtOld->fetch();

    if ($oldImage && $oldImage['category'] !== $category) {
        $oldPath = __DIR__ . '/../uploads/' . $oldImage['category'] . '/' . $oldImage['filename'];
        $newDir = __DIR__ . '/../uploads/' . $category;
        $newPath = $newDir . '/' . $oldImage['filename'];

        if (!is_dir($newDir)) {
            mkdir($newDir, 0755, true);
        }

        if (file_exists($oldPath)) {
            rename($oldPath, $newPath);
        }
    }

    // Adatbázis frissítése
    $stmt = $pdo->prepare("UPDATE images SET description = ?, size = ?, technique = ?, category = ?, visible = ? WHERE id = ?");
    $stmt->execute([$description, $size, $technique, $category, $visible, $id]);
}

header('Location: index.php');
exit;
