<?php
require_once '../authentication/auth.php';
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $filename = basename($_POST['filename'] ?? '');
    $category = preg_replace('/[^a-z]/', '', $_POST['category'] ?? '');

    $allowedCategories = ['festmenyek', 'ekszerek', 'tuzzomanc', 'szakralis'];
    if (!$id || !$filename || !in_array($category, $allowedCategories)) {
        die('Érvénytelen adatok.');
    }

    // Kép törlése fájlrendszerből
    $filePath = __DIR__ . "/../uploads/$category/$filename";
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Törlés az adatbázisból
    $stmt = $pdo->prepare("DELETE FROM images WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: index.php');
exit;
