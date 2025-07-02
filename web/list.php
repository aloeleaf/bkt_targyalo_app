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
$filtered_jegyzokonyvek = array_map(function ($row) {
    return [
        'court_name'     => $row['birosag'] ?? '',
        'council_name'   => $row['tanacs'] ?? '',
        'session_date'   => $row['date'] ?? '',
        'room_number'    => $row['rooms'] ?? '',
        'sorszam'        => $row['sorszam'] ?? '',
        'ido'            => $row['time'] ?? '',
        'ugyszam'        => $row['ugyszam'] ?? '',
        'persons'        => $row['resztvevok'] ?? '',
        'azon'           => $row['id'] ?? '', // ha van ilyen oszlopod
        'ugyminoseg'     => explode("\n", $row['subject'])[0] ?? '',
        'intezkedes'     => explode("\n", $row['subject'])[1] ?? '',
    ];
}, $rows);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tárgyalási Jegyzékek Listája</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/fontawesome/css/all.min.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    </head>
    
<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center mt-custom-top-margin">Tárgyalási Jegyzékek Listája </h1>

        <?php if (empty($filtered_jegyzokonyvek)): ?>
            <div class="alert alert-info" role="alert">
                Nincs megjeleníthető jegyzőkönyv az elmúlt 4 hétből.
            </div>
        <?php else: ?>
            <?php foreach ($filtered_jegyzokonyvek as $data): ?>
                <div class="card jegyzokonyv-card">
                    <div class="card-header" style="padding-left: 385px;">
                        Tárgyalási Jegyzék - <?php echo htmlspecialchars($data['ugyszam'] ?? 'N/A'); ?> (<?php echo htmlspecialchars($data['session_date'] ?? 'N/A'); ?>)
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm jegyzokonyv-table">
                            <tbody>
                                <tr>
                                    <th scope="row">Bíróság:</th>
                                    <td><?php echo htmlspecialchars($data['court_name'] ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Tanács:</th>
                                    <td><?php echo htmlspecialchars($data['council_name'] ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Dátum:</th>
                                    <td><?php echo htmlspecialchars($data['date'] ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Tárgyaló:</th>
                                    <td><?php echo htmlspecialchars($data['room_number'] ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Sorszám:</th>
                                    <td><?php echo htmlspecialchars($data['sorszam'] ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Idő:</th>
                                    <td><?php echo htmlspecialchars($data['ido'] ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Ügyszám:</th>
                                    <td><?php echo htmlspecialchars($data['ugyszam'] ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Résztvevők:</th>
                                    <td><?php echo nl2br(htmlspecialchars($data['persons'] ?? 'N/A')); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Id.:</th>
                                    <td><?php echo htmlspecialchars($data['letszam'] ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Ügyminőség:</th>
                                    <td><?php echo nl2br(htmlspecialchars($data['ugyminoseg'] ?? 'N/A')); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Intézkedés:</th>
                                    <td><?php echo nl2br(htmlspecialchars($data['intezkedes'] ?? 'N/A')); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>