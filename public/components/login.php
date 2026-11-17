<div id="login-page" class="d-flex min-vh-100 w-100 align-items-center justify-content-center">
  <div class="login-card w-100">
    <div class="login-logo">
      <div class="logo-icon"><i class="fa-solid fa-qrcode"></i></div>
      <h1>GIAT</h1>
      <p>Multi-Event & Smart Attendance System</p>
    </div>

    <?php if (has_error('csrf_token')): ?>
      <div class="alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= htmlspecialchars(error('csrf_token')) ?></span>
      </div>
    <?php endif; ?>

    <?php if (has_error('username')): ?>
      <div class="alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= htmlspecialchars(error('username')) ?></span>
      </div>
    <?php endif; ?>

    <?php if (has_error('password')): ?>
      <div class="alert-danger">
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
        <div class="password-input-wrap">
          <input type="password" name="password" id="login-password" placeholder="••••••••" required />
          <button type="button" class="btn-toggle-password" id="btn-toggle-password" onclick="togglePasswordVisibility()" title="Tampilkan / sembunyikan password" aria-label="Tampilkan atau sembunyikan password">
            <i class="fa-regular fa-eye" id="toggle-password-icon"></i>
          </button>
        </div>
      </div>
      <button type="submit" class="btn-primary login-submit-btn">
        <i class="fa-solid fa-arrow-right-to-bracket"></i>&nbsp; Masuk ke Sistem
      </button>
    </form>

  </div>
</div>
<script>
  function togglePasswordVisibility() {
    const pwdInput = document.getElementById('login-password');
    const icon = document.getElementById('toggle-password-icon');
    if (!pwdInput) return;

    if (pwdInput.type === 'password') {
      pwdInput.type = 'text';
      if (icon) {
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      }
    } else {
      pwdInput.type = 'password';
      if (icon) {
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }
  }
</script>
