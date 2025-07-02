<?php
$all_jegyzokonyvek = [
    [
        'id' => 1,
        'court_name' => 'Fővárosi Törvényszék',
        'council_name' => '1. Számú Tanács',
        'session_date' => '2025-06-28', 
        'room_number' => '5-ös Tárgyaló',
        'sorszam' => '1',
        'ido' => '09:30',
        'ugyszam' => 'P.2024/1234',
        'persons' => 'Dr. Kovács Béla (bíró), Szabó Éva (ügyvéd)',
        'azon' => '123456',
        'ugyminoseg' => 'Kereskedelmi jogvita',
        'intezkedes' => 'Következő tárgyalás kijelölése.'
    ],
    [
        'id' => 2,
        'court_name' => 'Pesti Központi Kerületi Bíróság',
        'council_name' => '2. Számú Tanács',
        'session_date' => '2025-06-20', 
        'room_number' => '2B',
        'sorszam' => '2',
        'ido' => '10:00',
        'ugyszam' => 'B.2025/567',
        'persons' => 'Dr. Nagy Anna (bíró), Kiss Gergő (vádlott)',
        'azon' => '789012',
        'ugyminoseg' => 'Büntető ügy: lopás',
        'intezkedes' => 'Bizonyítási eljárás lezárva.'
    ],
    [
        'id' => 3,
        'court_name' => 'Fővárosi Ítélőtábla',
        'council_name' => 'C. Tanács',
        'session_date' => '2025-06-05', 
        'room_number' => '12-es terem',
        'sorszam' => '3',
        'ido' => '11:15',
        'ugyszam' => 'F.2023/890',
        'persons' => 'Dr. Tóth Csaba (bíró), Molnár Vera (felperes képv.)',
        'azon' => '345678',
        'ugyminoseg' => 'Fellebbezési ügy',
        'intezkedes' => 'Ítélet kihirdetésének időpontja: 2025.07.10.'
    ],
    [
        'id' => 4,
        'court_name' => 'Szolnoki Törvényszék',
        'council_name' => 'A. Tanács',
        'session_date' => '2025-05-15', 
        'room_number' => '1-es Tárgyaló',
        'sorszam' => '4',
        'ido' => '14:00',
        'ugyszam' => 'K.2022/321',
        'persons' => 'Dr. Varga Zoltán (bíró), Pap Géza (tanú)',
        'azon' => '901234',
        'ugyminoseg' => 'Környezetvédelmi per',
        'intezkedes' => 'Szakértői vélemény bekérése.'
    ]
];
$filtered_jegyzokonyvek = [];
$four_weeks_ago = date('Y-m-d', strtotime('-4 weeks')); 

foreach ($all_jegyzokonyvek as $jegyzokonyv) {
    if ($jegyzokonyv['session_date'] >= $four_weeks_ago) {
        $filtered_jegyzokonyvek[] = $jegyzokonyv;
    }
}

usort($filtered_jegyzokonyvek, function($a, $b) {
    return strtotime($b['session_date']) - strtotime($a['session_date']);
});


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
                                    <td><?php echo htmlspecialchars($data['session_date'] ?? 'N/A'); ?></td>
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
                                    <td><?php echo htmlspecialchars($data['azon'] ?? 'N/A'); ?></td>
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