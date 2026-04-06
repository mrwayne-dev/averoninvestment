<?php
/* =====================================================================
   pages/user/membership.php
   Membership — current status card + all available membership plans.
   All data loaded via JS initMembershipPage() in dashboard.js.
   ===================================================================== */
require_once '../../includes/auth-guard.php';
require_once '../../includes/icons.php';

$pageTitle = 'Membership';

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
          <h1 class="topbar-title">Membership</h1>
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


      <!-- ── 1. CURRENT MEMBERSHIP STATUS ───────────────── -->
      <section class="dashboard-section" aria-label="Current membership">
        <div class="dashboard-section-header">
          <h2 class="dashboard-section-title">Your Membership</h2>
        </div>

        <div class="membership-status-card" id="membership-status-card">
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
            >Unlock benefits by choosing a plan below</div>
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


      <!-- ── 2. AVAILABLE MEMBERSHIP PLANS ──────────────── -->
      <section class="dashboard-section" aria-label="Available membership plans">
        <div class="dashboard-section-header">
          <h2 class="dashboard-section-title">Choose a Plan</h2>
        </div>

        <!-- JS populates plan cards here -->
        <div class="membership-plan-grid" id="membership-plans-grid" aria-live="polite">
          <div class="dashboard-empty-state" id="membership-plans-loading">
            <?= ph('crown', 40) ?>
            <p>Loading membership plans…</p>
          </div>
        </div>
      </section>


      <!-- ── 3. MEMBERSHIP BENEFITS COMPARISON ──────────── -->
      <section class="dashboard-section" aria-label="Benefits comparison">
        <div class="dashboard-section-header">
          <h2 class="dashboard-section-title">Benefits Overview</h2>
        </div>

        <div class="admin-table-wrapper">
          <div class="admin-table-scroll">
            <table class="admin-table" aria-label="Membership benefits comparison">
              <thead>
                <tr>
                  <th scope="col">Feature</th>
                  <th scope="col">Basic</th>
                  <th scope="col">Silver</th>
                  <th scope="col">Gold</th>
                  <th scope="col">Platinum</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="td-primary">Monthly Fee</td>
                  <td>$49</td>
                  <td>$99</td>
                  <td>$199</td>
                  <td>$499</td>
                </tr>
                <tr>
                  <td class="td-primary">Max Investments</td>
                  <td>2</td>
                  <td>5</td>
                  <td>10</td>
                  <td>Unlimited</td>
                </tr>
                <tr>
                  <td class="td-primary">Withdrawal Speed</td>
                  <td>72 hours</td>
                  <td>24 hours</td>
                  <td>12 hours</td>
                  <td>1 hour</td>
                </tr>
                <tr>
                  <td class="td-primary">Referral Commission</td>
                  <td>3%</td>
                  <td>5%</td>
                  <td>7%</td>
                  <td>10%</td>
                </tr>
                <tr>
                  <td class="td-primary">Priority Support</td>
                  <td>
                    <span class="status-badge status-badge--cancelled">Standard</span>
                  </td>
                  <td>
                    <span class="status-badge status-badge--active">Priority</span>
                  </td>
                  <td>
                    <span class="status-badge status-badge--active">Dedicated</span>
                  </td>
                  <td>
                    <span class="status-badge status-badge--confirmed">Manager</span>
                  </td>
                </tr>
                <tr>
                  <td class="td-primary">Analytics Access</td>
                  <td>
                    <span class="status-badge status-badge--cancelled">No</span>
                  </td>
                  <td>
                    <span class="status-badge status-badge--cancelled">No</span>
                  </td>
                  <td>
                    <span class="status-badge status-badge--confirmed">Yes</span>
                  </td>
                  <td>
                    <span class="status-badge status-badge--confirmed">Yes</span>
                  </td>
                </tr>
                <tr>
                  <td class="td-primary">Elite Plan Access</td>
                  <td>
                    <span class="status-badge status-badge--cancelled">No</span>
                  </td>
                  <td>
                    <span class="status-badge status-badge--cancelled">No</span>
                  </td>
                  <td>
                    <span class="status-badge status-badge--confirmed">Yes</span>
                  </td>
                  <td>
                    <span class="status-badge status-badge--confirmed">Yes</span>
                  </td>
                </tr>
                <tr>
                  <td class="td-primary">Strategy Reports</td>
                  <td>
                    <span class="status-badge status-badge--cancelled">No</span>
                  </td>
                  <td>
                    <span class="status-badge status-badge--cancelled">No</span>
                  </td>
                  <td>
                    <span class="status-badge status-badge--cancelled">No</span>
                  </td>
                  <td>
                    <span class="status-badge status-badge--confirmed">Quarterly</span>
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
<?php include '../../includes/modals/enroll-membership-modal.php'; ?>

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
