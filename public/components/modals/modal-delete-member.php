<div class="modal-overlay" id="modal-delete-member">
  <div class="modal">
    <div class="modal-header">
      <h3>Hapus Anggota</h3>
      <button type="button" class="modal-close" onclick="closeModal('modal-delete-member')">&times;</button>
    </div>
    <div class="modal-body">
      <p>Yakin ingin menghapus <strong id="delete-member-name"></strong> dari daftar anggota?</p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-outline" onclick="closeModal('modal-delete-member')">Batal</button>
      <button type="button" class="btn btn-danger" id="confirm-delete-member" onclick="confirmDeleteMember()">Ya, Hapus</button>
    </div>
  </div>
</div>
