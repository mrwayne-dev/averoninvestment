<?php
/* =====================================================================
   pages/admin/forgot-password.php — Admin password reset request
   Standalone page — no public header/footer.
   Accepts admin email; API response is always generic (no enumeration).
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Forgot Password — Averon Investment</title>
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

      <!-- Default view: request form -->
      <div id="view-form">
        <div class="admin-auth-card-top">
          <div class="admin-badge-pill">
            <span class="admin-badge-dot"></span>
            ADMIN ACCESS
          </div>
          <h1 class="admin-auth-title">Reset your password</h1>
          <p class="admin-auth-subtitle">Enter your admin email and we'll send a reset link if it's registered</p>
        </div>

        <form id="forgot-form" novalidate>

          <div class="form-group">
            <label class="form-label" for="email">Admin Email Address</label>
            <input class="form-control" type="email" id="email" name="email"
                   placeholder="admin@example.com" required autocomplete="email">
          </div>

          <button class="btn-admin-auth" type="submit" id="submit-btn">
            Send Reset Link
          </button>

        </form>

        <p class="admin-auth-footer">
          Remembered your password? <a href="/admin/login">Back to login</a>
        </p>
      </div>

      <!-- Success view (shown after submission) -->
      <div id="view-sent" style="display:none; text-align:center;">
        <div class="sent-icon-wrap" aria-hidden="true">
          <svg viewBox="0 0 256 256" fill="currentColor" width="48" height="48" style="color:var(--color-accent)">
            <path d="M224,48H32a8,8,0,0,0-8,8V192a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A8,8,0,0,0,224,48ZM98.71,128,40,181.81V74.19Zm11.84,10.85,12,11.05a8,8,0,0,0,10.82,0l12-11.05,58,53.15H52.57ZM157.29,128,216,74.19V181.81ZM40,56H216l-88,80.86Z"/>
          </svg>
        </div>
        <h2 class="sent-title">Check your inbox</h2>
        <p class="sent-msg">If that admin email is registered and active, a password reset link has been sent. It expires in <strong>30 minutes</strong>.</p>
        <a href="/admin/login" class="btn-admin-auth" style="display:inline-block;text-decoration:none;margin-top:var(--space-2);">
          Back to Login
        </a>
        <p class="admin-auth-footer">Didn't receive it?
          <button type="button" id="resend-btn" class="link-btn">Try again</button>
        </p>
      </div>

    </div>
  </div>
</div>

<script src="/assets/js/main.js"></script>
<script>
(function () {
  'use strict';

  var viewForm = document.getElementById('view-form');
  var viewSent = document.getElementById('view-sent');

  document.getElementById('forgot-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    var btn   = document.getElementById('submit-btn');
    var email = document.getElementById('email').value.trim();

    if (!email) { showToast('Please enter your email address', 'warning'); return; }

    btn.disabled    = true;
    btn.textContent = 'Sending…';

    try {
      await apiRequest('/api/auth/admin-forgot-pass.php', 'POST', { email: email });
      // Always show success view (API never reveals if email exists)
      viewForm.style.display = 'none';
      viewSent.style.display = 'block';
    } catch (_) {
      btn.disabled    = false;
      btn.textContent = 'Send Reset Link';
    }
  });

  // "Try again" — show the form again
  document.getElementById('resend-btn').addEventListener('click', function () {
    viewSent.style.display = 'none';
    viewForm.style.display = 'block';
    var btn = document.getElementById('submit-btn');
    btn.disabled    = false;
    btn.textContent = 'Send Reset Link';
  });
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
  text-align: center;
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

/* Link-style button */
.link-btn {
  background: none;
  border: none;
  color: var(--color-primary);
  font-size: inherit;
  font-weight: 500;
  cursor: pointer;
  padding: 0;
  text-decoration: none;
}
.link-btn:hover { text-decoration: underline; }

/* Success view */
.sent-icon-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 80px; height: 80px;
  background: #FFF4E8;
  border-radius: 50%;
  margin: 0 auto var(--space-6);
}
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
