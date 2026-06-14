<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_admin();

require_once '../../backend/models/UserModel.php';
require_once '../../backend/models/CourseModel.php';
require_once '../../backend/models/EvaluationModel.php';

$userModel = new UserModel();
$courseModel = new CourseModel();
$evaluationModel = new EvaluationModel();

$studentCount = $userModel->countByRole('student');
$advisorCount = $userModel->countByRole('advisor');
$adminCount   = $userModel->countByRole('admin');
$courses = $courseModel->getAll();
$courseCount = $courses->num_rows;
$recStats = $evaluationModel->getRecommendationStats();

$users = $userModel->listAll();

$pageTitle = 'Admin Dashboard';
include '../partials/shell_open.php';
?>
<div class="mb-6">
  <h2 class="text-2xl font-bold text-slate-900">System Overview</h2>
  <p class="text-slate-500 text-sm">Manage users, courses, prerequisites and academic records.</p>
</div>

<div class="grid grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
  <div class="stat"><div><div class="stat-label">Students</div><div class="stat-value"><?php echo $studentCount; ?></div></div>
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg></span></div>
  <div class="stat"><div><div class="stat-label">Advisors</div><div class="stat-value"><?php echo $advisorCount; ?></div></div>
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg></span></div>
  <div class="stat"><div><div class="stat-label">Courses</div><div class="stat-value"><?php echo $courseCount; ?></div></div>
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-violet-50 text-violet-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg></span></div>
  <div class="stat"><div><div class="stat-label">Rec. Acceptance</div><div class="stat-value"><?php echo $recStats['acceptance_rate']; ?>%</div>
    <div class="text-xs text-slate-400 mt-1"><?php echo (int) $recStats['total']; ?> generated</div></div>
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg></span></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-8">
  <a href="/frontend/pages/admin_courses.php" class="card p-5 hover:border-brand-300 transition"><div class="font-semibold text-slate-900">Manage Courses</div><p class="text-sm text-slate-500 mt-1">Add, edit and remove courses.</p></a>
  <a href="/frontend/pages/admin_records.php" class="card p-5 hover:border-brand-300 transition"><div class="font-semibold text-slate-900">Enter Grades</div><p class="text-sm text-slate-500 mt-1">Record student academic results.</p></a>
  <a href="/frontend/pages/admin_users.php" class="card p-5 hover:border-brand-300 transition"><div class="font-semibold text-slate-900">Manage Users</div><p class="text-sm text-slate-500 mt-1">View users and create staff accounts.</p></a>
</div>

<div class="card p-0 overflow-hidden">
  <div class="p-4 border-b border-slate-100"><h3 class="font-semibold text-slate-900">Recent Users</h3></div>
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Department</th></tr></thead>
      <tbody>
        <?php $i = 0; while ($i < 8 && ($u = $users->fetch_assoc())): $i++; ?>
          <tr>
            <td class="font-medium text-slate-900"><?php echo htmlspecialchars($u['name']); ?></td>
            <td class="text-slate-500"><?php echo htmlspecialchars($u['email']); ?></td>
            <td><span class="chip capitalize"><?php echo htmlspecialchars($u['role']); ?></span></td>
            <td><?php echo htmlspecialchars($u['department'] ?? '—'); ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../partials/shell_close.php'; ?>
