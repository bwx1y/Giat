<div class="modal-overlay" id="modal-add-member">
  <div class="modal">
    <div class="modal-header">
      <h3>Tambah Anggota Baru</h3>
      <button class="modal-close" onclick="closeModal('modal-add-member')">&times;</button>
    </div>
    <div class="modal-body">
      <div class="field-group">
        <label>Nama Lengkap</label>
        <input id="new-name" type="text" placeholder="Nama anggota" />
      </div>
      <div class="field-row">
        <div class="field-group">
          <label>Email</label>
          <input id="new-email" type="email" placeholder="email@org.id" />
        </div>
        <div class="field-group">
          <label>NIM / ID Anggota</label>
          <input id="new-id" type="text" placeholder="A009" />
        </div>
      </div>
      <div class="field-group">
        <label>Divisi</label>
        <select id="new-div">
          <option>Divisi Teknologi</option>
          <option>Divisi Kreatif</option>
          <option>Divisi Humas</option>
          <option>Divisi Keuangan</option>
          <option>Divisi Akademik</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('modal-add-member')">Batal</button>
      <button class="btn btn-blue" onclick="addMember()"><i class="fa-solid fa-plus"></i> Tambah Anggota</button>
    </div>
  </div>
</div>
