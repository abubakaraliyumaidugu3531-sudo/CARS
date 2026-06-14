<?php
// Shared <head> + design system. Used by every page.
// Expects an optional $pageTitle string set before inclusion.
$pageTitle = $pageTitle ?? 'Course Advisory System';
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle); ?> &middot; CARS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
          colors: {
            brand: {
              50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',
              500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81'
            }
          },
          boxShadow: { card: '0 1px 2px 0 rgb(15 23 42 / 0.04), 0 1px 3px 0 rgb(15 23 42 / 0.08)' }
        }
      }
    }
  </script>
  <style type="text/tailwindcss">
    @layer components {
      .card        { @apply bg-white rounded-xl shadow-card border border-slate-100; }
      .stat        { @apply card p-5 flex items-start justify-between; }
      .stat-value  { @apply text-3xl font-bold text-slate-900 mt-1; }
      .stat-label  { @apply text-sm font-medium text-slate-500; }
      .btn         { @apply inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-brand-300 focus:ring-offset-1 disabled:opacity-50; }
      .btn-primary { @apply btn bg-brand-600 text-white hover:bg-brand-700; }
      .btn-secondary { @apply btn bg-white text-slate-700 border border-slate-300 hover:bg-slate-50; }
      .btn-danger  { @apply btn bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-300; }
      .btn-ghost   { @apply btn text-slate-600 hover:bg-slate-100; }
      .btn-sm      { @apply px-2.5 py-1 text-xs rounded-md; }
      .input       { @apply w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200; }
      .label       { @apply block text-sm font-medium text-slate-700 mb-1; }
      .badge       { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium; }
      .chip        { @apply inline-flex items-center px-2 py-0.5 rounded-md text-xs bg-slate-100 text-slate-600; }
      .table       { @apply min-w-full text-sm; }
      .table thead th { @apply text-left text-xs font-semibold uppercase tracking-wide text-slate-500 px-4 py-3 border-b border-slate-200; }
      .table tbody td { @apply px-4 py-3 border-b border-slate-100 align-middle; }
      .table tbody tr:hover { @apply bg-slate-50/70; }
      .nav-link    { @apply flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-slate-300 hover:bg-white/10 hover:text-white transition; }
      .nav-link-active { @apply bg-white/10 text-white; }
    }
    @media print {
      .no-print { display: none !important; }
      aside, header.topbar { display: none !important; }
      main { padding: 0 !important; }
    }
  </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">
