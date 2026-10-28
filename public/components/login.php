<div id="login-page" class="d-flex min-vh-100 w-100 align-items-center justify-content-center">
  <div class="login-card w-100">
    <div class="login-logo">
      <div class="logo-icon"><i class="fa-solid fa-qrcode"></i></div>
      <h1>GIAT</h1>
      <p>Multi-Event & Smart Attendance System</p>
    </div>

    <?php if (has_error('csrf_token')): ?>
      <div style="background:#fee2e2; border:1px solid #f87171; color:#991b1b; padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:14px; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= htmlspecialchars(error('csrf_token')) ?></span>
      </div>
    <?php endif; ?>

    <?php if (has_error('username')): ?>
      <div style="background:#fee2e2; border:1px solid #f87171; color:#991b1b; padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:14px; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= htmlspecialchars(error('username')) ?></span>
      </div>
    <?php endif; ?>

    <?php if (has_error('password')): ?>
      <div style="background:#fee2e2; border:1px solid #f87171; color:#991b1b; padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:14px; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= htmlspecialchars(error('password')) ?></span>
      </div>
    <?php endif; ?>

    <form action="/login_handler" method="POST">
      <?= csrf_field() ?>
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" id="login-email" placeholder="Masukkan username anda" value="<?= htmlspecialchars(old('username', '')) ?>" required autofocus />
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" id="login-password" placeholder="••••••••" required />
      </div>
      <button type="submit" class="btn-primary" style="width:100%; margin-top:8px;">
        <i class="fa-solid fa-arrow-right-to-bracket"></i>&nbsp; Masuk ke Sistem
      </button>
    </form>

    <div style="margin-top:20px; padding-top:16px; border-top:1px solid var(--slate-100); font-size:12px; color:var(--slate-400); text-align:center;">
      Akun Demo: <strong>ahmad_fauzi</strong> (Admin) &bull; <strong>budi_santoso</strong> (Member)<br>Password: <strong>password</strong>
    </div>
  </div>
</div>
