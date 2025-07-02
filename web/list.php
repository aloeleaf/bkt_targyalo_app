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
    <style>
        .jegyzokonyv-table {
            max-width: 800px;
            margin: 0 auto;
        }
        .jegyzokonyv-table th {
            width: 150px; 
            text-align: right;
            padding-right: 15px;
            vertical-align: top; 
        }
        .jegyzokonyv-table td {
            vertical-align: top; 
        }
        .jegyzokonyv-card {
            margin-bottom: 40px; 
            border: 5px solid #dee2e6; 
            border-radius: 0.375rem; 
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); 
        }
        .jegyzokonyv-card .card-header {
            background-color: #f8f9fa; 
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1.25rem;
            margin-bottom: 0;
            font-weight: bold;
        }
        .jegyzokonyv-card .card-body {
            padding: 1.25rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Tárgyalási Jegyzékek Listája </h1>

        <?php if (empty($filtered_jegyzokonyvek)): ?>
            <div class="alert alert-info" role="alert">
                Nincs megjeleníthető jegyzőkönyv az elmúlt 4 hétből.
            </div>
        <?php else: ?>
            <?php foreach ($filtered_jegyzokonyvek as $data): ?>
                <div class="card jegyzokonyv-card">
                    <div class="card-header">
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