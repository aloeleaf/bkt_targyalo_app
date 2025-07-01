<?php
session_start();
require_once __DIR__ . '/app/Auth.php';

if (!Auth::isAuthenticated()) {
    header("Location: index.php");
    exit;
}
$nev = $_SESSION['display_name'] ?? $_SESSION['user'];
$loginIdo = $_SESSION['login_time'] ?? 'ismeretlen időpont';
echo date_default_timezone_get();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kezdőlap</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/main.css" rel="stylesheet" />
</head>
<body class="no-flex">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Tárgyalási Jegyzék Rögzítése</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="nav-link text-white">Üdvözlünk <?= htmlspecialchars($nev) ?>!</span>
                    </li>
                    <li class="nav-item">
                        <span class="nav-link text-white">Belépés ideje: <?= htmlspecialchars($loginIdo) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light btn-sm ms-2" href="logout.php">Kijelentkezés</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="mb-4">Tárgyalási Jegyzék Rögzítése</h1>

        <div class="card p-4">
            <h3 class="card-title mb-3">Tárgyalási Jegyzék fejléce  </h3>
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
                        <label for="session_date" class="form-label">Dátum</label>
                        <input type="date" class="form-control form-control-sm" id="session_date" name="session_date" required>
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
                        <label for="court_name" class="form-label">Résztvevők</label>
                        <select class="form-select form-select-sm" id="court_name" name="court_name" required>
                            <option value="">Válasszon...</option>
                            <?php foreach ($persons as $persons): ?>
                                <option value="<?php echo htmlspecialchars($persons); ?>"><?php echo htmlspecialchars($persons); ?></option>
                            <?php endforeach; ?>
                            </select>
                    </div>
                    <div class="col-md-3">
                        <label for="azon" class="form-label">Id.</label>
                        <input type="text" class="form-control form-control-sm" id="azon" name="azon" placeholder="Id. (pl. 215)">
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
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
