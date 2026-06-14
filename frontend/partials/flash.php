<?php
// Renders a dismissible flash message from ?msg= (success) or ?err= (error).
$msg = $_GET['msg'] ?? '';
$err = $_GET['err'] ?? '';
if ($msg === '' && $err === '') return;
$isError = $err !== '';
$text = $isError ? $err : $msg;
$cls = $isError ? 'bg-rose-50 text-rose-800 border-rose-200' : 'bg-emerald-50 text-emerald-800 border-emerald-200';
?>
<div class="flash no-print mx-4 lg:mx-8 mt-4 flex items-start justify-between gap-3 rounded-lg border px-4 py-3 text-sm <?php echo $cls; ?>">
  <span><?php echo htmlspecialchars($text); ?></span>
  <button type="button" class="flash-close text-current/60 hover:text-current" aria-label="Dismiss">&times;</button>
</div>
