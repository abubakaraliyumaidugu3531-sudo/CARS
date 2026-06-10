<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_advisor();
require_once '../../backend/models/ApprovalModel.php';
require_once '../../backend/models/UserModel.php';

$advisor_id = $_SESSION['user_id'];
$approvalModel = new ApprovalModel();
$userModel = new UserModel();
$approvals = $approvalModel->getByAdvisor($advisor_id);
$assigned_students = [];
$pending_count = 0;
$approved_count = 0;
if ($approvals) {
	while ($row = $approvals->fetch_assoc()) {
		$assigned_students[] = $row;
		if ($row['status'] === 'pending') $pending_count++;
		if ($row['status'] === 'approved') $approved_count++;
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Advisor Dashboard</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script src="/frontend/js/main.js" defer></script>
</head>
<body class="bg-gray-100">
	<?php include '_dashboard_header.php'; ?>
	<div class="flex">
		<?php include '_sidebar.php'; ?>
		<main class="flex-1 p-8">
			<h2 class="text-xl font-semibold mb-4">Advisor Dashboard</h2>
			<!-- Summary Cards -->
			<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
				<div class="bg-white p-6 rounded shadow">
					<div class="text-gray-500">Assigned Students</div>
					<div class="text-2xl font-bold"><?php echo count($assigned_students); ?></div>
				</div>
				<div class="bg-white p-6 rounded shadow">
					<div class="text-gray-500">Pending Approvals</div>
					<div class="text-2xl font-bold"><?php echo $pending_count; ?></div>
				</div>
				<div class="bg-white p-6 rounded shadow">
					<div class="text-gray-500">Approved Plans</div>
					<div class="text-2xl font-bold"><?php echo $approved_count; ?></div>
				</div>
			</div>
			<!-- Table: Assigned Students (Pagination) -->
			<div class="bg-white rounded shadow p-4">
				<h3 class="font-semibold mb-2">Assigned Students</h3>
				<table class="min-w-full text-sm">
					<thead>
						<tr>
							<th class="py-2 px-4">Student Name</th>
							<th class="py-2 px-4">Status</th>
							<th class="py-2 px-4">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($assigned_students) === 0): ?>
							<tr><td colspan="3" class="text-center py-4">No assigned students.</td></tr>
						<?php else: ?>
							<?php foreach ($assigned_students as $student): ?>
								<tr>
									<td class="py-2 px-4"><?php echo htmlspecialchars($student['student_name']); ?></td>
									<td class="py-2 px-4"><?php echo htmlspecialchars(ucfirst($student['status'])); ?></td>
									<td class="py-2 px-4">
										<?php if ($student['status'] === 'pending'): ?>
											<form method="POST" action="../../backend/controllers/ApprovalController.php" style="display:inline;">
												<input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
												<button type="submit" name="approve" class="bg-green-500 text-white px-3 py-1 rounded">Approve</button>
											</form>
											<form method="POST" action="../../backend/controllers/ApprovalController.php" style="display:inline;">
												<input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
												<button type="submit" name="reject" class="bg-red-500 text-white px-3 py-1 rounded">Reject</button>
											</form>
										<?php else: ?>
											<span class="text-gray-400">-</span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
				<!-- Pagination controls here -->
			</div>
		</main>
	</div>
</body>
</html>