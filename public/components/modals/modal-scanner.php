<div class="modal-overlay" id="modal-scanner">
  <div class="modal">
    <div class="modal-header">
      <h3>Scan QR Absensi Sub Event</h3>
      <button class="modal-close" onclick="closeModal('modal-scanner')">&times;</button>
    </div>
    <div class="modal-body" id="scanner-body">
      <div class="scanner-frame">
        <div class="scanner-line"></div>
        <i class="fa-solid fa-camera"></i>
        <p>Arahkan kamera ke QR Code Sub Event</p>
      </div>
      <p class="scanner-hint">
        Absensi akan otomatis tercatat untuk Sub Event yang sedang berlangsung
      </p>
    </div>
    <div class="modal-footer" id="scanner-footer">
      <button class="btn btn-outline" onclick="closeModal('modal-scanner')">Batal</button>
      <button class="btn btn-blue" onclick="simulateScanSuccess()">
        <i class="fa-solid fa-bolt"></i> SimulaGIATn QR Berhasil (+3 Point)
      </button>
    </div>
  </div>
</div>
