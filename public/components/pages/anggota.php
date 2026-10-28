<div id="page-anggota" class="page">
        <div class="card">
          <div class="toolbar">
            <div class="search-wrap">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" id="member-search" placeholder="Cari nama atau NIM anggota…"
                oninput="filterMembers()" />
            </div>
            <select class="filter-select" id="member-filter-div" onchange="filterMembers()">
              <option value="">Semua Divisi</option>
              <option>Divisi Teknologi</option>
              <option>Divisi Kreatif</option>
              <option>Divisi Humas</option>
              <option>Divisi Keuangan</option>
              <option>Divisi Akademik</option>
            </select>
            <button class="btn btn-blue" onclick="openModal('modal-add-member')">
              <i class="fa-solid fa-user-plus"></i> Tambah Anggota
            </button>
          </div>
          <div class="table-wrap">
            <table id="member-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nama Anggota</th>
                  <th>Divisi</th>
                  <th>Sub Event Hadir</th>
                  <th>Sub Event Absen</th>
                  <th>Total Point</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="member-tbody"></tbody>
            </table>
          </div>
        </div>
      </div>
