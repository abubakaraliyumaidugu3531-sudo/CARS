<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_admin();

require_once '../../backend/models/UserModel.php';

$userModel = new UserModel();
$users = $userModel->listAll();

$roleBadge = [
  'student' => 'bg-brand-100 text-brand-700',
  'advisor' => 'bg-emerald-100 text-emerald-700',
  'admin'   => 'bg-amber-100 text-amber-700',
];

$pageTitle = 'Users';
include '../partials/shell_open.php';
?>
<div class="mb-6">
  <h2 class="text-2xl font-bold text-slate-900">Users</h2>
  <p class="text-slate-500 text-sm">All accounts. Create advisor or admin staff accounts here (students self-register).</p>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
  <div class="card p-6 xl:col-span-1 h-fit">
    <h3 class="font-semibold text-slate-900 mb-4">Create Staff Account</h3>
    <form method="POST" action="/backend/controllers/Authcontroller.php" class="space-y-3">
      <input type="hidden" name="create_staff" value="1">
      <div><label class="label">Name</label><input name="name" class="input" required></div>
      <div><label class="label">Email</label><input name="email" type="email" class="input" required></div>
      <div><label class="label">Password</label><input name="password" type="password" class="input" required minlength="6" placeholder="At least 6 characters"></div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Role</label>
          <select name="role" class="input">
            <option value="advisor">Advisor</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div><label class="label">Department</label><input name="department" class="input" placeholder="Computer Science"></div>
      </div>
      <button type="submit" class="btn-primary">Create account</button>
    </form>
  </div>

  <div class="card p-0 overflow-hidden xl:col-span-2">
    <div class="p-4 border-b border-slate-100"><h3 class="font-semibold text-slate-900">All Users</h3></div>
    <div class="overflow-x-auto">
      <table class="table">
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Department</th><th>Level</th></tr></thead>
        <tbody>
          <?php if ($users->num_rows === 0): ?>
            <tr><td colspan="5" class="text-center text-slate-400 py-10">No users.</td></tr>
          <?php else: while ($u = $users->fetch_assoc()): ?>
            <tr>
              <td class="font-medium text-slate-900"><?php echo htmlspecialchars($u['name']); ?></td>
              <td class="text-slate-500"><?php echo htmlspecialchars($u['email']); ?></td>
              <td><span class="badge <?php echo $roleBadge[$u['role']] ?? 'bg-slate-100 text-slate-700'; ?> capitalize"><?php echo htmlspecialchars($u['role']); ?></span></td>
              <td><?php echo htmlspecialchars($u['department'] ?? '—'); ?></td>
              <td><?php echo htmlspecialchars($u['level'] ?? '—'); ?></td>
            </tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include '../partials/shell_close.php'; ?>
