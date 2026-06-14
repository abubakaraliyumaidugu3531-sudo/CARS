<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_admin();

require_once '../../backend/models/CourseModel.php';

$courseModel = new CourseModel();
$courses = $courseModel->getAll();

// Edit mode: load the selected course into the form.
$editing = null;
if (!empty($_GET['edit'])) {
    $editing = $courseModel->findById((int) $_GET['edit']);
}
$semesters = ['first' => 'First', 'second' => 'Second', 'any' => 'Any'];

$pageTitle = 'Manage Courses';
include '../partials/shell_open.php';
?>
<div class="mb-6">
  <h2 class="text-2xl font-bold text-slate-900">Manage Courses</h2>
  <p class="text-slate-500 text-sm">Add, edit and remove courses in the catalogue.</p>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
  <!-- Form -->
  <div class="card p-6 xl:col-span-1 h-fit">
    <h3 class="font-semibold text-slate-900 mb-4"><?php echo $editing ? 'Edit Course' : 'Add Course'; ?></h3>
    <form method="POST" action="/backend/controllers/CourseController.php" class="space-y-3">
      <input type="hidden" name="action" value="<?php echo $editing ? 'update' : 'create'; ?>">
      <?php if ($editing): ?><input type="hidden" name="id" value="<?php echo (int) $editing['id']; ?>"><?php endif; ?>
      <div><label class="label">Code</label><input name="code" class="input" required value="<?php echo htmlspecialchars($editing['code'] ?? ''); ?>" placeholder="CSC201"></div>
      <div><label class="label">Title</label><input name="title" class="input" required value="<?php echo htmlspecialchars($editing['title'] ?? ''); ?>" placeholder="Data Structures"></div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Credit units</label><input name="credit_unit" type="number" min="1" max="9" class="input" required value="<?php echo htmlspecialchars($editing['credit_unit'] ?? '3'); ?>"></div>
        <div><label class="label">Level</label><input name="level" class="input" required value="<?php echo htmlspecialchars($editing['level'] ?? '100'); ?>" placeholder="200"></div>
      </div>
      <div><label class="label">Department</label><input name="department" class="input" required value="<?php echo htmlspecialchars($editing['department'] ?? 'Computer Science'); ?>"></div>
      <div class="grid grid-cols-2 gap-3 items-end">
        <div><label class="label">Semester</label>
          <select name="semester" class="input">
            <?php foreach ($semesters as $val => $lbl): ?>
              <option value="<?php echo $val; ?>" <?php echo ($editing['semester'] ?? 'any') === $val ? 'selected' : ''; ?>><?php echo $lbl; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <label class="flex items-center gap-2 pb-2 text-sm text-slate-700">
          <input type="checkbox" name="is_core" value="1" class="rounded border-slate-300" <?php echo (!isset($editing) || !empty($editing['is_core'])) ? 'checked' : ''; ?>>
          Core course
        </label>
      </div>
      <div><label class="label">Description</label><textarea name="description" class="input" rows="2" placeholder="Short description"><?php echo htmlspecialchars($editing['description'] ?? ''); ?></textarea></div>
      <div class="flex gap-2 pt-1">
        <button type="submit" class="btn-primary"><?php echo $editing ? 'Save changes' : 'Add course'; ?></button>
        <?php if ($editing): ?><a href="/frontend/pages/admin_courses.php" class="btn-ghost">Cancel</a><?php endif; ?>
      </div>
    </form>
  </div>

  <!-- List -->
  <div class="card p-0 overflow-hidden xl:col-span-2">
    <div class="p-4 border-b border-slate-100"><h3 class="font-semibold text-slate-900">All Courses</h3></div>
    <div class="overflow-x-auto">
      <table class="table">
        <thead><tr><th>Code</th><th>Title</th><th>Units</th><th>Level</th><th>Type</th><th>Action</th></tr></thead>
        <tbody>
          <?php if ($courses->num_rows === 0): ?>
            <tr><td colspan="6" class="text-center text-slate-400 py-10">No courses yet.</td></tr>
          <?php else: while ($c = $courses->fetch_assoc()): ?>
            <tr>
              <td class="font-medium text-slate-900"><?php echo htmlspecialchars($c['code']); ?></td>
              <td><?php echo htmlspecialchars($c['title']); ?></td>
              <td><?php echo htmlspecialchars($c['credit_unit']); ?></td>
              <td><?php echo htmlspecialchars($c['level']); ?></td>
              <td><span class="chip"><?php echo !empty($c['is_core']) ? 'Core' : 'Elective'; ?></span></td>
              <td>
                <div class="flex items-center gap-2">
                  <a href="/frontend/pages/admin_courses.php?edit=<?php echo (int) $c['id']; ?>" class="btn-secondary btn-sm">Edit</a>
                  <form method="POST" action="/backend/controllers/CourseController.php" data-confirm="Delete <?php echo htmlspecialchars($c['code']); ?>? This cannot be undone.">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo (int) $c['id']; ?>">
                    <button type="submit" class="btn-danger btn-sm">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include '../partials/shell_close.php'; ?>
