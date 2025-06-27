<?php
$host = 'localhost';
$db = 'galeria_db';       // az előző lépésben létrehozott adatbázis neve
$user = 'root';           // XAMPP alapértelmezett felhasználó
$pass = '';               // XAMPP alapértelmezett jelszó (üres)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Adatbázis kapcsolat sikertelen: " . $e->getMessage());
}