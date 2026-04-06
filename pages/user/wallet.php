<?php
/* =====================================================================
   pages/user/wallet.php
   Wallet — balance hero card, quick actions, TSLA live chart,
   transaction history with filter tabs + search + CSV export.
   All data populated via dashboard.js / wallet page init.
   ===================================================================== */
require_once '../../includes/auth-guard.php';
require_once '../../includes/icons.php';

$pageTitle = 'Wallet';

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
          <h1 class="topbar-title">Wallet</h1>
          <p class="topbar-date"><?= $todayDisplay ?></p>
        </div>
      </div>

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


      <!-- ── 1. WALLET HERO + TSLA CHART ─────────────── -->
      <div class="dashboard-row wallet-hero-row">

        <!-- Wallet Hero Card -->
        <section aria-label="Wallet balance">
          <div class="wallet-card">
            <div class="wallet-card-label">Total Balance</div>
            <div
              class="wallet-card-balance"
              data-wallet-balance
              aria-live="polite"
              aria-label="Wallet balance"
            >$0.00</div>

            <div class="wallet-card-meta">
              <div class="wallet-card-meta-item">
                <div class="wallet-meta-label">Profit Balance</div>
                <div class="wallet-meta-value" data-wallet-profit aria-live="polite">$0.00</div>
              </div>
              <div class="wallet-card-meta-item">
                <div class="wallet-meta-label">Invested</div>
                <div class="wallet-meta-value" data-wallet-invested aria-live="polite">$0.00</div>
              </div>
            </div>

            <div class="wallet-card-actions">
              <button
                type="button"
                class="wallet-action-btn primary"
                onclick="openModal('modal-deposit')"
                aria-label="Deposit funds"
              >
                <?= ph('download', 16) ?> Deposit
              </button>
              <button
                type="button"
                class="wallet-action-btn ghost"
                onclick="openWithdrawModal()"
                aria-label="Withdraw funds"
              >
                <?= ph('upload', 16) ?> Withdraw
              </button>
              <button
                type="button"
                class="wallet-action-btn ghost"
                onclick="openTransferModal()"
                aria-label="Transfer to another user"
              >
                <?= ph('arrows-left-right', 16) ?> Transfer
              </button>
            </div>
          </div>
        </section>

        <!-- TSLA Live Chart -->
        <section aria-label="TSLA live stock chart">
          <div class="dashboard-section-header">
            <h2 class="dashboard-section-title">TSLA Live Chart</h2>
          </div>
          <div class="tsla-widget-card">
            <div class="tsla-price-row">
              <span class="tsla-price-symbol">NASDAQ: TSLA</span>
              <span class="tsla-price-value" id="tsla-price-val" aria-live="polite">—</span>
              <span class="tsla-price-change" id="tsla-price-change" aria-live="polite"></span>
            </div>
            <div class="tsla-chart-area" id="tsla-chart" aria-label="Tesla stock price chart"></div>
          </div>
        </section>

      </div><!-- /.wallet-hero-row -->


      <!-- ── 2. QUICK STAT CARDS ──────────────────────── -->
      <section class="stats-grid stats-grid--3" aria-label="Wallet statistics">

        <article class="stat-overview-card stat-overview-card--primary">
          <div class="stat-overview-header">
            <span class="stat-overview-label">Wallet Balance</span>
            <div class="stat-overview-icon" aria-hidden="true"><?= ph('wallet', 18) ?></div>
          </div>
          <div class="stat-overview-value" data-wallet-balance aria-live="polite">$0.00</div>
          <div class="stat-overview-change neutral">Available to withdraw</div>
        </article>

        <article class="stat-overview-card stat-overview-card--success">
          <div class="stat-overview-header">
            <span class="stat-overview-label">Total Profit</span>
            <div class="stat-overview-icon" aria-hidden="true"><?= ph('chart-line', 18) ?></div>
          </div>
          <div class="stat-overview-value" data-wallet-profit aria-live="polite">$0.00</div>
          <div class="stat-overview-change neutral">Earned from investments</div>
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


      <!-- ── 3. TRANSACTION HISTORY ──────────────────── -->
      <section class="dashboard-section" aria-label="Transaction history">
        <div class="dashboard-section-header">
          <h2 class="dashboard-section-title">Transaction History</h2>
          <a href="/dashboard/history" class="dashboard-section-action">Full history</a>
        </div>

        <div class="admin-table-wrapper">

          <div class="admin-table-toolbar">
            <div class="filter-tab-group" role="tablist" aria-label="Filter by transaction type">
              <button class="filter-tab active" role="tab" aria-selected="true"  data-filter="all">All</button>
              <button class="filter-tab" role="tab" aria-selected="false" data-filter="deposit">Deposits</button>
              <button class="filter-tab" role="tab" aria-selected="false" data-filter="withdrawal">Withdrawals</button>
              <button class="filter-tab" role="tab" aria-selected="false" data-filter="profit">Profits</button>
              <button class="filter-tab" role="tab" aria-selected="false" data-filter="referral_bonus">Referrals</button>
            </div>
            <div class="admin-table-actions">
              <div class="admin-table-search">
                <?= ph('magnifying-glass', 16) ?>
                <input
                  type="text"
                  id="wallet-tx-search"
                  placeholder="Search transactions…"
                  aria-label="Search transactions"
                  autocomplete="off"
                >
              </div>
              <button type="button" class="btn btn-secondary btn-sm" id="wallet-csv-btn" aria-label="Export to CSV">
                <?= ph('download-simple', 16) ?> Export CSV
              </button>
            </div>
          </div>

          <div class="admin-table-scroll">
            <table class="admin-table" aria-label="Transaction history">
              <thead>
                <tr>
                  <th scope="col">Date</th>
                  <th scope="col">Type</th>
                  <th scope="col">Amount</th>
                  <th scope="col">Status</th>
                  <th scope="col" class="col-hide-mobile">Reference</th>
                  <th scope="col" class="col-hide-mobile">Note</th>
                </tr>
              </thead>
              <tbody id="wallet-tx-body" aria-live="polite">
                <tr id="wallet-tx-empty">
                  <td colspan="6" class="table-empty-cell">No transactions yet</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="admin-table-pagination">
            <span class="pagination-info" id="wallet-pagination-info"></span>
            <div class="pagination-controls" id="wallet-pagination-controls"></div>
          </div>

        </div>
      </section>


    </div><!-- /.dashboard-content -->
  </main>
</div><!-- /.dashboard-layout -->


<?php include '../../includes/mobile-dock.php'; ?>

<?php include '../../includes/modals/deposit-modal.php'; ?>
<?php include '../../includes/modals/withdraw-modal.php'; ?>
<?php include '../../includes/modals/transfer-modal.php'; ?>
<?php include '../../includes/modals/notifications-panel.php'; ?>

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

<script src="https://s3.tradingview.com/tv.js"></script>
<script src="/assets/js/main.js" defer></script>
<script src="/assets/js/dashboard.js" defer></script>

</body>
</html>
