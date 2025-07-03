function addItem(type) {
  const input = document.getElementById(type + 'Input');
  const name = input.value.trim();
  if (!name) return;

  fetch('/app/addItem.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ name, type })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      input.value = '';
      reloadAllLists();
    }
  });
}

function deleteItem(id, type) {
  fetch('/app/removeItem.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, type })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      reloadAllLists();
    }
  });
}

function loadList(type) {
  fetch(`/app/getItems.php?type=${type}`)
    .then(res => res.json())
    .then(data => {
      const list = document.getElementById(type + 'List');
      list.innerHTML = '';
      data.forEach(item => {
        const li = document.createElement('li');
        li.textContent = item.value + ' ';
        const btn = document.createElement('button');
        btn.classList.add('btn', 'btn-sm', 'btn-danger'); // opcionális bootstrap gomb stílus
        btn.title = 'Törlés'; // tooltip        
        const icon = document.createElement('i'); // ikon létrehozása
        icon.classList.add('fa', 'fa-trash'); // vagy 'fas', 'fa-trash' a verziódtól függően
        btn.appendChild(icon);
        btn.onclick = () => deleteItem(item.id, type);
        li.appendChild(btn);
        list.appendChild(li);
      });
    });
}

function reloadAllLists() {
  ['birosag', 'tanacs', 'room', 'resztvevok'].forEach(type => loadList(type));
};

console.log('Script betöltődött');

//document.addEventListener('DOMContentLoaded', () => {
//  console.log('DOM kész, reloadAllLists hívás');
//  reloadAllLists();
//});