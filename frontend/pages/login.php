<?php
session_start();
if (isset($_SESSION['user_id'])) {
        $role = $_SESSION['role'];
        if ($role === 'admin') header('Location: admin_dashboard.php');
        elseif ($role === 'advisor') header('Location: advisor_dashboard.php');
        else header('Location: student_dashboard.php');
        exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <form class="bg-white p-8 rounded shadow w-full max-w-md" method="POST" action="../../backend/controllers/Authcontroller.php">
        <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Email</label>
            <input type="email" name="email" required class="w-full border px-3 py-2 rounded focus:outline-none focus:ring">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Password</label>
            <input type="password" name="password" required class="w-full border px-3 py-2 rounded focus:outline-none focus:ring">
        </div>
        <button type="submit" name="login" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 font-semibold">Login</button>
        <div class="mt-4 text-center">
            <a href="signup.php" class="text-blue-600 hover:underline">Don't have an account? Sign up</a>
        </div>
    </form>
</body>
</html>