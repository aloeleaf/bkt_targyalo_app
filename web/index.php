<?php

require_once __DIR__ . '/app/Auth.php';
$config = require_once __DIR__ . '/config/config.php';

$auth = new Auth($config);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $result = $auth->login($username, $password);

    if ($result === true) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = $result;
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Bejelentkezés</title>
</head>
<body>
    <h2>Bejelentkezés</h2>
    <?php if (!empty($error)): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <label>Felhasználónév: <input type="text" name="username" required></label><br>
        <label>Jelszó: <input type="password" name="password" required></label><br>
        <button type="submit">Belépés</button>
    </form>
</body>
</html>
