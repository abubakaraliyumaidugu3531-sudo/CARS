<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_advisor();

require_once '../../backend/config/app.php';
require_once '../../backend/models/UserModel.php';
require_once '../../backend/models/AcademicRecordModel.php';
require_once '../../backend/models/RegistrationModel.php';
require_once '../../backend/controllers/RecommendationController.php';
require_once '../../backend/controllers/ApprovalController.php';

$advisor_id = $_SESSION['user_id'];
$userModel = new UserModel();
$advisor = $userModel->findById($advisor_id);

$student_id = (int) ($_GET['student_id'] ?? 0);
$approval_id = (int) ($_GET['approval_id'] ?? 0);
$student = $student_id ? $userModel->findById($student_id) : null;

// Guard: only review students that exist and share the advisor's department.
if (!$student || $student['role'] !== 'student'
    || ($advisor['department'] && $student['department'] !== $advisor['department'])) {
    header('Location: /cars/frontend/pages/advisor_dashboard.php?err=' . urlencode('You cannot review that student.'));
    exit;
}

$academicRecordModel = new AcademicRecordModel();
$registrationModel = new RegistrationModel();
$recommendationController = new RecommendationController();
$approvalController = new ApprovalController();

$records = $academicRecordModel->getByStudent($student_id);
$gpa = $academicRecordModel->getGPA($student_id);
$registrations = $registrationModel->getByStudent($student_id);
$recommendations = $recommendationController->getByStudent($student_id);
$plan = $approvalController->getLatestForStudent($student_id);

$pageTitle = 'Review: ' . $student['name'];
include '../partials/shell_open.php';
?>
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
  <div>
    <a href="/cars/frontend/pages/advisor_dashboard.php" class="text-sm text-brand-600 hover:underline">&larr; Back to queue</a>
    <h2 class="text-2xl font-bold text-slate-900 mt-1"><?php echo htmlspecialchars($student['name']); ?></h2>
    <p class="text-slate-500 text-sm"><?php echo htmlspecialchars($student['department'] ?? '—'); ?> &middot; <?php echo htmlspecialchars($student['level'] ?? '—'); ?> Level &middot; GPA <?php echo number_format($gpa, 2); ?> (<?php echo htmlspecialchars(gpa_classification($gpa)); ?>)</p>
  </div>
  <?php if ($plan): ?>
    <span class="badge <?php echo ['pending'=>'bg-amber-100 text-amber-700','approved'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-rose-100 text-rose-700'][$plan['status']] ?? 'bg-slate-100 text-slate-700'; ?> capitalize"><?php echo htmlspecialchars($plan['status']); ?></span>
  <?php endif; ?>
</div>

<!-- Decision panel -->
<?php if ($approval_id): ?>
<div class="card p-5 mb-6">
  <h3 class="font-semibold text-slate-900 mb-3">Decision</h3>
  <form method="POST" action="/cars/backend/controllers/ApprovalController.php" class="space-y-3">
    <input type="hidden" name="approval_id" value="<?php echo $approval_id; ?>">
    <div>
      <label class="label" for="comment">Comment (optional)</label>
      <input id="comment" name="comment" type="text" class="input" placeholder="Feedback for the student">
    </div>
    <div class="flex gap-3">
      <button type="submit" name="action" value="approve" class="btn-primary">Approve plan</button>
      <button type="submit" name="action" value="reject" class="btn-danger" data-confirm="Reject this student's plan?">Reject plan</button>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  <!-- Registered courses (the plan) -->
  <div class="card p-0 overflow-hidden">
    <div class="p-4 border-b border-slate-100"><h3 class="font-semibold text-slate-900">Registered Courses</h3></div>
    <div class="overflow-x-auto">
      <table class="table">
        <thead><tr><th>Code</th><th>Title</th><th>Units</th><th>Semester</th></tr></thead>
        <tbody>
          <?php if ($registrations->num_rows === 0): ?>
            <tr><td colspan="4" class="text-center text-slate-400 py-8">No registrations.</td></tr>
          <?php else: while ($row = $registrations->fetch_assoc()): ?>
            <tr><td class="font-medium text-slate-900"><?php echo htmlspecialchars($row['code']); ?></td><td><?php echo htmlspecialchars($row['title']); ?></td><td><?php echo htmlspecialchars($row['credit_unit']); ?></td><td><?php echo htmlspecialchars($row['semester']); ?></td></tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Recommendations -->
  <div class="card p-0 overflow-hidden">
    <div class="p-4 border-b border-slate-100"><h3 class="font-semibold text-slate-900">System Recommendations</h3></div>
    <div class="overflow-x-auto">
      <table class="table">
        <thead><tr><th>Code</th><th>Title</th><th>Score</th><th>Status</th></tr></thead>
        <tbody>
          <?php if ($recommendations->num_rows === 0): ?>
            <tr><td colspan="4" class="text-center text-slate-400 py-8">No recommendations generated.</td></tr>
          <?php else: while ($row = $recommendations->fetch_assoc()): ?>
            <tr><td class="font-medium text-slate-900"><?php echo htmlspecialchars($row['code']); ?></td><td><?php echo htmlspecialchars($row['title']); ?></td><td><?php echo htmlspecialchars($row['score']); ?></td><td><span class="chip capitalize"><?php echo htmlspecialchars($row['status']); ?></span></td></tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Transcript -->
<div class="card p-0 overflow-hidden mt-6">
  <div class="p-4 border-b border-slate-100"><h3 class="font-semibold text-slate-900">Transcript</h3></div>
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Code</th><th>Title</th><th>Units</th><th>Semester</th><th>Grade</th><th>Status</th></tr></thead>
      <tbody>
        <?php if ($records->num_rows === 0): ?>
          <tr><td colspan="6" class="text-center text-slate-400 py-8">No academic records.</td></tr>
        <?php else: while ($row = $records->fetch_assoc()): ?>
          <tr>
            <td class="font-medium text-slate-900"><?php echo htmlspecialchars($row['code']); ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['credit_unit']); ?></td>
            <td><?php echo htmlspecialchars($row['semester']); ?></td>
            <td class="font-semibold"><?php echo htmlspecialchars($row['grade']); ?></td>
            <td><span class="badge <?php echo $row['status'] === 'passed' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'; ?> capitalize"><?php echo htmlspecialchars($row['status']); ?></span></td>
          </tr>
        <?php endwhile; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../partials/shell_close.php'; ?>
