<?php
session_start();
require_once __DIR__ . '/app/Auth.php';

if (!Auth::isAuthenticated()) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Kezdőlap</title>
</head>
<body>
    <h1>Üdvözlünk, <?= htmlspecialchars($_SESSION['user']) ?>!</h1>
    <p><a href="logout.php">Kijelentkezés</a></p>
</body>
</html>
