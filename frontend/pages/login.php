<?php
require_once '../../backend/helpers/session.php';
if (is_logged_in()) {
    $role = $_SESSION['role'] ?? 'student';
    $dest = $role === 'admin' ? 'admin_dashboard.php' : ($role === 'advisor' ? 'advisor_dashboard.php' : 'student_dashboard.php');
    header('Location: /cars/frontend/pages/' . $dest);
    exit();
}
$msg = $_GET['msg'] ?? '';
$err = $_GET['err'] ?? '';
$pageTitle = 'Login';
include '../partials/head.php';
?>
<div class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-brand-50 via-white to-violet-50">
  <div class="w-full max-w-md">
    <a href="/cars/index.php" class="flex items-center justify-center gap-2 mb-6">
      <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-brand-600 text-white font-bold text-lg">C</span>
      <span class="font-bold text-xl tracking-tight text-slate-900">CARS</span>
    </a>
    <div class="card p-8">
      <h1 class="text-2xl font-bold text-slate-900 mb-1">Welcome back</h1>
      <p class="text-slate-500 text-sm mb-6">Log in to your account.</p>

      <?php if ($msg): ?><div class="mb-4 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 px-4 py-3 text-sm"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
      <?php if ($err): ?><div class="mb-4 rounded-lg bg-rose-50 text-rose-800 border border-rose-200 px-4 py-3 text-sm"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>

      <form method="POST" action="/cars/backend/controllers/Authcontroller.php" class="space-y-4">
        <div><label class="label" for="email">Email</label><input id="email" name="email" type="email" required class="input" autofocus></div>
        <div><label class="label" for="password">Password</label><input id="password" name="password" type="password" required class="input"></div>
        <button type="submit" name="login" class="btn-primary w-full py-2.5">Log in</button>
      </form>
      <p class="mt-6 text-center text-sm text-slate-500">Don't have an account? <a href="/cars/frontend/pages/signup.php" class="text-brand-600 font-medium hover:underline">Sign up</a></p>
    </div>
  </div>
</div>
<?php include '../partials/foot.php'; ?>
