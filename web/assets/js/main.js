// assets/js/main.js

document.addEventListener('DOMContentLoaded', function () {
    const links = document.querySelectorAll('.load-page');
    const contentArea = document.getElementById('content-area');

    // Segédfüggvény az üzenetek megjelenítéséhez
    function showMessage(elementId, message, type) {
        const messageDiv = document.getElementById(elementId);
        if (messageDiv) {
            messageDiv.textContent = message;
            messageDiv.className = `alert alert-${type}`;
            messageDiv.classList.remove('d-none');
            setTimeout(() => {
                messageDiv.classList.add('d-none');
            }, 5000); // Üzenet elrejtése 5 másodperc után
        }
    }

    // Funkció az oldalbetöltés kezelésére
    function loadPage(page, dataId = null) {
        fetch(page)
            .then(response => {
                if (!response.ok) throw new Error('Hiba a betöltés során');
                return response.text();
            })
            .then(html => {
                contentArea.innerHTML = html;

                // Ha a list.php-t töltöttük be, akkor inicializáljuk a keresőt
                if (page === 'list.php' && typeof initSearch === 'function') {
                    initSearch();
                } 
                // Ha settings.php-t töltünk be, akkor hívjuk meg a reloadAllLists() függvényt
                else if (page === 'settings.php' && typeof reloadAllLists === 'function') {
                    reloadAllLists();
                }
                // Ha az edit_entry_form.php-t töltöttük be, akkor hívjuk meg a loadEditData függvényt
                else if (page === 'edit_entry_form.php' && dataId !== null) {
                    loadEditData(dataId);
                }
            })
            .catch(error => {
                contentArea.innerHTML = `<div class="alert alert-danger">Hiba történt: ${error.message}</div>`;
            });
    }

    // Funkció a szerkesztő űrlap adatainak betöltésére és kezelésére
    // Ezt a függvényt hívja meg a list.php-ban lévő "Szerkesztés" gomb
    window.loadEditForm = function(id) {
        // Először betöltjük az űrlap HTML struktúráját
        loadPage('edit_entry_form.php', id);
        // A loadPage függvény hívja majd a loadEditData-t, miután az űrlap betöltődött
    };

    // Funkció a szerkesztendő adatok lekérdezésére és az űrlap feltöltésére
    function loadEditData(id) {
        fetch(`app/edit_entry_api.php?id=${id}`)
            .then(response => {
                if (!response.ok) throw new Error('Hiba az adatok lekérdezése során');
                return response.json();
            })
            .then(data => {
                if (data.success && data.data) {
                    const jegyzokonyv = data.data;
                    // Feltöltjük az űrlap mezőit az adatokkal
                    document.getElementById('editEntryId').value = jegyzokonyv.id || '';
                    document.getElementById('editBirosag').value = jegyzokonyv.birosag || '';
                    document.getElementById('editTanacs').value = jegyzokonyv.tanacs || '';
                    document.getElementById('editDate').value = jegyzokonyv.date || '';
                    document.getElementById('editRooms').value = jegyzokonyv.rooms || '';
                    document.getElementById('editSorszam').value = jegyzokonyv.sorszam || '';
                    document.getElementById('editTime').value = jegyzokonyv.time || '';
                    document.getElementById('editUgyszam').value = jegyzokonyv.ugyszam || '';
                    document.getElementById('editPersons').value = jegyzokonyv.resztvevok || '';
                    document.getElementById('editLetszam').value = jegyzokonyv.letszam || '';
                    document.getElementById('editUgyminoseg').value = jegyzokonyv.ugyminoseg || '';
                    document.getElementById('editIntezkedes').value = jegyzokonyv.intezkedes || '';

                    // Eseményfigyelő az űrlap beküldésére
                    const editForm = document.getElementById('editEntryForm');
                    if (editForm) {
                        editForm.addEventListener('submit', handleEditFormSubmit);
                    }

                    // Eseményfigyelő a "Mégse" gombra
                    const cancelBtn = document.getElementById('cancelEditBtn');
                    if (cancelBtn) {
                        cancelBtn.addEventListener('click', function() {
                            loadPage('list.php'); // Vissza a listanézetre
                        });
                    }

                } else {
                    showMessage('editMessage', data.message || 'Hiba az adatok betöltésekor.', 'danger');
                    loadPage('list.php'); // Hiba esetén visszatérünk a listanézetre
                }
            })
            .catch(error => {
                showMessage('editMessage', `Hiba az adatok lekérdezésekor: ${error.message}`, 'danger');
                loadPage('list.php'); // Hiba esetén visszatérünk a listanézetre
            });
    }

    // Funkció a szerkesztő űrlap AJAX-os beküldésének kezelésére
    function handleEditFormSubmit(e) {
        e.preventDefault(); // Megakadályozzuk az alapértelmezett űrlap beküldést

        const form = e.target;
        const formData = new FormData(form);

        fetch('app/edit_entry_api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Hiba a mentés során');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showMessage('editMessage', data.message, 'success');
                loadPage('list.php'); // Sikeres mentés után visszatérünk a listanézetre
            } else {
                showMessage('editMessage', data.message || 'Hiba történt a mentéskor.', 'danger');
            }
        })
        .catch(error => {
            showMessage('editMessage', `Hiba a mentés során: ${error.message}`, 'danger');
        });
    }

    // Automatikusan betöltjük az alapértelmezett oldalt (list.php) az oldalbetöltéskor
    loadPage('list.php');

    // Kattintásra betöltjük a linkhez tartozó oldalt
    links.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            loadPage(page); // Használjuk a közös loadPage függvényt
        });
    });

    // Ez a rész a form beküldésére vonatkozik, maradhat változatlanul a dashboard.php-ban,
    // de ha a rogzites.php űrlapját is AJAX-szal akarod kezelni, akkor ezt is ide lehetne hozni.
    // Jelenleg feltételezzük, hogy ez a dashboard.php belső scriptjében marad.
});
