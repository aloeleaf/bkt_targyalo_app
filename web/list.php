<?php
// Betöltjük a configot és a Database osztályt
$config = require __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Database.php';

$db = new Database($config);
$pdo = $db->getPdo();

// Adatok lekérése az elmúlt 4 hétből (ha szükséges szűrés)
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE date >= CURDATE() - INTERVAL 28 DAY ORDER BY date DESC, time ASC");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Átalakítjuk a mezőket a megjelenítéshez
$filtered_entries = array_map(function ($row) { // Változó neve módosítva
    $time = $row['time'] ?? '';
    $formatted_time = $time ? substr($time, 0, 5) : '';
    return [
        'court_name'     => $row['birosag'] ?? '',
        'council_name'   => $row['tanacs'] ?? '',
        'session_date'   => $row['date'] ?? '',
        'room_number'    => $row['rooms'] ?? '',
        'sorszam'        => $row['sorszam'] ?? '',
        'ido'            => $formatted_time,
        'ugyszam'        => $row['ugyszam'] ?? '',
        'persons'        => $row['resztvevok'] ?? '',
        'azon'           => $row['letszam'] ?? '', 
        'id'             => $row['id'] ?? '', 
        'ugyminoseg'     => explode("\n", $row['subject'])[0] ?? '',
        'intezkedes'     => explode("\n", $row['subject'])[1] ?? '',
    ];
}, $rows);
?>

    <div class="container mt-5">
        <h1 class="mb-4 text-center mt-custom-top-margin">Tárgyalási Bejegyzések Listája </h1>

        <div class="mb-4">
            <!-- Az ID megtartva a JS kompatibilitás miatt, de a placeholder módosítva -->
            <input type="text" class="form-control" id="jegyzokonyvSearch" placeholder="Keresés az ügyszám, bíróság és tanács alapján...">
        </div>
        
        <!-- Új export gomb hozzáadása -->
        <div class="mb-4 text-end">
            <button id="exportCsvBtn" class="btn btn-info btn-sm">
                <i class="fa-solid fa-file-csv"></i> Exportálás CSV-be
            </button>
        </div>

        <div id="jegyzokonyvListContainer">
            <?php if (empty($filtered_entries)): ?>
                <div class="alert alert-info" role="alert">
                    Nincs megjeleníthető bejegyzés az elmúlt 4 hétből.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($filtered_entries as $data): ?>
                        <div class="col-12 col-md-6 mb-4">
                            <div class="card entry-card h-100"> <!-- Osztály neve módosítva -->
                                <div class="card-header text-center">
                                    Tárgyalási Bejegyzés - <?php echo htmlspecialchars($data['ugyszam'] ?? 'N/A'); ?> (<?php echo htmlspecialchars($data['session_date'] ?? 'N/A'); ?>)
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 mb-2"><strong title="A tárgyalás helyszínéül szolgáló bíróság neve.">Bíróság:</strong> <?= htmlspecialchars($data['court_name'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2"><strong title="A tárgyalást vezető bíró.">Tanács:</strong> <?= htmlspecialchars($data['council_name'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2"><strong title="A tárgyalás dátuma.">Dátum:</strong> <?= htmlspecialchars($data['session_date'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2"><strong title="A tárgyalás helyszínéül szolgáló tárgyalóterem száma vagy neve.">Tárgyaló:</strong> <?= htmlspecialchars($data['room_number'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2"><strong title="A tárgyalás sorszáma az adott napon.">Sorszám:</strong> <?= htmlspecialchars($data['sorszam'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2"><strong title="A tárgyalás kezdési időpontja.">Idő:</strong> <?= htmlspecialchars($data['ido'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2"><strong title="A tárgyalás ügyazonosító száma.">Ügyszám:</strong> <?= htmlspecialchars($data['ugyszam'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2"><strong title="A tárgyalásban résztvevő felek minősége.">Résztvevők:</strong> <?= nl2br(htmlspecialchars($data['persons'] ?? 'N/A')); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2"><strong title="Az idézettek szám.">Id.:</strong> <?= htmlspecialchars($data['azon'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2"><strong title="Az ügy minősítése vagy típusa.">Ügyminőség:</strong> <?= nl2br(htmlspecialchars($data['ugyminoseg'] ?? 'N/A')); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2"><strong title="Az ügyben hozott intézkedés vagy döntés.">Intézkedés:</strong> <?= nl2br(htmlspecialchars($data['intezkedes'] ?? 'N/A')); ?></div>
                                    </div>
                                </div>
                                <div class="card-footer text-center">
                                    <?php if (!empty($data['id'])): ?>
                                        <a href="#" class="btn btn-primary btn-sm edit-button" data-id="<?= htmlspecialchars($data['id']); ?>">
                                            <i class="fa-solid fa-edit"></i> Szerkesztés
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Nincs azonosító a szerkesztéshez</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div> <?php endif; ?>
        </div>
    </div>
