<?php
require_once '../../backend/helpers/session.php';
require_login();

require_once '../../backend/models/EvaluationModel.php';
require_once '../../backend/models/RegistrationModel.php';

$evaluationModel = new EvaluationModel();
$recStats = $evaluationModel->getRecommendationStats();
$compliance = $evaluationModel->getPrerequisiteCompliance();
$coverage = $evaluationModel->getCoverage();

// Personal registration report (students).
$registrations = null;
if (($_SESSION['role'] ?? '') === 'student') {
    $registrationModel = new RegistrationModel();
    $registrations = $registrationModel->getByStudent($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports & Evaluation</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <?php include '_dashboard_header.php'; ?>
  <div class="flex">
    <?php include '_sidebar.php'; ?>
    <main class="flex-1 p-8">
      <h2 class="text-xl font-semibold mb-4">System Effectiveness Evaluation</h2>
      <p class="text-gray-500 mb-6 text-sm">
        These indicators measure how the system improves course selection (Objective 5):
        whether students act on recommendations and whether their registrations respect prerequisites.
      </p>

      <!-- Effectiveness metrics -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded shadow">
          <div class="text-gray-500">Recommendation Acceptance</div>
          <div class="text-3xl font-bold"><?php echo $recStats['acceptance_rate']; ?>%</div>
          <div class="text-xs text-gray-400 mt-1">
            <?php echo (int) $recStats['accepted']; ?> accepted of <?php echo (int) $recStats['total']; ?> suggested
          </div>
        </div>
        <div class="bg-white p-6 rounded shadow">
          <div class="text-gray-500">Prerequisite Compliance</div>
          <div class="text-3xl font-bold"><?php echo $compliance['compliance_rate']; ?>%</div>
          <div class="text-xs text-gray-400 mt-1">
            <?php echo (int) $compliance['compliant']; ?> of <?php echo (int) $compliance['total']; ?> registrations valid
          </div>
        </div>
        <div class="bg-white p-6 rounded shadow">
          <div class="text-gray-500">Student Coverage</div>
          <div class="text-3xl font-bold"><?php echo $coverage['coverage_rate']; ?>%</div>
          <div class="text-xs text-gray-400 mt-1">
            <?php echo (int) $coverage['covered']; ?> of <?php echo (int) $coverage['students']; ?> students advised
          </div>
        </div>
      </div>

      <?php if ($registrations !== null): ?>
      <!-- Printable personal registration report -->
      <div class="bg-white rounded shadow p-4">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-semibold">My Course Registration Report</h3>
          <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Print Report</button>
        </div>
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left border-b">
              <th class="py-2 px-4">Course Code</th>
              <th class="py-2 px-4">Course Title</th>
              <th class="py-2 px-4">Units</th>
              <th class="py-2 px-4">Semester</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($registrations->num_rows === 0): ?>
              <tr><td colspan="4" class="py-4 px-4 text-center text-gray-500">No registered courses to report.</td></tr>
            <?php else: ?>
              <?php while ($row = $registrations->fetch_assoc()): ?>
                <tr class="border-b">
                  <td class="py-2 px-4 font-medium"><?php echo htmlspecialchars($row['code']); ?></td>
                  <td class="py-2 px-4"><?php echo htmlspecialchars($row['title']); ?></td>
                  <td class="py-2 px-4"><?php echo htmlspecialchars($row['credit_unit']); ?></td>
                  <td class="py-2 px-4"><?php echo htmlspecialchars($row['semester']); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>
