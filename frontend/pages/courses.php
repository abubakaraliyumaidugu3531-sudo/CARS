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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Courses</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <?php include '_dashboard_header.php'; ?>
  <div class="flex">
    <?php include '_sidebar.php'; ?>
    <main class="flex-1 p-8">
      <h2 class="text-xl font-semibold mb-4">Course Catalogue</h2>
      <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by code or title" class="border px-3 py-2 rounded w-64">
        <select name="department" class="border px-3 py-2 rounded">
          <option value="">All Departments</option>
          <?php foreach ($departments as $dept): ?>
            <option value="<?php echo htmlspecialchars($dept); ?>" <?php echo $department === $dept ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($dept); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Search</button>
      </form>
      <div class="bg-white rounded shadow p-4">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left border-b">
              <th class="py-2 px-4">Code</th>
              <th class="py-2 px-4">Title</th>
              <th class="py-2 px-4">Units</th>
              <th class="py-2 px-4">Level</th>
              <th class="py-2 px-4">Department</th>
              <th class="py-2 px-4">Prerequisites</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($courses->num_rows === 0): ?>
              <tr><td colspan="6" class="py-4 px-4 text-center text-gray-500">No courses match your search.</td></tr>
            <?php else: ?>
              <?php while ($course = $courses->fetch_assoc()): ?>
                <?php
                  $prereqIds = $prereqMap[(int) $course['id']] ?? [];
                  $prereqCodes = array_map(function ($id) use ($codeById) { return $codeById[$id] ?? '#' . $id; }, $prereqIds);
                ?>
                <tr class="border-b hover:bg-gray-50">
                  <td class="py-2 px-4 font-medium"><?php echo htmlspecialchars($course['code']); ?></td>
                  <td class="py-2 px-4"><?php echo htmlspecialchars($course['title']); ?></td>
                  <td class="py-2 px-4"><?php echo htmlspecialchars($course['credit_unit']); ?></td>
                  <td class="py-2 px-4"><?php echo htmlspecialchars($course['level']); ?></td>
                  <td class="py-2 px-4"><?php echo htmlspecialchars($course['department']); ?></td>
                  <td class="py-2 px-4 text-gray-600"><?php echo $prereqCodes ? htmlspecialchars(implode(', ', $prereqCodes)) : '<span class="text-gray-400">None</span>'; ?></td>
                </tr>
              <?php endwhile; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>
