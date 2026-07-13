<?php
// Top bar: page title, user identity, role badge, logout.
// Expects $pageTitle; reads identity from session.
$role = $_SESSION['role'] ?? 'student';
$name = $_SESSION['name'] ?? ucfirst($role);
$initials = strtoupper(substr(trim($name), 0, 1) ?: 'U');
$roleBadge = [
  'student' => 'bg-brand-100 text-brand-700',
  'advisor' => 'bg-emerald-100 text-emerald-700',
  'admin'   => 'bg-amber-100 text-amber-700',
][$role] ?? 'bg-slate-100 text-slate-700';
?>
<header class="topbar sticky top-0 z-10 flex items-center justify-between gap-4 h-16 px-4 lg:px-8 bg-white/90 backdrop-blur border-b border-slate-200">
  <div class="flex items-center gap-3 min-w-0">
    <button id="sidebar-toggle" class="md:hidden btn-ghost p-2 -ml-2" aria-label="Toggle navigation">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
    </button>
    <h1 class="text-lg font-semibold text-slate-900 truncate"><?php echo htmlspecialchars($pageTitle ?? 'Dashboard'); ?></h1>
  </div>
  <div class="flex items-center gap-3">
    <div class="hidden sm:flex flex-col items-end leading-tight">
      <span class="text-sm font-medium text-slate-800"><?php echo htmlspecialchars($name); ?></span>
      <span class="badge <?php echo $roleBadge; ?> capitalize"><?php echo htmlspecialchars($role); ?></span>
    </div>
    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-brand-600 text-white text-sm font-semibold"><?php echo htmlspecialchars($initials); ?></span>
    <a href="/cars/backend/controllers/logout.php" class="btn-secondary btn-sm">Logout</a>
  </div>
</header>
