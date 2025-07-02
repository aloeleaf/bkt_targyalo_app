document.addEventListener('DOMContentLoaded', function () {
    const links = document.querySelectorAll('.load-page');
    const contentArea = document.getElementById('content-area');

    // Automatikusan betöltjük az alapértelmezett oldalt (list.php)
    fetch('list.php')
        .then(response => {
            if (!response.ok) throw new Error('Hiba a betöltés során');
            return response.text();
        })
        .then(html => {
            contentArea.innerHTML = html;
        })
        .catch(error => {
            contentArea.innerHTML = `<div class="alert alert-danger">Hiba történt: ${error.message}</div>`;
        });

    // Kattintásra továbbra is betöltjük a linkhez tartozó oldalt
    links.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');

            fetch(page)
                .then(response => {
                    if (!response.ok) throw new Error('Hiba a betöltés során');
                    return response.text();
                })
                .then(html => {
                    contentArea.innerHTML = html;
                })
                .catch(error => {
                    contentArea.innerHTML = `<div class="alert alert-danger">Hiba történt: ${error.message}</div>`;
                });
        });
    });
});
