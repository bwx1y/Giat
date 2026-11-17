// ============================================================
// pages/anggota.js — Daftar anggota, CRUD member
// ============================================================

function renderMembers() {
  const tbody = document.getElementById('member-tbody');
  tbody.innerHTML = '';

  const list = Array.isArray(window.dbMembers) ? window.dbMembers : members;

  if (list.length === 0) {
    tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:30px;color:var(--slate-400);">Tidak ada anggota yang ditemukan.</td></tr>';
    return;
  }

  list.forEach(m => {
    const isActive = m.active === true || m.active === 't' || m.active === 1 || m.active === 'true' || m.status === 'Aktif';
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${m.id}</td>
      <td><strong>${m.full_name || m.name}</strong></td>
      <td>${m.username || m.id}</td>
      <td><span class="badge ${m.role === 'Admin' ? 'badge-blue' : 'badge-gray'}">${m.role || 'Member'}</span></td>
      <td>${m.hadir || 0} sesi</td>
      <td>${m.absen || 0} sesi</td>
      <td><strong style="color:var(--blue-600);font-size:14px;">${m.point || 0} pt</strong></td>
      <td><span class="badge ${isActive ? 'badge-green' : 'badge-gray'}">${isActive ? 'Aktif' : 'Non-Aktif'}</span></td>
      <td>
        <div class="actions-cell">
          <button class="action-link" onclick="editMemberPrompt(${m.id})">Edit</button>
          <button class="action-link danger" onclick="deleteMemberRow(${m.id})">Hapus</button>
        </div>
      </td>
    `;
    tbody.appendChild(row);
  });
}

function editMemberPrompt(id) {
  const list = Array.isArray(window.dbMembers) ? window.dbMembers : members;
  const m = list.find(item => String(item.id) === String(id));
  if (!m) { showToast('Data anggota tidak ditemukan.', 'error'); return; }

  document.getElementById('edit-member-id').value = m.id;
  document.getElementById('edit-member-name').value = m.full_name || m.name || '';
  document.getElementById('edit-member-username').value = m.username || m.id || '';
  document.getElementById('edit-member-role').value = m.role || 'Member';
  document.getElementById('edit-member-password').value = '';
  openModal('modal-edit-member');
}

function deleteMemberRow(id) {
  const list = Array.isArray(window.dbMembers) ? window.dbMembers : members;
  const member = list.find(item => String(item.id) === String(id));
  if (!member) { showToast('Data anggota tidak ditemukan.', 'error'); return; }

  pendingDeleteMemberId = id;
  document.getElementById('delete-member-name').textContent = member.full_name || member.name || `#${id}`;
  openModal('modal-delete-member');
}

async function confirmDeleteMember() {
  if (pendingDeleteMemberId === null) return;

  const confirmButton = document.getElementById('confirm-delete-member');
  confirmButton.disabled = true;

  try {
    const formData = new FormData();
    formData.append('id', pendingDeleteMemberId);
    const response = await fetch('/admin/users/delete_handler', { method: 'POST', body: formData });
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    const resultUrl = new URL(response.url);
    if (!resultUrl.searchParams.has('success')) {
      throw new Error('Server tidak mengonfirmasi penghapusan anggota.');
    }
    closeModal('modal-delete-member');
    pendingDeleteMemberId = null;
    showToast('Anggota berhasil dihapus.', 'success');
    setTimeout(() => window.location.reload(), 2000);
  } catch (error) {
    showToast('Gagal menghapus anggota: ' + error.message, 'error');
  } finally {
    confirmButton.disabled = false;
  }
}

async function submitMemberForm(form, modalId) {
  const submitButton = form.querySelector('button[type="submit"]');
  submitButton.disabled = true;

  try {
    const response = await fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      credentials: 'same-origin',
    });
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    const resultUrl = new URL(response.url);
    const successMessage = resultUrl.searchParams.get('success');
    if (!successMessage) throw new Error('Data tidak tersimpan. Periksa kembali isian dan coba lagi.');
    closeModal(modalId);
    showToast(successMessage, 'success');
    setTimeout(() => window.location.reload(), 1200);
  } catch (error) {
    showToast(`Gagal menyimpan anggota: ${error.message}`, 'error');
  } finally {
    submitButton.disabled = false;
  }
}
