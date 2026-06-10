<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Advisory & Recommendation System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-100 to-green-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-10 rounded-2xl shadow-2xl max-w-3xl w-full">
        <h1 class="text-4xl font-extrabold mb-2 text-blue-800 text-center">Course Advisory & Recommendation System</h1>
        <p class="mb-8 text-gray-600 text-center text-lg">A modern platform for students, advisors, and admins to manage courses, recommendations, and approvals.</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <!-- Student Card -->
            <div class="bg-blue-50 rounded-xl shadow p-6 flex flex-col items-center">
                <svg class="w-12 h-12 text-blue-500 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422A12.083 12.083 0 0112 21.5c-2.5-2.5-4.5-5.5-6.16-10.922L12 14z"/></svg>
                <h2 class="font-bold text-xl mb-2">Student</h2>
                <p class="text-gray-500 mb-4 text-center">View recommendations, register for courses, and print reports.</p>
                <a href="frontend/pages/student_dashboard.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-semibold w-full text-center mb-2">Student Dashboard</a>
                <a href="frontend/pages/login.php" class="text-blue-600 hover:underline text-sm">Login as Student</a>
            </div>
            <!-- Advisor Card -->
            <div class="bg-green-50 rounded-xl shadow p-6 flex flex-col items-center">
                <svg class="w-12 h-12 text-green-500 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2"/></svg>
                <h2 class="font-bold text-xl mb-2">Advisor</h2>
                <p class="text-gray-500 mb-4 text-center">Approve/reject registrations and view student plans.</p>
                <a href="frontend/pages/advisor_dashboard.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 font-semibold w-full text-center mb-2">Advisor Dashboard</a>
                <a href="frontend/pages/login.php" class="text-green-600 hover:underline text-sm">Login as Advisor</a>
            </div>
            <!-- Admin Card -->
            <div class="bg-yellow-50 rounded-xl shadow p-6 flex flex-col items-center">
                <svg class="w-12 h-12 text-yellow-500 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 3v4M8 3v4"/></svg>
                <h2 class="font-bold text-xl mb-2">Admin</h2>
                <p class="text-gray-500 mb-4 text-center">Manage users, courses, and view system statistics.</p>
                <a href="frontend/pages/admin_dashboard.php" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 font-semibold w-full text-center mb-2">Admin Dashboard</a>
                <a href="frontend/pages/login.php" class="text-yellow-600 hover:underline text-sm">Login as Admin</a>
            </div>
        </div>
        <div class="flex flex-col md:flex-row gap-4 justify-center mb-2">
            <a href="frontend/pages/login.php" class="bg-blue-600 text-white px-6 py-2 rounded font-semibold hover:bg-blue-700 transition">Login</a>
            <a href="frontend/pages/signup.php" class="bg-green-600 text-white px-6 py-2 rounded font-semibold hover:bg-green-700 transition">Create an Account</a>
        </div>
        <footer class="mt-8 text-xs text-gray-400 text-center">&copy; <?php echo date('Y'); ?> Course Advisory System. All rights reserved.</footer>
    </div>
</body>
</html>