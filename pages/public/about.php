<?php $pageTitle = 'About Us'; ?>
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

  <!-- ── Hero ── -->
  <section class="section section-white hero-section fade-in">
    <div class="container">
      <div class="section-header text-center">
        <div class="section-eyebrow">About Averon Investment</div>
        <h1 class="section-title">Built on Precision.<br>Driven by Performance.</h1>
        <p class="section-subtitle">We combine institutional-grade investment strategies with the transparency and accessibility that modern investors demand — powered by Tesla's market momentum.</p>
      </div>
    </div>
  </section>


  <!-- ── Stats Band ── -->
  <section class="stats-band fade-in">
    <div class="container">
      <div class="stats-band-grid">
        <div class="stats-band-item">
          <div class="stats-band-value" data-target="48" data-prefix="$" data-suffix="M+">$48M+</div>
          <div class="stats-band-label">Assets Under Management</div>
        </div>
        <div class="stats-band-item">
          <div class="stats-band-value" data-target="12400" data-suffix="+">12,400+</div>
          <div class="stats-band-label">Active Investors</div>
        </div>
        <div class="stats-band-item">
          <div class="stats-band-value" data-target="99.8" data-suffix="%">99.8%</div>
          <div class="stats-band-label">Platform Uptime</div>
        </div>
        <div class="stats-band-item">
          <div class="stats-band-value" data-target="4" data-suffix=" Yrs">4 Yrs</div>
          <div class="stats-band-label">Operating Experience</div>
        </div>
      </div>
    </div>
  </section>


  <!-- ── Mission ── -->
  <section class="section section-white fade-in">
    <div class="container">
      <div class="grid grid-2" style="gap:var(--space-16);align-items:center">
        <div>
          <div class="section-eyebrow">Our Mission</div>
          <h2 class="section-title" style="text-align:left;margin-bottom:var(--space-5)">Democratising Institutional Returns</h2>
          <p style="font-size:var(--text-lg);line-height:1.8;margin-bottom:var(--space-6)">
            Averon Investment was founded on a single conviction: that the outsized returns historically reserved for hedge funds and family offices should be accessible to any disciplined investor — regardless of geography, background, or starting capital.
          </p>
          <p style="line-height:1.8;margin-bottom:var(--space-6)">
            We build strategies around Tesla's market dynamics — one of the most data-rich, momentum-driven equities in modern history — and translate that edge into predictable daily yields for our investor community.
          </p>
          <a href="/register" class="btn btn-primary btn-lg">Start Investing</a>
        </div>
        <div class="stat-group">
          <div class="stat-card">
            <div class="stat-value">0.20%</div>
            <div class="stat-label">Minimum daily yield</div>
          </div>
          <div class="stat-card">
            <div class="stat-value">1.20%</div>
            <div class="stat-label">Maximum daily yield</div>
          </div>
          <div class="stat-card">
            <div class="stat-value">30–90</div>
            <div class="stat-label">Day investment terms</div>
          </div>
          <div class="stat-card">
            <div class="stat-value">4</div>
            <div class="stat-label">Investment plan tiers</div>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- ── Core Values ── -->
  <section class="section section-surface fade-in">
    <div class="container">

      <div class="section-header text-center">
        <div class="section-eyebrow">Core Values</div>
        <h2 class="section-title">What We Stand For</h2>
        <p class="section-subtitle">Every decision at Averon Investment is guided by four principles that protect and grow investor capital.</p>
      </div>

      <div class="feature-grid">

        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="24" height="24" aria-hidden="true">
              <path d="M208,40H48A16,16,0,0,0,32,56v56c0,52.72,25.52,84.67,46.93,102.19,23.06,18.86,46,25.26,47,25.53a8,8,0,0,0,4.2,0c1-.27,23.91-6.67,47-25.53C198.48,196.67,224,164.72,224,112V56A16,16,0,0,0,208,40Zm0,72c0,37.07-13.66,67.16-40.6,89.42A129.3,129.3,0,0,1,128,223.62a128.25,128.25,0,0,1-38.92-21.81C61.82,179.51,48,149.3,48,112l0-56,160,0Z"/>
            </svg>
          </div>
          <div class="feature-title">Security First</div>
          <p class="feature-desc">Your capital is protected by 256-bit SSL encryption, cold-storage reserves, and multi-factor authentication. We treat security not as a feature but as a foundation.</p>
        </div>

        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="24" height="24" aria-hidden="true">
              <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm16-40a8,8,0,0,1-8,8,16,16,0,0,1-16-16V128a8,8,0,0,1,0-16,16,16,0,0,1,16,16v40A8,8,0,0,1,144,176ZM112,84a12,12,0,1,1,12,12A12,12,0,0,1,112,84Z"/>
            </svg>
          </div>
          <div class="feature-title">Full Transparency</div>
          <p class="feature-desc">Every yield rate, plan term, and fee is published openly. You see exactly what you'll earn before you invest. No hidden charges. No surprises.</p>
        </div>

        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="24" height="24" aria-hidden="true">
              <path d="M215.79,118.17a8,8,0,0,0-5-5.66L153.18,90.9l14.66-73.33a8,8,0,0,0-13.69-7l-112,120a8,8,0,0,0,3,13l57.63,21.61L88.16,238.43a8,8,0,0,0,13.69,7l112-120A8,8,0,0,0,215.79,118.17Z"/>
            </svg>
          </div>
          <div class="feature-title">Proven Performance</div>
          <p class="feature-desc">Four years of consistent daily credit cycles. Withdrawal requests processed on schedule. We've built a track record — not just a pitch deck.</p>
        </div>
      </div>
    </div>
  </section>


  <!-- ── How We Generate Returns ── -->
  <section class="section section-white fade-in">
    <div class="container">

      <div class="section-header text-center">
        <div class="section-eyebrow">How Returns Work</div>
        <h2 class="section-title">The Averon Edge</h2>
        <p class="section-subtitle">Our quantitative strategies capture systematic patterns in TSLA price action, sector rotation, and EV market sentiment — translating them into daily yields for our investors.</p>
      </div>

      <div class="how-steps">
        <div class="how-step stagger-item">
          <div class="how-step-number">1</div>
          <div class="how-step-title">Capital Allocation</div>
          <p class="how-step-desc">Investor capital is pooled into structured strategies aligned with your chosen plan tier. Each strategy targets a defined daily yield range with capital protection as a baseline.</p>
        </div>
        <div class="how-step stagger-item">
          <div class="how-step-number">2</div>
          <div class="how-step-title">Daily Profit Crediting</div>
          <p class="how-step-desc">Every 24 hours, net returns are calculated and credited directly to your profit balance. You see the exact amount added — no rounding, no ambiguity.</p>
        </div>
        <div class="how-step stagger-item">
          <div class="how-step-number">3</div>
          <div class="how-step-title">Flexible Access</div>
          <p class="how-step-desc">Withdraw profit balances on your schedule. Processing speed scales with your membership tier — from standard 72-hour to priority 1-hour for Platinum members.</p>
        </div>
      </div>

    </div>
  </section>


  <!-- ── Compliance & Crypto ── -->
  <section class="section section-surface fade-in">
    <div class="container">

      <div class="section-header text-center">
        <div class="section-eyebrow">Infrastructure</div>
        <h2 class="section-title">Built for Global Access</h2>
        <p class="section-subtitle">We accept crypto deposits in BTC, ETH, USDT (TRC-20), and USDT (ERC-20) — enabling investors from any country to participate without wire transfers or banking friction.</p>
      </div>

      <div class="feature-grid">
        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="24" height="24" aria-hidden="true">
              <path d="M216,64H56a8,8,0,0,1,0-16H192a8,8,0,0,0,0-16H56A24,24,0,0,0,32,56V184a24,24,0,0,0,24,24H216a16,16,0,0,0,16-16V80A16,16,0,0,0,216,64Zm0,128H56a8,8,0,0,1-8-8V78.63A23.84,23.84,0,0,0,56,80H216Zm-48-60a12,12,0,1,1,12,12A12,12,0,0,1,168,132Z"/>
            </svg>
          </div>
          <div class="feature-title">Crypto-Native Deposits</div>
          <p class="feature-desc">Fund your account in Bitcoin, Ethereum, or USDT. Deposits confirm automatically via our NOWPayments integration — no manual processing delays.</p>
        </div>
        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="24" height="24" aria-hidden="true">
              <path d="M232,208a8,8,0,0,1-8,8H32a8,8,0,0,1-8-8V48a8,8,0,0,1,16,0V156.69l50.34-50.35a8,8,0,0,1,11.32,0L128,132.69,180.69,80H160a8,8,0,0,1,0-16h40a8,8,0,0,1,8,8v40a8,8,0,0,1-16,0V91.31l-58.34,58.35a8,8,0,0,1-11.32,0L96,123.31l-56,56V200H224A8,8,0,0,1,232,208Z"/>
            </svg>
          </div>
          <div class="feature-title">Real-Time Dashboard</div>
          <p class="feature-desc">Track your portfolio, daily credits, and withdrawal history from a single live dashboard. Refreshes every 30 seconds — always accurate.</p>
        </div>
        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="24" height="24" aria-hidden="true">
              <path d="M128,24h0A104,104,0,1,0,232,128,104.12,104.12,0,0,0,128,24Zm88,104a87.61,87.61,0,0,1-3.33,24H174.16a157.44,157.44,0,0,0,0-48h38.51A87.61,87.61,0,0,1,216,128ZM102,168H154a115.11,115.11,0,0,1-26,45A115.27,115.27,0,0,1,102,168Zm-3.9-16a140.84,140.84,0,0,1,0-48h59.88a140.84,140.84,0,0,1,0,48ZM40,128a87.61,87.61,0,0,1,3.33-24H81.84a157.44,157.44,0,0,0,0,48H43.33A87.61,87.61,0,0,1,40,128ZM154,88H102a115.11,115.11,0,0,1,26-45A115.27,115.27,0,0,1,154,88Zm52.33,0H170.71a135.28,135.28,0,0,0-22.3-45.6A88.29,88.29,0,0,1,206.37,88ZM107.59,42.4A135.28,135.28,0,0,0,85.29,88H49.63A88.29,88.29,0,0,1,107.59,42.4ZM49.63,168H85.29a135.28,135.28,0,0,0,22.3,45.6A88.29,88.29,0,0,1,49.63,168Zm98.78,45.6a135.28,135.28,0,0,0,22.3-45.6h35.66A88.29,88.29,0,0,1,148.41,213.6Z"/>
            </svg>
          </div>
          <div class="feature-title">Global Investor Base</div>
          <p class="feature-desc">Investors from 60+ countries trust Averon Investment. Our platform supports multiple languages and currencies, with 24/7 access from any device.</p>
        </div>
      </div>
    </div>
  </section>


  <!-- ── CTA ── -->
  <section class="section cta-section fade-in">
    <div class="container">
      <div class="section-header text-center">
        <div class="section-eyebrow">Ready to Begin?</div>
        <h2 class="section-title">Join the Averon Investment Community</h2>
        <p class="section-subtitle">Create your free account in minutes. No minimum commitment — start with what you have.</p>
      </div>
      <div class="cta-actions">
        <a href="/register" class="btn btn-primary btn-xl">Create Free Account</a>
        <a href="/contact" class="btn btn-secondary btn-xl">Talk to Us First</a>
      </div>
      <p class="cta-note">Zero sign-up fees · Cancel or withdraw after your plan term ends</p>
    </div>
  </section>

</main>

<?php include '../../includes/footer.php'; ?>
<script src="/assets/js/main.js"></script>
</body>
</html>
