<?php
/* =====================================================================
   pages/admin/login.php — Admin login (standalone, no public header)
   If already authenticated as admin, redirect immediately.
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

$pageTitle = 'Admin Login';
$reason    = $_GET['reason'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — Averon Investment</title>
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
    <div style="display: flex; align-items: center; justify-content:center; flex-direction:column;" class="admin-auth-brand">
      <img src="/assets/images/logo/avernonlogo.png" alt="Averon Investment" style="height:40px;width:auto;">
      <p style="margin-top: 10px;" class="admin-auth-tagline">Secure Admin Portal</p>
    </div>
    <div class="admin-auth-decor">
      <div class="admin-decor-ring admin-decor-ring--1"></div>
      <div class="admin-decor-ring admin-decor-ring--2"></div>
      <div class="admin-decor-ring admin-decor-ring--3"></div>
    </div>
  </div>

  <!-- Right panel — form -->
  <div class="admin-auth-right">
    <div class="admin-auth-card">

      <div class="admin-auth-card-top">
        <div class="admin-badge-pill">
          <span class="admin-badge-dot"></span>
          ADMIN ACCESS
        </div>
        <h1 class="admin-auth-title">Sign in to Admin</h1>
        <p class="admin-auth-subtitle">Restricted to authorised administrators only</p>
      </div>

      <?php if ($reason === 'timeout'): ?>
      <div class="admin-alert admin-alert--warning" role="alert">
        Your session expired due to inactivity. Please sign in again.
      </div>
      <?php endif; ?>

      <form id="admin-login-form" novalidate>

        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input class="form-control" type="email" id="email" name="email"
                 placeholder="admin@example.com" required autocomplete="email">
        </div>

        <div class="form-group">
          <div class="form-label-row">
            <label class="form-label" for="password">Password</label>
            <a href="/admin/forgot-password" class="admin-forgot-link">Forgot password?</a>
          </div>
          <div class="pw-wrap">
            <input class="form-control" type="password" id="password" name="password"
                   placeholder="••••••••" required autocomplete="current-password">
            <button type="button" class="pw-toggle" id="pw-toggle" aria-label="Toggle password visibility">
              <svg id="pw-eye" viewBox="0 0 256 256" fill="currentColor" width="18" height="18"><path d="M247.31,124.76c-.35-.79-8.82-19.58-27.65-38.41C194.57,61.26,162.88,48,128,48S61.43,61.26,36.34,86.35C17.51,105.18,9,124,8.69,124.76a8,8,0,0,0,0,6.5c.35.79,8.82,19.57,27.65,38.4C61.43,194.74,93.12,208,128,208s66.57-13.26,91.66-38.34c18.83-18.83,27.3-37.61,27.65-38.4A8,8,0,0,0,247.31,124.76ZM128,192c-30.78,0-57.67-11.19-79.93-33.25A133.47,133.47,0,0,1,25,128,133.33,133.33,0,0,1,48.07,97.25C70.33,75.19,97.22,64,128,64s57.67,11.19,79.93,33.25A133.46,133.46,0,0,1,231.05,128C223.84,141.46,192.43,192,128,192Zm0-112a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Z"/></svg>
            </button>
          </div>
        </div>

        <button class="btn-admin-auth" type="submit" id="login-btn">
          Sign In to Admin Panel
        </button>

      </form>

      <p class="admin-auth-footer">
        Not an admin? <a href="/login">User login</a>
      </p>

    </div>
  </div>
</div>

<script src="/assets/js/main.js"></script>
<script>
(function () {
  'use strict';

  // Password toggle
  var pwToggle = document.getElementById('pw-toggle');
  var pwInput  = document.getElementById('password');
  if (pwToggle && pwInput) {
    pwToggle.addEventListener('click', function () {
      var isText = pwInput.type === 'text';
      pwInput.type = isText ? 'password' : 'text';
      pwToggle.setAttribute('aria-label', isText ? 'Show password' : 'Hide password');
    });
  }

  // Login form
  document.getElementById('admin-login-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    var btn   = document.getElementById('login-btn');
    var email = document.getElementById('email').value.trim();
    var pass  = document.getElementById('password').value;

    if (!email || !pass) { showToast('Please enter your email and password', 'warning'); return; }

    btn.disabled = true;
    btn.textContent = 'Signing in…';

    try {
      var res = await apiRequest('/api/auth/admin-login.php', 'POST', { email: email, password: pass });
      showToast('Signed in successfully', 'success');
      setTimeout(function () {
        window.location.href = res.data && res.data.redirect ? res.data.redirect : '/pages/admin/dashboard.php';
      }, 600);
    } catch (_) {
      btn.disabled = false;
      btn.textContent = 'Sign In to Admin Panel';
    }
  });
})();
</script>

<style>
/* ── Admin auth shared variables ─────────────────────────── */
.admin-auth-body {
  margin: 0;
  padding: 0;
  font-family: var(--font-sans);
  background: var(--bg-surface);
  min-height: 100vh;
}

.admin-auth-layout {
  display: grid;
  grid-template-columns: 420px 1fr;
  min-height: 100vh;
}

/* ── Left panel ──────────────────────────────────────────── */
.admin-auth-left {
  background: #01110A;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: var(--space-12);
}
.admin-auth-brand {
  text-align: center;
  position: relative;
  z-index: 2;
}
.admin-brand-logo {
  width: 140px;
  margin-bottom: var(--space-5);
  opacity: 0.95;
}
.admin-auth-tagline {
  color: rgba(255,255,255,0.5);
  font-size: var(--text-sm);
  font-weight: 500;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  margin: 0;
}
/* Decorative rings */
.admin-auth-decor { position: absolute; inset: 0; pointer-events: none; }
.admin-decor-ring {
  position: absolute;
  border: 1px solid rgba(115, 186, 155, 0.15);
  border-radius: 50%;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
}
.admin-decor-ring--1 { width: 300px; height: 300px; }
.admin-decor-ring--2 { width: 480px; height: 480px; border-color: rgba(115,186,155,0.08); }
.admin-decor-ring--3 { width: 680px; height: 680px; border-color: rgba(115,186,155,0.05); }

/* ── Right panel ─────────────────────────────────────────── */
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
  width: 6px;
  height: 6px;
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
.admin-auth-subtitle {
  font-size: var(--text-sm);
  color: var(--text-muted);
  margin: 0;
}

.admin-alert {
  padding: var(--space-3) var(--space-4);
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
  margin-bottom: var(--space-5);
}
.admin-alert--warning { background: #FFF8E1; color: #7A5F00; border: 1px solid #FFD740; }
.admin-alert--error   { background: #FFF4E8; color: #C47A2B; border: 1px solid rgba(115,186,155,0.3); }

.form-group { margin-bottom: var(--space-5); }
.form-label {
  display: block;
  font-size: var(--text-sm);
  font-weight: 600;
  color: var(--text-secondary);
  margin-bottom: var(--space-2);
}
.form-label-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: var(--space-2);
}
.form-label-row .form-label { margin-bottom: 0; }
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

.admin-forgot-link {
  font-size: var(--text-xs);
  color: var(--color-accent);
  text-decoration: none;
  font-weight: 500;
}
.admin-forgot-link:hover { text-decoration: underline; }

.pw-wrap { position: relative; }
.pw-wrap .form-control { padding-right: 44px; }
.pw-toggle {
  position: absolute;
  right: 12px;
  top: 50%;
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

/* Mobile: stack layout */
@media (max-width: 768px) {
  .admin-auth-layout { grid-template-columns: 1fr; }
  .admin-auth-left   { display: none; }
  .admin-auth-right  { padding: var(--space-8) var(--space-4); }
  .admin-auth-card   { padding: var(--space-8) var(--space-5); }
}
</style>

</body>
</html>
