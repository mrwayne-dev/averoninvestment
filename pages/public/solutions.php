<?php
$pageTitle = 'Solutions';
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

  <!-- Hero -->
  <section class="section section-white fade-in">
    <div class="container">
      <div class="section-header text-center">
        <span class="section-eyebrow">Our Solutions</span>
        <h1 class="section-title">Built for Every Investor</h1>
        <p class="section-subtitle">From first-time depositors to high-capital professionals — Averon Investment provides the tools, protection, and transparency you need to invest with confidence.</p>
      </div>
    </div>
  </section>

  <!-- Core Solutions -->
  <section class="section section-surface">
    <div class="container">
      <div class="feature-grid feature-grid--3">

        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="28" height="28" aria-hidden="true"><path d="M216,40H40A16,16,0,0,0,24,56V200a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A16,16,0,0,0,216,40Zm0,160H40V56H216V200ZM176,88a48,48,0,1,1-48-48A48.05,48.05,0,0,1,176,88Zm-16,0a32,32,0,1,0-32,32A32,32,0,0,0,160,88Zm0,72H96a8,8,0,0,0,0,16h64a8,8,0,0,0,0-16Z"/></svg>
          </div>
          <h3 class="feature-title">Crypto-Powered Deposits</h3>
          <p class="feature-desc">Fund your wallet instantly using Bitcoin, Ethereum, or USDT. All payments are processed on-chain with real-time confirmation — no bank delays, no friction.</p>
        </div>

        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="28" height="28" aria-hidden="true"><path d="M232,128a104,104,0,1,1-104-104A104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Zm-72,36V128H128a8,8,0,0,1,0-16h8V88a8,8,0,0,1,16,0v56a8,8,0,0,1,0,16Z"/></svg>
          </div>
          <h3 class="feature-title">Daily Profit Crediting</h3>
          <p class="feature-desc">Profits are credited to your balance every 24 hours automatically. Track your earnings in real time from any device — desktop or mobile.</p>
        </div>

        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="28" height="28" aria-hidden="true"><path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm48-88a48,48,0,1,1-48-48A48.05,48.05,0,0,1,176,128Zm-16,0a32,32,0,1,0-32,32A32,32,0,0,0,160,128Z"/></svg>
          </div>
          <h3 class="feature-title">Transparent Tracking</h3>
          <p class="feature-desc">Your dashboard shows every transaction, profit credit, and withdrawal request — all timestamped and immutable. No hidden fees, no surprises.</p>
        </div>

        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="28" height="28" aria-hidden="true"><path d="M208,80H176V56a48,48,0,0,0-96,0V80H48A16,16,0,0,0,32,96V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V96A16,16,0,0,0,208,80ZM96,56a32,32,0,0,1,64,0V80H96ZM208,208H48V96H208V208Zm-48-56a32,32,0,1,1-32-32A32,32,0,0,1,160,152Z"/></svg>
          </div>
          <h3 class="feature-title">Bank-Grade Security</h3>
          <p class="feature-desc">All API communications use TLS encryption. Sessions are protected with secure cookies, CSRF safeguards, and automatic timeout after inactivity.</p>
        </div>

        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="28" height="28" aria-hidden="true"><path d="M213.66,82.34l-56-56A8,8,0,0,0,152,24H56A16,16,0,0,0,40,40V216a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V88A8,8,0,0,0,213.66,82.34ZM160,51.31,188.69,80H160ZM200,216H56V40h88V88a8,8,0,0,0,8,8h48V216Zm-48-60a36,36,0,1,0-36,36A36,36,0,0,0,152,156Zm-56,0a20,20,0,1,1,20,20A20,20,0,0,1,96,156Z"/></svg>
          </div>
          <h3 class="feature-title">Quarterly Strategy Reports</h3>
          <p class="feature-desc">Platinum members receive detailed performance reports with market analysis, TSLA stock outlook, and personalized investment recommendations.</p>
        </div>

        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="28" height="28" aria-hidden="true"><path d="M244.8,150.4a8,8,0,0,1-11.2-1.6A51.6,51.6,0,0,0,192,128a8,8,0,0,1-7.37-4.89,8,8,0,0,1,1.75-8.78l.28-.26A59.78,59.78,0,0,0,192,69.65,60,60,0,0,0,76.65,96,8,8,0,0,1,68.4,97.7,60,60,0,0,0,8,156v12a8,8,0,0,0,16,0V156a44,44,0,0,1,44-44,8,8,0,0,1,7.37,4.89,8,8,0,0,1-1.75,8.78l-.28.26A59.78,59.78,0,0,0,68,184.35,60,60,0,0,0,179.35,158,8,8,0,0,1,188,156a51.6,51.6,0,0,1,41.6,21.2A8,8,0,1,1,218.4,166.4,35.5,35.5,0,0,0,192,152a76,76,0,0,1-152,0,75.74,75.74,0,0,1,24.23-55.3A44,44,0,0,1,76,16a44,44,0,0,1,41.6,60A60,60,0,0,0,128,100Z"/></svg>
          </div>
          <h3 class="feature-title">Referral Network</h3>
          <p class="feature-desc">Invite friends and earn up to 10% commission on every deposit they make. Your referral earnings are credited automatically and available to withdraw.</p>
        </div>

      </div>
    </div>
  </section>

  <!-- For Individual Investors -->
  <section class="section section-white">
    <div class="container">
      <div class="section-header text-center fade-in">
        <span class="section-eyebrow">Designed For You</span>
        <h2 class="section-title">Whether You're Starting Small or Going Big</h2>
      </div>
      <div class="how-steps">
        <div class="how-step stagger-item">
          <div class="how-step-number">01</div>
          <div class="how-step-content">
            <h3 class="how-step-title">New Investors</h3>
            <p class="how-step-desc">Start with the Launch Plan at just $100. No experience required. Your dashboard guides you through every step and profit is earned automatically.</p>
          </div>
        </div>
        <div class="how-step stagger-item">
          <div class="how-step-number">02</div>
          <div class="how-step-content">
            <h3 class="how-step-title">Growing Portfolios</h3>
            <p class="how-step-desc">Run multiple plans simultaneously with a Gold membership. Stack investments across terms to create a rolling income stream.</p>
          </div>
        </div>
        <div class="how-step stagger-item">
          <div class="how-step-number">03</div>
          <div class="how-step-content">
            <h3 class="how-step-title">High-Capital Investors</h3>
            <p class="how-step-desc">The Plaid Elite Plan delivers up to 1.20% daily with compound growth. Combine with Platinum membership for dedicated management and 1-hour withdrawals.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta-section fade-in">
    <div class="container">
      <div class="cta-content">
        <h2 class="cta-title">Find Your Solution</h2>
        <p class="cta-subtitle">Explore plans built for your capital level and goals. Start with zero commitment.</p>
        <div class="cta-actions">
          <a href="/investments" class="btn btn-primary">View Investment Plans</a>
          <a href="/register" class="btn btn-outline-light">Create Free Account</a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php include '../../includes/footer.php'; ?>
<script src="/assets/js/main.js"></script>
</body>
</html>
