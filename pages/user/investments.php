<?php
/* =====================================================================
   pages/user/investments.php
   Investments — stat cards, available investment plans grid,
   user's active investments table with progress bars and status badges.
   All data loaded via JS initInvestmentsPage() in dashboard.js.
   ===================================================================== */
require_once '../../includes/auth-guard.php';
require_once '../../includes/icons.php';

$pageTitle = 'Investments';

$extraCss = '
  <link rel="stylesheet" href="/assets/css/dashboard.css">
  <link rel="stylesheet" href="/assets/css/dashboard-responsive.css">
';

$todayDisplay = date('l, F j, Y');
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../../includes/head.php'; ?>

<body class="dashboard-body">

<?php include '../../includes/sidebar.php'; ?>

<div class="dashboard-layout">
  <main class="dashboard-main" id="dashboard-main">

    <!-- ══════════════════════════════════════════════════
         TOPBAR
    ══════════════════════════════════════════════════ -->
    <header class="dashboard-topbar" role="banner">

      <div class="dashboard-topbar-left">
        <button
          class="sidebar-toggle"
          id="sidebar-toggle"
          type="button"
          aria-label="Toggle navigation sidebar"
          aria-expanded="false"
          aria-controls="dashboard-sidebar"
        >
          <?= ph('list', 20) ?>
        </button>
        <div class="topbar-welcome">
          <h1 class="topbar-title">Investments</h1>
          <p class="topbar-date"><?= $todayDisplay ?></p>
        </div>
      </div>

      <div class="dashboard-topbar-right">

        <div class="topbar-notif-wrap">
          <button
            class="topbar-icon-btn"
            id="notif-btn"
            type="button"
            aria-label="Notifications"
            aria-expanded="false"
            aria-controls="notif-dropdown"
            aria-haspopup="true"
          >
            <?= ph('bell', 18) ?>
            <span class="notif-badge" id="notif-badge" aria-label="unread notifications" aria-live="polite">0</span>
          </button>

          <div class="topbar-dropdown" id="notif-dropdown" role="region" aria-label="Notifications">
            <div class="topbar-dropdown-header">
              <span class="topbar-dropdown-title">Notifications</span>
              <button type="button" class="btn btn-ghost btn-sm" id="notif-mark-all-read">Mark all read</button>
            </div>
            <div class="topbar-dropdown-body" id="notif-list" aria-live="polite">
              <p class="topbar-dropdown-empty">No new notifications</p>
            </div>
            <div class="topbar-dropdown-footer">
              <a href="/dashboard/notifications" class="topbar-dropdown-link">View all notifications</a>
            </div>
          </div>
        </div>

        <div class="topbar-user-wrap">
          <button
            class="topbar-user-btn"
            id="topbar-user-btn"
            type="button"
            aria-label="Account menu"
            aria-expanded="false"
            aria-controls="user-dropdown"
            aria-haspopup="true"
          >
            <span class="topbar-avatar" aria-hidden="true">
              <?= mb_strtoupper(mb_substr($authUserName, 0, 1, 'UTF-8'), 'UTF-8') ?>
            </span>
            <span class="topbar-username-label"><?= $authUserName ?></span>
            <?= ph('caret-down', 14) ?>
          </button>

          <div class="topbar-dropdown" id="user-dropdown" role="menu" aria-label="Account menu">
            <a href="/dashboard/account" class="topbar-dropdown-item" role="menuitem">
              <?= ph('user', 16) ?> Profile
            </a>
            <div class="topbar-dropdown-divider" role="separator" aria-hidden="true"></div>
            <button
              type="button"
              class="topbar-dropdown-item topbar-dropdown-item--danger"
              role="menuitem"
              data-logout="/api/auth/user-logout.php"
            >
              <?= ph('sign-out', 16) ?> Sign Out
            </button>
          </div>
        </div>

      </div>
    </header>


    <!-- ══════════════════════════════════════════════════
         PAGE CONTENT
    ══════════════════════════════════════════════════ -->
    <div class="dashboard-content">


      <!-- ── 1. STAT CARDS ──────────────────────────────── -->
      <section class="stats-grid stats-grid--2" aria-label="Investment overview">

        <article class="stat-overview-card stat-overview-card--accent">
          <div class="stat-overview-header">
            <span class="stat-overview-label">Active Plans</span>
            <div class="stat-overview-icon" aria-hidden="true"><?= ph('lightning', 18) ?></div>
          </div>
          <div
            class="stat-overview-value"
            data-stat="active_plans"
            aria-live="polite"
            aria-label="Number of active investment plans"
          >0</div>
          <div class="stat-overview-change neutral">Running investments</div>
        </article>

        <article class="stat-overview-card stat-overview-card--warning">
          <div class="stat-overview-header">
            <span class="stat-overview-label">Amount Invested</span>
            <div class="stat-overview-icon" aria-hidden="true"><?= ph('buildings', 18) ?></div>
          </div>
          <div class="stat-overview-value" data-wallet-invested aria-live="polite">$0.00</div>
          <div class="stat-overview-change neutral">Across all active plans</div>
        </article>

      </section>


      <!-- ── 2. AVAILABLE INVESTMENT PLANS ──────────────── -->
      <section class="dashboard-section" aria-label="Available investment plans">
        <div class="dashboard-section-header">
          <h2 class="dashboard-section-title">Investment Plans</h2>
        </div>

        <!-- JS populates cards here -->
        <div class="investment-plan-grid" id="investment-plans-grid" aria-live="polite">
          <div class="dashboard-empty-state" id="plans-loading-state">
            <?= ph('chart-line', 40) ?>
            <p>Loading plans…</p>
          </div>
        </div>
      </section>


      <!-- ── 3. MY INVESTMENTS TABLE ─────────────────────── -->
      <section class="dashboard-section" aria-label="My investments">
        <div class="dashboard-section-header">
          <h2 class="dashboard-section-title">My Investments</h2>
          <button
            type="button"
            class="btn btn-primary btn-sm"
            onclick="openModal('modal-start-investment')"
            aria-label="Start a new investment"
          >
            <?= ph('plus', 14) ?> New Investment
          </button>
        </div>

        <div class="admin-table-wrapper">
          <div class="admin-table-scroll">
            <table class="admin-table" aria-label="My active investments">
              <thead>
                <tr>
                  <th scope="col">Plan</th>
                  <th scope="col">Amount</th>
                  <th scope="col">Start Date</th>
                  <th scope="col">Progress</th>
                  <th scope="col" class="col-hide-mobile">Profit Earned</th>
                  <th scope="col">Status</th>
                </tr>
              </thead>
              <tbody id="my-investments-body" aria-live="polite">
                <tr id="my-investments-empty">
                  <td colspan="6" class="table-empty-cell">
                    No active investments yet.
                    <button
                      type="button"
                      class="btn btn-primary btn-sm"
                      onclick="openModal('modal-start-investment')"
                    >Start Investing</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </section>


    </div><!-- /.dashboard-content -->
  </main>
</div><!-- /.dashboard-layout -->


<?php include '../../includes/mobile-dock.php'; ?>

<?php include '../../includes/modals/deposit-modal.php'; ?>
<?php include '../../includes/modals/start-investment-modal.php'; ?>

<div id="toast-container" role="status" aria-live="polite" aria-atomic="false"></div>

<div id="global-loader" class="loader-overlay" style="display:none" aria-hidden="true">
  <div class="loader-inner">
    <div class="loader-symbol-wrap">
      <svg class="loader-ring" viewBox="0 0 88 88" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <circle class="loader-ring-track" cx="44" cy="44" r="40"/>
        <circle class="loader-ring-arc"   cx="44" cy="44" r="40"/>
      </svg>
      <img src="/assets/images/logo/avernonlogo.png" alt="" aria-hidden="true" style="height:40px;width:auto;animation:logoPulse 1.5s ease-in-out infinite;">
    </div>
  </div>
</div>

<script src="/assets/js/main.js" defer></script>
<script src="/assets/js/dashboard.js" defer></script>

</body>
</html>
