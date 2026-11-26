// ============================================================
// pages/user.js — Dashboard & riwayat anggota (member)
// ============================================================

let attendanceCameraStream = null;
let attendanceQrDetector = null;
let attendanceScanCanvas = null;
let attendanceScanCtx = null;
let attendanceScanFrame = null;
let attendanceScanBusy = false;
let attendanceScannerStarting = false;
let attendanceScannerGeneration = 0;
let lastScannedQrValue = null;

function renderUserDashboard() {
  const container = document.getElementById('user-events-progress-container');
  if (!container) return;
  container.innerHTML = '';

  const progressRows = Array.isArray(window.dbUserDashboardProgress)
    ? window.dbUserDashboardProgress
    : [];
  if (progressRows.length === 0) {
    container.innerHTML = '<div class="card"><div class="card-body">Belum ada progres event.</div></div>';
    return;
  }

  const events = new Map();
  progressRows.forEach(row => {
    const eventId = String(row.event_id);
    if (!events.has(eventId)) {
      events.set(eventId, {
        name: row.event_title,
        startDate: row.event_start_date,
        endDate: row.event_end_date,
        subEvents: [],
      });
    }
    events.get(eventId).subEvents.push(row);
  });

  events.forEach(ev => {
    let completedCount = 0;
    let eventPoints = 0;
    const rowsHtml = ev.subEvents.map(sub => {
      const status = sub.status_kehadiran || 'Upcoming';
      const point = Number(sub.point_obtained) || 0;
      const statusCls = status === 'Hadir'
        ? 'status-hadir'
        : status === 'Tidak Hadir'
          ? 'status-absen'
          : 'status-upcoming';
      const statusIcon = status === 'Hadir'
        ? '<i class="fa-solid fa-check"></i>'
        : status === 'Tidak Hadir'
          ? '<i class="fa-solid fa-xmark"></i>'
          : '<i class="fa-regular fa-circle"></i>';
      const pointText = status === 'Upcoming'
        ? '<span style="color:var(--slate-400)">Upcoming</span>'
        : `<strong class="${point > 0 ? 'point-positive' : 'point-negative'}">${point > 0 ? '+' : ''}${point} Point</strong>`;
      const date = formatUserDashboardDate(sub.meeting_start_date);
      const startTime = formatUserDashboardTime(sub.meeting_start_date);
      const endTime = formatUserDashboardTime(sub.meeting_end_date);

      if (status !== 'Upcoming') completedCount++;
      eventPoints += point;

      return `
        <div class="user-subevent-row ${statusCls}">
          <div style="display:flex;align-items:center;">
            <span class="subevent-status-icon">${statusIcon}</span>
            <div>
              <strong>${escapeUserDashboardText(sub.meeting_title || '')}</strong>
              <span style="font-size:12px;color:var(--slate-400);margin-left:8px;">· ${escapeUserDashboardText(date)}, ${escapeUserDashboardText(startTime)}${endTime ? ` - ${escapeUserDashboardText(endTime)}` : ''}</span>
            </div>
          </div>
          <div>${pointText}</div>
        </div>
      `;
    }).join('');
    const dateRange = ev.startDate && ev.endDate
      ? `${formatUserDashboardDate(ev.startDate)} s/d ${formatUserDashboardDate(ev.endDate)}`
      : 'Belum diatur';
    const block = document.createElement('div');
    block.className = 'user-event-block';
    block.innerHTML = `
      <div class="user-event-head">
        <div>
          <div class="user-event-title">${escapeUserDashboardText(ev.name || '')}</div>
          <div class="user-event-dates"><i class="fa-regular fa-calendar"></i> ${escapeUserDashboardText(dateRange)}</div>
        </div>
        <div style="text-align:right;">
          <div style="font-size:12px;color:var(--slate-400);">Progress Selesai</div>
          <div style="font-weight:700;font-size:14px;color:var(--navy);">${completedCount} / ${ev.subEvents.length} Sub Event Selesai</div>
          <div style="font-size:12px;margin-top:2px;">
            Total Point Event: <strong style="color:var(--blue-600);font-size:13px;">${eventPoints >= 0 ? '+' + eventPoints : eventPoints} Point</strong>
          </div>
        </div>
      </div>
      <div style="font-size:12.5px;font-weight:600;color:var(--navy-600);text-transform:uppercase;letter-spacing:.04em;">Daftar Sub Event:</div>
      <div class="user-subevent-list">${rowsHtml}</div>
    `;
    container.appendChild(block);
  });
}

function escapeUserDashboardText(value) {
  const element = document.createElement('span');
  element.textContent = String(value);
  return element.innerHTML;
}

function formatUserDashboardDate(value) {
  const date = String(value || '').slice(0, 10);
  return date ? formatDateId(date) : '-';
}

function formatUserDashboardTime(value) {
  return String(value || '').slice(11, 16);
}

function hasSupportedQrDecoder() {
  return typeof BarcodeDetector === 'function' || typeof jsQR === 'function';
}

function createAttendanceQrDetector() {
  if (typeof BarcodeDetector !== 'function') return null;
  try {
    return new BarcodeDetector({ formats: ['qr_code'] });
  } catch {
    return null;
  }
}

async function requestAttendanceCameraStream() {
  try {
    return await navigator.mediaDevices.getUserMedia({
      audio: false,
      video: { facingMode: { ideal: 'environment' } },
    });
  } catch (error) {
    if (error?.name === 'OverconstrainedError') {
      return navigator.mediaDevices.getUserMedia({ audio: false, video: true });
    }
    throw error;
  }
}

async function detectAttendanceQrValue(video) {
  if (!video || video.readyState < 2 || video.videoWidth <= 0 || video.videoHeight <= 0) {
    return null;
  }

  if (attendanceQrDetector) {
    try {
      const detections = await attendanceQrDetector.detect(video);
      return detections[0]?.rawValue || null;
    } catch (error) {
      if (typeof jsQR !== 'function') throw error;
      // Fallback ke jsQR jika BarcodeDetector gagal pada runtime browser
      attendanceQrDetector = null;
    }
  }

  if (typeof jsQR !== 'function') return null;

  const maxDimension = 720;
  const scale = Math.min(1, maxDimension / Math.max(video.videoWidth, video.videoHeight));
  const width = Math.max(1, Math.round(video.videoWidth * scale));
  const height = Math.max(1, Math.round(video.videoHeight * scale));

  if (!attendanceScanCanvas) {
    attendanceScanCanvas = document.createElement('canvas');
    attendanceScanCtx = attendanceScanCanvas.getContext('2d', { willReadFrequently: true });
  }
  if (!attendanceScanCtx) return null;

  if (attendanceScanCanvas.width !== width || attendanceScanCanvas.height !== height) {
    attendanceScanCanvas.width = width;
    attendanceScanCanvas.height = height;
  }

  attendanceScanCtx.drawImage(video, 0, 0, width, height);
  const imageData = attendanceScanCtx.getImageData(0, 0, width, height);
  const decoded = jsQR(imageData.data, width, height, { inversionAttempts: 'attemptBoth' });
  return decoded?.data || null;
}

async function openAttendanceQrScanner() {
  openModal('modal-scanner');
  if (attendanceCameraStream || attendanceScannerStarting) return;

  const status = document.getElementById('qr-scanner-status');
  const video = document.getElementById('qr-scanner-video');
  const frame = video?.closest('.scanner-frame');
  if (!status || !video || !frame) return;

  if (!navigator.mediaDevices?.getUserMedia) {
    status.textContent = 'Browser tidak mendukung akses kamera. Buka aplikasi melalui HTTPS atau localhost.';
    return;
  }
  if (!hasSupportedQrDecoder()) {
    status.textContent = 'Decoder QR belum tersedia. Muat ulang halaman atau gunakan browser terbaru.';
    return;
  }

  const generation = ++attendanceScannerGeneration;
  attendanceScannerStarting = true;
  lastScannedQrValue = null;
  status.textContent = 'Meminta izin kamera...';

  try {
    attendanceQrDetector = createAttendanceQrDetector();
    const stream = await requestAttendanceCameraStream();

    if (
      generation !== attendanceScannerGeneration
      || !document.getElementById('modal-scanner')?.classList.contains('open')
    ) {
      stream.getTracks().forEach(track => track.stop());
      return;
    }

    attendanceCameraStream = stream;
    video.srcObject = stream;
    frame.classList.add('has-camera');
    await video.play();
    status.textContent = 'Kamera aktif. Arahkan ke QR absensi.';
    scanAttendanceQrFrame();
  } catch (error) {
    if (generation === attendanceScannerGeneration) {
      stopAttendanceQrScanner();
      status.textContent = `Kamera tidak dapat dibuka: ${error.message}`;
      showToast('Tidak dapat membuka kamera untuk scan QR.', 'error');
    }
  } finally {
    if (generation === attendanceScannerGeneration) {
      attendanceScannerStarting = false;
    }
  }
}

async function scanAttendanceQrFrame() {
  if (!attendanceCameraStream || attendanceScanBusy) return;
  attendanceScanBusy = true;

  try {
    const video = document.getElementById('qr-scanner-video');
    const rawValue = await detectAttendanceQrValue(video);
    if (rawValue && rawValue !== lastScannedQrValue) {
      lastScannedQrValue = rawValue;
      await submitAttendanceQr(rawValue);
    }
  } catch (error) {
    const status = document.getElementById('qr-scanner-status');
    if (status) status.textContent = `Gagal membaca QR: ${error.message}`;
  } finally {
    attendanceScanBusy = false;
    if (attendanceCameraStream) {
      attendanceScanFrame = requestAnimationFrame(scanAttendanceQrFrame);
    }
  }
}

async function submitAttendanceQr(rawValue) {
  const status = document.getElementById('qr-scanner-status');
  let payload;

  try {
    payload = JSON.parse(rawValue);
  } catch {
    if (status) status.textContent = 'QR tidak dikenali. Arahkan kamera ke QR absensi GIAT.';
    return;
  }

  const eventId = Number(payload.event_id);
  const meetingId = Number(payload.meeting_id);
  if (
    payload.type !== 'giat-attendance'
    || !Number.isInteger(eventId)
    || eventId <= 0
    || !Number.isInteger(meetingId)
    || meetingId <= 0
    || typeof payload.code !== 'string'
    || !/^[a-f0-9]{32}$/i.test(payload.code)
  ) {
    if (status) status.textContent = 'QR tidak dikenali. Arahkan kamera ke QR absensi GIAT.';
    return;
  }

  if (status) status.textContent = 'Memverifikasi QR dan mencatat presensi...';

  try {
    const response = await fetch(
      `/member/event/${eventId}/meeting/${meetingId}/qr`,
      {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ code: payload.code }),
      }
    );
    const result = await response.json().catch(() => null);

    if (!result) {
      throw new Error(`Respons server tidak valid (HTTP ${response.status}).`);
    }
    if (!response.ok || result.status !== 'success') {
      throw new Error(result.message || 'Presensi gagal dicatat.');
    }

    stopAttendanceQrScanner();
    closeModal('modal-scanner');
    if (status) status.textContent = 'Presensi berhasil tercatat.';
    showToast('Presensi QR berhasil dicatat.', 'success');
    setTimeout(() => window.location.reload(), 1200);
  } catch (error) {
    if (status) status.textContent = error.message || 'Presensi gagal dicatat.';
    showToast(error.message || 'Presensi gagal dicatat.', 'error');
  }
}

function stopAttendanceQrScanner() {
  attendanceScannerGeneration++;
  attendanceScannerStarting = false;
  if (attendanceScanFrame !== null) {
    cancelAnimationFrame(attendanceScanFrame);
    attendanceScanFrame = null;
  }
  if (attendanceCameraStream) {
    attendanceCameraStream.getTracks().forEach(track => track.stop());
    attendanceCameraStream = null;
  }
  const video = document.getElementById('qr-scanner-video');
  video?.pause();
  if (video) video.srcObject = null;
  video?.closest('.scanner-frame')?.classList.remove('has-camera');
  attendanceQrDetector = null;
  attendanceScanCanvas = null;
  attendanceScanCtx = null;
  attendanceScanBusy = false;
}

function renderUserHistory() {
  const tbody = document.getElementById('user-history-tbody');
  tbody.innerHTML = '';

  const userItems = Array.isArray(window.dbUserHistory) ? window.dbUserHistory : [];
  if (userItems.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:var(--slate-400);">Belum ada riwayat kehadiran.</td></tr>';
    return;
  }

  userItems.forEach(item => {
    const isHadir = item.status === 'Present';
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><strong>${item.main_event}</strong></td>
      <td><span class="subevent-tree-badge"><i class="fa-solid fa-sitemap"></i> ${item.sub_event}</span></td>
      <td>${item.date}</td>
      <td><span class="badge ${isHadir ? 'badge-green' : 'badge-red'}">${isHadir ? 'Hadir' : 'Tidak Hadir'}</span></td>
      <td>${item.method !== '—' ? `<span class="badge badge-blue">${item.method}</span>` : '—'}</td>
      <td><strong class="${isHadir ? 'point-positive' : 'point-negative'}">${item.point}</strong></td>
    `;
    tbody.appendChild(tr);
  });
}
