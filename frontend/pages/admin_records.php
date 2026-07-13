<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_admin();

require_once '../../backend/config/app.php';
require_once '../../backend/models/UserModel.php';
require_once '../../backend/models/CourseModel.php';
require_once '../../backend/models/AcademicRecordModel.php';

$userModel = new UserModel();
$courseModel = new CourseModel();
$academicRecordModel = new AcademicRecordModel();

$students = $userModel->getByRole('student');
$courses = [];
foreach ($courseModel->getAll() as $c) { $courses[] = $c; }

// Optionally preview a selected student's transcript.
$selectedId = (int) ($_GET['student_id'] ?? 0);
$transcript = $selectedId ? $academicRecordModel->getByStudent($selectedId) : null;

$grades = ['A', 'B', 'C', 'D', 'E', 'F'];

$pageTitle = 'Enter Grades';
include '../partials/shell_open.php';
?>
<div class="mb-6">
  <h2 class="text-2xl font-bold text-slate-900">Enter Academic Records</h2>
  <p class="text-slate-500 text-sm">Record a student's grade for a course. Pass/fail and grade points are derived automatically.</p>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
  <div class="card p-6 xl:col-span-1 h-fit">
    <h3 class="font-semibold text-slate-900 mb-4">Record a grade</h3>
    <form method="POST" action="/cars/backend/controllers/RecordController.php" class="space-y-3">
      <div>
        <label class="label">Student</label>
        <select name="student_id" class="input" required>
          <option value="">Select student…</option>
          <?php while ($s = $students->fetch_assoc()): ?>
            <option value="<?php echo (int) $s['id']; ?>"><?php echo htmlspecialchars($s['name'] . ' (' . $s['email'] . ')'); ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div>
        <label class="label">Course</label>
        <select name="course_id" class="input" required>
          <option value="">Select course…</option>
          <?php foreach ($courses as $c): ?><option value="<?php echo (int) $c['id']; ?>"><?php echo htmlspecialchars($c['code'] . ' — ' . $c['title']); ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Semester</label><input name="semester" class="input" required value="<?php echo htmlspecialchars(CURRENT_SEMESTER); ?>"></div>
        <div><label class="label">Grade</label>
          <select name="grade" class="input" required>
            <?php foreach ($grades as $g): ?><option value="<?php echo $g; ?>"><?php echo $g; ?></option><?php endforeach; ?>
          </select>
        </div>
      </div>
      <button type="submit" class="btn-primary">Save grade</button>
    </form>
  </div>

  <div class="card p-0 overflow-hidden xl:col-span-2">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-semibold text-slate-900">Preview transcript</h3>
      <form method="GET" class="flex items-center gap-2">
        <select name="student_id" class="input py-1.5" onchange="this.form.submit()">
          <option value="">Choose a student…</option>
          <?php $students2 = $userModel->getByRole('student'); while ($s = $students2->fetch_assoc()): ?>
            <option value="<?php echo (int) $s['id']; ?>" <?php echo $selectedId === (int) $s['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($s['name']); ?></option>
          <?php endwhile; ?>
        </select>
      </form>
    </div>
    <div class="overflow-x-auto">
      <table class="table">
        <thead><tr><th>Code</th><th>Title</th><th>Semester</th><th>Grade</th><th>Status</th></tr></thead>
        <tbody>
          <?php if (!$transcript): ?>
            <tr><td colspan="5" class="text-center text-slate-400 py-10">Select a student to preview their records.</td></tr>
          <?php elseif ($transcript->num_rows === 0): ?>
            <tr><td colspan="5" class="text-center text-slate-400 py-10">No records for this student yet.</td></tr>
          <?php else: while ($r = $transcript->fetch_assoc()): ?>
            <tr>
              <td class="font-medium text-slate-900"><?php echo htmlspecialchars($r['code']); ?></td>
              <td><?php echo htmlspecialchars($r['title']); ?></td>
              <td><?php echo htmlspecialchars($r['semester']); ?></td>
              <td class="font-semibold"><?php echo htmlspecialchars($r['grade']); ?></td>
              <td><span class="badge <?php echo $r['status'] === 'passed' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'; ?> capitalize"><?php echo htmlspecialchars($r['status']); ?></span></td>
            </tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include '../partials/shell_close.php'; ?>
