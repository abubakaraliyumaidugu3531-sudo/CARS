<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_admin();

require_once '../../backend/models/CourseModel.php';
require_once '../../backend/controllers/PrerequisiteController.php';

$courseModel = new CourseModel();
$prereqController = new PrerequisiteController();

$courses = [];
$res = $courseModel->getAll();
foreach ($res as $c) { $courses[] = $c; }
$pairs = $prereqController->listAll();

$pageTitle = 'Prerequisites';
include '../partials/shell_open.php';
?>
<div class="mb-6">
  <h2 class="text-2xl font-bold text-slate-900">Course Prerequisites</h2>
  <p class="text-slate-500 text-sm">Define which courses must be passed before another can be taken.</p>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
  <div class="card p-6 xl:col-span-1 h-fit">
    <h3 class="font-semibold text-slate-900 mb-4">Add Prerequisite</h3>
    <form method="POST" action="/backend/controllers/PrerequisiteController.php" class="space-y-3">
      <input type="hidden" name="action" value="add">
      <div>
        <label class="label">Course</label>
        <select name="course_id" class="input" required>
          <option value="">Select course…</option>
          <?php foreach ($courses as $c): ?><option value="<?php echo (int) $c['id']; ?>"><?php echo htmlspecialchars($c['code'] . ' — ' . $c['title']); ?></option><?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="label">Requires (prerequisite)</label>
        <select name="prerequisite_id" class="input" required>
          <option value="">Select prerequisite…</option>
          <?php foreach ($courses as $c): ?><option value="<?php echo (int) $c['id']; ?>"><?php echo htmlspecialchars($c['code'] . ' — ' . $c['title']); ?></option><?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn-primary">Add prerequisite</button>
    </form>
  </div>

  <div class="card p-0 overflow-hidden xl:col-span-2">
    <div class="p-4 border-b border-slate-100"><h3 class="font-semibold text-slate-900">Defined Prerequisites</h3></div>
    <div class="overflow-x-auto">
      <table class="table">
        <thead><tr><th>Course</th><th>Requires</th><th>Action</th></tr></thead>
        <tbody>
          <?php if ($pairs->num_rows === 0): ?>
            <tr><td colspan="3" class="text-center text-slate-400 py-10">No prerequisites defined.</td></tr>
          <?php else: while ($p = $pairs->fetch_assoc()): ?>
            <tr>
              <td class="font-medium text-slate-900"><?php echo htmlspecialchars($p['course_code']); ?> <span class="text-slate-400 font-normal"><?php echo htmlspecialchars($p['course_title']); ?></span></td>
              <td><?php echo htmlspecialchars($p['prereq_code']); ?> <span class="text-slate-400"><?php echo htmlspecialchars($p['prereq_title']); ?></span></td>
              <td>
                <form method="POST" action="/backend/controllers/PrerequisiteController.php" data-confirm="Remove this prerequisite?">
                  <input type="hidden" name="action" value="remove">
                  <input type="hidden" name="course_id" value="<?php echo (int) $p['course_id']; ?>">
                  <input type="hidden" name="prerequisite_id" value="<?php echo (int) $p['prerequisite_id']; ?>">
                  <button type="submit" class="btn-danger btn-sm">Remove</button>
                </form>
              </td>
            </tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include '../partials/shell_close.php'; ?>
