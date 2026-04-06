<?php
// Set default page title if not defined by the including page
if (!isset($pageTitle)) $pageTitle = 'Averon Investment';
$fullTitle = ($pageTitle === 'Averon Investment') ? $pageTitle : $pageTitle . ' — Averon Investment';
?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="Averon Investment — Premium investment platform for intelligent wealth growth.">

  <title><?= htmlspecialchars($fullTitle, ENT_QUOTES, 'UTF-8') ?></title>

    <!-- Favicon -->
  <link rel="icon" type="image/png" href="/assets/favicon/favicon-32x32.png" sizes="32x32">
  <link rel="shortcut icon" href="/assets/favicon/favicon.ico">
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/favicon/apple-touch-icon.png">
  <meta name="apple-mobile-web-app-title" content="Averon Investment">
  <link rel="manifest" href="/assets/favicon/site.webmanifest">


  <!-- Google Fonts — DM Sans (Grotesk) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;1,9..40,400&display=swap" rel="stylesheet">

  <!-- Core CSS -->
  <link rel="stylesheet" href="/assets/css/main.css">
  <link rel="stylesheet" href="/assets/css/responsive.css">

  <!-- i18n — load early so DOMContentLoaded translations fire before paint -->
  <script src="/assets/js/translations.js" defer></script>

  <!-- CSRF token — present only on authenticated dashboard pages -->
  <?php if (!empty($csrfToken)): ?>
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
  <?php endif; ?>

  <?php if (isset($extraCss)): ?>
    <?= $extraCss ?>
  <?php endif; ?>
</head>
