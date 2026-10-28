<?php
$topRole = $currentUser['role'] ?? 'Member';
$topInitials = $userInitials ?? 'US';
?>
<header id="topbar">
  <button class="topbar-toggle" onclick="toggleSidebar()">
    <i class="fa-solid fa-bars"></i>
  </button>
  <div class="topbar-title" id="topbar-title">Dashboard</div>
  <div class="topbar-actions">
    <div class="role-pill" id="topbar-role-badge"><?= htmlspecialchars($topRole) ?></div>
    <div class="topbar-btn" onclick="showToast('Tidak ada notifikasi baru', 'info')">
      <i class="fa-solid fa-bell"></i>
      <span class="notif-dot"></span>
    </div>
    <div class="topbar-avatar" id="top-avatar"><?= htmlspecialchars($topInitials) ?></div>
  </div>
</header>
