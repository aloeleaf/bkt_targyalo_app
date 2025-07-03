<?php
// app/edit_entry_api.php

// Betöltjük a configot és a Database osztályt
$config = require __DIR__ . '/../config/config.php'; // Fontos a helyes útvonal!
require_once __DIR__ . '/Database.php'; // Feltételezve, hogy a Database.php az app mappában van

$db = new Database($config);
$pdo = $db->getPdo();

header('Content-Type: application/json'); // Minden válasz JSON formátumú lesz

$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

// Adatok lekérdezése (GET kérés)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];

        try {
            $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $jegyzokonyv = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($jegyzokonyv) {
                // A 'subject' mező felosztása 'ugyminoseg'-re és 'intezkedes'-re
                $subject_parts = explode("\n", $jegyzokonyv['subject'] ?? '');
                $jegyzokonyv['ugyminoseg'] = $subject_parts[0] ?? '';
                $jegyzokonyv['intezkedes'] = $subject_parts[1] ?? '';

                $response['success'] = true;
                $response['data'] = $jegyzokonyv;
            } else {
                $response['message'] = 'A megadott azonosítóval nem található jegyzőkönyv.';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Adatbázis hiba a lekérdezéskor: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Nincs megadva jegyzőkönyv azonosító a lekérdezéshez.';
    }
} 
// Adatok frissítése (POST kérés)
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ellenőrizzük, hogy minden szükséges adat megérkezett-e
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $id = $_POST['id'];
        $birosag = $_POST['birosag'] ?? '';
        $tanacs = $_POST['tanacs'] ?? '';
        $date = $_POST['date'] ?? '';
        $rooms = $_POST['rooms'] ?? '';
        $sorszam = $_POST['sorszam'] ?? '';
        $time = $_POST['time'] ?? '';
        $ugyszam = $_POST['ugyszam'] ?? '';
        $persons = $_POST['persons'] ?? '';
        $letszam = $_POST['letszam'] ?? '';
        $ugyminoseg = $_POST['ugyminoseg'] ?? '';
        $intezkedes = $_POST['intezkedes'] ?? '';

        // A 'subject' mező visszaállítása a két részből
        $subject = $ugyminoseg . "\n" . $intezkedes;

        try {
            $stmt = $pdo->prepare("UPDATE rooms SET 
                birosag = :birosag, 
                tanacs = :tanacs, 
                date = :date, 
                rooms = :rooms, 
                sorszam = :sorszam, 
                time = :time, 
                ugyszam = :ugyszam, 
                resztvevok = :persons, 
                letszam = :letszam, 
                subject = :subject 
                WHERE id = :id");

            $stmt->bindParam(':birosag', $birosag);
            $stmt->bindParam(':tanacs', $tanacs);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':rooms', $rooms);
            $stmt->bindParam(':sorszam', $sorszam);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':ugyszam', $ugyszam);
            $stmt->bindParam(':persons', $persons);
            $stmt->bindParam(':letszam', $letszam);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'A jegyzőkönyv sikeresen frissítve!';
            } else {
                $response['message'] = 'Hiba történt a jegyzőkönyv frissítésekor.';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Adatbázis hiba a frissítéskor: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Hiányzó adatok a frissítéshez.';
    }
} else {
    $response['message'] = 'Érvénytelen kérés metódus.';
}

echo json_encode($response);
?>
