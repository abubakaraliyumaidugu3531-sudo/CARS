<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_student();

require_once '../../backend/controllers/RecommendationController.php';
require_once '../../backend/controllers/RegistrationController.php';

$student_id = $_SESSION['user_id'];
$recommendationController = new RecommendationController();
$registrationController = new RegistrationController();
$flash = '';

// Current academic session/semester label used when accepting a course.
$current_semester = '2024/2025-1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'generate') {
        $recommendationController->generate($student_id);
        $flash = 'Recommendations regenerated from your latest academic record.';
    } elseif ($action === 'register' && !empty($_POST['course_id'])) {
        $course_id = (int) $_POST['course_id'];
        if ($registrationController->register($student_id, $course_id, $current_semester)) {
            $recommendationController->setStatus($student_id, $course_id, 'accepted');
            $flash = 'Course registered successfully.';
        } else {
            $flash = 'Could not register (you may already be registered for this course).';
        }
    } elseif ($action === 'dismiss' && !empty($_POST['course_id'])) {
        $recommendationController->setStatus($student_id, (int) $_POST['course_id'], 'dismissed');
        $flash = 'Recommendation dismissed.';
    }
}

$recommendations = $recommendationController->getByStudent($student_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recommendations</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <?php include '_dashboard_header.php'; ?>
  <div class="flex">
    <?php include '_sidebar.php'; ?>
    <main class="flex-1 p-8">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">Recommended Courses</h2>
        <form method="POST">
          <input type="hidden" name="action" value="generate">
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Generate Recommendations</button>
        </form>
      </div>

      <?php if ($flash): ?>
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800 text-sm"><?php echo htmlspecialchars($flash); ?></div>
      <?php endif; ?>

      <div class="bg-white rounded shadow p-4">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left border-b">
              <th class="py-2 px-4">Code</th>
              <th class="py-2 px-4">Title</th>
              <th class="py-2 px-4">Level</th>
              <th class="py-2 px-4">Units</th>
              <th class="py-2 px-4">Why recommended</th>
              <th class="py-2 px-4">Score</th>
              <th class="py-2 px-4">Status</th>
              <th class="py-2 px-4">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($recommendations->num_rows === 0): ?>
              <tr><td colspan="8" class="py-4 px-4 text-center text-gray-500">
                No recommendations yet. Click "Generate Recommendations" to get suggestions based on your academic record.
              </td></tr>
            <?php else: ?>
              <?php while ($rec = $recommendations->fetch_assoc()): ?>
                <tr class="border-b hover:bg-gray-50">
                  <td class="py-2 px-4 font-medium"><?php echo htmlspecialchars($rec['code']); ?></td>
                  <td class="py-2 px-4"><?php echo htmlspecialchars($rec['title']); ?></td>
                  <td class="py-2 px-4"><?php echo htmlspecialchars($rec['level']); ?></td>
                  <td class="py-2 px-4"><?php echo htmlspecialchars($rec['credit_unit']); ?></td>
                  <td class="py-2 px-4 text-gray-600"><?php echo htmlspecialchars($rec['reason']); ?></td>
                  <td class="py-2 px-4"><?php echo htmlspecialchars($rec['score']); ?></td>
                  <td class="py-2 px-4">
                    <?php
                      $badge = ['pending' => 'bg-gray-100 text-gray-700', 'accepted' => 'bg-green-100 text-green-700', 'dismissed' => 'bg-red-100 text-red-700'];
                      $cls = $badge[$rec['status']] ?? 'bg-gray-100 text-gray-700';
                    ?>
                    <span class="px-2 py-1 rounded text-xs <?php echo $cls; ?>"><?php echo htmlspecialchars(ucfirst($rec['status'])); ?></span>
                  </td>
                  <td class="py-2 px-4">
                    <?php if ($rec['status'] === 'pending'): ?>
                      <form method="POST" class="inline">
                        <input type="hidden" name="action" value="register">
                        <input type="hidden" name="course_id" value="<?php echo (int) $rec['course_id']; ?>">
                        <button type="submit" class="text-green-600 hover:underline">Register</button>
                      </form>
                      <form method="POST" class="inline ml-2">
                        <input type="hidden" name="action" value="dismiss">
                        <input type="hidden" name="course_id" value="<?php echo (int) $rec['course_id']; ?>">
                        <button type="submit" class="text-gray-500 hover:underline">Dismiss</button>
                      </form>
                    <?php else: ?>
                      <span class="text-gray-400">—</span>
                    <?php endif; ?>
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
