<div class="modal-overlay" id="modal-add-member">
  <div class="modal">
    <div class="modal-header">
      <h3>Tambah Anggota Baru</h3>
      <button class="modal-close" onclick="closeModal('modal-add-member')">&times;</button>
    </div>
    <form method="POST" action="/admin/users/create_handler" target="member-create-result-frame">
      <?= csrf_field() ?>
      <div class="modal-body">
        <div class="field-group">
          <label>Nama Lengkap</label>
          <input type="text" name="full_name" id="new-member-name" placeholder="Nama anggota" required />
        </div>
        <div class="field-group">
          <label>Username</label>
          <input type="text" name="username" id="new-member-username" placeholder="Username untuk login" required />
        </div>
        <div class="field-group">
          <label>Password</label>
          <input type="password" name="password" id="new-member-password" placeholder="Minimal 6 karakter" required />
        </div>
        <div class="field-group">
          <label>Role</label>
          <select name="role" id="new-member-role">
            <option value="Member">Member</option>
            <option value="Admin">Admin</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modal-add-member')">Batal</button>
        <button type="submit" class="btn btn-blue"><i class="fa-solid fa-plus"></i> Tambah Anggota</button>
      </div>
    </form>
  </div>
</div>
<iframe name="member-create-result-frame" id="member-create-result-frame" title="Hasil tambah anggota" hidden></iframe>
