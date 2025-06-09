<?php
function custom_function() {
    return "This is a custom PHP function outside the web root.";
}

function get_current_time() {
    return date("Y-m-d H:i:s");
}

function get_server_info() {
    return $_SERVER['SERVER_SOFTWARE'] . ' running on ' . $_SERVER['SERVER_NAME'];
}

// Example usage
// echo custom_function();
// echo get_current_time();
// echo get_server_info();
// echo "This is the helper.php file.";

