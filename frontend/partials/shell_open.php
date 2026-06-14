<?php
// Authenticated app shell: head + sidebar + topbar + flash, then opens <main>.
// A page sets $pageTitle, then: include shell_open.php; ...content...; include shell_close.php;
include __DIR__ . '/head.php';
?>
<div class="min-h-screen md:flex">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <div class="flex-1 flex flex-col min-w-0">
    <?php include __DIR__ . '/topbar.php'; ?>
    <?php include __DIR__ . '/flash.php'; ?>
    <main class="flex-1 p-4 lg:p-8">
