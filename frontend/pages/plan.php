<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_student();

require_once '../../backend/config/app.php';
require_once '../../backend/models/RegistrationModel.php';
require_once '../../backend/controllers/ApprovalController.php';

$student_id = $_SESSION['user_id'];
$registrationModel = new RegistrationModel();
$approvalController = new ApprovalController();

// Courses registered for the current semester.
$allReg = $registrationModel->getByStudent($student_id);
$semesterCourses = [];
$totalUnits = 0;
while ($row = $allReg->fetch_assoc()) {
    if ($row['semester'] === CURRENT_SEMESTER) {
        $semesterCourses[] = $row;
        $totalUnits += (int) $row['credit_unit'];
    }
}

$plan = $approvalController->getLatestForStudent($student_id);
$statusBadge = [
  'pending'  => 'bg-amber-100 text-amber-700',
  'approved' => 'bg-emerald-100 text-emerald-700',
  'rejected' => 'bg-rose-100 text-rose-700',
];

$pageTitle = 'My Plan';
include '../partials/shell_open.php';
?>
<div class="mb-6">
  <h2 class="text-2xl font-bold text-slate-900">My Course Plan</h2>
  <p class="text-slate-500 text-sm">Semester <?php echo htmlspecialchars(CURRENT_SEMESTER); ?> &middot; submit your registered courses for advisor approval.</p>
</div>

<?php if ($plan): ?>
  <div class="card p-5 mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
      <div class="stat-label">Latest plan (<?php echo htmlspecialchars($plan['semester']); ?>)</div>
      <div class="mt-1"><span class="badge <?php echo $statusBadge[$plan['status']] ?? 'bg-slate-100 text-slate-700'; ?> capitalize"><?php echo htmlspecialchars($plan['status']); ?></span></div>
      <?php if (!empty($plan['comment'])): ?>
        <p class="text-sm text-slate-600 mt-2"><span class="font-medium">Advisor:</span> <?php echo htmlspecialchars($plan['comment']); ?>
          <?php if (!empty($plan['advisor_name'])): ?><span class="text-slate-400">— <?php echo htmlspecialchars($plan['advisor_name']); ?></span><?php endif; ?>
        </p>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<div class="card p-0 overflow-hidden">
  <div class="flex items-center justify-between p-4 border-b border-slate-100">
    <h3 class="font-semibold text-slate-900">Registered for <?php echo htmlspecialchars(CURRENT_SEMESTER); ?> <span class="text-slate-400 font-normal">(<?php echo $totalUnits; ?> units)</span></h3>
    <?php if (!empty($semesterCourses)): ?>
      <form method="POST" action="/cars/backend/controllers/ApprovalController.php"
            data-confirm="Submit your <?php echo count($semesterCourses); ?> course(s) for advisor approval?">
        <input type="hidden" name="action" value="submit_plan">
        <button type="submit" class="btn-primary btn-sm">Submit for approval</button>
      </form>
    <?php endif; ?>
  </div>
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Code</th><th>Title</th><th>Units</th></tr></thead>
      <tbody>
        <?php if (empty($semesterCourses)): ?>
          <tr><td colspan="3" class="text-center text-slate-400 py-10">
            You have no courses registered for this semester yet.
            <a href="/cars/frontend/pages/recommendations.php" class="text-brand-600 hover:underline">Find courses</a>.
          </td></tr>
        <?php else: ?>
          <?php foreach ($semesterCourses as $row): ?>
            <tr>
              <td class="font-medium text-slate-900"><?php echo htmlspecialchars($row['code']); ?></td>
              <td><?php echo htmlspecialchars($row['title']); ?></td>
              <td><?php echo htmlspecialchars($row['credit_unit']); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../partials/shell_close.php'; ?>
