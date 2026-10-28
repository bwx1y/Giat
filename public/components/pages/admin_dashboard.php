<div id="page-admin-dashboard" class="page">
        <div
          style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom: 22px;">
          <div>
            <h1 style="font-size:22px;font-weight:800;color:var(--navy);">Event & Sub Event yang Saya Kelola</h1>
            <p style="font-size:13.5px;color:var(--slate-400);margin-top:3px;">
              Anda ditugaskan sebagai PIC untuk Sub Event di bawah ini. Anda dapat membuat sub event baru, membuka sesi,
              mencetak QR Code, dan mengelola absensi.
            </p>
          </div>
          <button class="btn btn-blue" onclick="openAddSubEventModal()">
            <i class="fa-solid fa-plus"></i> Tambah Sub Event
          </button>
        </div>

        <div id="admin-managed-container">

        </div>
      </div>
