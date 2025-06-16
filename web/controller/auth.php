<?php
session_start();

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// AD szerver adatai
$ldap_server = "ldap://dc1.yourdomain.local";
$ldap_domain = "YOURDOMAIN"; // pl. "IDOMSOFT"
$ldap_dn = "$ldap_domain\\$username";

// LDAP kapcsolat létrehozása
$ldapconn = ldap_connect($ldap_server);
ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

if ($ldapconn) {
    // Hitelesítés
    $ldapbind = @ldap_bind($ldapconn, $ldap_dn, $password);

    if ($ldapbind) {
        // Sikeres bejelentkezés
        $_SESSION['user'] = $username;
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Hibás felhasználónév vagy jelszó.";
    }

    ldap_unbind($ldapconn);
} else {
    echo "Nem lehet csatlakozni az LDAP szerverhez.";
}
?>
