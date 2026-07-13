<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_admin();

require_once '../../backend/models/CourseModel.php';

$courseModel = new CourseModel();

// Handle search/filter
$search_keyword = trim($_GET['search'] ?? '');
$filter_dept = trim($_GET['department'] ?? '');

if (!empty($search_keyword) || !empty($filter_dept)) {
    $courses = $courseModel->search($search_keyword, $filter_dept);
} else {
    $courses = $courseModel->getAll();
}

// Edit mode: load the selected course into the form.
$editing = null;
if (!empty($_GET['edit'])) {
    $editing = $courseModel->findById((int) $_GET['edit']);
}

$departments = $courseModel->getDepartments();
$semesters = ['first' => 'First', 'second' => 'Second', 'any' => 'Any'];

$pageTitle = 'Manage Courses';
include '../partials/shell_open.php';
?>
<style>
  .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); }
  .modal.show { display: block; }
  .modal-content { background-color: white; margin: 50px auto; padding: 20px; border: 1px solid #888; border-radius: 0.5rem; width: 90%; max-width: 600px; }
  .modal-close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
  .modal-close:hover { color: #000; }
</style>

<div class="mb-6">
  <h2 class="text-2xl font-bold text-slate-900">Manage Courses</h2>
  <p class="text-slate-500 text-sm">Add, edit and remove courses in the catalogue.</p>
</div>

<?php if ($msg = $_GET['msg'] ?? ''): ?>
  <div class="p-4 mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>
<?php if ($err = $_GET['err'] ?? ''): ?>
  <div class="p-4 mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg"><?php echo htmlspecialchars($err); ?></div>
<?php endif; ?>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
  <!-- Form -->
  <div class="card p-6 xl:col-span-1 h-fit">
    <h3 class="font-semibold text-slate-900 mb-4"><?php echo $editing ? 'Edit Course' : 'Add Course'; ?></h3>
    <form method="POST" action="/cars/backend/controllers/AdminCourseController.php" class="space-y-3">
      <input type="hidden" name="<?php echo $editing ? 'edit_course' : 'create'; ?>" value="1">
      <?php if ($editing): ?><input type="hidden" name="course_id" value="<?php echo (int) $editing['id']; ?>"><?php endif; ?>
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
        <?php if ($editing): ?><a href="/cars/frontend/pages/admin_courses.php" class="btn-ghost">Cancel</a><?php endif; ?>
      </div>
    </form>
  </div>

  <!-- List -->
  <div class="card p-0 overflow-hidden xl:col-span-2">
    <div class="p-4 border-b border-slate-100">
      <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <h3 class="font-semibold text-slate-900 flex-1">All Courses</h3>
      </div>
      <div class="flex flex-col sm:flex-row gap-3">
        <input type="text" id="searchInput" placeholder="Search by code or title..." class="input flex-1" value="<?php echo htmlspecialchars($search_keyword); ?>">
        <select id="deptFilter" class="input">
          <option value="">All Departments</option>
          <?php foreach ($departments as $dept): ?>
            <option value="<?php echo htmlspecialchars($dept); ?>" <?php echo $filter_dept === $dept ? 'selected' : ''; ?>><?php echo htmlspecialchars($dept); ?></option>
          <?php endforeach; ?>
        </select>
        <button type="button" onclick="applyFilters()" class="btn-primary">Filter</button>
        <button type="button" onclick="clearFilters()" class="btn-secondary">Clear</button>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="table">
        <thead><tr><th>Code</th><th>Title</th><th>Units</th><th>Level</th><th>Dept</th><th>Type</th><th>Actions</th></tr></thead>
        <tbody>
          <?php if ($courses->num_rows === 0): ?>
            <tr><td colspan="7" class="text-center text-slate-400 py-10">No courses found.</td></tr>
          <?php else: while ($c = $courses->fetch_assoc()): ?>
            <tr>
              <td class="font-medium text-slate-900"><?php echo htmlspecialchars($c['code']); ?></td>
              <td class="text-sm"><?php echo htmlspecialchars($c['title']); ?></td>
              <td><?php echo htmlspecialchars($c['credit_unit']); ?></td>
              <td><?php echo htmlspecialchars($c['level']); ?></td>
              <td class="text-sm"><?php echo htmlspecialchars($c['department']); ?></td>
              <td><span class="chip text-xs"><?php echo !empty($c['is_core']) ? 'Core' : 'Elective'; ?></span></td>
              <td class="text-sm">
                <a href="/cars/frontend/pages/admin_courses.php?edit=<?php echo (int) $c['id']; ?>" class="text-blue-600 hover:underline mr-3">Edit</a>
                <button type="button" onclick="deleteCourse(<?php echo $c['id']; ?>, '<?php echo htmlspecialchars($c['code']); ?>')" class="text-red-600 hover:underline">Delete</button>
              </td>
            </tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Delete Course Modal -->
<div id="deleteModal" class="modal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeDeleteModal()">&times;</span>
    <h3 class="font-semibold text-slate-900 mb-4">Delete Course</h3>
    <p class="text-slate-600 mb-6">Are you sure you want to delete course <strong id="deleteCourseCode"></strong>? This action cannot be undone.</p>
    <form method="POST" action="/cars/backend/controllers/AdminCourseController.php" class="space-y-3">
      <input type="hidden" name="delete_course" value="1">
      <input type="hidden" name="course_id" id="deleteCourseId" value="">
      <div class="flex gap-3">
        <button type="submit" class="btn-danger bg-red-600 text-white">Delete Course</button>
        <button type="button" onclick="closeDeleteModal()" class="btn-secondary">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
function deleteCourse(courseId, code) {
  document.getElementById('deleteCourseId').value = courseId;
  document.getElementById('deleteCourseCode').textContent = code;
  document.getElementById('deleteModal').classList.add('show');
}

function closeDeleteModal() {
  document.getElementById('deleteModal').classList.remove('show');
}

function applyFilters() {
  const search = document.getElementById('searchInput').value;
  const dept = document.getElementById('deptFilter').value;
  const url = new URL(window.location);
  url.searchParams.set('search', search);
  url.searchParams.set('department', dept);
  window.location = url.toString();
}

function clearFilters() {
  window.location = window.location.pathname;
}

window.onclick = function(event) {
  const deleteModal = document.getElementById('deleteModal');
  if (event.target == deleteModal) deleteModal.classList.remove('show');
}
</script>

<?php include '../partials/shell_close.php'; ?>

