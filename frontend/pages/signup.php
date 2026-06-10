<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <form class="bg-white p-8 rounded shadow w-full max-w-md" method="POST" action="../../backend/controllers/Authcontroller.php">
        <h2 class="text-2xl font-bold mb-6 text-center">Sign Up</h2>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Name</label>
            <input type="text" name="name" required class="w-full border px-3 py-2 rounded focus:outline-none focus:ring">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Email</label>
            <input type="email" name="email" required class="w-full border px-3 py-2 rounded focus:outline-none focus:ring">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Password</label>
            <input type="password" name="password" required class="w-full border px-3 py-2 rounded focus:outline-none focus:ring">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Confirm Password</label>
            <input type="password" name="confirm_password" required class="w-full border px-3 py-2 rounded focus:outline-none focus:ring">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Role</label>
            <select name="role" id="role" class="w-full border px-3 py-2 rounded focus:outline-none focus:ring">
                <option value="student">Student</option>
                <option value="advisor">Academic Advisor</option>
            </select>
        </div>
        <div id="student-fields">
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Department</label>
                <input type="text" name="department" value="Computer Science" class="w-full border px-3 py-2 rounded focus:outline-none focus:ring">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Current Level</label>
                <select name="level" class="w-full border px-3 py-2 rounded focus:outline-none focus:ring">
                    <option value="100">100</option>
                    <option value="200">200</option>
                    <option value="300">300</option>
                    <option value="400">400</option>
                </select>
            </div>
        </div>
        <script>
            // Programme fields only apply to students.
            const roleSelect = document.getElementById('role');
            const studentFields = document.getElementById('student-fields');
            roleSelect.addEventListener('change', function () {
                studentFields.style.display = this.value === 'student' ? 'block' : 'none';
            });
        </script>
        <button type="submit" name="signup" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 font-semibold">Sign Up</button>
        <div class="mt-4 text-center">
            <a href="login.php" class="text-blue-600 hover:underline">Already have an account? Login</a>
        </div>
    </form>
</body>
</html>