<?php
// rogzites.php - FIGYELEM: Ez a fájl CSAK a HTML struktúrát tartalmazza, PHP logikát NEM!
// A PHP logikát (adatbázis kapcsolat, dropdown elemek lekérdezése) eltávolítottuk innen,
// mert a dashboard.php már betölti a szükséges JS fájlokat, és az adatok feltöltése
// a main.js-ben történik AJAX-on keresztül.

// A dropdown elemeket (courts, councils, rooms, persons) itt már nem kérdezzük le,
// feltételezzük, hogy ezek az adatok valamilyen módon a kliens oldalon is elérhetőek
// vagy a szerkesztés API-ból jönnek, ha szerkesztésről van szó.
// Ha feltétlenül szükségesek a PHP által generált dropdown opciók,
// akkor a loadEditData függvénynek kellene valahogy lekérdeznie őket,
// vagy a rogzites.php-nak kellene JSON-t is visszaadnia a HTML mellett,
// de ez bonyolítaná a fetch-et.
// A legegyszerűbb, ha ezek a dropdown adatok statikusan vannak a rogzites.php-ban,
// vagy egy külön API-ból töltődnek be JavaScripttel.
// Jelenleg úgy hagyom, ahogy volt, de tudatában kell lenni, hogy a PHP ciklusok
// csak akkor fognak működni, ha a változók definiálva vannak.
// Mivel ezt a fájlt AJAX-szal töltjük be, a PHP változók értéke nem lesz elérhető.
// EZÉRT EZEKET A PHP CIKLUSOKAT EL KELL TÁVOLÍTANI, ÉS A DROPDOWN OPCIÓKAT
// JAVASCRIPTTEL KELL FELTÖLTENI, VAGY STATIKUSAN BELEÍRNI.
// A legegyszerűbb megoldás most, hogy a PHP ciklusokat eltávolítom,
// és feltételezem, hogy a dropdown-ok opciói statikusak, vagy JS-sel töltődnek.
// Ha a dropdown-ok dinamikusak, akkor azokat is AJAX-szal kell lekérdezni a main.js-ben.

// A biztonság kedvéért, ha mégis futna PHP-ként, definiáljuk a változókat üres tömbként
$courts = [];
$councils = [];
$rooms = [];
$persons = [];

?>
<h2 class="mb-4 text-center">Tárgyalási Jegyzék Rögzítése</h2>

<div class="card p-4">
    <h3 class="card-title mb-3 ">Tárgyalási Jegyzék fejléce  </h3>
    <!-- Üzenet megjelenítésére szolgáló div -->
    <div id="formMessage" class="alert d-none" role="alert"></div>

    <form id="jegyzekForm">
        <!-- Rejtett ID mező a szerkesztéshez -->
        <input type="hidden" name="id" id="recordId" value="">
        
        <div class="row g-2 mb-4">
            <div class="col-md-6">
                <label for="court_name" class="form-label">Bíróság</label>
                <select class="form-select form-select-sm" id="court_name" name="court_name" required>
                    <option value="">Válasszon...</option>
                    <!-- Ezeket az opciókat JS-sel kell feltölteni, vagy statikusan beírni -->
                    <!-- Példa statikus opciókra: -->
                    <!-- <option value="Fővárosi Törvényszék">Fővárosi Törvényszék</option> -->
                    <!-- <option value="Pesti Központi Kerületi Bíróság">Pesti Központi Kerületi Bíróság</option> -->
                </select>
            </div>
            
            <div class="col-md-6">
                <label for="council_name" class="form-label">Tanács</label>
                <select class="form-select form-select-sm" id="council_name" name="council_name" required>
                    <option value="">Válasszon...</option>
                    <!-- Ezeket az opciókat JS-sel kell feltölteni, vagy statikusan beírni -->
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
                    <!-- Ezeket az opciókat JS-sel kell feltölteni, vagy statikusan beírni -->
                </select>
            </div>
        </div>

        <hr class="my-4">

        <h3 class="card-title mb-3">Tárgyalási Jegyzék</h3>
        <div class="row g-2 align-items-center mb-3">
            <div class="col-md-2">
                <label for="sorszam_display" class="form-label">Sorszám</label>
                <input type="text" class="form-control form-control-sm" id="sorszam_display" value="1" disabled>
                <input type="hidden" name="sorszam" id="sorszam_hidden" value="1">
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
                    <!-- Ezeket az opciókat JS-sel kell feltölteni, vagy statikusan beírni -->
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

        <button type="submit" class="btn btn-success mt-3" id="jegyzekFormSubmitBtn">Rögzítés és Mentés</button>
        <button type="button" class="btn btn-secondary mt-3 ms-2" id="cancelEditBtn">Mégse</button>
    </form>
</div>
