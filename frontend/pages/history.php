<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_student();

require_once '../../backend/config/app.php';
require_once '../../backend/models/AcademicRecordModel.php';

$student_id = $_SESSION['user_id'];
$academicRecordModel = new AcademicRecordModel();
$records = $academicRecordModel->getByStudent($student_id);
$gpa = $academicRecordModel->getGPA($student_id);

$pageTitle = 'Academic Record';
include '../partials/shell_open.php';
?>
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
  <div>
    <h2 class="text-2xl font-bold text-slate-900">Academic Record</h2>
    <p class="text-slate-500 text-sm">Your full grade transcript.</p>
  </div>
  <div class="card px-5 py-3 flex items-center gap-6">
    <div><div class="stat-label">Cumulative GPA</div><div class="text-xl font-bold text-slate-900"><?php echo number_format($gpa, 2); ?></div></div>
    <div><div class="stat-label">Classification</div><div class="text-sm font-semibold text-slate-700"><?php echo htmlspecialchars(gpa_classification($gpa)); ?></div></div>
  </div>
</div>

<div class="card p-0 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Code</th><th>Title</th><th>Units</th><th>Semester</th><th>Grade</th><th>Status</th></tr></thead>
      <tbody>
        <?php if ($records->num_rows === 0): ?>
          <tr><td colspan="6" class="text-center text-slate-400 py-10">No academic records on file yet.</td></tr>
        <?php else: ?>
          <?php while ($row = $records->fetch_assoc()): ?>
            <tr>
              <td class="font-medium text-slate-900"><?php echo htmlspecialchars($row['code']); ?></td>
              <td><?php echo htmlspecialchars($row['title']); ?></td>
              <td><?php echo htmlspecialchars($row['credit_unit']); ?></td>
              <td><?php echo htmlspecialchars($row['semester']); ?></td>
              <td class="font-semibold"><?php echo htmlspecialchars($row['grade']); ?></td>
              <td>
                <?php $ok = $row['status'] === 'passed'; ?>
                <span class="badge <?php echo $ok ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'; ?> capitalize"><?php echo htmlspecialchars($row['status']); ?></span>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../partials/shell_close.php'; ?>
