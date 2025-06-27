<?php
require_once '../authentication/auth.php';
require_once '../db.php';

// Vízjel hozzáadó függvény (egyszerű szöveges vízjel átlátszó fehér színnel)
function addWatermark($sourcePath, $destPath, $watermarkText) {
    $imageInfo = getimagesize($sourcePath);
    $mime = $imageInfo['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $image = imagecreatefrompng($sourcePath);
            break;
        default:
            return false;
    }

    // 🔤 Használjunk egy dőlt fontot (pl. ariali.ttf — Arial Italic)
    $fontFile = __DIR__ . '/arial.ttf';
    if (!file_exists($fontFile)) {
        die('Hiányzik a fontfájl (pl. ariali.ttf)');
    }

    $imageWidth = imagesx($image);
    $imageHeight = imagesy($image);

    // 🧮 Átló hossza: Pitagorasz-tétel
    $diagonalLength = sqrt($imageWidth ** 2 + $imageHeight ** 2);

    // 📏 Betűméret a kép méretéhez viszonyítva
    $fontSize = (int) round(min($imageWidth, $imageHeight) / 10);

    // 🧊 70% átlátszóság (alpha skálán: 127 → teljesen átlátszó, 0 → teljesen látható)
    $textColor = imagecolorallocatealpha($image, 255, 255, 255, 89);

    // ↖ Szöget -45°-ra állítjuk (átlós vízjel)
    $angleRad = atan2($imageHeight, $imageWidth);
    $angle = -rad2deg($angleRad);   

    // 🧾 A teljes hosszhoz szükséges ismétlés kiszámítása
    $textBox = imagettfbbox($fontSize, $angle, $fontFile, $watermarkText . '   ');
    $textWidth = abs($textBox[4] - $textBox[0]);
    $repeats = ceil($diagonalLength / $textWidth);
    $repeatedText = str_repeat($watermarkText . '   ', $repeats);

    // 📍 Kiindulási pozíció bal felső sarokból úgy, hogy a teljes szöveg átlósan haladjon át
    $x = 0;
    $y = $fontSize;

    // ✍️ Kiírás
    imagettftext($image, $fontSize, $angle, $x, $y, $textColor, $fontFile, $repeatedText);

    // 💾 Mentés
    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($image, $destPath, 90);
            break;
        case 'image/png':
            imagepng($image, $destPath);
            break;
    }

    imagedestroy($image);
    return true;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Érvénytelen kérés.');
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    die('Hiba a fájl feltöltése közben.');
}

$category = $_POST['category'] ?? '';
$watermark = trim($_POST['watermark'] ?? '');
$description = trim($_POST['description'] ?? '');
$size = trim($_POST['size'] ?? '');
$technique = trim($_POST['technique'] ?? '');

$allowedCategories = ['festmenyek', 'ekszerek', 'tuzzomanc', 'szakralis'];
if (!in_array($category, $allowedCategories)) {
    die('Érvénytelen kategória.');
}
if ($watermark === '' || $description === '') {
    die('Hiányzó leírás vagy vízjel szöveg.');
}

$uploadDir = __DIR__ . '/../uploads/' . $category . '/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$tmpName = $_FILES['image']['tmp_name'];
$originalName = basename($_FILES['image']['name']);
$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

// Kiterjesztés ellenőrzése
if (!in_array($ext, $allowedExtensions)) {
    die('Csak JPG, JPEG, PNG vagy GIF képek tölthetők fel.');
}

// MIME típus ellenőrzése
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $tmpName);
finfo_close($finfo);
if (!in_array($mimeType, $allowedMimeTypes)) {
    die('Csak JPG, JPEG, PNG vagy GIF képek tölthetők fel.');
}

$newFileName = uniqid() . '.' . $ext;
$destPath = $uploadDir . $newFileName;

// Feltöltés a szerverre
if (!move_uploaded_file($tmpName, $destPath)) {
    die('Nem sikerült a fájl feltöltése.');
}

// Vízjel hozzáadása (felülírja az eredetit)
if (!addWatermark($destPath, $destPath, $watermark)) {
    unlink($destPath);
    die('Nem sikerült a vízjel hozzáadása.');
}

// Adatok mentése az adatbázisba
$stmt = $pdo->prepare("INSERT INTO images (filename, category, visible, description, watermark_text, size, technique) VALUES (?, ?, 1, ?, ?, ?, ?)");
$stmt->execute([$newFileName, $category, $description, $watermark, $size, $technique]);

// Visszairányítás az admin oldalra
header('Location: index.php');
exit;
