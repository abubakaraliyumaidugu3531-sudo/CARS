<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_student();

require_once '../../backend/config/app.php';
require_once '../../backend/controllers/RecommendationController.php';
require_once '../../backend/controllers/RegistrationController.php';

$student_id = $_SESSION['user_id'];
$recommendationController = new RecommendationController();
$registrationController = new RegistrationController();
$flash = '';
$flashError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'generate') {
        $recommendationController->generate($student_id);
        $flash = 'Recommendations regenerated from your latest academic record.';
    } elseif ($action === 'register' && !empty($_POST['course_id'])) {
        $course_id = (int) $_POST['course_id'];
        if ($registrationController->register($student_id, $course_id, CURRENT_SEMESTER)) {
            $recommendationController->setStatus($student_id, $course_id, 'accepted');
            $flash = 'Course registered for ' . CURRENT_SEMESTER . '.';
        } else {
            $flash = 'Could not register (you may already be registered for this course).';
            $flashError = true;
        }
    } elseif ($action === 'dismiss' && !empty($_POST['course_id'])) {
        $recommendationController->setStatus($student_id, (int) $_POST['course_id'], 'dismissed');
        $flash = 'Recommendation dismissed.';
    }
}

$recommendations = $recommendationController->getByStudent($student_id);
$badge = ['pending' => 'bg-slate-100 text-slate-700', 'accepted' => 'bg-emerald-100 text-emerald-700', 'dismissed' => 'bg-rose-100 text-rose-700'];
$maxScore = 12; // approximate engine ceiling, for the score bar

$pageTitle = 'Recommendations';
include '../partials/shell_open.php';
?>
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
  <div>
    <h2 class="text-2xl font-bold text-slate-900">Recommended Courses</h2>
    <p class="text-slate-500 text-sm">Ranked from your grades, prerequisites and programme level.</p>
  </div>
  <form method="POST">
    <input type="hidden" name="action" value="generate">
    <button type="submit" class="btn-primary">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
      Generate Recommendations
    </button>
  </form>
</div>

<?php if ($flash): ?>
  <div class="flash mb-4 flex items-start justify-between gap-3 rounded-lg border px-4 py-3 text-sm <?php echo $flashError ? 'bg-rose-50 text-rose-800 border-rose-200' : 'bg-emerald-50 text-emerald-800 border-emerald-200'; ?>">
    <span><?php echo htmlspecialchars($flash); ?></span>
    <button type="button" class="flash-close text-current/60 hover:text-current" aria-label="Dismiss">&times;</button>
  </div>
<?php endif; ?>

<div class="card p-0 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="table">
      <thead>
        <tr><th>Code</th><th>Title</th><th>Level</th><th>Units</th><th>Why recommended</th><th>Score</th><th>Status</th><th class="no-print">Action</th></tr>
      </thead>
      <tbody>
        <?php if ($recommendations->num_rows === 0): ?>
          <tr><td colspan="8" class="text-center text-slate-400 py-10">
            No recommendations yet. Click <span class="font-medium text-slate-600">Generate Recommendations</span> to get suggestions based on your academic record.
          </td></tr>
        <?php else: ?>
          <?php while ($rec = $recommendations->fetch_assoc()): ?>
            <?php
              $pct = max(8, min(100, round(((float) $rec['score'] / $maxScore) * 100)));
              $reasons = array_filter(array_map('trim', explode(';', $rec['reason'] ?? '')));
            ?>
            <tr>
              <td class="font-medium text-slate-900"><?php echo htmlspecialchars($rec['code']); ?></td>
              <td><?php echo htmlspecialchars($rec['title']); ?></td>
              <td><?php echo htmlspecialchars($rec['level']); ?></td>
              <td><?php echo htmlspecialchars($rec['credit_unit']); ?></td>
              <td>
                <div class="flex flex-wrap gap-1">
                  <?php foreach ($reasons as $r): ?><span class="chip"><?php echo htmlspecialchars($r); ?></span><?php endforeach; ?>
                </div>
              </td>
              <td>
                <div class="flex items-center gap-2">
                  <div class="w-20 h-2 rounded-full bg-slate-100 overflow-hidden"><div class="h-full bg-brand-500" style="width: <?php echo $pct; ?>%"></div></div>
                  <span class="text-xs text-slate-500"><?php echo htmlspecialchars($rec['score']); ?></span>
                </div>
              </td>
              <td><span class="badge <?php echo $badge[$rec['status']] ?? 'bg-slate-100 text-slate-700'; ?> capitalize"><?php echo htmlspecialchars($rec['status']); ?></span></td>
              <td class="no-print">
                <?php if ($rec['status'] === 'pending'): ?>
                  <div class="flex items-center gap-2">
                    <form method="POST">
                      <input type="hidden" name="action" value="register">
                      <input type="hidden" name="course_id" value="<?php echo (int) $rec['course_id']; ?>">
                      <button type="submit" class="btn-primary btn-sm">Register</button>
                    </form>
                    <form method="POST">
                      <input type="hidden" name="action" value="dismiss">
                      <input type="hidden" name="course_id" value="<?php echo (int) $rec['course_id']; ?>">
                      <button type="submit" class="btn-ghost btn-sm">Dismiss</button>
                    </form>
                  </div>
                <?php else: ?>
                  <span class="text-slate-300">—</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../partials/shell_close.php'; ?>
