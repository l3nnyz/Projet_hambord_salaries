document.addEventListener('DOMContentLoaded', () => {
  console.log('Main.js loaded');

  const stopEvent = (e) => {
    if (e) {
      if (e.preventDefault) e.preventDefault();
      if (e.stopPropagation) e.stopPropagation();
    }
  };

  try {
    const params = new URLSearchParams(window.location.search);
    if (params.get('updated') === '1') {
      const toastEl = document.getElementById('updateSuccessToast');
      if (toastEl && window.bootstrap && typeof window.bootstrap.Toast === 'function') {
        const toast = new window.bootstrap.Toast(toastEl);
        toast.show();
      }
    }
  } catch (e) { console.error('Toast error:', e); }

  const startEdit = (td) => {
    if (td.querySelector('input')) return;
    const id = td.dataset.id;
    const field = td.dataset.field;
    const type = td.dataset.type || 'text';
    const original = td.textContent.trim();

    const input = document.createElement('input');
    input.className = 'form-control form-control-sm';
    input.value = original;
    input.type = type === 'number' ? 'number' : 'text';
    if (type === 'number') { input.step = '0.01'; input.min = '0'; }
    if (type !== 'number') { input.placeholder = 'YYYY-MM-DD ou JJ/MM/AAAA'; }

    input.addEventListener('keydown', (ev) => {
      if (ev.key === 'Escape') { td.textContent = original; }
      if (ev.key === 'Enter') { input.blur(); }
    });

    input.addEventListener('blur', async () => {
      const newVal = input.value.trim();
      if (newVal === original) { td.textContent = original; return; }
      
      const ok = window.confirm(`Confirmer la modification de "${field}" ?`);
      if (!ok) { td.textContent = original; return; }

      const form = new URLSearchParams();
      form.append('id', id);
      form.append('field', field);
      form.append('value', newVal);

      try {
        const res = await fetch('updateSalaryField.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: form.toString()
        });
        const data = await res.json();
        if (!data.success) {
          alert(data.message || 'Erreur lors de la mise à jour');
          td.textContent = original;
          return;
        }
        
        const saved = data.value ?? newVal;
        td.innerHTML = '';
        const span = document.createElement('span');
        span.textContent = saved;
        td.appendChild(span);

        // Show success toast with dynamic message
        const toastEl = document.getElementById('updateSuccessToast');
        const toastBody = toastEl.querySelector('.toast-body');
        const row = td.closest('tr');
        const nom = row.querySelector('[data-field="nom"]').textContent;
        const prenom = row.querySelector('[data-field="prenom"]').textContent;
        toastBody.textContent = `Salarié ${nom} ${prenom} mis à jour avec succès.`;
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
      } catch (e) {
        alert('Erreur réseau');
        td.textContent = original;
      }
    });

    td.textContent = '';
    td.appendChild(input);
    input.focus();
  };

  const tbody = document.getElementById('salariesBody');
  if (tbody) {
    const userDroit = tbody.dataset.userDroit;
    tbody.addEventListener('dblclick', (e) => {
      if (userDroit !== '2') return;
      const td = e.target.closest('td.editable');
      if (td) { stopEvent(e); startEdit(td); }
    });
  }

  let currentSort = 'id';
  let currentOrder = 'ASC';
  let currentSearch = '';
  let searchTimeout = null;

  const loadSalaries = async () => {
    const fields = Array.from(document.querySelectorAll('.search-field:checked')).map(cb => cb.value).join(',');
    const url = `getSalaries.php?sort=${currentSort}&order=${currentOrder}&search=${encodeURIComponent(currentSearch)}&fields=${fields}`;
    
    try {
      const res = await fetch(url);
      const result = await res.json();
      if (result.success) {
        renderTable(result.data);
      } else { console.error('Fetch error:', result.message); }
    } catch (e) { console.error('Network error:', e); }
  };

  const renderTable = (data) => {
    if (!tbody) return;
    const userDroit = tbody.dataset.userDroit;
    
    const countBadge = document.getElementById('totalCountBadge');
    if (countBadge) {
      countBadge.textContent = `Total : ${data.length}`;
    }

    tbody.innerHTML = data.map(s => {
      let actionHtml = '';
      if (userDroit === '2') {
        actionHtml = `
          <a href="deleteSalaries.php?id=${s.id}" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer ${s.nom} ${s.prenom} ?');">
            <i class="bi bi-trash"></i> Supprimer
          </a>`;
      } else {
        actionHtml = `<span class="text-muted small">Action restreinte</span>`;
      }

      return `
        <tr>
          <td>${s.id}</td>
          <td class="editable" data-id="${s.id}" data-field="nom" data-type="text">${s.nom}</td>
          <td class="editable" data-id="${s.id}" data-field="prenom" data-type="text">${s.prenom}</td>
          <td class="editable" data-id="${s.id}" data-field="date_naissance" data-type="text">${s.date_naissance}</td>
          <td class="editable" data-id="${s.id}" data-field="date_embauche" data-type="text">${s.date_embauche}</td>
          <td class="editable" data-id="${s.id}" data-field="salaire" data-type="number">${s.salaire}</td>
          <td class="editable" data-id="${s.id}" data-field="service" data-type="text">${s.service}</td>
          <td>${actionHtml}</td>
        </tr>`;
    }).join('');
  };

  const updateHeaderIcons = () => {
    document.querySelectorAll('.sortable').forEach(th => {
      const col = th.dataset.sort;
      const icon = th.querySelector('i');
      const opacity = (col === currentSort) ? 'opacity-100' : 'opacity-50';
      let iconClass = 'bi-arrow-down-up';
      if (col === currentSort) { iconClass = (currentOrder === 'ASC') ? 'bi-sort-up' : 'bi-sort-down'; }
      icon.className = `bi ${iconClass} ms-1 ${opacity}`;
      th.dataset.order = (col === currentSort && currentOrder === 'ASC') ? 'DESC' : 'ASC';
    });
  };

  const tableHeader = document.getElementById('tableHeader');
  if (tableHeader) {
    tableHeader.addEventListener('click', (e) => {
      const th = e.target.closest('.sortable');
      if (th) {
        stopEvent(e);
        currentSort = th.dataset.sort;
        currentOrder = th.dataset.order;
        updateHeaderIcons();
        loadSalaries();
      }
    });
  }

  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      currentSearch = e.target.value;
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => { loadSalaries(); }, 300);
    });
    searchInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') stopEvent(e); });
  }

  document.querySelectorAll('.search-field').forEach(cb => {
    cb.addEventListener('change', (e) => {
      stopEvent(e);
      loadSalaries();
    });
    cb.addEventListener('click', (e) => { e.stopPropagation(); });
  });

  document.querySelectorAll('.dropdown-menu').forEach(menu => {
    menu.addEventListener('click', (e) => { e.stopPropagation(); });
  });

});