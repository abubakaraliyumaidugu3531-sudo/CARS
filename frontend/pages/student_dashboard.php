<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_student();

require_once '../../backend/config/app.php';
require_once '../../backend/models/UserModel.php';
require_once '../../backend/models/RegistrationModel.php';
require_once '../../backend/models/RecommendationModel.php';
require_once '../../backend/models/AcademicRecordModel.php';
require_once '../../backend/controllers/ApprovalController.php';

$student_id = $_SESSION['user_id'];
$userModel = new UserModel();
$registrationModel = new RegistrationModel();
$recommendationModel = new RecommendationModel();
$academicRecordModel = new AcademicRecordModel();
$approvalController = new ApprovalController();

$student = $userModel->findById($student_id);
$registrations = $registrationModel->getByStudent($student_id);
$registeredCount = $registrations->num_rows;
$recommendedCount = $recommendationModel->countByStudent($student_id);
$gpa = $academicRecordModel->getGPA($student_id);
$plan = $approvalController->getLatestForStudent($student_id);

$statusBadge = [
  'pending'  => 'bg-amber-100 text-amber-700',
  'approved' => 'bg-emerald-100 text-emerald-700',
  'rejected' => 'bg-rose-100 text-rose-700',
];
$planStatus = $plan['status'] ?? null;

$pageTitle = 'Dashboard';
include '../partials/shell_open.php';
?>
<div class="mb-6">
  <h2 class="text-2xl font-bold text-slate-900">Welcome, <?php echo htmlspecialchars($student['name']); ?></h2>
  <p class="text-slate-500">
    <?php echo htmlspecialchars($student['department'] ?? 'No department set'); ?>
    <?php echo $student['level'] ? ' &middot; ' . htmlspecialchars($student['level']) . ' Level' : ''; ?>
  </p>
</div>

<!-- KPI cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
  <div class="stat">
    <div><div class="stat-label">Registered Courses</div><div class="stat-value"><?php echo $registeredCount; ?></div></div>
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg></span>
  </div>
  <div class="stat">
    <div><div class="stat-label">Recommended</div><div class="stat-value"><?php echo $recommendedCount; ?></div></div>
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-violet-50 text-violet-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/></svg></span>
  </div>
  <div class="stat">
    <div><div class="stat-label">Cumulative GPA</div><div class="stat-value"><?php echo number_format($gpa, 2); ?></div>
      <div class="text-xs text-slate-400 mt-1"><?php echo htmlspecialchars(gpa_classification($gpa)); ?></div></div>
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg></span>
  </div>
  <div class="stat">
    <div><div class="stat-label">Plan Status</div>
      <div class="mt-2">
        <?php if ($planStatus): ?>
          <span class="badge <?php echo $statusBadge[$planStatus] ?? 'bg-slate-100 text-slate-700'; ?> capitalize"><?php echo htmlspecialchars($planStatus); ?></span>
        <?php else: ?>
          <span class="badge bg-slate-100 text-slate-600">Not submitted</span>
        <?php endif; ?>
      </div>
    </div>
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
  </div>
</div>

<!-- Registered courses -->
<div class="card p-0 overflow-hidden">
  <div class="flex items-center justify-between p-4 border-b border-slate-100">
    <h3 class="font-semibold text-slate-900">Registered Courses</h3>
    <a href="/frontend/pages/recommendations.php" class="btn-primary btn-sm">Find courses</a>
  </div>
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Code</th><th>Title</th><th>Units</th><th>Semester</th></tr></thead>
      <tbody>
        <?php if ($registeredCount === 0): ?>
          <tr><td colspan="4" class="text-center text-slate-400 py-8">You have not registered for any courses yet.</td></tr>
        <?php else: ?>
          <?php while ($row = $registrations->fetch_assoc()): ?>
            <tr>
              <td class="font-medium text-slate-900"><?php echo htmlspecialchars($row['code']); ?></td>
              <td><?php echo htmlspecialchars($row['title']); ?></td>
              <td><?php echo htmlspecialchars($row['credit_unit']); ?></td>
              <td><?php echo htmlspecialchars($row['semester']); ?></td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../partials/shell_close.php'; ?>
