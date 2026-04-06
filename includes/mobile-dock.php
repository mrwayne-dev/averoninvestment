<?php
$currentPath = $_SERVER['REQUEST_URI'] ?? '';

$dockItems = [
  [
    'label' => 'Dashboard',
    'href'  => '/dashboard',
    'icon'  => '<path d="M219.31,108.68l-80-80a16,16,0,0,0-22.62,0l-80,80A15.87,15.87,0,0,0,32,120v96a8,8,0,0,0,8,8h64a8,8,0,0,0,8-8V160h32v56a8,8,0,0,0,8,8h64a8,8,0,0,0,8-8V120A15.87,15.87,0,0,0,219.31,108.68ZM208,208H160V152a8,8,0,0,0-8-8H104a8,8,0,0,0-8,8v56H48V120l80-80,80,80Z"/>',
  ],
  [
    'label' => 'Wallet',
    'href'  => '/dashboard/wallet',
    'icon'  => '<path d="M216,64H56a8,8,0,0,1,0-16H192a8,8,0,0,0,0-16H56A24,24,0,0,0,32,56V184a24,24,0,0,0,24,24H216a16,16,0,0,0,16-16V80A16,16,0,0,0,216,64Zm0,128H56a8,8,0,0,1-8-8V78.63A23.84,23.84,0,0,0,56,80H216Zm-48-60a12,12,0,1,1,12,12A12,12,0,0,1,168,132Z"/>',
  ],
  [
    'label' => 'Invest',
    'href'  => '/dashboard/investments',
    'icon'  => '<path d="M232,208a8,8,0,0,1-8,8H32a8,8,0,0,1-8-8V48a8,8,0,0,1,16,0v94.37L90.73,98a8,8,0,0,1,10.07-.38l58.81,44.11L218.73,90a8,8,0,1,1,10.54,12l-64,56a8,8,0,0,1-10.07.38L96.39,114.29,40,163.63V200H224A8,8,0,0,1,232,208Z"/>',
  ],
  [
    'label' => 'Members',
    'href'  => '/dashboard/membership',
    'icon'  => '<path d="M248,80a28,28,0,1,0-51.12,15.77l-26.79,33L146,73.4a28,28,0,1,0-36.06,0L85.91,128.74l-26.79-33a28,28,0,1,0-26.6,12L47,194.63A16,16,0,0,0,62.78,208H193.22A16,16,0,0,0,209,194.63l14.47-86.85A28,28,0,0,0,248,80ZM128,40a12,12,0,1,1-12,12A12,12,0,0,1,128,40ZM24,80A12,12,0,1,1,36,92,12,12,0,0,1,24,80ZM193.22,192H62.78L48.86,108.52,81.79,149A8,8,0,0,0,88,152a7.83,7.83,0,0,0,1.08-.07,8,8,0,0,0,6.26-4.74l29.3-67.4a27,27,0,0,0,6.72,0l29.3,67.4a8,8,0,0,0,6.26,4.74A7.83,7.83,0,0,0,168,152a8,8,0,0,0,6.21-3l32.93-40.52ZM220,92a12,12,0,1,1,12-12A12,12,0,0,1,220,92Z"/>',
  ],
];

function isDockActive(string $href, string $current): bool {
  $currentClean = strtok($current, '?');
  return rtrim($href, '/') === rtrim($currentClean, '/');
}
?>

<nav class="mobile-dock" id="mobile-dock" aria-label="Mobile navigation">
  <ul class="dock-list" role="list">
    <?php foreach ($dockItems as $item):
      $active = isDockActive($item['href'], $currentPath);
    ?>
      <li class="dock-item">
        <a
          href="<?= htmlspecialchars($item['href']) ?>"
          class="dock-link<?= $active ? ' active' : '' ?>"
          aria-label="<?= htmlspecialchars($item['label']) ?>"
          aria-current="<?= $active ? 'page' : 'false' ?>"
          data-nav-link="<?= htmlspecialchars($item['href']) ?>"
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
.mobile-dock {
  display: none; /* shown only on mobile via CSS */
  position: fixed;
  bottom: var(--space-4);
  left: 50%;
  transform: translateX(-50%);
  width: calc(100% - var(--space-8));
  max-width: 420px;
  z-index: var(--z-dropdown);
  background: rgba(220, 236, 228, 0.88);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(229, 229, 229, 0.7);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-lg);
  padding: var(--space-2) var(--space-3);
}
.dock-list {
  list-style: none;
  margin: 0; padding: 0;
  display: flex;
  align-items: stretch;
  width: 100%; /* fill the dock so flex:1 items spread across the full width */
}
.dock-item {
  flex: 1;           /* each tab gets exactly 25% of the dock width */
  display: flex;
  align-items: stretch;
}
.dock-link {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 3px;
  width: 100%;       /* fill dock-item so tap targets are equal-width */
  padding: var(--space-3) var(--space-1);
  border-radius: var(--radius-lg);
  text-decoration: none;
  color: var(--text-muted);
  transition: color var(--transition-fast), background var(--transition-fast);
  -webkit-tap-highlight-color: transparent;
}
.dock-link:hover,
.dock-link.active {
  color: var(--color-primary);
  background: var(--color-primary-light);
}
.dock-icon { display: flex; align-items: center; justify-content: center; }
.dock-label {
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.02em;
  line-height: 1;
}

@media (max-width: 767px) {
  .mobile-dock { display: flex; align-items: center; }
  /* Push page content up so it isn't hidden behind dock */
  body { padding-bottom: 90px; }
}
</style>

<?php include __DIR__ . '/support-chat.php'; ?>
