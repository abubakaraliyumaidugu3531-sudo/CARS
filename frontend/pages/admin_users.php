<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_admin();

require_once '../../backend/models/UserModel.php';

$userModel = new UserModel();

// Handle search/filter
$search_keyword = trim($_GET['search'] ?? '');
$filter_role = trim($_GET['role'] ?? '');

if (!empty($search_keyword) || !empty($filter_role)) {
    $users = $userModel->search($search_keyword, $filter_role);
} else {
    $users = $userModel->listAll();
}

$roleBadge = [
  'student' => 'bg-brand-100 text-brand-700',
  'advisor' => 'bg-emerald-100 text-emerald-700',
  'admin'   => 'bg-amber-100 text-amber-700',
];

$pageTitle = 'Users';
include '../partials/shell_open.php';
?>
<style>
  .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.4); }
  .modal.show { display: block; }
  .modal-content { background-color: white; margin: 50px auto; padding: 20px; border: 1px solid #888; border-radius: 0.5rem; width: 90%; max-width: 600px; }
  .modal-close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
  .modal-close:hover { color: #000; }
</style>

<div class="mb-6">
  <h2 class="text-2xl font-bold text-slate-900">Users</h2>
  <p class="text-slate-500 text-sm">All accounts. Create advisor or admin staff accounts here (students self-register).</p>
</div>

<?php if ($msg = $_GET['msg'] ?? ''): ?>
  <div class="p-4 mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>
<?php if ($err = $_GET['err'] ?? ''): ?>
  <div class="p-4 mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg"><?php echo htmlspecialchars($err); ?></div>
<?php endif; ?>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
  <div class="card p-6 xl:col-span-1 h-fit">
    <h3 class="font-semibold text-slate-900 mb-4">Create Staff Account</h3>
    <form method="POST" action="/cars/backend/controllers/Authcontroller.php" class="space-y-3">
      <input type="hidden" name="create_staff" value="1">
      <div><label class="label">Name</label><input name="name" class="input" required></div>
      <div><label class="label">Email</label><input name="email" type="email" class="input" required></div>
      <div><label class="label">Password</label><input name="password" type="password" class="input" required minlength="6" placeholder="At least 6 characters"></div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Role</label>
          <select name="role" class="input">
            <option value="advisor">Advisor</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div><label class="label">Department</label><input name="department" class="input" placeholder="Computer Science"></div>
      </div>
      <button type="submit" class="btn-primary">Create account</button>
    </form>
  </div>

  <div class="card p-0 overflow-hidden xl:col-span-2">
    <div class="p-4 border-b border-slate-100">
      <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <h3 class="font-semibold text-slate-900 flex-1">All Users</h3>
      </div>
      <div class="flex flex-col sm:flex-row gap-3">
        <input type="text" id="searchInput" placeholder="Search by name or email..." class="input flex-1" value="<?php echo htmlspecialchars($search_keyword); ?>">
        <select id="roleFilter" class="input">
          <option value="">All Roles</option>
          <option value="student" <?php echo $filter_role === 'student' ? 'selected' : ''; ?>>Student</option>
          <option value="advisor" <?php echo $filter_role === 'advisor' ? 'selected' : ''; ?>>Advisor</option>
          <option value="admin" <?php echo $filter_role === 'admin' ? 'selected' : ''; ?>>Admin</option>
        </select>
        <button type="button" onclick="applyFilters()" class="btn-primary">Filter</button>
        <button type="button" onclick="clearFilters()" class="btn-secondary">Clear</button>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="table">
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Department</th><th>Level</th><th>Actions</th></tr></thead>
        <tbody>
          <?php if ($users->num_rows === 0): ?>
            <tr><td colspan="6" class="text-center text-slate-400 py-10">No users found.</td></tr>
          <?php else: while ($u = $users->fetch_assoc()): ?>
            <tr>
              <td class="font-medium text-slate-900"><?php echo htmlspecialchars($u['name']); ?></td>
              <td class="text-slate-500 text-sm"><?php echo htmlspecialchars($u['email']); ?></td>
              <td><span class="badge <?php echo $roleBadge[$u['role']] ?? 'bg-slate-100 text-slate-700'; ?> capitalize text-xs"><?php echo htmlspecialchars($u['role']); ?></span></td>
              <td class="text-sm"><?php echo htmlspecialchars($u['department'] ?? '—'); ?></td>
              <td class="text-sm"><?php echo htmlspecialchars($u['level'] ?? '—'); ?></td>
              <td class="text-sm">
                <button type="button" onclick="editUser(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['name']); ?>', '<?php echo htmlspecialchars($u['email']); ?>', '<?php echo $u['role']; ?>', '<?php echo htmlspecialchars($u['department'] ?? ''); ?>', '<?php echo htmlspecialchars($u['level'] ?? ''); ?>')" class="text-blue-600 hover:underline mr-3">Edit</button>
                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                  <button type="button" onclick="deleteUser(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['name']); ?>')" class="text-red-600 hover:underline">Delete</button>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Edit User Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeEditModal()">&times;</span>
    <h3 class="font-semibold text-slate-900 mb-4">Edit User</h3>
    <form method="POST" action="/cars/backend/controllers/AdminUserController.php" class="space-y-3">
      <input type="hidden" name="edit_user" value="1">
      <input type="hidden" name="user_id" id="editUserId" value="">
      <div><label class="label">Name</label><input name="name" id="editName" class="input" required></div>
      <div><label class="label">Email</label><input name="email" type="email" id="editEmail" class="input" required></div>
      <div><label class="label">Role</label>
        <select name="role" id="editRole" class="input">
          <option value="student">Student</option>
          <option value="advisor">Advisor</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <div><label class="label">Department</label><input name="department" id="editDepartment" class="input" placeholder="Computer Science"></div>
      <div><label class="label">Level</label><input name="level" id="editLevel" class="input" placeholder="100 / 200 / 300 / 400"></div>
      <div class="flex gap-3">
        <button type="submit" class="btn-primary">Save Changes</button>
        <button type="button" onclick="closeEditModal()" class="btn-secondary">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete User Modal -->
<div id="deleteModal" class="modal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeDeleteModal()">&times;</span>
    <h3 class="font-semibold text-slate-900 mb-4">Delete User</h3>
    <p class="text-slate-600 mb-6">Are you sure you want to delete <strong id="deleteUserName"></strong>? This action cannot be undone. All related records (grades, registrations, recommendations) will be deleted.</p>
    <form method="POST" action="/cars/backend/controllers/AdminUserController.php" class="space-y-3">
      <input type="hidden" name="delete_user" value="1">
      <input type="hidden" name="user_id" id="deleteUserId" value="">
      <div class="flex gap-3">
        <button type="submit" class="btn-danger bg-red-600 text-white">Delete User</button>
        <button type="button" onclick="closeDeleteModal()" class="btn-secondary">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
function editUser(userId, name, email, role, department, level) {
  document.getElementById('editUserId').value = userId;
  document.getElementById('editName').value = name;
  document.getElementById('editEmail').value = email;
  document.getElementById('editRole').value = role;
  document.getElementById('editDepartment').value = department;
  document.getElementById('editLevel').value = level;
  document.getElementById('editModal').classList.add('show');
}

function closeEditModal() {
  document.getElementById('editModal').classList.remove('show');
}

function deleteUser(userId, name) {
  document.getElementById('deleteUserId').value = userId;
  document.getElementById('deleteUserName').textContent = name;
  document.getElementById('deleteModal').classList.add('show');
}

function closeDeleteModal() {
  document.getElementById('deleteModal').classList.remove('show');
}

function applyFilters() {
  const search = document.getElementById('searchInput').value;
  const role = document.getElementById('roleFilter').value;
  const url = new URL(window.location);
  url.searchParams.set('search', search);
  url.searchParams.set('role', role);
  window.location = url.toString();
}

function clearFilters() {
  window.location = window.location.pathname;
}

// Close modal on outside click
window.onclick = function(event) {
  const editModal = document.getElementById('editModal');
  const deleteModal = document.getElementById('deleteModal');
  if (event.target == editModal) editModal.classList.remove('show');
  if (event.target == deleteModal) deleteModal.classList.remove('show');
}
</script>

<?php include '../partials/shell_close.php'; ?>
