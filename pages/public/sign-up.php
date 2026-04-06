<?php $pageTitle = 'Create Account'; ?>
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
    <div class="auth-card" style="max-width:480px">

      <div class="auth-logo">
        <img src="/assets/images/logo/avernologo-dark.png" alt="Averon Investment" style="height:28px;width:auto;">
      </div>

      <h1 class="auth-title" data-i18n="signup.title">Create Account</h1>
      <p class="auth-subtitle">Join 12,400+ investors on Averon Investment</p>

      <!-- Step Indicator -->
      <div class="steps">
        <div class="step-item active">
          <div class="step-bubble">1</div>
          <div class="step-connector"></div>
        </div>
        <div class="step-item">
          <div class="step-bubble">2</div>
          <div class="step-connector"></div>
        </div>
        <div class="step-item">
          <div class="step-bubble">3</div>
        </div>
      </div>

      <form id="signup-form" novalidate>

        <!-- ── Step 1: Personal Info ── -->
        <div id="signup-step-1" class="form-stack">
          <p class="form-hint" style="margin-bottom:var(--space-2)" data-i18n="signup.step1">Step 1 of 3 — Personal details</p>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="first_name" data-i18n="signup.firstname">First Name</label>
              <input type="text" id="first_name" name="first_name"
                     placeholder="John" autocomplete="given-name" required
                     data-i18n-ph="ph.firstname">
            </div>
            <div class="form-group">
              <label class="form-label" for="last_name" data-i18n="signup.lastname">Last Name</label>
              <input type="text" id="last_name" name="last_name"
                     placeholder="Smith" autocomplete="family-name" required
                     data-i18n-ph="ph.lastname">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="region" data-i18n="signup.region">Region / Country</label>
            <select id="region" name="region" required>
              <option value="" disabled selected>Select your region</option>
              <option>United States</option>
              <option>United Kingdom</option>
              <option>Canada</option>
              <option>Australia</option>
              <option>Germany</option>
              <option>France</option>
              <option>Netherlands</option>
              <option>Sweden</option>
              <option>UAE</option>
              <option>Saudi Arabia</option>
              <option>Qatar</option>
              <option>India</option>
              <option>Singapore</option>
              <option>China</option>
              <option>Japan</option>
              <option>South Korea</option>
              <option>South Africa</option>
              <option>Nigeria</option>
              <option>Kenya</option>
              <option>Brazil</option>
              <option>Mexico</option>
              <option>Argentina</option>
              <option>Other</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="language" data-i18n="signup.language">Preferred Language</label>
            <select id="language" name="language" required>
              <option value="" disabled selected>Select language</option>
              <option value="en">English</option>
              <option value="es">Spanish</option>
              <option value="fr">French</option>
              <option value="de">German</option>
              <option value="ar">Arabic</option>
              <option value="zh">Chinese</option>
              <option value="ja">Japanese</option>
              <option value="pt">Portuguese</option>
              <option value="other">Other</option>
            </select>
          </div>

          <!-- text wrapped in span so the SVG icon is preserved -->
          <button type="button" id="signup-next-1" class="btn btn-primary btn-full">
            <span data-i18n="signup.continue">Continue</span>
            <svg viewBox="0 0 256 256" fill="currentColor" width="18" height="18" aria-hidden="true">
              <path d="M181.66,133.66l-80,80a8,8,0,0,1-11.32-11.32L164.69,128,90.34,53.66a8,8,0,0,1,11.32-11.32l80,80A8,8,0,0,1,181.66,133.66Z"/>
            </svg>
          </button>
        </div>

        <!-- ── Step 2: Email & Password ── -->
        <div id="signup-step-2" class="form-stack hidden">
          <p class="form-hint" style="margin-bottom:var(--space-2)" data-i18n="signup.step2">Step 2 of 3 — Account credentials</p>

          <div class="form-group">
            <label class="form-label" for="email" data-i18n="signup.email">Email Address</label>
            <input type="email" id="email" name="email"
                   placeholder="you@example.com" required autocomplete="email"
                   data-i18n-ph="ph.email">
          </div>

          <div class="form-group">
            <label class="form-label" for="password" data-i18n="signup.password">Password</label>
            <div class="password-wrap">
              <input type="password" id="password" name="password"
                     placeholder="Min. 8 chars, 1 uppercase, 1 number, 1 special" required autocomplete="new-password"
                     data-i18n-ph="ph.password">
              <button type="button" class="password-toggle" aria-label="Show password"></button>
            </div>
            <span class="form-hint">At least 8 characters · 1 uppercase · 1 number · 1 special character</span>
          </div>

          <div class="form-group">
            <label class="form-label" for="confirm_password" data-i18n="signup.confirm">Confirm Password</label>
            <div class="password-wrap">
              <input type="password" id="confirm_password" name="confirm_password"
                     placeholder="Re-enter your password" required autocomplete="new-password"
                     data-i18n-ph="ph.confirm">
              <button type="button" class="password-toggle" aria-label="Show password"></button>
            </div>
          </div>

          <div class="flex gap-3">
            <!-- Back button — SVG + text; wrap text in span -->
            <button type="button" id="signup-back-2" class="btn btn-secondary" style="flex:0 0 auto;padding:var(--space-3) var(--space-5)">
              <svg viewBox="0 0 256 256" fill="currentColor" width="18" height="18" aria-hidden="true">
                <path d="M165.66,202.34a8,8,0,0,1-11.32,11.32l-80-80a8,8,0,0,1,0-11.32l80-80a8,8,0,0,1,11.32,11.32L91.31,128Z"/>
              </svg>
              <span data-i18n="signup.back">Back</span>
            </button>
            <button type="button" id="signup-next-2" class="btn btn-primary" style="flex:1"
                    data-i18n="signup.send_code">
              Send Verification Code
            </button>
          </div>
        </div>

        <!-- ── Step 3: Email Verify ── -->
        <div id="signup-step-3" class="form-stack hidden">
          <p class="form-hint" style="margin-bottom:var(--space-2)" data-i18n="signup.step3">Step 3 of 3 — Verify your email</p>

          <div class="alert alert-info">
            <svg viewBox="0 0 256 256" fill="currentColor" width="18" height="18" aria-hidden="true" style="flex-shrink:0;margin-top:1px">
              <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm16-40a8,8,0,0,1-8,8,16,16,0,0,1-16-16V128a8,8,0,0,1,0-16,16,16,0,0,1,16,16v40A8,8,0,0,1,144,176ZM112,84a12,12,0,1,1,12,12A12,12,0,0,1,112,84Z"/>
            </svg>
            <span>A 6-digit code has been sent to <strong id="signup-email-hint"></strong>. Enter it below to activate your account.</span>
          </div>

          <div class="form-group">
            <label class="form-label" for="code">Verification Code</label>
            <input type="text" id="code" name="code"
                   placeholder="000000" maxlength="6" inputmode="numeric"
                   autocomplete="one-time-code" class="code-input" required>
          </div>

          <div class="flex gap-3">
            <!-- Back button — SVG + text; wrap text in span -->
            <button type="button" id="signup-back-3" class="btn btn-secondary" style="flex:0 0 auto;padding:var(--space-3) var(--space-5)">
              <svg viewBox="0 0 256 256" fill="currentColor" width="18" height="18" aria-hidden="true">
                <path d="M165.66,202.34a8,8,0,0,1-11.32,11.32l-80-80a8,8,0,0,1,0-11.32l80-80a8,8,0,0,1,11.32,11.32L91.31,128Z"/>
              </svg>
              <span data-i18n="signup.back">Back</span>
            </button>
            <button type="button" id="signup-verify-btn" class="btn btn-primary" style="flex:1"
                    data-i18n="signup.verify">
              Verify &amp; Activate
            </button>
          </div>

          <p class="auth-footer-text">
            Didn't receive a code?
            <button type="button" id="signup-resend" class="btn btn-ghost"
                    style="display:inline;padding:0;font-size:inherit;color:var(--color-primary);font-weight:600"
                    data-i18n="signup.resend">Resend</button>
          </p>
        </div>

      </form>

      <hr class="divider">

      <p class="auth-footer-text">
        <span data-i18n="signup.have_account">Already have an account?</span>
        <a href="/login" data-i18n="signup.signin">Sign In</a>
      </p>

    </div>
  </section>
</main>

<?php include '../../includes/footer.php'; ?>
<script src="/assets/js/main.js"></script>
</body>
</html>
