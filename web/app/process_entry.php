<?php
// Betöltjük a configot és a Database osztályt
$config = require __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

// Létrehozzuk az adatbázis kapcsolatot a már meglévő Database osztállyal
$db = new Database($config);
$pdo = $db->getPdo();  // Ez adja a PDO objektumot

// POST adatok fogadása
$birosag = $_POST['court_name'] ?? '';
$tanacs = $_POST['council_name'] ?? '';
$date = $_POST['date'] ?? '';
$room = $_POST['room_number'] ?? '';
$time = $_POST['ido'] ?? '';
$ugyszam = $_POST['ugyszam'] ?? '';
$resztvevok = $_POST['resztvevok'] ?? '';
$letszam = $_POST['letszam'] ?? '';
$ugyminoseg = $_POST['ugyminoseg'] ?? '';
$intezkedes = $_POST['intezkedes'] ?? '';


// Az ügyminőség és intézkedés összefűzése a subject mezőbe
$subject = trim($ugyminoseg . "\n" . $intezkedes);

// Ha szeretnél, beállíthatsz alapértelmezett értéket a letszamhoz
$letszam = 0; // vagy pl. számold meg a résztvevők számát, ha van ilyen mező a formban

echo '<pre>';
print_r($_POST);
echo '</pre>';

// Egyszerű validáció
if (!$birosag || !$tanacs || !$date || !$time || !$room) {
    die('Hiányzó kötelező mező(k).');
}

try {
    $stmt = $pdo->prepare("INSERT INTO rooms (birosag, tanacs, date, time, rooms, ugyszam, subject, letszam, resztvevok)
                           VALUES (:birosag, :tanacs, :date, :time, :rooms, :ugyszam, :subject, :letszam, :resztvevok)");
    $stmt->execute([
        ':birosag' => $birosag,
        ':tanacs' => $tanacs,
        ':date' => $date,
        ':time' => $time,
        ':rooms' => $room,
        ':ugyszam' => $ugyszam,
        ':subject' => $subject,
        ':letszam' => $letszam,
        ':resztvevok' => $resztvevok,
    ]);
    echo "Sikeres rögzítés!";
} catch (PDOException $e) {
    echo "Hiba az adatbázis művelet közben: " . $e->getMessage();
}
