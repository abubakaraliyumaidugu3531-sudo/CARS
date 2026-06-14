<?php
require_once '../../backend/helpers/session.php';
require_login();

require_once '../../backend/models/EvaluationModel.php';
require_once '../../backend/models/RegistrationModel.php';

$evaluationModel = new EvaluationModel();
$recStats = $evaluationModel->getRecommendationStats();
$compliance = $evaluationModel->getPrerequisiteCompliance();
$coverage = $evaluationModel->getCoverage();

// Personal registration report (students only).
$registrations = null;
if (($_SESSION['role'] ?? '') === 'student') {
    $registrationModel = new RegistrationModel();
    $registrations = $registrationModel->getByStudent($_SESSION['user_id']);
}

$metrics = [
  ['Recommendation Acceptance', $recStats['acceptance_rate'] . '%', (int) $recStats['accepted'] . ' accepted of ' . (int) $recStats['total'] . ' suggested', 'violet'],
  ['Prerequisite Compliance', $compliance['compliance_rate'] . '%', (int) $compliance['compliant'] . ' of ' . (int) $compliance['total'] . ' registrations valid', 'emerald'],
  ['Student Coverage', $coverage['coverage_rate'] . '%', (int) $coverage['covered'] . ' of ' . (int) $coverage['students'] . ' students advised', 'brand'],
];

$pageTitle = 'Reports & Evaluation';
include '../partials/shell_open.php';
?>
<div class="mb-6">
  <h2 class="text-2xl font-bold text-slate-900">System Effectiveness Evaluation</h2>
  <p class="text-slate-500 text-sm max-w-2xl">
    These indicators measure how the system improves course selection (Objective 5): whether students
    act on recommendations and whether their registrations respect prerequisites.
  </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
  <?php foreach ($metrics as [$label, $value, $sub, $color]): ?>
    <div class="card p-6">
      <div class="stat-label"><?php echo htmlspecialchars($label); ?></div>
      <div class="text-3xl font-bold text-<?php echo $color; ?>-600 mt-1"><?php echo htmlspecialchars($value); ?></div>
      <div class="text-xs text-slate-400 mt-1"><?php echo htmlspecialchars($sub); ?></div>
    </div>
  <?php endforeach; ?>
</div>

<?php if ($registrations !== null): ?>
<div class="card p-0 overflow-hidden">
  <div class="flex items-center justify-between p-4 border-b border-slate-100">
    <h3 class="font-semibold text-slate-900">My Course Registration Report</h3>
    <button onclick="window.print()" class="btn-secondary btn-sm no-print">Print</button>
  </div>
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Course Code</th><th>Course Title</th><th>Units</th><th>Semester</th></tr></thead>
      <tbody>
        <?php if ($registrations->num_rows === 0): ?>
          <tr><td colspan="4" class="text-center text-slate-400 py-10">No registered courses to report.</td></tr>
        <?php else: ?>
          <?php while ($row = $registrations->fetch_assoc()): ?>
            <tr>
              <td class="font-medium text-slate-900"><?php echo htmlspecialchars($row['code']); ?></td>
              <td><?php echo htmlspecialchars($row['title']); ?></td>
              <td><?php echo htmlspecialchars($row['credit_unit']); ?></td>
              <td><?php echo htmlspecialchars($row['semester']); ?></td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
<?php include '../partials/shell_close.php'; ?>
