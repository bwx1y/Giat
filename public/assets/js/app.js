// ============================================================
// app.js — Entry point: bootstrap sesi dan event listeners
// ============================================================

if (typeof window !== 'undefined' && window.currentUser) {
  document.addEventListener('DOMContentLoaded', function () {
    const r = (window.currentUser.role || 'Member').toLowerCase();
    const roleKey = (r === 'admin') ? 'superadmin' : (window.isEventAdmin ? 'admin' : 'member');
    currentRole = roleKey;

    if (userInfo[roleKey]) {
      userInfo[roleKey].name = window.currentUser.full_name || window.currentUser.username;
      userInfo[roleKey].role = (roleKey === 'admin') ? 'Admin / PIC' : window.currentUser.role;
      const initials = (window.currentUser.full_name || window.currentUser.username || 'US')
        .split(' ').filter(Boolean).map(w => w[0]).join('').substring(0, 2).toUpperCase();
      userInfo[roleKey].initials = initials;
    }

    applyRoleUI(roleKey);
    buildSidebar(roleKey);
    setUserInfo(roleKey);

    const urlParams = new URLSearchParams(window.location.search);
    const initialPage = pageFromLocation();

    if (initialPage === 'event-detail' && window.routeParams && window.routeParams.event_id) {
      currentActiveEventId = Number(window.routeParams.event_id);
    }
    if (initialPage === 'subevent-detail' && window.routeParams && window.routeParams.subevent_id) {
      currentActiveSubEventId = Number(window.routeParams.subevent_id);
    }

    navTo(initialPage, 'replace');

    const hasEventCrudFlash = showEventCrudFlash();
    if (!hasEventCrudFlash && urlParams.get('created_id')) {
      showToast('Event Utama berhasil dibuat!', 'success');
    }

    // Tutup modal saat klik overlay
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', function (e) {
        if (e.target === this) closeModal(this.id);
      });
    });

    // Event create form (iframe submission)
    const createEventForm = document.querySelector('#modal-add-event form');
    const createEventFrame = document.getElementById('event-create-result-frame');
    if (createEventForm && createEventFrame) {
      createEventFrame.addEventListener('load', function () {
        if (createEventForm.dataset.submitted === 'true') {
          createEventForm.dataset.submitted = 'false';
          closeModal('modal-add-event');
          showToast('Event berhasil dibuat.', 'success');
          setTimeout(() => window.location.reload(), 2000);
        }
      });
      createEventForm.addEventListener('submit', function () {
        createEventForm.dataset.submitted = 'true';
      });
    }

    // Event edit form (iframe submission)
    const editEventForm = document.getElementById('form-edit-event');
    const editEventFrame = document.getElementById('event-edit-result-frame');
    if (editEventForm && editEventFrame) {
      editEventFrame.addEventListener('load', function () {
        if (editEventForm.dataset.submitted === 'true') {
          editEventForm.dataset.submitted = 'false';
          closeModal('modal-edit-event');
          showToast('Event berhasil diperbarui.', 'success');
          setTimeout(() => window.location.reload(), 2000);
        }
      });
      editEventForm.addEventListener('submit', function () {
        editEventForm.dataset.submitted = 'true';
      });
    }

    // Member create form
    const createMemberForm = document.querySelector('#modal-add-member form');
    if (createMemberForm) {
      createMemberForm.addEventListener('submit', function (e) {
        e.preventDefault();
        submitMemberForm(createMemberForm, 'modal-add-member');
      });
    }

    // Member edit form
    const editMemberForm = document.querySelector('#modal-edit-member form');
    if (editMemberForm) {
      editMemberForm.addEventListener('submit', function (e) {
        e.preventDefault();
        submitMemberForm(editMemberForm, 'modal-edit-member');
      });
    }
  });
}
