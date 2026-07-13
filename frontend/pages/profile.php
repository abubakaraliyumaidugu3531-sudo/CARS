<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_student();

require_once '../../backend/models/UserModel.php';

$student_id = $_SESSION['user_id'];
$userModel = new UserModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department = trim($_POST['department'] ?? '') ?: null;
    $level = trim($_POST['level'] ?? '') ?: null;
    $userModel->updateProfile($student_id, $department, $level);
    header('Location: /cars/frontend/pages/profile.php?msg=' . urlencode('Profile updated. Regenerate recommendations to use the new details.'));
    exit;
}

$student = $userModel->findById($student_id);
$levels = ['100', '200', '300', '400', '500'];

$pageTitle = 'Profile';
include '../partials/shell_open.php';
?>
<div class="mb-6">
  <h2 class="text-2xl font-bold text-slate-900">My Profile</h2>
  <p class="text-slate-500 text-sm">Your programme details drive level-appropriate recommendations.</p>
</div>

<div class="card p-6 max-w-xl">
  <form method="POST" class="space-y-4">
    <div>
      <label class="label">Full name</label>
      <input type="text" class="input bg-slate-50" value="<?php echo htmlspecialchars($student['name']); ?>" disabled>
    </div>
    <div>
      <label class="label">Email</label>
      <input type="email" class="input bg-slate-50" value="<?php echo htmlspecialchars($student['email']); ?>" disabled>
    </div>
    <div>
      <label class="label" for="department">Department</label>
      <input id="department" name="department" type="text" class="input" value="<?php echo htmlspecialchars($student['department'] ?? ''); ?>" placeholder="e.g. Computer Science">
    </div>
    <div>
      <label class="label" for="level">Current level</label>
      <select id="level" name="level" class="input">
        <?php foreach ($levels as $lv): ?>
          <option value="<?php echo $lv; ?>" <?php echo ($student['level'] ?? '') === $lv ? 'selected' : ''; ?>><?php echo $lv; ?> Level</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="pt-2">
      <button type="submit" class="btn-primary">Save changes</button>
    </div>
  </form>
</div>
<?php include '../partials/shell_close.php'; ?>
