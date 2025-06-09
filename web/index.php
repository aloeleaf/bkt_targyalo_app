<?php
require '/var/www/php/helper.php';
echo "Hello from web root!<br>";
echo custom_function();
echo "<br>Current time: " . get_current_time() . "<br>";
echo "Server info: " . get_server_info() . "<br>";
echo "This is the web root index.php file.<br>";
