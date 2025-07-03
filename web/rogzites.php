<?php
// Betöltjük a configot és a Database osztályt
$config = require __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Database.php';

// Létrehozzuk az adatbázis kapcsolatot a már meglévő Database osztállyal
$db = new Database($config);
$pdo = $db->getPdo();  // Ez adja a PDO objektumot

// Lekérdezzük az adott kategóriákhoz tartozó értékeket
function getDropdownItems(PDO $pdo, string $category): array {
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE category = :category AND active = 1 ORDER BY value ASC");
    $stmt->execute(['category' => $category]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

$courts = getDropdownItems($pdo, 'birosag');
$councils = getDropdownItems($pdo, 'tanacs');
$rooms = getDropdownItems($pdo, 'room');
$persons = getDropdownItems($pdo, 'resztvevok'); 

?>

<h1 class="mb-4">Tárgyalási Jegyzék Rögzítése</h1>

        <div class="card p-4">
            <h3 class="card-title mb-3">Tárgyalási Jegyzék fejléce  </h3>
            <form action="/app/process_entry.php" method="POST">
<h1 class="mb-4 text-center">Tárgyalási Jegyzék Rögzítése</h1>

        <div class="card p-4">
            <h3 class="card-title mb-3 ">Tárgyalási Jegyzék fejléce  </h3>
            <form action="process_entry.php" method="POST">
                <div class="row g-2 mb-4">
                    <div class="col-md-6">
                        <label for="court_name" class="form-label">Bíróság</label>
                        <select class="form-select form-select-sm" id="court_name" name="court_name" required>
                            <option value="">Válasszon...</option>
                            <?php foreach ($courts as $court): ?>
                                <option value="<?php echo htmlspecialchars($court); ?>"><?php echo htmlspecialchars($court); ?></option>
                            <?php endforeach; ?>
                            </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="council_name" class="form-label">Tanács</label>
                        <select class="form-select form-select-sm" id="council_name" name="council_name" required>
                            <option value="">Válasszon...</option>
                            <?php foreach ($councils as $council): ?>
                                <option value="<?php echo htmlspecialchars($council); ?>"><?php echo htmlspecialchars($council); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-4">
                    <div class="col-md-6">
                        <label for="date" class="form-label">Dátum</label>
                        <input type="date" class="form-control form-control-sm" id="date" name="date" required>
                    </div>
                    <div class="col-md-6">
                        <label for="room_number" class="form-label">Tárgyaló</label>
                        <select class="form-select form-select-sm" id="room_number" name="room_number" required>
                            <option value="">Válasszon...</option>
                            <?php foreach ($rooms as $room): ?>
                                <option value="<?php echo htmlspecialchars($room); ?>"><?php echo htmlspecialchars($room); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <hr class="my-4">

                <h3 class="card-title mb-3">Tárgyalási Jegyzék</h3>
                <div class="row g-2 align-items-center mb-3">
                    <div class="col-md-2">
                        <label for="sorszam_display" class="form-label">Sorszám</label>
                        <input type="text" class="form-control form-control-sm" id="sorszam_display" value="1" disabled>
                        <input type="hidden" name="sorszam" value="1">
                    </div>
                    <div class="col-md-4">
                        <label for="ido" class="form-label">Idő</label>
                        <input type="time" class="form-control form-control-sm" id="ido" name="ido" placeholder="Idő (pl. 13:00)">
                    </div>
                    <div class="col-md-3">
                        <label for="ugyszam" class="form-label">Ügyszám</label>
                        <input type="text" class="form-control form-control-sm" id="ugyszam" name="ugyszam" placeholder="Ügyszám (pl. P.2023/2023)">
                    </div>
                    <div class="row g-2 mb-4">
                    <div class="col-md-6">
                        <label for="resztvevok" class="form-label">Résztvevők</label>
                        <select class="form-select form-select-sm" id="resztvevok" name="resztvevok" required>
                            <option value="">Válasszon...</option>
                            <?php foreach ($persons as $persons): ?>
                                <option value="<?php echo htmlspecialchars($persons); ?>"><?php echo htmlspecialchars($persons); ?></option>
                            <?php endforeach; ?>
                            </select>
                    </div>
                    <div class="col-md-3">
                        <label for="letszam" class="form-label">Id.</label>
                        <input type="text" class="form-control form-control-sm" id="letszam" name="letszam" placeholder="Id. (pl. 215)">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label for="ugyminoseg" class="form-label">Ügyminőség</label>
                        <textarea class="form-control form-control-sm" id="ugyminoseg" name="ugyminoseg" rows="2" placeholder="Ügyminőség (pl. Sajtóhelyreigazítási per)"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="intezkedes" class="form-label">Intézkedés</label>
                        <textarea class="form-control form-control-sm" id="intezkedes" name="intezkedes" rows="2" placeholder="Intézkedés"></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-success mt-3">Rögzítés és Mentés</button>
            </form>
        </div>