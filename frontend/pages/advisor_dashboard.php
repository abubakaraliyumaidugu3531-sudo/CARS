<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_advisor();

require_once '../../backend/models/UserModel.php';
require_once '../../backend/controllers/ApprovalController.php';

$advisor_id = $_SESSION['user_id'];
$userModel = new UserModel();
$advisor = $userModel->findById($advisor_id);
$department = $advisor['department'] ?? null;

$approvalController = new ApprovalController();
$queue = $approvalController->getQueue($department);

// Counts across this advisor's department for the KPI cards.
$handled = $approvalController->getHandledByDepartment($department);
$pending = 0; $approved = 0; $rejected = 0;
$queueRows = [];
while ($row = $handled->fetch_assoc()) {
    if ($row['status'] === 'pending') $pending++;
    elseif ($row['status'] === 'approved') $approved++;
    elseif ($row['status'] === 'rejected') $rejected++;
}
while ($row = $queue->fetch_assoc()) { $queueRows[] = $row; }

$pageTitle = 'Advisor Dashboard';
include '../partials/shell_open.php';
?>
<div class="mb-6">
  <h2 class="text-2xl font-bold text-slate-900">Plan Review Queue</h2>
  <p class="text-slate-500 text-sm"><?php echo htmlspecialchars($department ?? 'All departments'); ?> &middot; review and approve student course plans.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
  <div class="stat"><div><div class="stat-label">Pending Review</div><div class="stat-value"><?php echo $pending; ?></div></div>
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span></div>
  <div class="stat"><div><div class="stat-label">Approved</div><div class="stat-value"><?php echo $approved; ?></div></div>
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span></div>
  <div class="stat"><div><div class="stat-label">Rejected</div><div class="stat-value"><?php echo $rejected; ?></div></div>
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-rose-50 text-rose-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span></div>
</div>

<div class="card p-0 overflow-hidden">
  <div class="p-4 border-b border-slate-100"><h3 class="font-semibold text-slate-900">Pending Plans</h3></div>
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Student</th><th>Level</th><th>Semester</th><th>Submitted</th><th>Action</th></tr></thead>
      <tbody>
        <?php if (empty($queueRows)): ?>
          <tr><td colspan="5" class="text-center text-slate-400 py-10">No plans awaiting review.</td></tr>
        <?php else: ?>
          <?php foreach ($queueRows as $row): ?>
            <tr>
              <td class="font-medium text-slate-900"><?php echo htmlspecialchars($row['student_name']); ?></td>
              <td><?php echo htmlspecialchars($row['level'] ?? '—'); ?></td>
              <td><?php echo htmlspecialchars($row['semester']); ?></td>
              <td class="text-slate-500"><?php echo htmlspecialchars($row['created_at']); ?></td>
              <td><a href="/cars/frontend/pages/advisor_student.php?student_id=<?php echo (int) $row['student_id']; ?>&approval_id=<?php echo (int) $row['id']; ?>" class="btn-primary btn-sm">Review</a></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../partials/shell_close.php'; ?>
