// assets/js/main.js

document.addEventListener('DOMContentLoaded', function () {
    const links = document.querySelectorAll('.load-page');
    const contentArea = document.getElementById('content-area'); // Ez a fő tartalomterület

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

    // Segédfüggvény a select elem kiválasztott opciójának beállítására
    function setSelectedOption(selectElement, valueToSelect) {
        if (!selectElement || !valueToSelect) return;
        for (let i = 0; i < selectElement.options.length; i++) {
            if (selectElement.options[i].value === valueToSelect) {
                selectElement.selectedIndex = i;
                return;
            }
        }
    }

    // Funkció a dropdown listák feltöltésére API-ból
    async function populateDropdowns() {
        const dropdownsToFetch = {
            'court_name': 'birosag',
            'council_name': 'tanacs',
            'room_number': 'room',
            'resztvevok': 'resztvevok'
        };

        for (const [selectId, category] of Object.entries(dropdownsToFetch)) {
            const selectElement = document.getElementById(selectId);
            if (!selectElement) continue;

            // Csak akkor töltjük fel, ha még nincsenek opciók (az "Válasszon..." kivételével)
            // Vagy ha az opciók száma csak 1, ami az alapértelmezett "Válasszon..."
            if (selectElement.options.length > 1) {
                // Ha már vannak opciók, töröljük őket, kivéve az elsőt
                while (selectElement.options.length > 1) {
                    selectElement.remove(1);
                }
            }

            try {
                const response = await fetch(`app/get_dropdown_items.php?category=${category}`);
                if (!response.ok) throw new Error(`Hiba az adatok lekérdezésekor a(z) ${category} kategóriához.`);
                const data = await response.json();

                if (data.success && Array.isArray(data.data)) {
                    data.data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item;
                        option.textContent = item;
                        selectElement.appendChild(option);
                    });
                } else {
                    console.error(`Hiba a(z) ${category} dropdown adatok betöltésekor:`, data.message || 'Ismeretlen hiba');
                }
            } catch (error) {
                console.error(`Hiba a(z) ${category} dropdown betöltésekor:`, error);
            }
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
                // Ha a rogzites.php-t töltöttük be (akár rögzítésre, akár szerkesztésre),
                // akkor hívjuk meg a loadEditData függvényt, ha van ID
                else if (page === 'rogzites.php') {
                    // Először feltöltjük a dropdown listákat
                    populateDropdowns().then(() => {
                        // Az adatfeltöltés csak akkor történik meg, ha van dataId.
                        // Ezt a then() blokkba tesszük, hogy a dropdownok már feltöltődjenek,
                        // mielőtt megpróbáljuk kiválasztani az értékeket.
                        if (dataId !== null) {
                            loadEditData(dataId);
                        } else {
                            // Ha új rögzítés, akkor is be kell állítani az eseményfigyelőt
                            // a jegyzekForm űrlapra
                            const jegyzekForm = document.getElementById('jegyzekForm');
                            if (jegyzekForm) {
                                jegyzekForm.addEventListener('submit', handleNewEntrySubmit); // Új függvény az új bejegyzés rögzítéséhez
                            }
                            // Reseteljük az űrlapot új rögzítés esetén
                            jegyzekForm.reset();
                            // Visszaállítjuk a címet és gombot eredeti állapotba
                            const formTitle = contentArea.querySelector('h2');
                            if (formTitle) {
                                formTitle.textContent = 'Tárgyalási Jegyzék Rögzítése';
                            }
                            const submitBtn = document.getElementById('jegyzekFormSubmitBtn');
                            if (submitBtn) {
                                submitBtn.innerHTML = 'Rögzítés és Mentés';
                                submitBtn.classList.remove('btn-primary');
                                submitBtn.classList.add('btn-success');
                            }
                            // Rejtett ID mező törlése
                            document.getElementById('recordId').value = '';
                        }
                    });
                }
            })
            .catch(error => {
                contentArea.innerHTML = `<div class="alert alert-danger">Hiba történt: ${error.message}</div>`;
            });
    }

    // Funkció a szerkesztő űrlap adatainak betöltésére és kezelésére
    // Ezt a függvényt hívja meg a kattintás eseményfigyelő
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
                    // Rejtett ID mező
                    document.getElementById('recordId').value = jegyzokonyv.id || '';

                    // Select mezők feltöltése (MIUTÁN a populateDropdowns lefutott)
                    setSelectedOption(document.getElementById('court_name'), jegyzokonyv.birosag);
                    setSelectedOption(document.getElementById('council_name'), jegyzokonyv.tanacs);
                    setSelectedOption(document.getElementById('room_number'), jegyzokonyv.rooms);
                    setSelectedOption(document.getElementById('resztvevok'), jegyzokonyv.resztvevok);

                    // Input/textarea mezők feltöltése
                    document.getElementById('date').value = jegyzokonyv.date || '';
                    document.getElementById('sorszam_display').value = jegyzokonyv.sorszam || '';
                    document.getElementById('sorszam_hidden').value = jegyzokonyv.sorszam || ''; // A rejtett sorszám is
                    document.getElementById('ido').value = jegyzokonyv.time ? jegyzokonyv.time.substring(0, 5) : ''; // Idő formázása hh:mm-re
                    document.getElementById('ugyszam').value = jegyzokonyv.ugyszam || '';
                    document.getElementById('letszam').value = jegyzokonyv.letszam || '';
                    document.getElementById('ugyminoseg').value = jegyzokonyv.ugyminoseg || '';
                    document.getElementById('intezkedes').value = jegyzokonyv.intezkedes || '';

                    // Módosítjuk az űrlap címét és a gomb szövegét
                    const formTitle = contentArea.querySelector('h2');
                    if (formTitle) {
                        formTitle.textContent = 'Tárgyalási Jegyzék Szerkesztése';
                    }
                    const submitBtn = document.getElementById('jegyzekFormSubmitBtn');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="fa-solid fa-save"></i> Módosítások mentése';
                        submitBtn.classList.remove('btn-success');
                        submitBtn.classList.add('btn-primary'); // Vagy tetszőleges szín
                    }

                    // Eseményfigyelő az űrlap beküldésére (szerkesztés)
                    const jegyzekForm = document.getElementById('jegyzekForm');
                    if (jegyzekForm) {
                        // Először eltávolítjuk a régi eseményfigyelőt, ha volt
                        jegyzekForm.removeEventListener('submit', handleNewEntrySubmit); 
                        jegyzekForm.addEventListener('submit', handleEditFormSubmit);
                    }

                    // Eseményfigyelő a "Mégse" gombra
                    const cancelBtn = document.getElementById('cancelEditBtn');
                    if (cancelBtn) {
                        cancelBtn.addEventListener('click', function() {
                            loadPage('list.php'); // Vissza a listanézetre
                        });
                    }

                } else {
                    showMessage('formMessage', data.message || 'Hiba az adatok betöltésekor.', 'danger');
                    loadPage('list.php'); // Hiba esetén visszatérünk a listanézetre
                }
            })
            .catch(error => {
                showMessage('formMessage', `Hiba az adatok lekérdezésekor: ${error.message}`, 'danger');
                loadPage('list.php'); // Hiba esetén visszatérünk a listanézetre
            });
    }

    // Funkció az ÚJ bejegyzés rögzítő űrlap AJAX-os beküldésének kezelésére
    function handleNewEntrySubmit(e) {
        e.preventDefault(); // Megakadályozzuk az alapértelmezett űrlap beküldést

        const form = e.target;
        const formData = new FormData(form);

        fetch('/app/process_entry.php', { // Feltételezve, hogy ez az API az új rögzítéshez
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Hiba a rögzítés során');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showMessage('formMessage', 'Sikeres rögzítés!', 'success'); 
                loadPage('list.php'); // Sikeres rögzítés után visszatérünk a listanézetre
            } else {
                showMessage('formMessage', data.message || 'Hiba történt a rögzítéskor.', 'danger');
            }
        })
        .catch(error => {
            showMessage('formMessage', `Hiba a rögzítés során: ${error.message}`, 'danger');
        });
    }


    // Funkció a szerkesztő űrlap AJAX-os beküldésének kezelésére
    function handleEditFormSubmit(e) {
        e.preventDefault(); // Megakadályozzuk az alapértelmezett űrlap beküldést

        const form = e.target;
        const formData = new FormData(form);

        fetch('app/edit_entry_api.php', { // Ez az API a szerkesztéshez
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Hiba a mentés során');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showMessage('formMessage', data.message, 'success');
                loadPage('list.php'); // Sikeres mentés után visszatérünk a listanézetre
            } else {
                showMessage('formMessage', data.message || 'Hiba történt a mentéskor.', 'danger');
            }
        })
        .catch(error => {
            showMessage('formMessage', `Hiba a mentés során: ${error.message}`, 'danger');
        });
    }

    // Automatikusan betöltjük az alapértelmezett oldalt (list.php) az oldalbetöltéskor
    loadPage('list.php');

    // Kattintásra betöltjük a linkhez tartozó oldalt
    links.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            
            // Ha a rogzites.php-t töltjük be, de nem szerkesztési módban,
            // akkor győződjünk meg róla, hogy az űrlap tiszta legyen
            if (page === 'rogzites.php') {
                loadPage(page, null); // Nincs ID, tehát új rögzítés
            } else {
                loadPage(page);
            }
        });
    });

    // Eseménydelegálás a contentArea-ra a dinamikusan betöltött elemekhez
    contentArea.addEventListener('click', function(e) {
        // Ellenőrizzük, hogy a kattintás egy "edit-button" osztályú elemen történt-e
        const closestButton = e.target.closest('.edit-button');
        if (closestButton) {
            e.preventDefault(); // Megakadályozzuk a link alapértelmezett viselkedését
            const id = closestButton.getAttribute('data-id');
            if (id) {
                loadPage('rogzites.php', id); // Meghívjuk a szerkesztő űrlap betöltését a rogzites.php-val
            } else {
                console.error('Hiányzó ID a szerkesztés gombhoz.');
            }
        }
    });
});
