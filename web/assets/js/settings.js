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
      loadList(type);
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
      loadList(type);
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
        btn.textContent = '−';
        btn.onclick = () => deleteItem(item.id, type);
        li.appendChild(btn);
        list.appendChild(li);
      });
    });
}

document.addEventListener('DOMContentLoaded', () => {
  ['birosag', 'tanacs', 'room'].forEach(type => loadList(type));
});
