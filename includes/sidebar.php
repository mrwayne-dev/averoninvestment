<?php
// Usage: set $isAdmin = true for admin sidebar before including
$isAdmin     = isset($isAdmin) && $isAdmin === true;
$currentPath = $_SERVER['REQUEST_URI'] ?? '';

// Nav items
$userNav = [
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
    'label' => 'Investments',
    'href'  => '/dashboard/investments',
    'icon'  => '<path d="M232,208a8,8,0,0,1-8,8H32a8,8,0,0,1-8-8V48a8,8,0,0,1,16,0v94.37L90.73,98a8,8,0,0,1,10.07-.38l58.81,44.11L218.73,90a8,8,0,1,1,10.54,12l-64,56a8,8,0,0,1-10.07.38L96.39,114.29,40,163.63V200H224A8,8,0,0,1,232,208Z"/>',
  ],
  [
    'label' => 'Membership',
    'href'  => '/dashboard/membership',
    'icon'  => '<path d="M248,80a28,28,0,1,0-51.12,15.77l-26.79,33L146,73.4a28,28,0,1,0-36.06,0L85.91,128.74l-26.79-33a28,28,0,1,0-26.6,12L47,194.63A16,16,0,0,0,62.78,208H193.22A16,16,0,0,0,209,194.63l14.47-86.85A28,28,0,0,0,248,80ZM128,40a12,12,0,1,1-12,12A12,12,0,0,1,128,40ZM24,80A12,12,0,1,1,36,92,12,12,0,0,1,24,80ZM193.22,192H62.78L48.86,108.52,81.79,149A8,8,0,0,0,88,152a7.83,7.83,0,0,0,1.08-.07,8,8,0,0,0,6.26-4.74l29.3-67.4a27,27,0,0,0,6.72,0l29.3,67.4a8,8,0,0,0,6.26,4.74A7.83,7.83,0,0,0,168,152a8,8,0,0,0,6.21-3l32.93-40.52ZM220,92a12,12,0,1,1,12-12A12,12,0,0,1,220,92Z"/>',
  ],
  [
    'label' => 'Account',
    'href'  => '/dashboard/account',
    'icon'  => '<path d="M230.92,212c-15.23-26.33-38.7-45.21-66.09-54.16a72,72,0,1,0-73.66,0C63.78,166.78,40.31,185.66,25.08,212a8,8,0,1,0,13.85,8c18.84-32.56,52.14-52,89.07-52s70.23,19.44,89.07,52a8,8,0,1,0,13.85-8ZM72,96a56,56,0,1,1,56,56A56.06,56.06,0,0,1,72,96Z"/>',
  ],
];

$adminNav = [
  [
    'label' => 'Dashboard',
    'href'  => '/admin',
    'icon'  => '<path d="M219.31,108.68l-80-80a16,16,0,0,0-22.62,0l-80,80A15.87,15.87,0,0,0,32,120v96a8,8,0,0,0,8,8h64a8,8,0,0,0,8-8V160h32v56a8,8,0,0,0,8,8h64a8,8,0,0,0,8-8V120A15.87,15.87,0,0,0,219.31,108.68ZM208,208H160V152a8,8,0,0,0-8-8H104a8,8,0,0,0-8,8v56H48V120l80-80,80,80Z"/>',
  ],
  [
    'label' => 'Users',
    'href'  => '/admin/users',
    'icon'  => '<path d="M230.92,212c-15.23-26.33-38.7-45.21-66.09-54.16a72,72,0,1,0-73.66,0C63.78,166.78,40.31,185.66,25.08,212a8,8,0,1,0,13.85,8c18.84-32.56,52.14-52,89.07-52s70.23,19.44,89.07,52a8,8,0,1,0,13.85-8ZM72,96a56,56,0,1,1,56,56A56.06,56.06,0,0,1,72,96Z"/>',
  ],
  [
    'label' => 'Transactions',
    'href'  => '/admin/transactions',
    'icon'  => '<path d="M216,64H56a8,8,0,0,1,0-16H192a8,8,0,0,0,0-16H56A24,24,0,0,0,32,56V184a24,24,0,0,0,24,24H216a16,16,0,0,0,16-16V80A16,16,0,0,0,216,64Zm0,128H56a8,8,0,0,1-8-8V78.63A23.84,23.84,0,0,0,56,80H216Zm-48-60a12,12,0,1,1,12,12A12,12,0,0,1,168,132Z"/>',
  ],
  [
    'label' => 'Investments',
    'href'  => '/admin/investments',
    'icon'  => '<path d="M232,208a8,8,0,0,1-8,8H32a8,8,0,0,1-8-8V48a8,8,0,0,1,16,0v94.37L90.73,98a8,8,0,0,1,10.07-.38l58.81,44.11L218.73,90a8,8,0,1,1,10.54,12l-64,56a8,8,0,0,1-10.07.38L96.39,114.29,40,163.63V200H224A8,8,0,0,1,232,208Z"/>',
  ],
  [
    'label' => 'Membership',
    'href'  => '/admin/membership',
    'icon'  => '<path d="M248,80a28,28,0,1,0-51.12,15.77l-26.79,33L146,73.4a28,28,0,1,0-36.06,0L85.91,128.74l-26.79-33a28,28,0,1,0-26.6,12L47,194.63A16,16,0,0,0,62.78,208H193.22A16,16,0,0,0,209,194.63l14.47-86.85A28,28,0,0,0,248,80ZM128,40a12,12,0,1,1-12,12A12,12,0,0,1,128,40ZM24,80A12,12,0,1,1,36,92,12,12,0,0,1,24,80ZM193.22,192H62.78L48.86,108.52,81.79,149A8,8,0,0,0,88,152a7.83,7.83,0,0,0,1.08-.07,8,8,0,0,0,6.26-4.74l29.3-67.4a27,27,0,0,0,6.72,0l29.3,67.4a8,8,0,0,0,6.26,4.74A7.83,7.83,0,0,0,168,152a8,8,0,0,0,6.21-3l32.93-40.52ZM220,92a12,12,0,1,1,12-12A12,12,0,0,1,220,92Z"/>',
  ],
  [
    'label' => 'Statistics',
    'href'  => '/admin/statistics',
    'icon'  => '<path d="M128,80a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Zm88-29.84q.06-2.16,0-4.32l14.92-18.64a8,8,0,0,0,1.48-7.06,107.21,107.21,0,0,0-10.88-26.25,8,8,0,0,0-6-3.93l-23.72-2.64q-1.48-1.56-3-3L186,40.54a8,8,0,0,0-3.94-6,107.71,107.71,0,0,0-26.25-10.87,8,8,0,0,0-7.06,1.49L130.16,40Q128,40,125.84,40L107.2,25.11a8,8,0,0,0-7.06-1.48A107.6,107.6,0,0,0,73.89,34.51a8,8,0,0,0-3.93,6L67.32,64.27q-1.56,1.49-3,3L40.54,70a8,8,0,0,0-6,3.94,107.71,107.71,0,0,0-10.87,26.25,8,8,0,0,0,1.49,7.06L40,125.84Q40,128,40,130.16L25.11,148.8a8,8,0,0,0-1.48,7.06,107.21,107.21,0,0,0,10.88,26.25,8,8,0,0,0,6,3.93l23.72,2.64q1.49,1.56,3,3L70,215.46a8,8,0,0,0,3.94,6,107.71,107.71,0,0,0,26.25,10.87,8,8,0,0,0,7.06-1.49L125.84,216q2.16.06,4.32,0l18.64,14.92a8,8,0,0,0,7.06,1.48,107.21,107.21,0,0,0,26.25-10.88,8,8,0,0,0,3.93-6l2.64-23.72q1.56-1.48,3-3L215.46,186a8,8,0,0,0,6-3.94,107.71,107.71,0,0,0,10.87-26.25,8,8,0,0,0-1.49-7.06Z"/>',
  ],
];

$navItems    = $isAdmin ? $adminNav : $userNav;
$logoutHref  = $isAdmin ? '/api/auth/admin-logout.php' : '/api/auth/user-logout.php';

function isSidebarActive(string $href, string $current): bool {
  $currentClean = strtok($current, '?');
  return rtrim($href, '/') === rtrim($currentClean, '/');
}
?>

<!-- Mobile overlay -->
<div id="sidebar-overlay" class="sidebar-overlay"></div>

<aside id="dashboard-sidebar" class="dashboard-sidebar<?= $isAdmin ? ' dashboard-sidebar--admin' : '' ?>">

  <!-- Logo -->
  <div class="sidebar-logo-wrap">
    <a href="<?= $isAdmin ? '/admin' : '/dashboard' ?>" class="sidebar-logo-link" aria-label="Averon Investment">
      <img src="/assets/images/logo/avernonlogo.png" alt="Averon Investment" style="height:28px;width:auto;" class="sidebar-logo-img">
      <span class="sidebar-logo-wordmark">Averon Investment</span>
      <?php if ($isAdmin): ?>
        <span class="sidebar-admin-badge">Admin</span>
      <?php endif; ?>
    </a>
  </div>

  <!-- Nav -->
  <nav class="sidebar-nav" aria-label="Dashboard navigation">
    <ul class="sidebar-nav-list" role="list">
      <?php foreach ($navItems as $item): ?>
        <?php $active = isSidebarActive($item['href'], $currentPath); ?>
        <li class="sidebar-nav-item">
          <a
            href="<?= htmlspecialchars($item['href']) ?>"
            class="sidebar-nav-link<?= $active ? ' active' : '' ?>"
            data-nav-link="<?= htmlspecialchars($item['href']) ?>"
            aria-current="<?= $active ? 'page' : 'false' ?>"
          >
            <span class="sidebar-nav-icon" aria-hidden="true">
              <svg viewBox="0 0 256 256" fill="currentColor" width="20" height="20">
                <?= $item['icon'] ?>
              </svg>
            </span>
            <span class="sidebar-nav-label"><?= htmlspecialchars($item['label']) ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>

  <!-- Bottom -->
  <div class="sidebar-footer">
    <button
      class="sidebar-logout-btn"
      data-logout="<?= htmlspecialchars($logoutHref) ?>"
      aria-label="Log out"
    >
      <svg viewBox="0 0 256 256" fill="currentColor" width="18" height="18" aria-hidden="true">
        <path d="M120,216a8,8,0,0,1-8,8H48a8,8,0,0,1-8-8V40a8,8,0,0,1,8-8h64a8,8,0,0,1,0,16H56V208h56A8,8,0,0,1,120,216Zm109.66-93.66-40-40a8,8,0,0,0-11.32,11.32L204.69,120H112a8,8,0,0,0,0,16h92.69l-26.35,26.34a8,8,0,0,0,11.32,11.32l40-40A8,8,0,0,0,229.66,122.34Z"/>
      </svg>
      <span>Log Out</span>
    </button>
    <p class="sidebar-version">v1.0.0</p>
  </div>

</aside>

<style>
.sidebar-overlay {
  display: none;
  position: fixed; inset: 0;
  background: var(--bg-overlay);
  z-index: calc(var(--z-modal) - 1);
}
.sidebar-overlay.active { display: block; }

.dashboard-sidebar {
  position: fixed;
  top: 0; left: 0; bottom: 0;
  width: 240px;
  background: var(--bg-elevated);
  border-right: 1px solid var(--border-color);
  display: flex;
  flex-direction: column;
  z-index: var(--z-modal);
  transition: transform var(--transition-base);
}

.sidebar-logo-wrap {
  padding: var(--space-6) var(--space-5);
  border-bottom: 1px solid var(--border-color);
  display: flex; align-items: center;
}
.sidebar-logo-link {
  display: flex; align-items: center; gap: var(--space-3); text-decoration: none;
}
.sidebar-logo-symbol {
  width: 28px; height: 28px;
  object-fit: contain;
  flex-shrink: 0;
}
.sidebar-logo-wordmark {
  font-size: var(--text-sm);
  font-weight: 700;
  color: var(--text-primary);
  letter-spacing: -0.01em;
  white-space: nowrap;
}
.sidebar-admin-badge {
  font-size: var(--text-xs);
  font-weight: 700;
  background: var(--color-accent);
  color: white;
  padding: 2px 7px;
  border-radius: var(--radius-full);
  letter-spacing: 0.04em;
  text-transform: uppercase;
}

.sidebar-nav { flex: 1; overflow-y: auto; padding: var(--space-4) var(--space-3); }
.sidebar-nav-list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 2px; }

.sidebar-nav-link {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-3) var(--space-3);
  border-radius: var(--radius-md);
  color: var(--text-secondary);
  text-decoration: none;
  font-size: var(--text-sm);
  font-weight: 500;
  transition: background var(--transition-fast), color var(--transition-fast);
}
.sidebar-nav-link:hover {
  background: var(--bg-surface);
  color: var(--text-primary);
}
.sidebar-nav-link.active {
  background: var(--color-primary-light);
  color: var(--color-primary);
  font-weight: 600;
}
.sidebar-nav-icon { display: flex; align-items: center; flex-shrink: 0; }

.sidebar-footer {
  padding: var(--space-4) var(--space-5);
  border-top: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.sidebar-logout-btn {
  display: flex; align-items: center; gap: var(--space-2);
  background: none; border: none; cursor: pointer;
  color: var(--text-muted);
  font-size: var(--text-sm);
  font-family: var(--font-sans);
  padding: var(--space-2) var(--space-2);
  border-radius: var(--radius-sm);
  transition: color var(--transition-fast), background var(--transition-fast);
}
.sidebar-logout-btn:hover { color: var(--color-danger); background: rgba(196,122,43,0.08); }
.sidebar-version { font-size: var(--text-xs); color: var(--text-muted); margin: 0; }

/* Mobile: sidebar hidden off-screen by default */
@media (max-width: 767px) {
  .dashboard-sidebar { transform: translateX(-100%); }
  .dashboard-sidebar.open { transform: translateX(0); }
}
@media (min-width: 768px) {
  .sidebar-overlay { display: none !important; }
}
</style>
