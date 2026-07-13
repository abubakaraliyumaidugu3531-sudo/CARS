<?php
require_once '../../backend/helpers/session.php';
require_login();

require_once '../../backend/models/CourseModel.php';
require_once '../../backend/models/PrerequisiteModel.php';

$courseModel = new CourseModel();
$prerequisiteModel = new PrerequisiteModel();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$department = isset($_GET['department']) ? trim($_GET['department']) : '';

$courses = $courseModel->search($search, $department);
$departments = $courseModel->getDepartments();
$prereqMap = $prerequisiteModel->getAllMap();

// Resolve prerequisite IDs to course codes for display.
$allCourses = $courseModel->getAll();
$codeById = [];
foreach ($allCourses as $c) {
    $codeById[(int) $c['id']] = $c['code'];
}

$pageTitle = 'Course Catalogue';
include '../partials/shell_open.php';
?>
<div class="mb-6">
  <h2 class="text-2xl font-bold text-slate-900">Course Catalogue</h2>
  <p class="text-slate-500 text-sm">Browse all courses, their levels and prerequisites.</p>
</div>

<form method="GET" class="card p-4 mb-5 flex flex-wrap gap-3 items-end">
  <div class="flex-1 min-w-[14rem]">
    <label class="label">Search</label>
    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Code or title" class="input">
  </div>
  <div class="min-w-[12rem]">
    <label class="label">Department</label>
    <select name="department" class="input">
      <option value="">All Departments</option>
      <?php foreach ($departments as $dept): ?>
        <option value="<?php echo htmlspecialchars($dept); ?>" <?php echo $department === $dept ? 'selected' : ''; ?>><?php echo htmlspecialchars($dept); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <button type="submit" class="btn-primary">Search</button>
  <?php if ($search || $department): ?><a href="/cars/frontend/pages/courses.php" class="btn-ghost">Clear</a><?php endif; ?>
</form>

<div class="card p-0 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Code</th><th>Title</th><th>Units</th><th>Level</th><th>Type</th><th>Department</th><th>Prerequisites</th></tr></thead>
      <tbody>
        <?php if ($courses->num_rows === 0): ?>
          <tr><td colspan="7" class="text-center text-slate-400 py-10">No courses match your search.</td></tr>
        <?php else: ?>
          <?php while ($course = $courses->fetch_assoc()): ?>
            <?php
              $prereqIds = $prereqMap[(int) $course['id']] ?? [];
              $prereqCodes = array_map(function ($id) use ($codeById) { return $codeById[$id] ?? '#' . $id; }, $prereqIds);
            ?>
            <tr>
              <td class="font-medium text-slate-900"><?php echo htmlspecialchars($course['code']); ?></td>
              <td><?php echo htmlspecialchars($course['title']); ?></td>
              <td><?php echo htmlspecialchars($course['credit_unit']); ?></td>
              <td><?php echo htmlspecialchars($course['level']); ?></td>
              <td><?php echo !empty($course['is_core']) ? '<span class="chip">Core</span>' : '<span class="chip">Elective</span>'; ?></td>
              <td><?php echo htmlspecialchars($course['department']); ?></td>
              <td><?php echo $prereqCodes ? htmlspecialchars(implode(', ', $prereqCodes)) : '<span class="text-slate-300">None</span>'; ?></td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../partials/shell_close.php'; ?>
