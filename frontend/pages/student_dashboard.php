<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_student();

require_once '../../backend/models/UserModel.php';
require_once '../../backend/models/RegistrationModel.php';
require_once '../../backend/models/RecommendationModel.php';
require_once '../../backend/models/ApprovalModel.php';
require_once '../../backend/models/AcademicRecordModel.php';

$student_id = $_SESSION['user_id'];
$userModel = new UserModel();
$registrationModel = new RegistrationModel();
$recommendationModel = new RecommendationModel();
$approvalModel = new ApprovalModel();
$academicRecordModel = new AcademicRecordModel();

$student = $userModel->findById($student_id);
$registrations = $registrationModel->getByStudent($student_id);
$registeredCount = $registrations->num_rows;
$recommendedCount = $recommendationModel->countByStudent($student_id);
$gpa = $academicRecordModel->getGPA($student_id);

// Count pending advisor approvals for this student.
$approvals = $approvalModel->getByStudent($student_id);
$pendingApprovals = 0;
while ($a = $approvals->fetch_assoc()) {
    if ($a['status'] === 'pending') {
        $pendingApprovals++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Student Dashboard</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script src="/frontend/js/main.js" defer></script>
</head>
<body class="bg-gray-100">
	<?php include '_dashboard_header.php'; ?>
	<div class="flex">
		<?php include '_sidebar.php'; ?>
		<main class="flex-1 p-8">
			<h2 class="text-xl font-semibold mb-1">Welcome, <?php echo htmlspecialchars($student['name']); ?>!</h2>
			<p class="text-gray-500 mb-6">
				<?php echo htmlspecialchars($student['department'] ?? 'No department set'); ?>
				<?php echo $student['level'] ? ' &middot; ' . htmlspecialchars($student['level']) . ' Level' : ''; ?>
			</p>
			<!-- Summary Cards -->
			<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
				<div class="bg-white p-6 rounded shadow">
					<div class="text-gray-500">Registered Courses</div>
					<div class="text-2xl font-bold"><?php echo $registeredCount; ?></div>
				</div>
				<div class="bg-white p-6 rounded shadow">
					<div class="text-gray-500">Recommended Courses</div>
					<div class="text-2xl font-bold"><?php echo $recommendedCount; ?></div>
				</div>
				<div class="bg-white p-6 rounded shadow">
					<div class="text-gray-500">Pending Approvals</div>
					<div class="text-2xl font-bold"><?php echo $pendingApprovals; ?></div>
				</div>
				<div class="bg-white p-6 rounded shadow">
					<div class="text-gray-500">Cumulative GPA</div>
					<div class="text-2xl font-bold"><?php echo number_format($gpa, 2); ?></div>
				</div>
			</div>
			<!-- Registered Courses -->
			<div class="bg-white rounded shadow p-4">
				<h3 class="font-semibold mb-2">Registered Courses</h3>
				<table class="min-w-full text-sm">
					<thead>
						<tr class="text-left border-b">
							<th class="py-2 px-4">Code</th>
							<th class="py-2 px-4">Title</th>
							<th class="py-2 px-4">Units</th>
							<th class="py-2 px-4">Semester</th>
						</tr>
					</thead>
					<tbody>
						<?php if ($registeredCount === 0): ?>
							<tr><td colspan="4" class="py-4 px-4 text-center text-gray-500">You have not registered for any courses yet.</td></tr>
						<?php else: ?>
							<?php while ($row = $registrations->fetch_assoc()): ?>
								<tr class="border-b hover:bg-gray-50">
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
		</main>
	</div>
</body>
</html>
