<?php
$pageTitle = 'Verify Email';
if (session_status() === PHP_SESSION_NONE) session_start();
$userEmail = $_SESSION['reg_pending_user_email'] ?? '';
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

      <h1 class="auth-title">Verify Your Email</h1>
      <?php if ($userEmail): ?>
        <p class="auth-subtitle">Enter the 6-digit code sent to <strong><?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') ?></strong></p>
      <?php else: ?>
        <p class="auth-subtitle">Enter the 6-digit verification code sent to your email address</p>
      <?php endif; ?>

      <form id="verify-email-form" class="form-stack" novalidate>
        <div class="form-group">
          <label class="form-label" for="ve-code">Verification Code</label>
          <input type="text" id="ve-code" name="code"
                 placeholder="000000" maxlength="6" inputmode="numeric"
                 autocomplete="one-time-code" class="code-input" required>
          <span class="form-hint">Check your email inbox and spam folder. Codes expire after 15 minutes.</span>
        </div>

        <button type="submit" class="btn btn-primary btn-full">Verify Email</button>
      </form>

      <hr class="divider">

      <p class="auth-footer-text">
        Wrong email? <a href="/register">Start over</a>
      </p>
      <p class="auth-footer-text" style="margin-top:var(--space-2)">
        Already verified? <a href="/login">Sign In</a>
      </p>

    </div>
  </section>
</main>

<?php include '../../includes/footer.php'; ?>
<script src="/assets/js/main.js"></script>
</body>
</html>
