<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Dashboard</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script src="/frontend/js/main.js" defer></script>
</head>
<body class="bg-gray-100">
	<?php include '_dashboard_header.php'; ?>
	<div class="flex">
		<?php include '_sidebar.php'; ?>
		<main class="flex-1 p-8">
			<h2 class="text-xl font-semibold mb-4">Admin Dashboard</h2>
			<!-- Summary Cards -->
			<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
				<div class="bg-white p-6 rounded shadow">
					<div class="text-gray-500">Total Users</div>
					<div class="text-2xl font-bold">0</div>
				</div>
				<div class="bg-white p-6 rounded shadow">
					<div class="text-gray-500">Total Courses</div>
					<div class="text-2xl font-bold">0</div>
				</div>
				<div class="bg-white p-6 rounded shadow">
					<div class="text-gray-500">System Stats</div>
					<div class="text-2xl font-bold">0</div>
				</div>
			</div>
			<!-- Table: Users (Pagination) -->
			<div class="bg-white rounded shadow p-4 mb-8">
				<h3 class="font-semibold mb-2">Users</h3>
				<table class="min-w-full text-sm">
					<thead>
						<tr>
							<th class="py-2 px-4">Name</th>
							<th class="py-2 px-4">Email</th>
							<th class="py-2 px-4">Role</th>
							<th class="py-2 px-4">Action</th>
						</tr>
					</thead>
					<tbody>
						<!-- PHP: Loop users here -->
					</tbody>
				</table>
				<!-- Pagination controls here -->
			</div>
			<!-- Table: Courses (Pagination) -->
			<div class="bg-white rounded shadow p-4">
				<h3 class="font-semibold mb-2">Courses</h3>
				<table class="min-w-full text-sm">
					<thead>
						<tr>
							<th class="py-2 px-4">Code</th>
							<th class="py-2 px-4">Title</th>
							<th class="py-2 px-4">Department</th>
							<th class="py-2 px-4">Action</th>
						</tr>
					</thead>
					<tbody>
						<!-- PHP: Loop courses here -->
					</tbody>
				</table>
				<!-- Pagination controls here -->
			</div>
		</main>
	</div>
</body>
</html>