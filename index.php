<?php
require_once __DIR__ . '/backend/helpers/session.php';
// Send authenticated users straight to their dashboard.
if (is_logged_in()) {
    $role = $_SESSION['role'] ?? 'student';
    $dest = $role === 'admin' ? 'admin_dashboard.php' : ($role === 'advisor' ? 'advisor_dashboard.php' : 'student_dashboard.php');
    header('Location: /frontend/pages/' . $dest);
    exit;
}
$pageTitle = 'Welcome';
include __DIR__ . '/frontend/partials/head.php';
?>
<div class="min-h-screen flex flex-col">
  <header class="flex items-center justify-between px-6 lg:px-10 h-16 border-b border-slate-200 bg-white/80 backdrop-blur">
    <div class="flex items-center gap-2">
      <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 text-white font-bold">C</span>
      <span class="font-bold tracking-tight text-slate-900">CARS</span>
    </div>
    <div class="flex items-center gap-2">
      <a href="/frontend/pages/login.php" class="btn-secondary btn-sm">Login</a>
      <a href="/frontend/pages/signup.php" class="btn-primary btn-sm">Sign up</a>
    </div>
  </header>

  <main class="flex-1">
    <section class="px-6 lg:px-10 py-16 lg:py-24 bg-gradient-to-br from-brand-50 via-white to-violet-50">
      <div class="max-w-3xl mx-auto text-center">
        <span class="badge bg-brand-100 text-brand-700 mb-4">Course Advisory &amp; Recommendation System</span>
        <h1 class="text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900">Pick the right courses, with confidence.</h1>
        <p class="mt-4 text-lg text-slate-600">CARS analyses your academic record, prerequisites and programme level to recommend the courses you should take next — and connects you with your academic advisor for approval.</p>
        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
          <a href="/frontend/pages/signup.php" class="btn-primary px-6 py-3">Create a student account</a>
          <a href="/frontend/pages/login.php" class="btn-secondary px-6 py-3">I already have an account</a>
        </div>
      </div>
    </section>

    <section class="px-6 lg:px-10 py-14">
      <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card p-6">
          <div class="inline-flex h-11 w-11 items-center justify-center rounded-lg bg-brand-50 text-brand-600 mb-3"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342"/></svg></div>
          <h3 class="font-semibold text-slate-900">Students</h3>
          <p class="text-sm text-slate-500 mt-1">See personalised recommendations, register courses and track your GPA.</p>
        </div>
        <div class="card p-6">
          <div class="inline-flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 mb-3"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg></div>
          <h3 class="font-semibold text-slate-900">Advisors</h3>
          <p class="text-sm text-slate-500 mt-1">Review student plans against their transcript and approve with feedback.</p>
        </div>
        <div class="card p-6">
          <div class="inline-flex h-11 w-11 items-center justify-center rounded-lg bg-amber-50 text-amber-600 mb-3"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.43.992a6.759 6.759 0 010 .255c-.008.378.137.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
          <h3 class="font-semibold text-slate-900">Administrators</h3>
          <p class="text-sm text-slate-500 mt-1">Manage courses, prerequisites, grades and accounts.</p>
        </div>
      </div>
    </section>
  </main>

  <footer class="px-6 lg:px-10 py-6 text-center text-xs text-slate-400 border-t border-slate-200">&copy; <?php echo date('Y'); ?> Course Advisory System</footer>
</div>
<?php include __DIR__ . '/frontend/partials/foot.php'; ?>
