<?php
// app/get_list_data.php

// Betöltjük a configot és a Database osztályt
$config = require __DIR__ . '/../config/config.php'; // Fontos a helyes útvonal!
require_once __DIR__ . '/Database.php'; // Feltételezve, hogy a Database.php az app mappában van

$db = new Database($config);
$pdo = $db->getPdo();

header('Content-Type: application/json'); // Minden válasz JSON formátumú lesz

$response = [
    'success' => false,
    'message' => '',
    'data' => []
];

// Alapértelmezett rendezési paraméterek
$orderBy = 'date';
$orderDir = 'DESC';

// Ha van rendezési paraméter az URL-ben, használjuk azt
if (isset($_GET['orderBy'])) {
    switch ($_GET['orderBy']) {
        case 'date':
            $orderBy = 'date';
            $orderDir = 'DESC';
            break;
        case 'room_number':
            $orderBy = 'rooms'; // Az adatbázis oszlop neve
            $orderDir = 'ASC';
            break;
        case 'council_name':
            $orderBy = 'tanacs'; // Az adatbázis oszlop neve
            $orderDir = 'ASC';
            break;
        case 'ugyszam':
            $orderBy = 'ugyszam';
            $orderDir = 'ASC';
            break;
        default:
            // Alapértelmezett, ha érvénytelen paramétert kapunk
            $orderBy = 'date';
            $orderDir = 'DESC';
            break;
    }
}

try {
    // Adatok lekérése az elmúlt 4 hétből a kiválasztott rendezés szerint
    // Fontos: Az ORDER BY záradékot dinamikusan illesztjük be, de csak megbízható forrásból származó oszlopnevekkel!
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE date >= CURDATE() - INTERVAL 28 DAY ORDER BY " . $orderBy . " " . $orderDir . ", time ASC");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Átalakítjuk a mezőket a megjelenítéshez hasonlóan, de most JSON-nak
    $formatted_data = array_map(function ($row) {
        $time = $row['time'] ?? '';
        $formatted_time = $time ? substr($time, 0, 5) : '';
        $subject_parts = explode("\n", $row['subject'] ?? '');

        return [
            'id'             => $row['id'] ?? '',
            'birosag'        => $row['birosag'] ?? '',
            'tanacs'         => $row['tanacs'] ?? '',
            'datum'          => $row['date'] ?? '',
            'targyalo'       => $row['rooms'] ?? '',
            'sorszam'        => $row['sorszam'] ?? '',
            'ido'            => $formatted_time,
            'ugyszam'        => $row['ugyszam'] ?? '',
            'resztvevok'     => $row['resztvevok'] ?? '',
            'azon'           => $row['letszam'] ?? '', 
            'ugyminoseg'     => $subject_parts[0] ?? '',
            'intezkedes'     => $subject_parts[1] ?? '',
        ];
    }, $rows);

    $response['success'] = true;
    $response['data'] = $formatted_data;

} catch (PDOException $e) {
    $response['message'] = 'Adatbázis hiba a listázáskor: ' . $e->getMessage();
}

echo json_encode($response);
?>
