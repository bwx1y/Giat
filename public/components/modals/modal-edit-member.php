<div class="modal-overlay" id="modal-edit-member">
  <div class="modal">
    <div class="modal-header">
      <h3>Edit Anggota</h3>
      <button class="modal-close" onclick="closeModal('modal-edit-member')">&times;</button>
    </div>
    <form method="POST" action="/admin/users/update_handler" target="member-edit-result-frame">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="edit-member-id" />
      <div class="modal-body">
        <div class="field-group">
          <label>Nama Lengkap</label>
          <input type="text" name="full_name" id="edit-member-name" required />
        </div>
        <div class="field-group">
          <label>Username</label>
          <input type="text" id="edit-member-username" disabled />
        </div>
        <div class="field-group">
          <label>Role</label>
          <select name="role" id="edit-member-role">
            <option value="Member">Member</option>
            <option value="Admin">Admin</option>
          </select>
        </div>
        <div class="field-group">
          <label>Password Baru (opsional)</label>
          <input type="password" name="password" id="edit-member-password" placeholder="Kosongkan jika tidak ingin ganti" />
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modal-edit-member')">Batal</button>
        <button type="submit" class="btn btn-blue">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>
<iframe name="member-edit-result-frame" id="member-edit-result-frame" title="Hasil edit anggota" hidden></iframe>
