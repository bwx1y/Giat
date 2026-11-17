<div class="modal-overlay" id="modal-scanner">
  <div class="modal">
    <div class="modal-header">
      <h3>Scan QR Absensi Sub Event</h3>
      <button class="modal-close" onclick="closeModal('modal-scanner')">&times;</button>
    </div>
    <div class="modal-body" id="scanner-body">
      <div class="scanner-frame">
        <video id="qr-scanner-video" autoplay playsinline muted></video>
        <div class="scanner-line"></div>
        <i class="fa-solid fa-camera" id="qr-scanner-camera-icon"></i>
        <p id="qr-scanner-instruction">Arahkan kamera ke QR Code Sub Event</p>
      </div>
      <p class="scanner-hint" id="qr-scanner-status" role="status">
        Meminta akses kamera...
      </p>
    </div>
    <div class="modal-footer" id="scanner-footer">
      <button class="btn btn-outline" onclick="closeModal('modal-scanner')">Batal</button>
    </div>
  </div>
</div>
