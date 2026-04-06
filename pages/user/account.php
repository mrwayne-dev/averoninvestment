<?php
/* =====================================================================
   pages/user/account.php
   Account — profile card, edit profile form, change password,
   notification settings toggles, 2FA placeholder, danger zone,
   referral code display.
   All data loaded / submitted via JS initAccountPage() in dashboard.js.
   ===================================================================== */
require_once '../../includes/auth-guard.php';
require_once '../../includes/icons.php';

$pageTitle = 'Account';

$extraCss = '
  <link rel="stylesheet" href="/assets/css/dashboard.css">
  <link rel="stylesheet" href="/assets/css/dashboard-responsive.css">
';

$todayDisplay = date('l, F j, Y');

// Initial avatar letter (PHP-side for first render before JS loads)
$avatarLetter = mb_strtoupper(mb_substr($authUserName, 0, 1, 'UTF-8'), 'UTF-8');
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
          <h1 class="topbar-title">Account</h1>
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
            <span class="topbar-avatar" aria-hidden="true"><?= $avatarLetter ?></span>
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


      <!-- ── 1. PROFILE HERO CARD ─────────────────────── -->
      <div class="profile-card" aria-label="User profile summary">
        <div class="profile-avatar-large" id="profile-avatar-large" aria-hidden="true">
          <?= $avatarLetter ?>
        </div>
        <div>
          <div class="profile-card-name" id="profile-full-name"><?= htmlspecialchars($authUserName) ?></div>
          <div class="profile-card-email" id="profile-email-display">—</div>
          <div class="profile-card-meta">
            <span class="profile-card-tag" id="profile-member-since">
              <?= ph('calendar', 12) ?> Member since —
            </span>
            <span class="profile-card-tag" data-membership="tier" aria-live="polite">
              <?= ph('crown', 12) ?> No Membership
            </span>
          </div>
        </div>
      </div>


      <!-- ── 2. EDIT PROFILE FORM ──────────────────────── -->
      <section class="settings-section" aria-label="Edit profile">
        <h2 class="settings-section-title" data-i18n="account.profile">Profile Information</h2>

        <form id="profile-form" novalidate aria-label="Edit profile form">

          <div class="settings-field-row">
            <div>
              <div class="settings-field-label">First Name</div>
              <div class="settings-field-desc">Your given name</div>
            </div>
            <div class="form-group">
              <input
                type="text"
                name="first_name"
                class="form-input"
                placeholder="First name"
                autocomplete="given-name"
                aria-label="First name"
              >
            </div>
          </div>

          <div class="settings-field-row">
            <div>
              <div class="settings-field-label">Last Name</div>
              <div class="settings-field-desc">Your family name</div>
            </div>
            <div class="form-group">
              <input
                type="text"
                name="last_name"
                class="form-input"
                placeholder="Last name"
                autocomplete="family-name"
                aria-label="Last name"
              >
            </div>
          </div>

          <div class="settings-field-row">
            <div>
              <div class="settings-field-label">Email</div>
              <div class="settings-field-desc">Login email — contact support to change</div>
            </div>
            <div class="form-group">
              <input
                type="email"
                name="email"
                class="form-input"
                placeholder="Email address"
                autocomplete="email"
                aria-label="Email address"
                readonly
              >
            </div>
          </div>

          <div class="settings-field-row">
            <div>
              <div class="settings-field-label">Phone</div>
              <div class="settings-field-desc">Optional — used for account recovery</div>
            </div>
            <div class="form-group">
              <input
                type="tel"
                name="phone"
                class="form-input"
                placeholder="+1 555 000 0000"
                autocomplete="tel"
                aria-label="Phone number"
              >
            </div>
          </div>

          <div class="settings-field-row">
            <div>
              <div class="settings-field-label">Country / Region</div>
              <div class="settings-field-desc">Your country of residence</div>
            </div>
            <div class="form-group">
              <input
                type="text"
                name="country"
                class="form-input"
                placeholder="e.g. United States"
                autocomplete="country-name"
                aria-label="Country or region"
              >
            </div>
          </div>

          <div class="settings-field-row">
            <div>
              <div class="settings-field-label">Language</div>
              <div class="settings-field-desc">Preferred display language</div>
            </div>
            <div class="form-group">
              <select name="language" class="form-input" aria-label="Preferred language">
                <option value="en">English</option>
                <option value="es">Spanish</option>
                <option value="fr">French</option>
                <option value="de">German</option>
                <option value="pt">Portuguese</option>
                <option value="zh">Chinese</option>
                <option value="ar">Arabic</option>
              </select>
            </div>
          </div>

          <div class="settings-field-row">
            <div></div>
            <div>
              <button type="submit" class="btn btn-primary" aria-label="Save profile changes">
                <?= ph('check', 16) ?> <span data-i18n="account.save">Save Changes</span>
              </button>
            </div>
          </div>

        </form>
      </section>


      <!-- ── 3. CHANGE PASSWORD ────────────────────────── -->
      <section class="settings-section" aria-label="Change password">
        <h2 class="settings-section-title" data-i18n="account.password">Change Password</h2>

        <form id="password-form" novalidate aria-label="Change password form">

          <div class="settings-field-row">
            <div>
              <div class="settings-field-label">Current Password</div>
              <div class="settings-field-desc">Required to verify your identity</div>
            </div>
            <div class="form-group">
              <div class="password-wrap">
                <input
                  type="password"
                  name="current_password"
                  class="form-input"
                  placeholder="Current password"
                  autocomplete="current-password"
                  aria-label="Current password"
                >
                <button type="button" class="password-toggle" aria-label="Show password"></button>
              </div>
            </div>
          </div>

          <div class="settings-field-row">
            <div>
              <div class="settings-field-label">New Password</div>
              <div class="settings-field-desc">Min 8 characters — mix letters, numbers &amp; symbols</div>
            </div>
            <div class="form-group">
              <div class="password-wrap">
                <input
                  type="password"
                  name="new_password"
                  class="form-input"
                  placeholder="New password"
                  autocomplete="new-password"
                  aria-label="New password"
                >
                <button type="button" class="password-toggle" aria-label="Show password"></button>
              </div>
              <!-- Password strength meter -->
              <div class="pwd-strength-wrap" aria-live="polite">
                <div class="pwd-strength-track">
                  <div class="pwd-strength-bar" id="pwd-strength-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <span class="pwd-strength-text" id="pwd-strength-text"></span>
              </div>
            </div>
          </div>

          <div class="settings-field-row">
            <div>
              <div class="settings-field-label">Confirm New Password</div>
            </div>
            <div class="form-group">
              <div class="password-wrap">
                <input
                  type="password"
                  name="confirm_password"
                  class="form-input"
                  placeholder="Confirm new password"
                  autocomplete="new-password"
                  aria-label="Confirm new password"
                >
                <button type="button" class="password-toggle" aria-label="Show password"></button>
              </div>
            </div>
          </div>

          <div class="settings-field-row">
            <div></div>
            <div>
              <button type="submit" class="btn btn-primary" aria-label="Change password">
                <?= ph('lock-key', 16) ?> <span data-i18n="account.update_pwd">Update Password</span>
              </button>
            </div>
          </div>

        </form>
      </section>


      <!-- ── 4. NOTIFICATION SETTINGS ─────────────────── -->
      <section class="settings-section" aria-label="Notification preferences">
        <h2 class="settings-section-title" data-i18n="account.notifs">Notifications</h2>

        <div class="settings-field-row">
          <div>
            <div class="settings-field-label">Deposit Confirmed</div>
            <div class="settings-field-desc">Email when a deposit is successfully confirmed</div>
          </div>
          <label class="toggle-switch" aria-label="Toggle deposit confirmed notifications">
            <input type="checkbox" name="notif_deposit_confirmed" class="toggle-switch-input notif-toggle" checked>
            <span class="toggle-switch-slider"></span>
          </label>
        </div>

        <div class="settings-field-row">
          <div>
            <div class="settings-field-label">Withdrawal Processed</div>
            <div class="settings-field-desc">Email when a withdrawal request is processed</div>
          </div>
          <label class="toggle-switch" aria-label="Toggle withdrawal processed notifications">
            <input type="checkbox" name="notif_withdrawal_processed" class="toggle-switch-input notif-toggle" checked>
            <span class="toggle-switch-slider"></span>
          </label>
        </div>

        <div class="settings-field-row">
          <div>
            <div class="settings-field-label">Profit Credited</div>
            <div class="settings-field-desc">Email when daily profit is credited to your account</div>
          </div>
          <label class="toggle-switch" aria-label="Toggle profit credited notifications">
            <input type="checkbox" name="notif_profit_credited" class="toggle-switch-input notif-toggle">
            <span class="toggle-switch-slider"></span>
          </label>
        </div>

        <div class="settings-field-row">
          <div>
            <div class="settings-field-label">Investment Completed</div>
            <div class="settings-field-desc">Email when an investment plan reaches maturity</div>
          </div>
          <label class="toggle-switch" aria-label="Toggle investment completed notifications">
            <input type="checkbox" name="notif_investment_completed" class="toggle-switch-input notif-toggle" checked>
            <span class="toggle-switch-slider"></span>
          </label>
        </div>

        <div class="settings-field-row">
          <div>
            <div class="settings-field-label">Security Alerts</div>
            <div class="settings-field-desc">Email on new login or password change (recommended)</div>
          </div>
          <label class="toggle-switch" aria-label="Toggle security alert notifications">
            <input type="checkbox" name="notif_security_alerts" class="toggle-switch-input notif-toggle" checked>
            <span class="toggle-switch-slider"></span>
          </label>
        </div>

      </section>


      <!-- ── 5. TWO-FACTOR AUTHENTICATION (Placeholder) ── -->
      <section class="settings-section" aria-label="Two-factor authentication">
        <h2 class="settings-section-title">Two-Factor Authentication</h2>

        <div class="settings-field-row">
          <div>
            <div class="settings-field-label">2FA Status</div>
            <div class="settings-field-desc">Add an extra layer of security to your account</div>
          </div>
          <div>
            <span class="status-badge status-badge--pending">Coming Soon</span>
          </div>
        </div>

      </section>


      <!-- ── 7. DANGER ZONE ──────────────────────────────── -->
      <div class="danger-zone" role="region" aria-label="Danger zone">
        <div class="danger-zone-title">
          <?= ph('warning', 16) ?> Danger Zone
        </div>
        <div class="danger-zone-desc">
          Permanently deleting your account will erase all data and investments. This action is irreversible.
          Our support team will review your request and process it within 72 hours.
        </div>
        <button
          type="button"
          class="btn btn-danger btn-sm"
          onclick="openModal('modal-delete-account')"
          aria-label="Request account deletion"
        >
          <?= ph('trash', 14) ?> Request Account Deletion
        </button>
      </div>


    </div><!-- /.dashboard-content -->
  </main>
</div><!-- /.dashboard-layout -->


<?php include '../../includes/mobile-dock.php'; ?>

<?php include '../../includes/modals/delete-account-modal.php'; ?>

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
