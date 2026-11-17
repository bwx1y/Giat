<div class="modal-overlay" id="modal-qr">
  <div class="modal">
    <div class="modal-header">
      <h3>QR Code Absensi Sub Event</h3>
      <button class="modal-close" onclick="closeModal('modal-qr')">&times;</button>
    </div>
    <div class="modal-body">
      <div class="qr-container">
        <div id="qr-canvas"></div>
        <div>
          <h2 id="qr-modal-sub-name" class="qr-modal-title">Rapat Persiapan 1</h2>
          <div class="qr-modal-parent">
            Parent Event: <strong id="qr-modal-parent-name" class="text-blue">Webinar Nasional 2026</strong>
          </div>
          <div class="qr-status-row">
            <span class="live-dot"></span>
            <span class="qr-status-text">Absensi Aktif</span>
          </div>
          <div class="qr-timer-box qr-timer-margin">
            Expired: <span id="qr-expire-time">21:00</span> &nbsp;|&nbsp; Sisa Waktu: <span id="qr-countdown">29:50</span>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-danger" onclick="closeSessionFromModal()">
        <i class="fa-solid fa-lock"></i> Tutup Sesi Absensi
      </button>
      <button class="btn btn-outline" onclick="closeModal('modal-qr')">Selesai</button>
    </div>
  </div>
</div>
