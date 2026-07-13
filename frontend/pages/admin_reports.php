<?php
require_once '../../backend/helpers/session.php';
require_login();
require_once '../../backend/middleware/role_middleware.php';
require_admin();

require_once '../../backend/models/UserModel.php';
require_once '../../backend/models/CourseModel.php';
require_once '../../backend/models/AcademicRecordModel.php';
require_once '../../backend/models/EvaluationModel.php';

$userModel = new UserModel();
$courseModel = new CourseModel();
$academicRecordModel = new AcademicRecordModel();
$evaluationModel = new EvaluationModel();

// Get statistics
$studentCount = $userModel->countByRole('student');
$advisorCount = $userModel->countByRole('advisor');
$adminCount = $userModel->countByRole('admin');
$courseCount = $courseModel->getAll()->num_rows;

$recStats = $evaluationModel->getRecommendationStats();
$complianceStats = $evaluationModel->getPrerequisiteCompliance();
$coverageStats = $evaluationModel->getCoverage();

// Get grade distribution
$gpa_stats = $academicRecordModel->getGPAStatistics();
$course_enrollments = $academicRecordModel->getCourseEnrollmentStats();

$pageTitle = 'Reports & Analytics';
include '../partials/shell_open.php';
?>

<div class="mb-6">
  <h2 class="text-2xl font-bold text-slate-900">Reports & Analytics</h2>
  <p class="text-slate-500 text-sm">System statistics and performance metrics.</p>
</div>

<!-- User Statistics -->
<div class="grid grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
  <div class="stat">
    <div>
      <div class="stat-label">Total Students</div>
      <div class="stat-value"><?php echo $studentCount; ?></div>
    </div>
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
      </svg>
    </span>
  </div>
  <div class="stat">
    <div>
      <div class="stat-label">Total Advisors</div>
      <div class="stat-value"><?php echo $advisorCount; ?></div>
    </div>
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
      </svg>
    </span>
  </div>
  <div class="stat">
    <div>
      <div class="stat-label">Total Courses</div>
      <div class="stat-value"><?php echo $courseCount; ?></div>
    </div>
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
      </svg>
    </span>
  </div>
  <div class="stat">
    <div>
      <div class="stat-label">Admins</div>
      <div class="stat-value"><?php echo $adminCount; ?></div>
    </div>
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.43.992a6.759 6.759 0 010 .255c-.008.378.137.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
      </svg>
    </span>
  </div>
</div>

<!-- Recommendation & Compliance Statistics -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-8">
  <div class="card p-6">
    <h3 class="font-semibold text-slate-900 mb-3">Recommendation Stats</h3>
    <div class="space-y-3">
      <div>
        <div class="flex justify-between mb-1">
          <span class="text-sm text-slate-600">Acceptance Rate</span>
          <span class="font-semibold text-brand-600"><?php echo $recStats['acceptance_rate']; ?>%</span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-2">
          <div class="bg-brand-600 h-2 rounded-full" style="width: <?php echo $recStats['acceptance_rate']; ?>%"></div>
        </div>
      </div>
      <div class="text-sm text-slate-500">
        <?php echo (int)$recStats['accepted']; ?> accepted of <?php echo (int)$recStats['total']; ?> total
      </div>
    </div>
  </div>

  <div class="card p-6">
    <h3 class="font-semibold text-slate-900 mb-3">Prerequisite Compliance</h3>
    <div class="space-y-3">
      <div>
        <div class="flex justify-between mb-1">
          <span class="text-sm text-slate-600">Compliance Rate</span>
          <span class="font-semibold text-emerald-600"><?php echo $complianceStats['compliance_rate']; ?>%</span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-2">
          <div class="bg-emerald-600 h-2 rounded-full" style="width: <?php echo $complianceStats['compliance_rate']; ?>%"></div>
        </div>
      </div>
      <div class="text-sm text-slate-500">
        <?php echo (int)$complianceStats['compliant']; ?> compliant of <?php echo (int)$complianceStats['total']; ?> registrations
      </div>
    </div>
  </div>

  <div class="card p-6">
    <h3 class="font-semibold text-slate-900 mb-3">Coverage</h3>
    <div class="space-y-3">
      <div>
        <div class="flex justify-between mb-1">
          <span class="text-sm text-slate-600">Students Covered</span>
          <span class="font-semibold text-violet-600"><?php echo $coverageStats['coverage_rate']; ?>%</span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-2">
          <div class="bg-violet-600 h-2 rounded-full" style="width: <?php echo $coverageStats['coverage_rate']; ?>%"></div>
        </div>
      </div>
      <div class="text-sm text-slate-500">
        <?php echo (int)$coverageStats['covered']; ?> of <?php echo (int)$coverageStats['students']; ?> students have recommendations
      </div>
    </div>
  </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
  <div class="card p-6">
    <h3 class="font-semibold text-slate-900 mb-4">System Health</h3>
    <div class="space-y-3">
      <div class="flex justify-between text-sm">
        <span class="text-slate-600">Total Users</span>
        <span class="font-semibold"><?php echo $studentCount + $advisorCount + $adminCount; ?></span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="text-slate-600">Student to Advisor Ratio</span>
        <span class="font-semibold"><?php echo $advisorCount > 0 ? round($studentCount / $advisorCount, 1) : 'N/A'; ?>:1</span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="text-slate-600">Avg Students per Advisor</span>
        <span class="font-semibold"><?php echo $advisorCount > 0 ? round($studentCount / $advisorCount) : 'N/A'; ?></span>
      </div>
      <div class="flex justify-between text-sm border-t border-slate-200 pt-3">
        <span class="text-slate-600">Total Recommendations</span>
        <span class="font-semibold"><?php echo (int)$recStats['total']; ?></span>
      </div>
    </div>
  </div>

  <div class="card p-6">
    <h3 class="font-semibold text-slate-900 mb-4">Key Insights</h3>
    <ul class="space-y-2 text-sm">
      <li class="flex items-start gap-2">
        <span class="text-brand-600 font-semibold">✓</span>
        <span class="text-slate-600">System covers <strong><?php echo $coverageStats['coverage_rate']; ?>%</strong> of students with recommendations</span>
      </li>
      <li class="flex items-start gap-2">
        <span class="text-emerald-600 font-semibold">✓</span>
        <span class="text-slate-600"><strong><?php echo $complianceStats['compliance_rate']; ?>%</strong> of registrations meet prerequisite requirements</span>
      </li>
      <li class="flex items-start gap-2">
        <span class="text-violet-600 font-semibold">✓</span>
        <span class="text-slate-600">Students accept <strong><?php echo $recStats['acceptance_rate']; ?>%</strong> of recommendations</span>
      </li>
    </ul>
  </div>
</div>

<?php include '../partials/shell_close.php'; ?>
