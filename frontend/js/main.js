// CARS frontend interactions: mobile sidebar, flash dismissal, destructive-action guards.
document.addEventListener('DOMContentLoaded', function () {
  // --- Mobile sidebar toggle ---
  const sidebar = document.getElementById('sidebar');
  const toggle = document.getElementById('sidebar-toggle');
  const backdrop = document.getElementById('sidebar-backdrop');

  function openSidebar() {
    if (!sidebar) return;
    sidebar.classList.remove('-translate-x-full');
    if (backdrop) backdrop.classList.remove('hidden');
  }
  function closeSidebar() {
    if (!sidebar) return;
    sidebar.classList.add('-translate-x-full');
    if (backdrop) backdrop.classList.add('hidden');
  }
  if (toggle) toggle.addEventListener('click', openSidebar);
  if (backdrop) backdrop.addEventListener('click', closeSidebar);

  // --- Auto-dismiss flash messages ---
  document.querySelectorAll('.flash').forEach(function (el) {
    const closeBtn = el.querySelector('.flash-close');
    if (closeBtn) closeBtn.addEventListener('click', () => el.remove());
    setTimeout(() => el.remove(), 5000);
  });

  // --- Confirm guards for destructive actions ---
  // Add data-confirm="message" to any form or link.
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('submit', function (e) {
      if (!window.confirm(el.getAttribute('data-confirm'))) e.preventDefault();
    });
    if (el.tagName === 'A') {
      el.addEventListener('click', function (e) {
        if (!window.confirm(el.getAttribute('data-confirm'))) e.preventDefault();
      });
    }
  });
});
