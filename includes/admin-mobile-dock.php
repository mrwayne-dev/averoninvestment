<?php
/* =====================================================================
   includes/admin-mobile-dock.php
   Bottom navigation dock for admin pages — mobile only.
   Mirrors the user mobile-dock.php pattern but with admin nav items.
   ===================================================================== */
$adminCurrentPath = $_SERVER['REQUEST_URI'] ?? '';

$adminDockItems = [
  [
    'label' => 'Overview',
    'href'  => '/admin',
    // house icon
    'icon'  => '<path d="M219.31,108.68l-80-80a16,16,0,0,0-22.62,0l-80,80A15.87,15.87,0,0,0,32,120v96a8,8,0,0,0,8,8h64a8,8,0,0,0,8-8V160h32v56a8,8,0,0,0,8,8h64a8,8,0,0,0,8-8V120A15.87,15.87,0,0,0,219.31,108.68ZM208,208H160V152a8,8,0,0,0-8-8H104a8,8,0,0,0-8,8v56H48V120l80-80,80,80Z"/>',
  ],
  [
    'label' => 'Users',
    'href'  => '/admin/users',
    // users icon
    'icon'  => '<path d="M117.25,157.92a60,60,0,1,0-66.5,0A95.83,95.83,0,0,0,3.53,195.63a8,8,0,1,0,13.4,8.74,80,80,0,0,1,134.14,0,8,8,0,0,0,13.4-8.74A95.83,95.83,0,0,0,117.25,157.92ZM40,108a44,44,0,1,1,44,44A44.05,44.05,0,0,1,40,108Zm210.14,98.7a8,8,0,0,1-11.07-2.33A79.83,79.83,0,0,0,172,168a8,8,0,0,1,0-16,44,44,0,1,0-16.34-84.87,8,8,0,1,1-5.94-14.85,60,60,0,0,1,55.53,105.64,95.83,95.83,0,0,1,47.22,37.71A8,8,0,0,1,250.14,206.7Z"/>',
  ],
  [
    'label' => 'Txns',
    'href'  => '/admin/transactions',
    // receipt icon
    'icon'  => '<path d="M72,104a8,8,0,0,1,8-8h96a8,8,0,0,1,0,16H80A8,8,0,0,1,72,104Zm8,40h96a8,8,0,0,0,0-16H80a8,8,0,0,0,0,16ZM232,56V208a8,8,0,0,1-11.58,7.16L192,200.94l-28.42,14.22a8,8,0,0,1-7.16,0L128,200.94,99.58,215.16a8,8,0,0,1-7.16,0L64,200.94,35.58,215.16A8,8,0,0,1,24,208V56A16,16,0,0,1,40,40H216A16,16,0,0,1,232,56Zm-16,0H40V195.06l20.42-10.22a8,8,0,0,1,7.16,0L96,199.06l28.42-14.22a8,8,0,0,1,7.16,0L160,199.06l28.42-14.22a8,8,0,0,1,7.16,0L216,195.06Z"/>',
  ],
  [
    'label' => 'Plans',
    'href'  => '/admin/investments',
    // chart-line-up icon
    'icon'  => '<path d="M232,208a8,8,0,0,1-8,8H32a8,8,0,0,1-8-8V48a8,8,0,0,1,16,0v94.37L90.73,98a8,8,0,0,1,10.07-.38l58.81,44.11L218.73,90a8,8,0,1,1,10.54,12l-64,56a8,8,0,0,1-10.07.38L96.39,114.29,40,163.63V200H224A8,8,0,0,1,232,208Z"/>',
  ],
  [
    'label' => 'Stats',
    'href'  => '/admin/statistics',
    // chart-bar icon
    'icon'  => '<path d="M224,200h-8V40a8,8,0,0,0-8-8H152a8,8,0,0,0-8,8V80H104a8,8,0,0,0-8,8v40H56a8,8,0,0,0-8,8v64H40a8,8,0,0,0,0,16H224a8,8,0,0,0,0-16ZM160,48h40V200H160ZM112,96h40V200H112ZM64,144h40v56H64Z"/>',
  ],
];

function isAdminDockActive(string $href, string $current): bool {
  $currentClean = strtok($current, '?');
  return rtrim($href, '/') === rtrim($currentClean, '/');
}
?>

<nav class="admin-mobile-dock" id="admin-mobile-dock" aria-label="Admin navigation">
  <ul class="dock-list" role="list">
    <?php foreach ($adminDockItems as $item):
      $active = isAdminDockActive($item['href'], $adminCurrentPath);
    ?>
      <li class="dock-item">
        <a
          href="<?= htmlspecialchars($item['href']) ?>"
          class="dock-link<?= $active ? ' active' : '' ?>"
          aria-label="<?= htmlspecialchars($item['label']) ?>"
          aria-current="<?= $active ? 'page' : 'false' ?>"
        >
          <span class="dock-icon" aria-hidden="true">
            <svg viewBox="0 0 256 256" fill="currentColor" width="22" height="22">
              <?= $item['icon'] ?>
            </svg>
          </span>
          <span class="dock-label"><?= htmlspecialchars($item['label']) ?></span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>

<style>
.admin-mobile-dock {
  display: none;
  position: fixed;
  bottom: var(--space-4);
  left: 50%;
  transform: translateX(-50%);
  width: calc(100% - var(--space-8));
  max-width: 460px;
  z-index: var(--z-dropdown);
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(229, 229, 229, 0.7);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-lg);
  padding: var(--space-2) var(--space-2);
}
/* Reuse .dock-list / .dock-item / .dock-link / .dock-icon / .dock-label
   from mobile-dock.php. If those aren't loaded, define them here. */
.admin-mobile-dock .dock-list {
  list-style: none;
  margin: 0; padding: 0;
  display: flex;
  align-items: stretch;
  width: 100%;
}
.admin-mobile-dock .dock-item {
  flex: 1;
  display: flex;
  align-items: stretch;
}
.admin-mobile-dock .dock-link {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 3px;
  width: 100%;
  padding: var(--space-2) var(--space-1);
  border-radius: var(--radius-lg);
  text-decoration: none;
  color: var(--text-muted);
  transition: color var(--transition-fast), background var(--transition-fast);
  -webkit-tap-highlight-color: transparent;
}
.admin-mobile-dock .dock-link:hover,
.admin-mobile-dock .dock-link.active {
  color: #BA2D0B;
  background: #FDF0EC;
}
.admin-mobile-dock .dock-icon {
  display: flex;
  align-items: center;
  justify-content: center;
}
.admin-mobile-dock .dock-label {
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.02em;
  line-height: 1;
}

@media (max-width: 767px) {
  .admin-mobile-dock { display: flex; align-items: center; }
  /* Push page content up so dock doesn't overlap last row */
  .dashboard-body { padding-bottom: 90px; }
}
</style>
