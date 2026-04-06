<?php
/* =====================================================================
   pages/admin/reset-password.php — Admin password reset (token-based)
   Standalone page — no public header/footer.

   URL: /pages/admin/reset-password.php?token=<hex64>
   - Missing/blank token → shows an error state immediately.
   - Valid token → shows new-password form.
   - On success → redirects to /admin/login
   ===================================================================== */
require_once '../../config/constants.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>false,'httponly'=>true,'samesite'=>'Strict']);
    session_start();
}

// Already logged in as admin → go to dashboard
if (!empty($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'admin') {
    header('Location: /admin');
    exit;
}

// Grab token from query string (PHP only; validation happens in JS → API)
$token   = trim($_GET['token'] ?? '');
$hasToken = $token !== '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Reset Password — Averon Investment</title>
  <link rel="icon" type="image/png" href="/assets/favicon/favicon-32x32.png" sizes="32x32">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;1,9..40,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body class="admin-auth-body">

<!-- Loader -->
<div id="global-loader" class="loader-overlay" style="display:none" aria-hidden="true">
  <div class="loader-inner">
    <img src="/assets/images/logo/avernonlogo.png" alt="" aria-hidden="true" style="height:40px;width:auto;animation:logoPulse 1.5s ease-in-out infinite;">
    <div class="loader-spinner"></div>
  </div>
</div>
<div id="toast-container" role="status" aria-live="polite"></div>

<div class="admin-auth-layout">

  <!-- Left panel — branding -->
  <div class="admin-auth-left" aria-hidden="true">
    <div class="admin-auth-brand">
      <img src="/assets/images/logo/avernonlogo.png" alt="Averon Investment" style="height:40px;width:auto;">
      <p class="admin-auth-tagline">Secure Admin Portal</p>
    </div>
    <div class="admin-auth-decor">
      <div class="admin-decor-ring admin-decor-ring--1"></div>
      <div class="admin-decor-ring admin-decor-ring--2"></div>
      <div class="admin-decor-ring admin-decor-ring--3"></div>
    </div>
  </div>

  <!-- Right panel -->
  <div class="admin-auth-right">
    <div class="admin-auth-card">

      <?php if (!$hasToken): ?>
      <!-- ── No token: invalid link ─────────────────────────── -->
      <div class="invalid-state" role="alert">
        <div class="invalid-icon-wrap" aria-hidden="true">
          <svg viewBox="0 0 256 256" fill="currentColor" width="48" height="48" style="color:var(--color-accent)">
            <path d="M236.8,188.09,149.35,36.22a24.76,24.76,0,0,0-42.7,0L19.2,188.09a23.51,23.51,0,0,0,0,23.72A24.35,24.35,0,0,0,40.55,224h174.9a24.35,24.35,0,0,0,21.33-12.19A23.51,23.51,0,0,0,236.8,188.09ZM120,104a8,8,0,0,1,16,0v40a8,8,0,0,1-16,0Zm8,88a12,12,0,1,1,12-12A12,12,0,0,1,128,192Z"/>
          </svg>
        </div>
        <h1 class="admin-auth-title">Invalid reset link</h1>
        <p class="admin-auth-subtitle" style="margin-bottom:var(--space-6)">
          This link is missing a reset token. Please request a new password reset email.
        </p>
        <a href="/admin/forgot-password" class="btn-admin-auth" style="display:block;text-decoration:none;text-align:center;">
          Request New Link
        </a>
        <p class="admin-auth-footer">
          Remembered your password? <a href="/admin/login">Back to login</a>
        </p>
      </div>

      <?php else: ?>
      <!-- ── Has token: show reset form ─────────────────────── -->

      <!-- Default: password form -->
      <div id="view-form">
        <div class="admin-auth-card-top">
          <div class="admin-badge-pill">
            <span class="admin-badge-dot"></span>
            ADMIN ACCESS
          </div>
          <h1 class="admin-auth-title">Set new password</h1>
          <p class="admin-auth-subtitle">Choose a strong password for your admin account</p>
        </div>

        <form id="reset-form" novalidate>

          <div class="form-group">
            <label class="form-label" for="password">New Password</label>
            <div class="pw-wrap">
              <input class="form-control" type="password" id="password" name="password"
                     placeholder="Min 8 chars, upper, number, symbol" required autocomplete="new-password">
              <button type="button" class="pw-toggle" id="pw-toggle-1" aria-label="Show password">
                <svg viewBox="0 0 256 256" fill="currentColor" width="18" height="18"><path d="M247.31,124.76c-.35-.79-8.82-19.58-27.65-38.41C194.57,61.26,162.88,48,128,48S61.43,61.26,36.34,86.35C17.51,105.18,9,124,8.69,124.76a8,8,0,0,0,0,6.5c.35.79,8.82,19.57,27.65,38.4C61.43,194.74,93.12,208,128,208s66.57-13.26,91.66-38.34c18.83-18.83,27.3-37.61,27.65-38.4A8,8,0,0,0,247.31,124.76ZM128,192c-30.78,0-57.67-11.19-79.93-33.25A133.47,133.47,0,0,1,25,128,133.33,133.33,0,0,1,48.07,97.25C70.33,75.19,97.22,64,128,64s57.67,11.19,79.93,33.25A133.46,133.46,0,0,1,231.05,128C223.84,141.46,192.43,192,128,192Zm0-112a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Z"/></svg>
              </button>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="confirm_password">Confirm New Password</label>
            <div class="pw-wrap">
              <input class="form-control" type="password" id="confirm_password" name="confirm_password"
                     placeholder="••••••••" required autocomplete="new-password">
              <button type="button" class="pw-toggle" id="pw-toggle-2" aria-label="Show password">
                <svg viewBox="0 0 256 256" fill="currentColor" width="18" height="18"><path d="M247.31,124.76c-.35-.79-8.82-19.58-27.65-38.41C194.57,61.26,162.88,48,128,48S61.43,61.26,36.34,86.35C17.51,105.18,9,124,8.69,124.76a8,8,0,0,0,0,6.5c.35.79,8.82,19.57,27.65,38.4C61.43,194.74,93.12,208,128,208s66.57-13.26,91.66-38.34c18.83-18.83,27.3-37.61,27.65-38.4A8,8,0,0,0,247.31,124.76ZM128,192c-30.78,0-57.67-11.19-79.93-33.25A133.47,133.47,0,0,1,25,128,133.33,133.33,0,0,1,48.07,97.25C70.33,75.19,97.22,64,128,64s57.67,11.19,79.93,33.25A133.46,133.46,0,0,1,231.05,128C223.84,141.46,192.43,192,128,192Zm0-112a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Z"/></svg>
              </button>
            </div>
          </div>

          <!-- Strength hints -->
          <div class="pw-strength-hints">
            <span class="pw-hint-item" id="hint-len">8+ characters</span>
            <span class="pw-hint-item" id="hint-upper">Uppercase letter</span>
            <span class="pw-hint-item" id="hint-num">Number</span>
            <span class="pw-hint-item" id="hint-sym">Special character</span>
          </div>

          <button class="btn-admin-auth" type="submit" id="reset-btn">
            Reset Password
          </button>

        </form>

        <p class="admin-auth-footer">
          Remembered your password? <a href="/admin/login">Back to login</a>
        </p>
      </div>

      <!-- Success view -->
      <div id="view-success" style="display:none; text-align:center;">
        <div class="success-icon-wrap" aria-hidden="true">
          <svg viewBox="0 0 256 256" fill="currentColor" width="48" height="48" style="color:var(--color-accent)">
            <path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/>
          </svg>
        </div>
        <h2 class="sent-title">Password updated!</h2>
        <p class="sent-msg">Your admin password has been changed successfully. You can now sign in with your new password.</p>
        <a href="/admin/login" class="btn-admin-auth" style="display:block;text-decoration:none;text-align:center;">
          Go to Login
        </a>
      </div>

      <?php endif; ?>

    </div>
  </div>

</div>

<script src="/assets/js/main.js"></script>
<script>
(function () {
  'use strict';

  <?php if ($hasToken): ?>
  var RESET_TOKEN = <?php echo json_encode($token); ?>;

  // ── Password toggles ────────────────────────────────────────────
  function bindToggle(btnId, inputId) {
    var btn   = document.getElementById(btnId);
    var input = document.getElementById(inputId);
    if (!btn || !input) return;
    btn.addEventListener('click', function () {
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
  }
  bindToggle('pw-toggle-1', 'password');
  bindToggle('pw-toggle-2', 'confirm_password');

  // ── Live strength hints ─────────────────────────────────────────
  var pwInput   = document.getElementById('password');
  var hintLen   = document.getElementById('hint-len');
  var hintUpper = document.getElementById('hint-upper');
  var hintNum   = document.getElementById('hint-num');
  var hintSym   = document.getElementById('hint-sym');

  function updateHints(val) {
    toggle(hintLen,   val.length >= 8);
    toggle(hintUpper, /[A-Z]/.test(val));
    toggle(hintNum,   /[0-9]/.test(val));
    toggle(hintSym,   /[^A-Za-z0-9]/.test(val));
  }
  function toggle(el, ok) {
    el.classList.toggle('pw-hint-ok',   ok);
    el.classList.toggle('pw-hint-fail', !ok && pwInput.value.length > 0);
  }
  pwInput.addEventListener('input', function () { updateHints(this.value); });

  // ── Reset form submit ───────────────────────────────────────────
  document.getElementById('reset-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    var btn         = document.getElementById('reset-btn');
    var password    = document.getElementById('password').value;
    var confirmPass = document.getElementById('confirm_password').value;

    if (!password)             { showToast('Please enter a new password', 'warning'); return; }
    if (password !== confirmPass) { showToast('Passwords do not match', 'error'); return; }

    btn.disabled    = true;
    btn.textContent = 'Resetting…';

    try {
      await apiRequest('/api/auth/admin-reset-pass.php', 'POST', {
        token:            RESET_TOKEN,
        password:         password,
        confirm_password: confirmPass,
      });

      // Show success view
      document.getElementById('view-form').style.display    = 'none';
      document.getElementById('view-success').style.display = 'block';

    } catch (_) {
      btn.disabled    = false;
      btn.textContent = 'Reset Password';
    }
  });
  <?php endif; ?>
})();
</script>

<style>
/* ── Admin auth shared layout ─────────────────────────────── */
.admin-auth-body {
  margin: 0; padding: 0;
  font-family: var(--font-sans);
  background: var(--bg-surface);
  min-height: 100vh;
}
.admin-auth-layout {
  display: grid;
  grid-template-columns: 420px 1fr;
  min-height: 100vh;
}

/* Left panel */
.admin-auth-left {
  background: #01110A;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: var(--space-12);
}
.admin-auth-brand { text-align: center; position: relative; z-index: 2; }
.admin-brand-logo { width: 140px; margin-bottom: var(--space-5); opacity: 0.95; }
.admin-auth-tagline {
  color: rgba(255,255,255,0.5);
  font-size: var(--text-sm);
  font-weight: 500;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  margin: 0;
}
.admin-auth-decor { position: absolute; inset: 0; pointer-events: none; }
.admin-decor-ring {
  position: absolute;
  border: 1px solid rgba(115,186,155,0.15);
  border-radius: 50%;
  left: 50%; top: 50%;
  transform: translate(-50%, -50%);
}
.admin-decor-ring--1 { width: 300px; height: 300px; }
.admin-decor-ring--2 { width: 480px; height: 480px; border-color: rgba(115,186,155,0.08); }
.admin-decor-ring--3 { width: 680px; height: 680px; border-color: rgba(115,186,155,0.05); }

/* Right panel */
.admin-auth-right {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: var(--space-8) var(--space-6);
  background: var(--bg-surface);
}
.admin-auth-card {
  width: 100%;
  max-width: 420px;
  background: var(--bg-elevated);
  border: 1px solid var(--border-color);
  border-radius: var(--radius-xl);
  padding: var(--space-10) var(--space-8);
  box-shadow: var(--shadow-lg);
}
.admin-auth-card-top { margin-bottom: var(--space-8); }

.admin-badge-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #FFF4E8;
  color: var(--color-accent);
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.1em;
  padding: 4px 12px;
  border-radius: var(--radius-full);
  border: 1px solid rgba(115,186,155,0.2);
  margin-bottom: var(--space-4);
}
.admin-badge-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: var(--color-accent);
  animation: adminDotPulse 2s ease-in-out infinite;
}
@keyframes adminDotPulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50%       { opacity: 0.5; transform: scale(0.7); }
}
.admin-auth-title {
  font-size: var(--text-2xl);
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 var(--space-2);
}
.admin-auth-subtitle { font-size: var(--text-sm); color: var(--text-muted); margin: 0; }

.form-group { margin-bottom: var(--space-5); }
.form-label {
  display: block;
  font-size: var(--text-sm);
  font-weight: 600;
  color: var(--text-secondary);
  margin-bottom: var(--space-2);
}
.form-control {
  width: 100%;
  padding: 11px var(--space-4);
  font-size: var(--text-sm);
  color: var(--text-primary);
  background: var(--bg-base);
  border: 1.5px solid var(--border-color);
  border-radius: var(--radius-md);
  outline: none;
  transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
  font-family: var(--font-sans);
  box-sizing: border-box;
}
.form-control:focus { border-color: var(--color-accent); box-shadow: 0 0 0 3px rgba(115,186,155,0.15); }

.pw-wrap { position: relative; }
.pw-wrap .form-control { padding-right: 44px; }
.pw-toggle {
  position: absolute;
  right: 12px; top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: var(--text-muted);
  padding: 0;
  display: flex;
  align-items: center;
  transition: color var(--transition-fast);
}
.pw-toggle:hover { color: var(--text-primary); }

/* Strength hints row */
.pw-strength-hints {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-2);
  margin: calc(-1 * var(--space-3)) 0 var(--space-5);
}
.pw-hint-item {
  font-size: var(--text-xs);
  padding: 2px 8px;
  border-radius: var(--radius-full);
  background: var(--bg-muted);
  color: var(--text-muted);
  border: 1px solid transparent;
  transition: background var(--transition-fast), color var(--transition-fast);
}
.pw-hint-item.pw-hint-ok   { background: #E8F5E9; color: var(--color-success); border-color: rgba(46,125,50,0.2); }
.pw-hint-item.pw-hint-fail { background: #FFF4E8; color: var(--color-danger);  border-color: rgba(211,47,47,0.2); }

.btn-admin-auth {
  width: 100%;
  padding: 13px var(--space-4);
  font-size: var(--text-base);
  font-weight: 700;
  background: var(--color-accent);
  color: #fff;
  border: none;
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: background var(--transition-fast), opacity var(--transition-fast);
  font-family: var(--font-sans);
  letter-spacing: 0.01em;
  margin-top: var(--space-2);
  box-sizing: border-box;
}
.btn-admin-auth:hover    { background: var(--color-accent-dark); }
.btn-admin-auth:disabled { opacity: 0.6; cursor: not-allowed; }

.admin-auth-footer {
  text-align: center;
  margin-top: var(--space-6);
  font-size: var(--text-sm);
  color: var(--text-muted);
}
.admin-auth-footer a { color: var(--color-primary); text-decoration: none; font-weight: 500; }
.admin-auth-footer a:hover { text-decoration: underline; }

/* Invalid / Success icon blocks */
.invalid-state { text-align: center; padding: var(--space-4) 0; }
.invalid-icon-wrap,
.success-icon-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 80px; height: 80px;
  background: #FFF4E8;
  border-radius: 50%;
  margin: 0 auto var(--space-6);
}
.success-icon-wrap { background: #E8F5E9; }
.success-icon-wrap svg { color: var(--color-success) !important; }

.sent-title {
  font-size: var(--text-xl);
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 var(--space-3);
}
.sent-msg {
  font-size: var(--text-sm);
  color: var(--text-muted);
  line-height: 1.6;
  margin: 0 0 var(--space-6);
}

/* Mobile */
@media (max-width: 768px) {
  .admin-auth-layout { grid-template-columns: 1fr; }
  .admin-auth-left   { display: none; }
  .admin-auth-right  { padding: var(--space-8) var(--space-4); }
  .admin-auth-card   { padding: var(--space-8) var(--space-5); }
}
</style>

</body>
</html>
