<?php
/* =====================================================================
   pages/admin/register.php — Admin registration (invite-code gated)
   Standalone page — no public header/footer.
   Already logged-in admins are redirected away.
   ===================================================================== */
require_once '../../config/constants.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>false,'httponly'=>true,'samesite'=>'Strict']);
    session_start();
}

// Already logged in as admin → go to dashboard
if (!empty($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'admin') {
    header('Location: /pages/admin/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Registration — Averon Investment</title>
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

  <!-- Right panel — form -->
  <div class="admin-auth-right">
    <div class="admin-auth-card">

      <div class="admin-auth-card-top">
        <div class="admin-badge-pill">
          <span class="admin-badge-dot"></span>
          ADMIN REGISTRATION
        </div>
        <h1 class="admin-auth-title">Create Admin Account</h1>
        <p class="admin-auth-subtitle">A valid invite code is required to register</p>
      </div>

      <form id="admin-register-form" novalidate>

        <!-- Invite code -->
        <div class="form-group">
          <label class="form-label" for="invite_code">Invite Code</label>
          <div class="invite-wrap">
            <svg class="invite-icon" viewBox="0 0 256 256" fill="currentColor" width="16" height="16">
              <path d="M208,80H176V56a48,48,0,0,0-96,0V80H48A16,16,0,0,0,32,96V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V96A16,16,0,0,0,208,80ZM96,56a32,32,0,0,1,64,0V80H96ZM208,208H48V96H208V208Zm-80-48a24,24,0,1,0-24-24A24,24,0,0,0,128,160Z"/>
            </svg>
            <input class="form-control invite-input" type="text" id="invite_code" name="invite_code"
                   placeholder="Enter your invite code" required autocomplete="off">
          </div>
        </div>

        <!-- Name row -->
        <div class="form-row-2">
          <div class="form-group">
            <label class="form-label" for="first_name">First Name</label>
            <input class="form-control" type="text" id="first_name" name="first_name"
                   placeholder="Jane" required autocomplete="given-name">
          </div>
          <div class="form-group">
            <label class="form-label" for="last_name">Last Name</label>
            <input class="form-control" type="text" id="last_name" name="last_name"
                   placeholder="Smith" autocomplete="family-name">
          </div>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input class="form-control" type="email" id="email" name="email"
                 placeholder="admin@example.com" required autocomplete="email">
        </div>

        <!-- Password -->
        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="pw-wrap">
            <input class="form-control" type="password" id="password" name="password"
                   placeholder="Min 8 chars, upper, number, symbol" required autocomplete="new-password">
            <button type="button" class="pw-toggle" id="pw-toggle-1" aria-label="Show password">
              <svg viewBox="0 0 256 256" fill="currentColor" width="18" height="18"><path d="M247.31,124.76c-.35-.79-8.82-19.58-27.65-38.41C194.57,61.26,162.88,48,128,48S61.43,61.26,36.34,86.35C17.51,105.18,9,124,8.69,124.76a8,8,0,0,0,0,6.5c.35.79,8.82,19.57,27.65,38.4C61.43,194.74,93.12,208,128,208s66.57-13.26,91.66-38.34c18.83-18.83,27.3-37.61,27.65-38.4A8,8,0,0,0,247.31,124.76ZM128,192c-30.78,0-57.67-11.19-79.93-33.25A133.47,133.47,0,0,1,25,128,133.33,133.33,0,0,1,48.07,97.25C70.33,75.19,97.22,64,128,64s57.67,11.19,79.93,33.25A133.46,133.46,0,0,1,231.05,128C223.84,141.46,192.43,192,128,192Zm0-112a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Z"/></svg>
            </button>
          </div>
        </div>

        <!-- Confirm password -->
        <div class="form-group">
          <label class="form-label" for="confirm_password">Confirm Password</label>
          <div class="pw-wrap">
            <input class="form-control" type="password" id="confirm_password" name="confirm_password"
                   placeholder="••••••••" required autocomplete="new-password">
            <button type="button" class="pw-toggle" id="pw-toggle-2" aria-label="Show password">
              <svg viewBox="0 0 256 256" fill="currentColor" width="18" height="18"><path d="M247.31,124.76c-.35-.79-8.82-19.58-27.65-38.41C194.57,61.26,162.88,48,128,48S61.43,61.26,36.34,86.35C17.51,105.18,9,124,8.69,124.76a8,8,0,0,0,0,6.5c.35.79,8.82,19.57,27.65,38.4C61.43,194.74,93.12,208,128,208s66.57-13.26,91.66-38.34c18.83-18.83,27.3-37.61,27.65-38.4A8,8,0,0,0,247.31,124.76ZM128,192c-30.78,0-57.67-11.19-79.93-33.25A133.47,133.47,0,0,1,25,128,133.33,133.33,0,0,1,48.07,97.25C70.33,75.19,97.22,64,128,64s57.67,11.19,79.93,33.25A133.46,133.46,0,0,1,231.05,128C223.84,141.46,192.43,192,128,192Zm0-112a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Z"/></svg>
            </button>
          </div>
        </div>

        <!-- Password strength hint -->
        <p class="pw-hint">Must be at least 8 characters with 1 uppercase, 1 number, and 1 special character.</p>

        <button class="btn-admin-auth" type="submit" id="register-btn">
          Create Admin Account
        </button>

      </form>

      <p class="admin-auth-footer">
        Already have an account? <a href="/admin/login">Sign in</a>
      </p>

    </div>
  </div>
</div>

<script src="/assets/js/main.js"></script>
<script>
(function () {
  'use strict';

  // Password toggles
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

  // Register form
  document.getElementById('admin-register-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    var btn          = document.getElementById('register-btn');
    var inviteCode   = document.getElementById('invite_code').value.trim();
    var firstName    = document.getElementById('first_name').value.trim();
    var lastName     = document.getElementById('last_name').value.trim();
    var email        = document.getElementById('email').value.trim();
    var password     = document.getElementById('password').value;
    var confirmPass  = document.getElementById('confirm_password').value;

    if (!inviteCode)  { showToast('Invite code is required', 'warning'); return; }
    if (!firstName)   { showToast('First name is required', 'warning'); return; }
    if (!email)       { showToast('Email address is required', 'warning'); return; }
    if (!password)    { showToast('Password is required', 'warning'); return; }
    if (password !== confirmPass) { showToast('Passwords do not match', 'error'); return; }

    btn.disabled    = true;
    btn.textContent = 'Creating account…';

    try {
      await apiRequest('/api/auth/admin-register.php', 'POST', {
        invite_code:      inviteCode,
        first_name:       firstName,
        last_name:        lastName,
        email:            email,
        password:         password,
        confirm_password: confirmPass,
      });
      showToast('Admin account created! Redirecting to login…', 'success');
      setTimeout(function () {
        window.location.href = '/admin/login';
      }, 1500);
    } catch (_) {
      btn.disabled    = false;
      btn.textContent = 'Create Admin Account';
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
  border: 1px solid rgba(115, 186, 155, 0.15);
  border-radius: 50%;
  left: 50%; top: 50%;
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
  max-width: 480px;
  background: var(--bg-elevated);
  border: 1px solid var(--border-color);
  border-radius: var(--radius-xl);
  padding: var(--space-10) var(--space-8);
  box-shadow: var(--shadow-lg);
}
.admin-auth-card-top { margin-bottom: var(--space-7); }

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

/* Invite code field */
.invite-wrap { position: relative; }
.invite-icon {
  position: absolute;
  left: 12px; top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
  pointer-events: none;
}
.invite-input { padding-left: 36px !important; font-family: var(--font-mono); letter-spacing: 0.05em; }

/* Name row */
.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4); }

/* Generic form */
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

.pw-hint {
  font-size: var(--text-xs);
  color: var(--text-muted);
  margin: calc(-1 * var(--space-3)) 0 var(--space-5);
  line-height: 1.5;
}

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

/* Mobile */
@media (max-width: 768px) {
  .admin-auth-layout { grid-template-columns: 1fr; }
  .admin-auth-left   { display: none; }
  .admin-auth-right  { padding: var(--space-8) var(--space-4); align-items: flex-start; }
  .admin-auth-card   { padding: var(--space-8) var(--space-5); }
  .form-row-2        { grid-template-columns: 1fr; gap: 0; }
}
</style>

</body>
</html>
