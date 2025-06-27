<?php
require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
  http_response_code(400);
  echo "Invalid data";
  exit;
}

foreach ($data as $item) {
  $stmt = $pdo->prepare("UPDATE images SET position = ? WHERE id = ?");
  $stmt->execute([$item['position'], $item['id']]);
}

echo "OK";
