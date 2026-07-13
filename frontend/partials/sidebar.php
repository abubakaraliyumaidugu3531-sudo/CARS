<?php
// Role-aware sidebar navigation. Highlights the active page.
// Relies on $_SESSION['role'] (set at login).
$role = $_SESSION['role'] ?? 'student';
$current = basename($_SERVER['PHP_SELF']);

// Simple inline icon set (Heroicons-style paths).
$icons = [
  'home'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.5 1.5 0 012.122 0L22.5 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/>',
  'book'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>',
  'sparkles'=> '<path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>',
  'clipboard'=> '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>',
  'check'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
  'chart'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>',
  'user'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>',
  'users'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>',
  'link'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>',
  'pencil'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>',
];

$menus = [
  'student' => [
    ['Dashboard',      'student_dashboard.php', 'home'],
    ['Courses',        'courses.php',           'book'],
    ['Recommendations','recommendations.php',   'sparkles'],
    ['Academic Record','history.php',           'clipboard'],
    ['My Plan',        'plan.php',              'check'],
    ['Reports',        'report.php',            'chart'],
    ['Profile',        'profile.php',           'user'],
  ],
  'advisor' => [
    ['Dashboard', 'advisor_dashboard.php', 'home'],
    ['Courses',   'courses.php',           'book'],
    ['Reports',   'report.php',            'chart'],
  ],
  'admin' => [
    ['Dashboard',      'admin_dashboard.php',     'home'],
    ['Manage Courses', 'admin_courses.php',       'book'],
    ['Prerequisites',  'admin_prerequisites.php', 'link'],
    ['Enter Grades',   'admin_records.php',       'pencil'],
    ['Users',          'admin_users.php',         'users'],
    ['Reports',        'report.php',              'chart'],
  ],
];
$menu = $menus[$role] ?? $menus['student'];
?>
<aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 -translate-x-full md:translate-x-0 md:static md:flex md:flex-col bg-slate-900 text-white transition-transform duration-200">
  <div class="flex items-center gap-2 px-5 h-16 border-b border-white/10">
    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 font-bold">C</span>
    <span class="font-bold tracking-tight">CARS</span>
  </div>
  <nav class="flex-1 overflow-y-auto p-3 space-y-1">
    <?php foreach ($menu as [$label, $file, $icon]): ?>
      <a href="/cars/frontend/pages/<?php echo $file; ?>"
         class="nav-link <?php echo $current === $file ? 'nav-link-active' : ''; ?>">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><?php echo $icons[$icon]; ?></svg>
        <span><?php echo $label; ?></span>
      </a>
    <?php endforeach; ?>
  </nav>
  <div class="p-3 border-t border-white/10 text-xs text-slate-400">
    &copy; <?php echo date('Y'); ?> CARS
  </div>
</aside>
<div id="sidebar-backdrop" class="fixed inset-0 z-20 bg-slate-900/50 hidden md:hidden"></div>
