function renderMembers(list) {
  const tbody = document.getElementById('member-tbody');
  if (!tbody) return;
  tbody.innerHTML = '';
  list.forEach((m, i) => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${m.id}</td>
      <td><strong>${m.name}</strong></td>
      <td>${m.div}</td>
      <td>${m.hadir} sesi</td>
      <td>${m.absen} sesi</td>
      <td><strong style="color:var(--blue-600);font-size:14px;">${m.point} pt</strong></td>
      <td><span class="badge ${m.status === 'Aktif' ? 'badge-green' : 'badge-gray'}">${m.status}</span></td>
      <td>
        <div class="actions-cell">
          <button class="action-link" onclick="showToast('Edit anggota: ${m.name}','info')">Edit</button>
          <button class="action-link danger" onclick="deleteMemberRow(${i})">Hapus</button>
        </div>
      </td>
    `;
    tbody.appendChild(row);
  });
}

function filterMembers() {
  const searchInput = document.getElementById('member-search');
  const divSelect = document.getElementById('member-filter-div');
  const q = searchInput ? searchInput.value.toLowerCase() : '';
  const div = divSelect ? divSelect.value : '';

  const members = getMembers();
  const filtered = members.filter(m =>
    (m.name.toLowerCase().includes(q) || m.id.toLowerCase().includes(q)) &&
    (div === '' || m.div === div)
  );
  renderMembers(filtered);
}

function addMember() {
  const nameInput = document.getElementById('new-name');
  const divSelect = document.getElementById('new-div');
  const emailInput = document.getElementById('new-email');
  const idInput = document.getElementById('new-id');

  const name = nameInput ? nameInput.value.trim() : '';
  if (!name) {
    showToast('Nama anggota tidak boleh kosong', 'error');
    return;
  }
  const div = divSelect ? divSelect.value : 'Divisi Teknologi';
  const members = getMembers();
  const id = (idInput && idInput.value.trim()) ? idInput.value.trim() : 'A' + String(members.length + 1).padStart(3, '0');

  members.push({ id, name, div, hadir: 0, absen: 0, point: 0, status: 'Aktif' });
  saveMembers(members);
  renderMembers(members);
  closeModal('modal-add-member');

  if (nameInput) nameInput.value = '';
  if (emailInput) emailInput.value = '';
  if (idInput) idInput.value = '';
  showToast('Anggota ' + name + ' berhasil ditambahkan!', 'success');
}

function deleteMemberRow(idx) {
  if (!confirm('Hapus anggota ini dari daftar?')) return;
  const members = getMembers();
  members.splice(idx, 1);
  saveMembers(members);
  renderMembers(members);
  showToast('Anggota berhasil dihapus', 'info');
}
