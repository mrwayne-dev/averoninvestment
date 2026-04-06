<?php
/* =====================================================================
   pages/user/dashboard.php
   Main authenticated user dashboard — layout skeleton only.
   All data values are populated via dashboard.js polling
   (GET /api/user-dashboard/get-overview.php every 30 s).
   No database queries or business logic in this file.
   ===================================================================== */
require_once '../../includes/auth-guard.php';
require_once '../../includes/icons.php';

$pageTitle = 'Dashboard';

// Extra CSS injected by head.php via the $extraCss hook
$extraCss = '
  <link rel="stylesheet" href="/assets/css/dashboard.css">
  <link rel="stylesheet" href="/assets/css/dashboard-responsive.css">
';

// Static display-only date (PHP-side only, no DB query)
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

        <!-- Mobile sidebar toggle (hidden on desktop via CSS) -->
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

        <!-- Welcome heading -->
        <div class="topbar-welcome">
          <h1 class="topbar-title">
            Welcome back,&nbsp;<span class="topbar-username"><?= $authUserName ?></span>
          </h1>
          <p class="topbar-date"><?= $todayDisplay ?></p>
        </div>

      </div><!-- /.dashboard-topbar-left -->

      <div class="dashboard-topbar-right">

        <!-- Notification Bell -->
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
            <span
              class="notif-badge"
              id="notif-badge"
              aria-label="unread notifications"
              aria-live="polite"
            >0</span>
          </button>

          <!-- Notification dropdown -->
          <div
            class="topbar-dropdown"
            id="notif-dropdown"
            role="region"
            aria-label="Notifications"
          >
            <div class="topbar-dropdown-header">
              <span class="topbar-dropdown-title">Notifications</span>
              <button type="button" class="btn btn-ghost btn-sm" id="notif-mark-all-read">
                Mark all read
              </button>
            </div>
            <div class="topbar-dropdown-body" id="notif-list" aria-live="polite">
              <p class="topbar-dropdown-empty">No new notifications</p>
            </div>
            <div class="topbar-dropdown-footer">
              <a href="/dashboard/notifications" class="topbar-dropdown-link">
                View all notifications
              </a>
            </div>
          </div>
        </div><!-- /.topbar-notif-wrap -->

        <!-- User Avatar + Dropdown -->
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

          <!-- Account dropdown menu -->
          <div
            class="topbar-dropdown"
            id="user-dropdown"
            role="menu"
            aria-label="Account menu"
          >
            <a
              href="/dashboard/account"
              class="topbar-dropdown-item"
              role="menuitem"
            >
              <?= ph('user', 16) ?>
              Profile
            </a>
            <div class="topbar-dropdown-divider" role="separator" aria-hidden="true"></div>
            <button
              type="button"
              class="topbar-dropdown-item topbar-dropdown-item--danger"
              role="menuitem"
              data-logout="/api/auth/user-logout.php"
            >
              <?= ph('sign-out', 16) ?>
              Sign Out
            </button>
          </div>
        </div><!-- /.topbar-user-wrap -->

      </div><!-- /.dashboard-topbar-right -->
    </header><!-- /.dashboard-topbar -->


    <!-- ══════════════════════════════════════════════════
         PAGE CONTENT
    ══════════════════════════════════════════════════ -->
    <div class="dashboard-content">


      <!-- ── 1. STATS ROW ─────────────────────────────── -->
      <section class="stats-grid" aria-label="Account overview">

        <!-- Wallet Balance -->
        <article class="stat-overview-card stat-overview-card--primary">
          <div class="stat-overview-header">
            <span class="stat-overview-label">Wallet Balance</span>
            <div class="stat-overview-icon" aria-hidden="true">
              <?= ph('wallet', 18) ?>
            </div>
          </div>
          <div
            class="stat-overview-value"
            data-wallet-balance
            aria-live="polite"
            aria-label="Wallet balance"
          >$0.00</div>
          <div class="stat-overview-change neutral">Available to withdraw</div>
        </article>

        <!-- Total Profit -->
        <article class="stat-overview-card stat-overview-card--success">
          <div class="stat-overview-header">
            <span class="stat-overview-label">Total Profit</span>
            <div class="stat-overview-icon" aria-hidden="true">
              <?= ph('chart-line', 18) ?>
            </div>
          </div>
          <div
            class="stat-overview-value"
            data-wallet-profit
            aria-live="polite"
            aria-label="Total profit earned"
          >$0.00</div>
          <div class="stat-overview-change neutral">Earned from investments</div>
        </article>

        <!-- Amount Invested -->
        <article class="stat-overview-card stat-overview-card--warning">
          <div class="stat-overview-header">
            <span class="stat-overview-label">Amount Invested</span>
            <div class="stat-overview-icon" aria-hidden="true">
              <?= ph('buildings', 18) ?>
            </div>
          </div>
          <div
            class="stat-overview-value"
            data-wallet-invested
            aria-live="polite"
            aria-label="Total amount invested"
          >$0.00</div>
          <div class="stat-overview-change neutral">Across all active plans</div>
        </article>

        <!-- Active Plans -->
        <article class="stat-overview-card stat-overview-card--accent">
          <div class="stat-overview-header">
            <span class="stat-overview-label">Active Plans</span>
            <div class="stat-overview-icon" aria-hidden="true">
              <?= ph('lightning', 18) ?>
            </div>
          </div>
          <div
            class="stat-overview-value"
            data-stat="active_plans"
            aria-live="polite"
            aria-label="Number of active investment plans"
          >0</div>
          <div class="stat-overview-change neutral">Running investments</div>
        </article>

      </section><!-- /.stats-grid -->


      <!-- ── 2. QUICK ACTIONS ──────────────────────────── -->
      <section class="dashboard-section" aria-label="Quick actions">
        <div class="dashboard-section-header">
          <h2 class="dashboard-section-title">Quick Actions</h2>
        </div>

        <div class="quick-actions-row">

          <button
            type="button"
            class="quick-action-btn"
            onclick="openModal('modal-deposit')"
            aria-label="Deposit funds"
          >
            <span class="quick-action-icon quick-action-icon--deposit" aria-hidden="true">
              <?= ph('download', 22) ?>
            </span>
            <span class="quick-action-label">Deposit</span>
          </button>

          <button
            type="button"
            class="quick-action-btn"
            onclick="openWithdrawModal()"
            aria-label="Withdraw funds"
          >
            <span class="quick-action-icon quick-action-icon--withdraw" aria-hidden="true">
              <?= ph('upload', 22) ?>
            </span>
            <span class="quick-action-label">Withdraw</span>
          </button>

          <button
            type="button"
            class="quick-action-btn"
            onclick="openModal('modal-start-investment')"
            aria-label="Start a new investment"
          >
            <span class="quick-action-icon quick-action-icon--invest" aria-hidden="true">
              <?= ph('trend-up', 22) ?>
            </span>
            <span class="quick-action-label">Invest</span>
          </button>

          <button
            type="button"
            class="quick-action-btn"
            onclick="openTransferModal()"
            aria-label="Transfer funds to another user"
          >
            <span class="quick-action-icon quick-action-icon--transfer" aria-hidden="true">
              <?= ph('arrows-left-right', 22) ?>
            </span>
            <span class="quick-action-label">Transfer</span>
          </button>

        </div>
      </section><!-- /.quick-actions -->


      <!-- ── 3. TWO-COLUMN ROW: Membership + Active Investments -->
      <div class="dashboard-row">

        <!-- Membership Status -->
        <section aria-label="Membership status">
          <div class="dashboard-section-header">
            <h2 class="dashboard-section-title">Membership</h2>
            <a href="/dashboard/membership" class="dashboard-section-action">View plans</a>
          </div>

          <div class="membership-status-card">
            <div class="membership-badge-icon" aria-hidden="true">
              <?= ph('crown', 26) ?>
            </div>

            <div class="membership-info">
              <div
                class="membership-tier-name"
                data-membership="tier"
                aria-live="polite"
              >No Membership</div>
              <div
                class="membership-expiry"
                data-membership="expiry"
                aria-live="polite"
              >—</div>
              <div
                class="membership-perks"
                id="membership-perks-list"
                aria-label="Active membership perks"
              >
                <!-- JS populates perks when membership is active -->
              </div>
            </div>

            <button
              type="button"
              class="btn btn-primary btn-sm"
              id="membership-cta-btn"
              aria-label="Upgrade or manage your membership"
            >Upgrade</button>
          </div>
        </section>

        <!-- Active Investments -->
        <section aria-label="Active investments">
          <div class="dashboard-section-header">
            <h2 class="dashboard-section-title">Active Investments</h2>
            <a href="/dashboard/investments" class="dashboard-section-action">View all</a>
          </div>

          <!-- JS replaces this empty state with investment items -->
          <div id="active-investments-list" aria-live="polite">
            <div class="dashboard-empty-state" id="active-investments-empty">
              <?= ph('chart-line', 40) ?>
              <p>No active investments yet.</p>
              <button
                type="button"
                class="btn btn-primary btn-sm"
                onclick="openModal('modal-start-investment')"
              >Start Investing</button>
            </div>
          </div>
        </section>

      </div><!-- /.dashboard-row -->


      <!-- ── 4. LIVE MARKET DATA ──────────────────────── -->
      <section class="dashboard-section" aria-label="Live market data">
        <div class="dashboard-section-header">
          <h2 class="dashboard-section-title">TSLA Live Market Data</h2>
        </div>
        <div style="height:420px;border-radius:var(--radius-lg);overflow:hidden;border:1px solid var(--border-color);">
          <div class="tradingview-widget-container" style="height:100%;width:100%">
            <div class="tradingview-widget-container__widget" style="height:100%;width:100%"></div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
            {
              "autosize": true,
              "symbol": "NASDAQ:TSLA",
              "interval": "D",
              "timezone": "Etc/UTC",
              "theme": "light",
              "style": "1",
              "locale": "en",
              "allow_symbol_change": false,
              "calendar": false,
              "hide_top_toolbar": false,
              "support_host": "https://www.tradingview.com"
            }
            </script>
          </div>
        </div>
      </section>


      <!-- ── 5. RECENT ACTIVITY TABLE ─────────────────── -->
      <section class="dashboard-section" aria-label="Recent transactions">
        <div class="dashboard-section-header">
          <h2 class="dashboard-section-title">Recent Activity</h2>
          <a href="/dashboard/wallet" class="dashboard-section-action">View all</a>
        </div>

        <div class="admin-table-wrapper">
          <div class="admin-table-scroll">
            <table class="admin-table" aria-label="Recent transactions">
              <thead>
                <tr>
                  <th scope="col">Date</th>
                  <th scope="col">Type</th>
                  <th scope="col">Amount</th>
                  <th scope="col">Status</th>
                  <th scope="col" class="col-hide-mobile">Reference</th>
                </tr>
              </thead>
              <tbody
                id="recent-transactions-body"
                data-table="recent-transactions"
                aria-live="polite"
              >
                <!-- JS populates rows from API response -->
                <tr id="recent-transactions-empty">
                  <td colspan="5" class="table-empty-cell">No recent transactions</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </section><!-- /recent activity -->


    </div><!-- /.dashboard-content -->
  </main><!-- /.dashboard-main -->
</div><!-- /.dashboard-layout -->


<!-- ══════════════════════════════════════════════════════
     MOBILE DOCK
══════════════════════════════════════════════════════ -->
<?php include '../../includes/mobile-dock.php'; ?>


<!-- ══════════════════════════════════════════════════════
     MODALS
══════════════════════════════════════════════════════ -->
<?php include '../../includes/modals/deposit-modal.php'; ?>
<?php include '../../includes/modals/withdraw-modal.php'; ?>
<?php include '../../includes/modals/start-investment-modal.php'; ?>
<?php include '../../includes/modals/enroll-membership-modal.php'; ?>
<?php include '../../includes/modals/transfer-modal.php'; ?>
<?php include '../../includes/modals/notifications-panel.php'; ?>

<!-- ══════════════════════════════════════════════════════
     GLOBAL UI  (controlled by main.js — do not move)
══════════════════════════════════════════════════════ -->

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


<!-- ══════════════════════════════════════════════════════
     SCRIPTS  (external only — no inline JS)
══════════════════════════════════════════════════════ -->
<script src="/assets/js/main.js" defer></script>
<script src="/assets/js/dashboard.js" defer></script>

</body>
</html>
