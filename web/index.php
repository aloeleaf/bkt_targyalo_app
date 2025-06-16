<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BKT Tárgyaló App</title>
</head>
<body>
<h1>Welcome to the BKT Tárgyaló App



</h1>
<p><a href="phpinfo.php">Rendszer információ</a></p>

<?php
require '/var/www/php/helper.php';
echo "Hello from web root!<br>";
echo custom_function();
echo "<br>Current time: " . get_current_time() . "<br>";
echo "Server info: " . get_server_info() . "<br>";
echo "This is the web root index.php file.<br>";
echo connect_to_mariadb('db', 'dbappuser', 'p1ssw2rd', 'bktAppdb');
?>

</body>
</html>