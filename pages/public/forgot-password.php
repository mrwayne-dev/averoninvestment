<?php
$pageTitle = 'Reset Password';
$token = trim($_GET['token'] ?? '');
$hasToken = $token !== '';
?>
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

<div id="toast-container"></div>

<main>
  <section class="page-center">
    <div class="auth-card">

      <div class="auth-logo">
        <img src="/assets/images/logo/avernologo-dark.png" alt="Averon Investment" style="height:28px;width:auto;">
      </div>

      <?php if (!$hasToken): ?>

        <h1 class="auth-title">Forgot Password?</h1>
        <p class="auth-subtitle">Enter your email and we'll send a secure reset link</p>

        <form id="forgot-email-form" class="form-stack" novalidate>
          <div class="form-group">
            <label class="form-label" for="fp-email">Email Address</label>
            <input type="email" id="fp-email" name="email"
                   placeholder="you@example.com" required autocomplete="email">
          </div>

          <button type="submit" class="btn btn-primary btn-full">Send Reset Link</button>
        </form>

      <?php else: ?>

        <h1 class="auth-title">Set New Password</h1>
        <p class="auth-subtitle">Choose a strong password for your account</p>

        <form id="reset-password-form" class="form-stack" novalidate>
          <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">

          <div class="form-group">
            <label class="form-label" for="rp-password">New Password</label>
            <input type="password" id="rp-password" name="password"
                   placeholder="Min. 8 chars, 1 uppercase, 1 number, 1 special" required autocomplete="new-password">
            <span class="form-hint">At least 8 characters · 1 uppercase · 1 number · 1 special character</span>
          </div>

          <div class="form-group">
            <label class="form-label" for="rp-confirm">Confirm Password</label>
            <input type="password" id="rp-confirm" name="confirm_password"
                   placeholder="Re-enter your new password" required autocomplete="new-password">
          </div>

          <button type="submit" class="btn btn-primary btn-full">Update Password</button>
        </form>

      <?php endif; ?>

      <hr class="divider">

      <p class="auth-footer-text">
        Remember your password? <a href="/login">Sign In</a>
      </p>
      <p class="auth-footer-text" style="margin-top:var(--space-2)">
        New here? <a href="/register">Create an account</a>
      </p>

    </div>
  </section>
</main>

<?php include '../../includes/footer.php'; ?>
<script src="/assets/js/main.js"></script>
</body>
</html>
