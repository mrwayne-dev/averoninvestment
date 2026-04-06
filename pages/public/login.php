<?php
$pageTitle = 'Sign In';
ob_start();
?>
<style>
.auth-wrap {
  min-height: calc(100vh - 64px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: var(--space-12) var(--space-4) var(--space-16);
  background: var(--bg-surface);
}
.auth-card {
  background: var(--bg-elevated);
  border: 1px solid var(--border-color);
  border-radius: var(--radius-xl);
  padding: var(--space-10) var(--space-8);
  width: 100%;
  max-width: 420px;
  box-shadow: var(--shadow-lg);
}
.auth-logo { display: flex; justify-content: center; margin-bottom: var(--space-8); }
.auth-logo img { height: 20px; }
.auth-title { font-size: var(--text-2xl); font-weight: 700; color: var(--text-primary); text-align: center; margin: 0 0 var(--space-2); }
.auth-subtitle { font-size: var(--text-sm); color: var(--text-muted); text-align: center; margin: 0 0 var(--space-8); }
.form-group { margin-bottom: var(--space-5); }
.form-label { display: block; font-size: var(--text-sm); font-weight: 500; color: var(--text-secondary); margin-bottom: var(--space-2); }
.form-label-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-2); }
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
.form-control:focus { border-color: var(--border-focus); box-shadow: 0 0 0 3px var(--color-primary-light); }
.forgot-link { font-size: var(--text-xs); color: var(--color-primary); text-decoration: none; font-weight: 500; }
.forgot-link:hover { text-decoration: underline; }
.checkbox-row { display: flex; align-items: center; gap: var(--space-2); margin-bottom: var(--space-6); }
.checkbox-row input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--color-primary); cursor: pointer; flex-shrink: 0; }
.checkbox-row label { font-size: var(--text-sm); color: var(--text-secondary); cursor: pointer; }
.btn-auth {
  width: 100%;
  padding: 12px var(--space-4);
  font-size: var(--text-base);
  font-weight: 600;
  background: var(--color-primary);
  color: #fff;
  border: none;
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: background var(--transition-fast);
  font-family: var(--font-sans);
}
.btn-auth:hover { background: var(--color-primary-hover); }
.auth-footer-text { text-align: center; margin-top: var(--space-6); font-size: var(--text-sm); color: var(--text-muted); }
.auth-footer-text a { color: var(--color-primary); text-decoration: none; font-weight: 500; }
.auth-footer-text a:hover { text-decoration: underline; }
.auth-divider { display: flex; align-items: center; gap: var(--space-3); margin: var(--space-6) 0; }
.auth-divider::before, .auth-divider::after { content: ''; flex: 1; height: 1px; background: var(--border-color); }
.auth-divider span { font-size: var(--text-xs); color: var(--text-muted); white-space: nowrap; }
@media (max-width: 480px) {
  .auth-card { padding: var(--space-8) var(--space-5); }
}
</style>
<?php $extraCss = ob_get_clean(); ?>
<!DOCTYPE html>
<html lang="en">
<?php include '../../includes/head.php'; ?>
<body>

<?php include '../../includes/header.php'; ?>

<div id="global-loader" class="loader-overlay" style="display:none">
  <div class="loader-inner">
    <img src="/assets/images/logo/avernonlogo.png" alt="" aria-hidden="true" style="height:40px;width:auto;animation:logoPulse 1.5s ease-in-out infinite;">
    <div class="loader-spinner"></div>
  </div>
</div>
<div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:var(--z-toast);display:flex;flex-direction:column;gap:8px;"></div>

<main>
  <section class="auth-wrap">
    <div class="auth-card">

      <div class="auth-logo">
        <img src="/assets/images/logo/avernologo-dark.png" alt="Averon Investment" style="height:28px;width:auto;">
      </div>

      <h1 class="auth-title">Welcome Back</h1>
      <p class="auth-subtitle">Sign in to your investment account</p>

      <form id="login-form" novalidate>

        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input class="form-control" type="email" id="email" name="email"
                 placeholder="you@example.com" required autocomplete="email">
        </div>

        <div class="form-group">
          <div class="form-label-row">
            <label class="form-label" for="password">Password</label>
            <a href="/forgot-password" class="forgot-link">Forgot password?</a>
          </div>
          <div class="password-wrap">
            <input class="form-control" type="password" id="password" name="password"
                   placeholder="Your password" required autocomplete="current-password">
            <button type="button" class="password-toggle" aria-label="Show password"></button>
          </div>
        </div>

        <div class="checkbox-row">
          <input type="checkbox" id="remember_me" name="remember_me">
          <label for="remember_me">Remember me for 30 days</label>
        </div>

        <button class="btn-auth" type="submit" id="login-btn">Sign In</button>

      </form>

      <div class="auth-divider"><span>New to Averon Investment?</span></div>

      <div class="auth-footer-text" style="margin-top:0">
        <a href="/register" style="display:inline-block;width:100%;padding:11px;text-align:center;border:1.5px solid var(--border-color);border-radius:var(--radius-md);font-weight:600;color:var(--text-secondary);text-decoration:none;transition:background var(--transition-fast);" onmouseover="this.style.background='var(--bg-surface)'" onmouseout="this.style.background='transparent'">
          Create Free Account
        </a>
      </div>

    </div>
  </section>
</main>

<?php include '../../includes/footer.php'; ?>
<script src="/assets/js/main.js"></script>
</body>
</html>
