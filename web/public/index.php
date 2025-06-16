<!DOCTYPE html>
<html>
<head>
  <title>Bejelentkezés</title>
  <meta charset="UTF-8">
</head>
<body>
  <h2>Bejelentkezés</h2>
  <form method="post" action="auth.php">
    <label>Felhasználónév:</label><br>
    <input type="text" name="username" required><br><br>
    <label>Jelszó:</label><br>
    <input type="password" name="password" required><br><br>
    <input type="submit" value="Belépés">
  </form>
</body>
</html>
