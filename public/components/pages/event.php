<div id="page-event" class="page">
        <div class="card">
          <div class="card-header">
            <div>
              <span class="card-title">Daftar Event Utama</span>
              <div class="card-subtitle">Setiap Event Utama memayungi beberapa Sub Event aktivitas</div>
            </div>

            <button class="btn btn-blue" id="btn-create-event-main" onclick="openModal('modal-add-event')">
              <i class="fa-solid fa-plus"></i> Buat Event
            </button>
          </div>
          <div class="toolbar">
            <div class="search-wrap" style="max-width:280px">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" id="search-event" placeholder="Cari Event Utama…" oninput="filterEvents()" />
            </div>
            <select class="filter-select" id="filter-event-status" onchange="filterEvents()">
              <option value="">Semua Status</option>
              <option value="Active">Active</option>
              <option value="Upcoming">Upcoming</option>
              <option value="Finished">Finished</option>
            </select>
          </div>
          <div class="event-primary-grid" id="event-grid">

          </div>
        </div>
      </div>
