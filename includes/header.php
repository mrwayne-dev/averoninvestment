<?php
$currentPath = $_SERVER['REQUEST_URI'] ?? '';

$navLinks = [
  ['label' => 'About',       'href' => '/about'],
  ['label' => 'Investments', 'href' => '/investments'],
  ['label' => 'Membership',  'href' => '/membership'],
  ['label' => 'Contact',     'href' => '/contact'],
];

function isNavActive(string $href, string $current): bool {
  // Exact path match — avoids false positives like /admin matching /admin/users
  $currentClean = strtok($current, '?');
  return rtrim($href, '/') === rtrim($currentClean, '/');
}
?>

<header class="site-header" id="site-header">
  <nav class="site-nav container">

    <!-- Logo -->
    <a href="/" class="nav-logo" aria-label="Averon Investment">
      <img src="/assets/images/logo/avernonlogo.png" class="nav-logo-img" alt="Averon Investment" style="height:32px;width:auto;">
    </a>

    <!-- Desktop nav links -->
    <ul class="nav-links" role="list">
      <?php foreach ($navLinks as $link): ?>
        <li>
          <a
            href="<?= htmlspecialchars($link['href']) ?>"
            class="nav-link<?= isNavActive($link['href'], $currentPath) ? ' active' : '' ?>"
          ><?= htmlspecialchars($link['label']) ?></a>
        </li>
      <?php endforeach; ?>
    </ul>

    <!-- Right slot -->
    <div class="nav-actions">
      <!-- Account button (desktop) -->
      <a href="/login" class="nav-account-btn">
        Get Started
      </a>

      <!-- Hamburger (mobile only) -->
      <button class="nav-hamburger" id="nav-hamburger" aria-label="Open navigation" aria-expanded="false" aria-controls="mobile-nav">
        <span class="nav-hamburger-line"></span>
        <span class="nav-hamburger-line"></span>
        <span class="nav-hamburger-line"></span>
      </button>
    </div>

  </nav>
</header>

<!-- Mobile Nav Overlay -->
<div class="mobile-nav-overlay" id="mobile-nav-overlay" aria-hidden="true"></div>

<!-- Mobile Nav Drawer -->
<nav class="mobile-nav" id="mobile-nav" aria-label="Mobile navigation" aria-hidden="true">
  <div class="mobile-nav-header">
    <a href="/" class="nav-logo" aria-label="Averon Investment">
      <img src="/assets/images/logo/avernonlogo.png" alt="Averon Investment" style="height:28px;width:auto;">
    </a>
    <button class="mobile-nav-close" id="mobile-nav-close" aria-label="Close navigation">
      <svg viewBox="0 0 256 256" fill="currentColor" width="20" height="20" aria-hidden="true">
        <path d="M205.66,194.34a8,8,0,0,1-11.32,11.32L128,139.31,61.66,205.66a8,8,0,0,1-11.32-11.32L116.69,128,50.34,61.66A8,8,0,0,1,61.66,50.34L128,116.69l66.34-66.35a8,8,0,0,1,11.32,11.32L139.31,128Z"/>
      </svg>
    </button>
  </div>

  <ul class="mobile-nav-links" role="list">
    <?php foreach ($navLinks as $link): ?>
      <li>
        <a
          href="<?= htmlspecialchars($link['href']) ?>"
          class="mobile-nav-link<?= isNavActive($link['href'], $currentPath) ? ' active' : '' ?>"
        ><?= htmlspecialchars($link['label']) ?></a>
      </li>
    <?php endforeach; ?>
  </ul>

  <div class="mobile-nav-footer">
    <a href="/login" class="btn btn-secondary btn-full">Sign In</a>
    <a href="/register" class="btn btn-primary btn-full">Get Started</a>
  </div>
</nav>
