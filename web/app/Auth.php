<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Auth {
    private $config;

    public function __construct($config) {
        $this->config = $config;
    }

    public function login($username, $password) {
        $ldap_user = $username . '@birosagiad.hu';
        $conn = ldap_connect($this->config['ldap_server']);

        ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);

        if ($conn && @ldap_bind($conn, $ldap_user, $password)) {
            $search = ldap_search(
                $conn,
                $this->config['ldap_base_dn'],
                "(sAMAccountName=$username)",
                ['memberof']
            );

            if (!$search) {
                return "LDAP keresési hiba: " . ldap_error($conn);
            }

            $entries = ldap_get_entries($conn, $search);
            if ($entries['count'] > 0 && isset($entries[0]['memberof'])) {
                foreach ($entries[0]['memberof'] as $group) {
                    if (stripos($group, $this->config['ldap_group_dn']) !== false) {
                        $_SESSION['user'] = $username;
                        return true;
                    }
                }
                return "Nincs jogosultságod a weboldalhoz.";
            }
            return "Nem található felhasználó az LDAP-ban.";
        }

        return "Hibás felhasználónév vagy jelszó, vagy nem elérhető az LDAP.";
    }

    public static function isAuthenticated() {
        return isset($_SESSION['user']);
    }

    public static function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Ürítsük ki a session adattárolót
    $_SESSION = [];

    // Töröljük a session cookie-t is, ha van
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Végül megszüntetjük a session-t
    session_destroy();
    }
}
