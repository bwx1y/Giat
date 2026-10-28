<div id="login-page" class="d-flex min-vh-100 w-100 align-items-center justify-content-center">
    <div class="login-card w-100">
      <div class="login-logo">
        <div class="logo-icon"><i class="fa-solid fa-qrcode"></i></div>
        <h1>GIAT</h1>
        <p>Multi-Event & Smart Attendance System</p>
      </div>

      <div class="form-group">
        <label>Email / Username</label>
        <input type="email" id="login-email" placeholder="email@organisasi.com" value="admin@GIAT.id" />
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" id="login-password" placeholder="••••••••" value="password" />
      </div>
      <div class="form-group">
        <label>Pilih Hak Akses / Role</label>
        <select id="login-role">
          <option value="superadmin">Super Admin (Kelola & Buat Event Utama)</option>
          <option value="admin">Admin / PIC (Kelola & Buat Sub Event)</option>
          <option value="member">Anggota (Scan QR & Riwayat Point)</option>
        </select>
      </div>
      <button class="btn-primary" onclick="doLogin()">
        <i class="fa-solid fa-arrow-right-to-bracket"></i>&nbsp; Masuk ke Sistem
      </button>
    </div>
  </div>
