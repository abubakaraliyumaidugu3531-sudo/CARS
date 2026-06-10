<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_student();

require_once '../../backend/models/AcademicRecordModel.php';

$student_id = $_SESSION['user_id'];
$academicRecordModel = new AcademicRecordModel();
$records = $academicRecordModel->getByStudent($student_id);
$gpa = $academicRecordModel->getGPA($student_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Academic Record</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <?php include '_dashboard_header.php'; ?>
  <div class="flex">
    <?php include '_sidebar.php'; ?>
    <main class="flex-1 p-8">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">Academic Record (Transcript)</h2>
        <div class="bg-white px-4 py-2 rounded shadow">
          <span class="text-gray-500 text-sm">Cumulative GPA:</span>
          <span class="text-lg font-bold ml-1"><?php echo number_format($gpa, 2); ?></span>
        </div>
      </div>
      <div class="bg-white rounded shadow p-4">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left border-b">
              <th class="py-2 px-4">Code</th>
              <th class="py-2 px-4">Title</th>
              <th class="py-2 px-4">Units</th>
              <th class="py-2 px-4">Semester</th>
              <th class="py-2 px-4">Grade</th>
              <th class="py-2 px-4">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($records->num_rows === 0): ?>
              <tr><td colspan="6" class="py-4 px-4 text-center text-gray-500">No academic records on file yet.</td></tr>
            <?php else: ?>
              <?php while ($row = $records->fetch_assoc()): ?>
                <tr class="border-b hover:bg-gray-50">
                  <td class="py-2 px-4 font-medium"><?php echo htmlspecialchars($row['code']); ?></td>
                  <td class="py-2 px-4"><?php echo htmlspecialchars($row['title']); ?></td>
                  <td class="py-2 px-4"><?php echo htmlspecialchars($row['credit_unit']); ?></td>
                  <td class="py-2 px-4"><?php echo htmlspecialchars($row['semester']); ?></td>
                  <td class="py-2 px-4 font-semibold"><?php echo htmlspecialchars($row['grade']); ?></td>
                  <td class="py-2 px-4">
                    <?php $ok = $row['status'] === 'passed'; ?>
                    <span class="px-2 py-1 rounded text-xs <?php echo $ok ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                      <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                    </span>
                  </td>
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
