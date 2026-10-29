<?php
$userFullName = $currentUser['full_name'] ?? $currentUser['username'] ?? 'User';
$userRoleName = $currentUser['role'] ?? 'Member';
$userInitials = '';
$nameParts = explode(' ', trim($userFullName));
foreach ($nameParts as $part) {
    if (!empty($part)) $userInitials .= strtoupper($part[0]);
    if (strlen($userInitials) >= 2) break;
}
if (empty($userInitials)) $userInitials = 'US';
?>
<aside id="sidebar" class="d-flex flex-column flex-shrink-0">
  <div class="sidebar-brand">
    <div class="brand-icon"><i class="fa-solid fa-qrcode"></i></div>
    <div>
      <div class="brand-name">GIAT</div>
      <div class="brand-sub">Smart Multi-Event</div>
    </div>
  </div>
  <div class="sidebar-user">
    <div class="avatar" id="sb-avatar"><?= htmlspecialchars($userInitials) ?></div>
    <div class="user-info">
      <div class="user-name" id="sb-name"><?= htmlspecialchars($userFullName) ?></div>
      <div class="user-role" id="sb-role"><?= htmlspecialchars($userRoleName) ?></div>
    </div>
  </div>

  <nav class="sidebar-nav" id="sidebar-nav">

  </nav>

  <div class="sidebar-bottom">
    <div class="nav-item" onclick="window.location.href='/logout'">
      <i class="fa-solid fa-right-from-bracket"></i>
      <span>Keluar</span>
    </div>
  </div>
</aside>
