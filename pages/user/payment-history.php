<?php
/* =====================================================================
   pages/user/payment-history.php
   Full paginated transaction history with type/status/date filters,
   search bar, and CSV export.
   All data loaded via JS initPaymentHistoryPage() in dashboard.js.
   ===================================================================== */
require_once '../../includes/auth-guard.php';
require_once '../../includes/icons.php';

$pageTitle = 'Payment History';

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
          <h1 class="topbar-title">Payment History</h1>
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


      <!-- ── FULL TRANSACTION HISTORY TABLE ─────────────── -->
      <section class="dashboard-section" aria-label="Full payment history">
        <div class="dashboard-section-header">
          <h2 class="dashboard-section-title">All Transactions</h2>
          <button type="button" class="btn btn-secondary btn-sm" id="history-csv-btn" aria-label="Export all to CSV">
            <?= ph('download-simple', 16) ?> Export CSV
          </button>
        </div>

        <div class="admin-table-wrapper">

          <!-- Filter Bar -->
          <div class="history-filter-bar" role="search" aria-label="Filter transactions">

            <!-- Type filter -->
            <select
              class="history-filter-select"
              id="history-filter-type"
              aria-label="Filter by transaction type"
            >
              <option value="all">All Types</option>
              <option value="deposit">Deposit</option>
              <option value="withdrawal">Withdrawal</option>
              <option value="profit">Profit</option>
              <option value="membership_fee">Membership Fee</option>
              <option value="referral_bonus">Referral Bonus</option>
            </select>

            <!-- Status filter -->
            <select
              class="history-filter-select"
              id="history-filter-status"
              aria-label="Filter by status"
            >
              <option value="all">All Statuses</option>
              <option value="confirmed">Confirmed</option>
              <option value="pending">Pending</option>
              <option value="failed">Failed</option>
              <option value="cancelled">Cancelled</option>
            </select>

            <!-- Date range -->
            <input
              type="date"
              class="history-date-input"
              id="history-date-from"
              aria-label="From date"
              title="From date"
            >
            <input
              type="date"
              class="history-date-input"
              id="history-date-to"
              aria-label="To date"
              title="To date"
            >

            <!-- Search -->
            <div class="admin-table-search">
              <?= ph('magnifying-glass', 16) ?>
              <input
                type="text"
                id="history-search"
                placeholder="Search by reference or amount…"
                aria-label="Search transactions"
                autocomplete="off"
              >
            </div>

          </div>

          <!-- Table -->
          <div class="admin-table-scroll">
            <table class="admin-table payment-history-table" aria-label="Full transaction history">
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
              <tbody id="history-tx-body" aria-live="polite">
                <tr id="history-tx-empty">
                  <td colspan="6" class="table-empty-cell">No transactions found</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="admin-table-pagination">
            <span class="pagination-info" id="history-pagination-info"></span>
            <div class="pagination-controls" id="history-pagination-controls"></div>
          </div>

        </div>
      </section>


    </div><!-- /.dashboard-content -->
  </main>
</div><!-- /.dashboard-layout -->


<?php include '../../includes/mobile-dock.php'; ?>

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
