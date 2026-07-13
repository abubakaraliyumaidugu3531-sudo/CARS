<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_admin();

require_once '../../backend/models/CourseModel.php';
require_once '../../backend/controllers/PrerequisiteController.php';

$courseModel = new CourseModel();
$prereqController = new PrerequisiteController();

$courses = [];
$res = $courseModel->getAll();
while ($c = $res->fetch_assoc()) { 
    $courses[] = $c; 
}

$search_keyword = trim($_GET['search'] ?? '');
$pairs = $prereqController->listAll();

$pageTitle = 'Prerequisites';
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
  <h2 class="text-2xl font-bold text-slate-900">Course Prerequisites</h2>
  <p class="text-slate-500 text-sm">Define which courses must be passed before another can be taken.</p>
</div>

<?php if ($msg = $_GET['msg'] ?? ''): ?>
  <div class="p-4 mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>
<?php if ($err = $_GET['err'] ?? ''): ?>
  <div class="p-4 mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg"><?php echo htmlspecialchars($err); ?></div>
<?php endif; ?>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
  <div class="card p-6 xl:col-span-1 h-fit">
    <h3 class="font-semibold text-slate-900 mb-4">Add Prerequisite</h3>
    <form method="POST" action="/cars/backend/controllers/PrerequisiteController.php" class="space-y-3">
      <input type="hidden" name="action" value="add">
      <div>
        <label class="label">Course</label>
        <select name="course_id" class="input" required>
          <option value="">Select course…</option>
          <?php foreach ($courses as $c): ?>
            <option value="<?php echo (int) $c['id']; ?>"><?php echo htmlspecialchars($c['code'] . ' — ' . $c['title']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="label">Requires (prerequisite)</label>
        <select name="prerequisite_id" class="input" required>
          <option value="">Select prerequisite…</option>
          <?php foreach ($courses as $c): ?>
            <option value="<?php echo (int) $c['id']; ?>"><?php echo htmlspecialchars($c['code'] . ' — ' . $c['title']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn-primary">Add prerequisite</button>
    </form>
  </div>

  <div class="card p-0 overflow-hidden xl:col-span-2">
    <div class="p-4 border-b border-slate-100">
      <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <h3 class="font-semibold text-slate-900 flex-1">Defined Prerequisites</h3>
      </div>
      <div class="flex flex-col sm:flex-row gap-3">
        <input type="text" id="searchInput" placeholder="Search by course code..." class="input flex-1" value="<?php echo htmlspecialchars($search_keyword); ?>">
        <button type="button" onclick="applySearch()" class="btn-primary">Search</button>
        <button type="button" onclick="clearSearch()" class="btn-secondary">Clear</button>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="table">
        <thead><tr><th>Course</th><th>Requires</th><th>Actions</th></tr></thead>
        <tbody>
          <?php if ($pairs->num_rows === 0): ?>
            <tr><td colspan="3" class="text-center text-slate-400 py-10">No prerequisites defined.</td></tr>
          <?php else: while ($p = $pairs->fetch_assoc()): 
            if (!empty($search_keyword) && stripos($p['course_code'], $search_keyword) === false && stripos($p['prereq_code'], $search_keyword) === false) {
              continue;
            }
          ?>
            <tr>
              <td class="font-medium text-slate-900"><?php echo htmlspecialchars($p['course_code']); ?> <span class="text-slate-400 font-normal text-sm"><?php echo htmlspecialchars($p['course_title']); ?></span></td>
              <td><span class="font-medium"><?php echo htmlspecialchars($p['prereq_code']); ?></span> <span class="text-slate-400 text-sm"><?php echo htmlspecialchars($p['prereq_title']); ?></span></td>
              <td class="text-sm">
                <button type="button" onclick="removePrereq(<?php echo (int) $p['course_id']; ?>, <?php echo (int) $p['prerequisite_id']; ?>, '<?php echo htmlspecialchars($p['course_code']); ?>', '<?php echo htmlspecialchars($p['prereq_code']); ?>')" class="text-red-600 hover:underline">Remove</button>
              </td>
            </tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Remove Prerequisite Modal -->
<div id="removeModal" class="modal">
  <div class="modal-content">
    <span class="modal-close" onclick="closeRemoveModal()">&times;</span>
    <h3 class="font-semibold text-slate-900 mb-4">Remove Prerequisite</h3>
    <p class="text-slate-600 mb-6">Remove the requirement that <strong id="courseCode"></strong> requires <strong id="prereqCode"></strong>?</p>
    <form method="POST" action="/cars/backend/controllers/PrerequisiteController.php" class="space-y-3">
      <input type="hidden" name="action" value="remove">
      <input type="hidden" name="course_id" id="removeCourseid" value="">
      <input type="hidden" name="prerequisite_id" id="removePrerequiteid" value="">
      <div class="flex gap-3">
        <button type="submit" class="btn-danger bg-red-600 text-white">Remove</button>
        <button type="button" onclick="closeRemoveModal()" class="btn-secondary">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
function removePrereq(courseId, prereqId, courseCode, prereqCode) {
  document.getElementById('removeCourseid').value = courseId;
  document.getElementById('removePrerequiteid').value = prereqId;
  document.getElementById('courseCode').textContent = courseCode;
  document.getElementById('prereqCode').textContent = prereqCode;
  document.getElementById('removeModal').classList.add('show');
}

function closeRemoveModal() {
  document.getElementById('removeModal').classList.remove('show');
}

function applySearch() {
  const search = document.getElementById('searchInput').value;
  const url = new URL(window.location);
  url.searchParams.set('search', search);
  window.location = url.toString();
}

function clearSearch() {
  window.location = window.location.pathname;
}

window.onclick = function(event) {
  const removeModal = document.getElementById('removeModal');
  if (event.target == removeModal) removeModal.classList.remove('show');
}
</script>

<?php include '../partials/shell_close.php'; ?>

