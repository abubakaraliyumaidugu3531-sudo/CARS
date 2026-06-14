<?php
require_once '../../backend/helpers/session.php';
if (is_logged_in()) {
    header('Location: /index.php');
    exit();
}
$err = $_GET['err'] ?? '';
$levels = ['100', '200', '300', '400', '500'];
$pageTitle = 'Sign Up';
include '../partials/head.php';
?>
<div class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-brand-50 via-white to-violet-50">
  <div class="w-full max-w-md">
    <a href="/index.php" class="flex items-center justify-center gap-2 mb-6">
      <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-brand-600 text-white font-bold text-lg">C</span>
      <span class="font-bold text-xl tracking-tight text-slate-900">CARS</span>
    </a>
    <div class="card p-8">
      <h1 class="text-2xl font-bold text-slate-900 mb-1">Create your student account</h1>
      <p class="text-slate-500 text-sm mb-6">Advisor and admin accounts are provisioned by an administrator.</p>

      <?php if ($err): ?><div class="mb-4 rounded-lg bg-rose-50 text-rose-800 border border-rose-200 px-4 py-3 text-sm"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>

      <form method="POST" action="/backend/controllers/Authcontroller.php" class="space-y-4">
        <div><label class="label" for="name">Full name</label><input id="name" name="name" type="text" required class="input"></div>
        <div><label class="label" for="email">Email</label><input id="email" name="email" type="email" required class="input"></div>
        <div class="grid grid-cols-2 gap-3">
          <div><label class="label" for="password">Password</label><input id="password" name="password" type="password" required minlength="6" class="input"></div>
          <div><label class="label" for="confirm_password">Confirm</label><input id="confirm_password" name="confirm_password" type="password" required minlength="6" class="input"></div>
        </div>
        <div><label class="label" for="department">Department</label><input id="department" name="department" type="text" value="Computer Science" class="input"></div>
        <div><label class="label" for="level">Current level</label>
          <select id="level" name="level" class="input">
            <?php foreach ($levels as $lv): ?><option value="<?php echo $lv; ?>"><?php echo $lv; ?> Level</option><?php endforeach; ?>
          </select>
        </div>
        <button type="submit" name="signup" class="btn-primary w-full py-2.5">Create account</button>
      </form>
      <p class="mt-6 text-center text-sm text-slate-500">Already have an account? <a href="/frontend/pages/login.php" class="text-brand-600 font-medium hover:underline">Log in</a></p>
    </div>
  </div>
</div>
<?php include '../partials/foot.php'; ?>
