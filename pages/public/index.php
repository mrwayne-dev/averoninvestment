<?php
$pageTitle = 'Averon Investment — Grow Wealth with Averon-Grade Precision';
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../../includes/head.php'; ?>
<body class="lp-main">

<?php include '../../includes/header.php'; ?>

<!-- Global Loader -->
<div id="global-loader" class="loader-overlay" style="display:none">
  <div class="loader-inner">
    <img src="/assets/images/logo/avernonlogo.png" alt="" aria-hidden="true" style="height:40px;width:auto;animation:logoPulse 1.5s ease-in-out infinite;">
    <div class="loader-spinner"></div>
  </div>
</div>

<!-- Toast Container -->
<div id="toast-container"></div>

<main>

  <!-- ==============================================================
       1. HERO SECTION
       ============================================================== -->
  <section class="lp-hero-v2" aria-label="Hero">
    <div class="lp-hero-v2-bg" aria-hidden="true">
      <div class="lp-hero-v2-noise"></div>
    </div>

    <div class="lp-container">
      <div class="lp-hero-v2-inner">

        <!-- Eyebrow pill -->
        <div class="lp-hero-v2-pill">
          <span class="lp-hero-v2-pill-dot" aria-hidden="true"></span>
          TSLA-Powered Daily Returns
        </div>

        <!-- Main headline — editorial serif -->
        <h1 class="lp-hero-v2-title">
          Institutional Returns.<br>
          <em>Built for Everyone.</em>
        </h1>

        <p class="lp-hero-v2-sub">
          Daily yields, compound growth, and full transparency — powered by Tesla's market momentum.
        </p>

        <!-- CTA -->
        <div class="lp-hero-v2-actions">
          <a href="/register" class="lp-hero-v2-cta-primary">
            Get Started For Free
          </a>
          <a href="/investments" class="lp-hero-v2-cta-ghost">
            View Plans &rarr;
          </a>
        </div>


      </div>
    </div>
  </section>

  <!-- (old device mockup section removed — replaced by hero-v2 above) -->
  <?php if (false): // old hero preserved below for reference ?>
  <section class="lp-hero-old" aria-label="Hero (deprecated)">
    <div class="lp-container" style="width:100%;">
      <div class="lp-hero-inner">
        <!-- LEFT: text content -->
        <div class="lp-hero-text">
          <!-- Social proof -->
        <div class="lp-device-wrap" data-lp-device aria-hidden="true">

          <!-- Floating stat chip — top left -->
          <div class="lp-stat-chip lp-stat-chip--tl">
            <div class="lp-chip-icon">
              <svg viewBox="0 0 256 256" fill="currentColor" width="18" height="18">
                <path d="M232,208a8,8,0,0,1-8,8H32a8,8,0,0,1-8-8V48a8,8,0,0,1,16,0V156.69l50.34-50.35a8,8,0,0,1,11.32,0L128,132.69,180.69,80H160a8,8,0,0,1,0-16h40a8,8,0,0,1,8,8v40a8,8,0,0,1-16,0V91.31l-58.34,58.35a8,8,0,0,1-11.32,0L96,123.31l-56,56V200H224A8,8,0,0,1,232,208Z"/>
              </svg>
            </div>
            <div>
              <strong>+18.4%</strong>
              <span>Monthly avg return</span>
            </div>
          </div>

          <!-- Phone frame -->
          <div class="lp-device">
            <div class="lp-device-notch"></div>
            <div class="lp-device-screen">

              <!-- App header -->
              <div class="lp-ds-header">
                <div class="lp-ds-brand">Averon Invest</div>
                <div class="lp-ds-bell">
                  <svg viewBox="0 0 256 256" fill="currentColor" width="14" height="14">
                    <path d="M221.8,175.94C216.25,166.38,208,139.33,208,104a80,80,0,1,0-160,0c0,35.34-8.26,62.38-13.81,71.94A16,16,0,0,0,48,200H88.81a40,40,0,0,0,78.38,0H208a16,16,0,0,0,13.8-24.06ZM128,216a24,24,0,0,1-22.62-16h45.24A24,24,0,0,1,128,216Z"/>
                  </svg>
                </div>
              </div>

              <!-- Portfolio value -->
              <div class="lp-ds-portfolio">
                <div class="lp-ds-port-label">Portfolio Value</div>
                <div class="lp-ds-port-value">$14,820.00</div>
                <div class="lp-ds-port-gain">
                  <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10">
                    <path d="M216.49,103.51a12,12,0,0,1-17,17L128,49,56.49,120.49a12,12,0,0,1-17-17l80-80a12,12,0,0,1,17,0Z"/>
                  </svg>
                  +$2,184 this month (+17.3%)
                </div>
              </div>

              <!-- Sparkline chart -->
              <div class="lp-ds-chart">
                <svg class="lp-sparkline" viewBox="0 0 220 48" fill="none" preserveAspectRatio="none">
                  <defs>
                    <linearGradient id="sparkGrad" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%" stop-color="#3E6AE1" stop-opacity="0.3"/>
                      <stop offset="100%" stop-color="#3E6AE1" stop-opacity="0"/>
                    </linearGradient>
                  </defs>
                  <path d="M0,38 L18,34 L36,36 L54,28 L72,30 L90,22 L108,24 L126,16 L144,18 L162,10 L180,12 L198,6 L220,4" stroke="#3E6AE1" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M0,38 L18,34 L36,36 L54,28 L72,30 L90,22 L108,24 L126,16 L144,18 L162,10 L180,12 L198,6 L220,4 L220,48 L0,48 Z" fill="url(#sparkGrad)"/>
                </svg>
              </div>

              <!-- Wallet row -->
              <div class="lp-ds-wallet">
                <div>
                  <div class="lp-ds-wallet-label">Available</div>
                  <div class="lp-ds-wallet-value">$2,820.00</div>
                </div>
                <div style="text-align:right;">
                  <div class="lp-ds-wallet-label">Invested</div>
                  <div class="lp-ds-wallet-value">$12,000.00</div>
                </div>
              </div>

              <!-- Quick actions -->
              <div class="lp-ds-actions">
                <div class="lp-ds-action">
                  <div class="lp-ds-action-icon" style="background:#E8F0FF;color:#3E6AE1;">
                    <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16">
                      <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm40,112H136v32a8,8,0,0,1-16,0V136H88a8,8,0,0,1,0-16h32V88a8,8,0,0,1,16,0v32h32a8,8,0,0,1,0,16Z"/>
                    </svg>
                  </div>
                  <span>Deposit</span>
                </div>
                <div class="lp-ds-action">
                  <div class="lp-ds-action-icon" style="background:#E8F5E9;color:#2E7D32;">
                    <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16">
                      <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm40,112H88a8,8,0,0,1,0-16h80a8,8,0,0,1,0,16Z"/>
                    </svg>
                  </div>
                  <span>Withdraw</span>
                </div>
                <div class="lp-ds-action">
                  <div class="lp-ds-action-icon" style="background:#FFF8E1;color:#F5A623;">
                    <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16">
                      <path d="M232,128a104,104,0,1,1-104-104A104.11,104.11,0,0,1,232,128Zm-104,40a8,8,0,0,0,0,16,48,48,0,0,0,0-96,32,32,0,0,1,0-64,48,48,0,0,0,0,96,8,8,0,0,0,0-16,32,32,0,0,1,0-64Z"/>
                    </svg>
                  </div>
                  <span>Invest</span>
                </div>
              </div>

              <!-- Active plan bar -->
              <div class="lp-ds-plan">
                <div class="lp-ds-plan-header">
                  <span class="lp-ds-plan-name">Drive Plan</span>
                  <span class="lp-ds-plan-badge">Active</span>
                </div>
                <div class="lp-ds-plan-meta">Day 32 of 45 &nbsp;·&nbsp; 0.42%/day avg</div>
                <div class="lp-ds-plan-bar">
                  <div class="lp-ds-plan-fill" style="width:71%;"></div>
                </div>
              </div>

            </div><!-- /.lp-device-screen -->
            <div class="lp-device-chin">
              <div class="lp-device-chin-bar"></div>
            </div>
          </div><!-- /.lp-device -->

          <!-- Floating stat chip — bottom right -->
          <div class="lp-stat-chip lp-stat-chip--br">
            <div class="lp-chip-icon" style="background:#E8F5E9;color:#2E7D32;">
              <svg viewBox="0 0 256 256" fill="currentColor" width="18" height="18">
                <path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/>
              </svg>
            </div>
            <div>
              <strong>$48M+</strong>
              <span>Assets managed</span>
            </div>
          </div>

        </div><!-- /.lp-device-wrap -->

      </div><!-- /.lp-hero-inner -->
    </div><!-- /.lp-container -->

    <!-- City silhouette -->
    <div class="lp-city" aria-hidden="true">
      <svg viewBox="0 0 1440 180" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <path fill="#D8EAFF" fill-opacity="0.4" d="
          M0,180 L0,140
          L30,140 L30,100 L50,100 L50,90 L60,90 L60,100 L80,100 L80,140
          L90,140 L90,80 L100,80 L100,60 L110,60 L110,80 L120,80 L120,140
          L130,140 L130,110 L140,110 L140,95 L148,95 L148,110 L160,110 L160,140
          L165,140 L165,50 L172,50 L172,40 L178,40 L178,50 L185,50 L185,140
          L195,140 L195,120 L205,120 L205,105 L212,105 L212,120 L225,120 L225,140
          L235,140 L235,70 L242,70 L242,55 L250,55 L250,45 L258,45 L258,55 L265,55 L265,70 L275,70 L275,140
          L285,140 L285,115 L295,115 L295,100 L305,100 L305,115 L318,115 L318,140
          L325,140 L325,85 L332,85 L332,68 L340,68 L340,60 L348,60 L348,68 L356,68 L356,85 L365,85 L365,140
          L375,140 L375,125 L390,125 L390,110 L400,110 L400,125 L415,125 L415,140
          L422,140 L422,75 L430,75 L430,55 L438,55 L438,48 L446,48 L446,55 L454,55 L454,75 L462,75 L462,140
          L472,140 L472,120 L485,120 L485,108 L492,108 L492,120 L505,120 L505,140
          L515,140 L515,88 L522,88 L522,72 L530,72 L530,60 L540,60 L540,72 L548,72 L548,88 L558,88 L558,140
          L568,140 L568,115 L578,115 L578,102 L586,102 L586,115 L598,115 L598,140
          L608,140 L608,65 L615,65 L615,50 L622,50 L622,42 L630,42 L630,50 L638,50 L638,65 L648,65 L648,140
          L658,140 L658,130 L672,130 L672,118 L680,118 L680,130 L695,130 L695,140
          L705,140 L705,82 L712,82 L712,66 L720,66 L720,58 L728,58 L728,66 L736,66 L736,82 L746,82 L746,140
          L755,140 L755,118 L768,118 L768,105 L776,105 L776,118 L790,118 L790,140
          L800,140 L800,78 L808,78 L808,62 L816,62 L816,52 L824,52 L824,62 L832,62 L832,78 L842,78 L842,140
          L852,140 L852,122 L865,122 L865,110 L874,110 L874,122 L888,122 L888,140
          L898,140 L898,70 L905,70 L905,55 L912,55 L912,44 L920,44 L920,55 L928,55 L928,70 L938,70 L938,140
          L948,140 L948,115 L960,115 L960,102 L968,102 L968,115 L982,115 L982,140
          L992,140 L992,85 L1000,85 L1000,68 L1008,68 L1008,58 L1016,58 L1016,68 L1024,68 L1024,85 L1035,85 L1035,140
          L1045,140 L1045,118 L1058,118 L1058,106 L1065,106 L1065,118 L1080,118 L1080,140
          L1090,140 L1090,72 L1097,72 L1097,56 L1105,56 L1105,48 L1113,48 L1113,56 L1121,56 L1121,72 L1132,72 L1132,140
          L1142,140 L1142,125 L1155,125 L1155,112 L1163,112 L1163,125 L1178,125 L1178,140
          L1188,140 L1188,80 L1196,80 L1196,63 L1204,63 L1204,55 L1212,55 L1212,63 L1220,63 L1220,80 L1232,80 L1232,140
          L1242,140 L1242,115 L1255,115 L1255,103 L1262,103 L1262,115 L1275,115 L1275,140
          L1285,140 L1285,90 L1292,90 L1292,74 L1300,74 L1300,65 L1308,65 L1308,74 L1316,74 L1316,90 L1328,90 L1328,140
          L1338,140 L1338,120 L1352,120 L1352,108 L1360,108 L1360,120 L1375,120 L1375,140
          L1385,140 L1385,75 L1392,75 L1392,60 L1400,60 L1400,52 L1408,52 L1408,60 L1416,60 L1416,75 L1428,75 L1428,140
          L1440,140 L1440,180 Z
        "/>
      </svg>
    </div>

  </section><!-- /.lp-hero-old (hidden) -->
  <?php endif; ?>


  <!-- ==============================================================
       2. STATS BAND
       ============================================================== -->
  <section class="lp-stats" aria-label="Platform statistics">
    <div class="lp-container">
      <div class="lp-stats-grid">

        <div class="lp-stats-item lp-reveal">
          <div class="lp-stats-value">$48M+</div>
          <div class="lp-stats-label">Assets Under Management</div>
        </div>

        <div class="lp-stats-item lp-reveal lp-reveal-delay-1">
          <div class="lp-stats-value">12,400+</div>
          <div class="lp-stats-label">Active Investors</div>
        </div>

        <div class="lp-stats-item lp-reveal lp-reveal-delay-2">
          <div class="lp-stats-value">0.20–1.20%</div>
          <div class="lp-stats-label">Daily Yield Range</div>
        </div>

        <div class="lp-stats-item lp-reveal lp-reveal-delay-3">
          <div class="lp-stats-value">99.8%</div>
          <div class="lp-stats-label">Platform Uptime</div>
        </div>

      </div>
    </div>
  </section>


  <!-- ==============================================================
       3. FEATURES SECTION
       ============================================================== -->
  <section class="lp-features lp-section" aria-label="Platform features">
    <div class="lp-container">

      <div class="lp-section-header lp-reveal">
        <div class="lp-section-eyebrow">Platform Advantages</div>
        <h2 class="lp-section-title">Built for Serious Investors</h2>
        <p class="lp-section-sub">Every feature engineered for transparency, security, and maximum returns on your capital.</p>
      </div>

      <div class="lp-feature-grid">

        <div class="lp-feature-card lp-reveal">
          <div class="lp-feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="24" height="24" aria-hidden="true">
              <path d="M208,40H48A16,16,0,0,0,32,56v56c0,52.72,25.52,84.67,46.93,102.19,23.06,18.86,46,25.26,47,25.53a8,8,0,0,0,4.2,0c1-.27,23.91-6.67,47-25.53C198.48,196.67,224,164.72,224,112V56A16,16,0,0,0,208,40Zm0,72c0,37.07-13.66,67.16-40.6,89.42A129.3,129.3,0,0,1,128,223.62a128.25,128.25,0,0,1-38.92-21.81C61.82,179.51,48,149.3,48,112l0-56,160,0ZM82.34,141.66a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35a8,8,0,0,1,11.32,11.32l-56,56a8,8,0,0,1-11.32,0Z"/>
            </svg>
          </div>
          <div class="lp-feature-title">Bank-Grade Security</div>
          <p class="lp-feature-desc">256-bit SSL encryption, cold storage reserves, and multi-layer authentication protect every dollar you invest. Your capital is always secure.</p>
        </div>

        <div class="lp-feature-card lp-reveal lp-reveal-delay-1">
          <div class="lp-feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="24" height="24" aria-hidden="true">
              <path d="M232,208a8,8,0,0,1-8,8H32a8,8,0,0,1-8-8V48a8,8,0,0,1,16,0V156.69l50.34-50.35a8,8,0,0,1,11.32,0L128,132.69,180.69,80H160a8,8,0,0,1,0-16h40a8,8,0,0,1,8,8v40a8,8,0,0,1-16,0V91.31l-58.34,58.35a8,8,0,0,1-11.32,0L96,123.31l-56,56V200H224A8,8,0,0,1,232,208Z"/>
            </svg>
          </div>
          <div class="lp-feature-title">Daily Profit Credits</div>
          <p class="lp-feature-desc">Profits are calculated and credited to your account every 24 hours. Watch your balance grow in real-time with zero manual action required.</p>
        </div>

        <div class="lp-feature-card lp-reveal lp-reveal-delay-2">
          <div class="lp-feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="24" height="24" aria-hidden="true">
              <path d="M215.79,118.17a8,8,0,0,0-5-5.66L153.18,90.9l14.66-73.33a8,8,0,0,0-13.69-7l-112,120a8,8,0,0,0,3,13l57.63,21.61L88.16,238.43a8,8,0,0,0,13.69,7l112-120A8,8,0,0,0,215.79,118.17ZM109.37,214l10.47-52.38a8,8,0,0,0-5-9.06L62,132.71l84.62-90.66L136.16,94.43a8,8,0,0,0,5,9.06l52.8,19.8Z"/>
            </svg>
          </div>
          <div class="lp-feature-title">Averon Market Edge</div>
          <p class="lp-feature-desc">Our strategies are aligned with TSLA stock cycles and EV sector momentum, giving you a systematic edge in one of the world's most dynamic markets.</p>
        </div>

        <div class="lp-feature-card lp-reveal lp-reveal-delay-1">
          <div class="lp-feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="24" height="24" aria-hidden="true">
              <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm40,120H128a8,8,0,0,1-8-8V72a8,8,0,0,1,16,0v56h32a8,8,0,0,1,0,16Z"/>
            </svg>
          </div>
          <div class="lp-feature-title">Instant Withdrawals</div>
          <p class="lp-feature-desc">Platinum members get 1-hour withdrawal processing. All tiers enjoy streamlined crypto payouts — no hidden delays or lock-up surprises.</p>
        </div>

        <div class="lp-feature-card lp-reveal lp-reveal-delay-2">
          <div class="lp-feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="24" height="24" aria-hidden="true">
              <path d="M244.8,150.4a8,8,0,0,1-11.2-1.6A51.6,51.6,0,0,0,192,128a8,8,0,0,1-7.37-4.89,8,8,0,0,1,1.75-8.78l.28-.26c.34-.35.66-.72,1-1.1a56,56,0,1,0-96.1-39,55.36,55.36,0,0,0,12.42,34.7c.3.36.6.71.91,1.05l.3.29a8,8,0,0,1,1.74,8.78A8,8,0,0,1,99,128a51.6,51.6,0,0,0-41.6,20.8,8,8,0,1,1-12.8-9.6A67.53,67.53,0,0,1,68,124.41a72,72,0,1,1,120,0,67.53,67.53,0,0,1,23.43,14.79A8,8,0,0,1,244.8,150.4Z"/>
            </svg>
          </div>
          <div class="lp-feature-title">Referral Commissions</div>
          <p class="lp-feature-desc">Earn 3–10% commission on every deposit made by people you refer. Higher membership tiers unlock higher commission rates — completely passive.</p>
        </div>

        <div class="lp-feature-card lp-reveal lp-reveal-delay-3">
          <div class="lp-feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="24" height="24" aria-hidden="true">
              <path d="M152,120H136V56h8a32,32,0,0,1,32,32,8,8,0,0,0,16,0,48.05,48.05,0,0,0-48-48h-8V24a8,8,0,0,0-16,0V40h-8a48,48,0,0,0,0,96h8v64H104a32,32,0,0,1-32-32,8,8,0,0,0-16,0,48.05,48.05,0,0,0,48,48h16v16a8,8,0,0,0,16,0V216h16a48,48,0,0,0,0-96Zm-32,0H104a32,32,0,0,1,0-64h16Zm32,80H136V136h16a32,32,0,0,1,0,64Z"/>
            </svg>
          </div>
          <div class="lp-feature-title">Compound Growth</div>
          <p class="lp-feature-desc">The Plaid Elite plan uses compound reinvestment, automatically rolling profits back into your position for exponential growth over the 90-day term.</p>
        </div>

      </div>
    </div>
  </section>


  <!-- ==============================================================
       4. INVESTMENT PLANS
       ============================================================== -->
  <section class="lp-plans lp-section" aria-label="Investment Plans">
    <div class="lp-container">

      <div class="lp-section-header lp-reveal">
        <div class="lp-section-eyebrow">Investment Plans</div>
        <h2 class="lp-section-title">Choose Your Strategy</h2>
        <p class="lp-section-sub">Four tiers designed for every investor profile — from first-time buyers to high-net-worth portfolios.</p>
      </div>

      <div class="lp-plans-grid">

        <!-- Launch Plan -->
        <div class="lp-plan-card lp-reveal">
          <div class="lp-plan-tier">Starter</div>
          <div class="lp-plan-price-row">
            <span class="lp-plan-sup">$</span>
            <span class="lp-plan-price">100</span>
          </div>
          <div class="lp-plan-meta">minimum &nbsp;·&nbsp; 30-day term<br>0.20%–0.30% daily &nbsp;·&nbsp; 6–9% total</div>
          <a href="/register" class="lp-plan-cta lp-plan-cta--default">Launch Plan</a>
          <div class="lp-plan-divider"></div>
          <ul class="lp-plan-features">
            <li class="lp-plan-feature">
              <div class="lp-plan-feature-icon">
                <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              </div>
              Daily profit credits
            </li>
            <li class="lp-plan-feature">
              <div class="lp-plan-feature-icon">
                <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              </div>
              6%–9% total return
            </li>
            <li class="lp-plan-feature">
              <div class="lp-plan-feature-icon">
                <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              </div>
              Crypto deposits accepted
            </li>
            <li class="lp-plan-feature lp-plan-feature--disabled">
              <div class="lp-plan-feature-icon">
                <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              </div>
              Dedicated account manager
            </li>
          </ul>
        </div>

        <!-- Drive Plan -->
        <div class="lp-plan-card lp-reveal lp-reveal-delay-1">
          <div class="lp-plan-tier">Popular</div>
          <div class="lp-plan-price-row">
            <span class="lp-plan-sup">$</span>
            <span class="lp-plan-price">1K</span>
          </div>
          <div class="lp-plan-meta">minimum &nbsp;·&nbsp; 45-day term<br>0.35%–0.50% daily &nbsp;·&nbsp; 15–22% total</div>
          <a href="/register" class="lp-plan-cta lp-plan-cta--default">Drive Plan</a>
          <div class="lp-plan-divider"></div>
          <ul class="lp-plan-features">
            <li class="lp-plan-feature">
              <div class="lp-plan-feature-icon">
                <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              </div>
              Daily profit credits
            </li>
            <li class="lp-plan-feature">
              <div class="lp-plan-feature-icon">
                <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              </div>
              15%–22% total return
            </li>
            <li class="lp-plan-feature">
              <div class="lp-plan-feature-icon">
                <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              </div>
              Priority email support
            </li>
            <li class="lp-plan-feature lp-plan-feature--disabled">
              <div class="lp-plan-feature-icon">
                <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              </div>
              Dedicated account manager
            </li>
          </ul>
        </div>

        <!-- Performance Plan -->
        <div class="lp-plan-card lp-reveal lp-reveal-delay-2">
          <div class="lp-plan-tier">Advanced</div>
          <div class="lp-plan-price-row">
            <span class="lp-plan-sup">$</span>
            <span class="lp-plan-price">10K</span>
          </div>
          <div class="lp-plan-meta">minimum &nbsp;·&nbsp; 60-day term<br>0.60%–0.80% daily &nbsp;·&nbsp; 36–48% total</div>
          <a href="/register" class="lp-plan-cta lp-plan-cta--default">Performance Plan</a>
          <div class="lp-plan-divider"></div>
          <ul class="lp-plan-features">
            <li class="lp-plan-feature">
              <div class="lp-plan-feature-icon">
                <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              </div>
              36%–48% total return
            </li>
            <li class="lp-plan-feature">
              <div class="lp-plan-feature-icon">
                <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              </div>
              Portfolio analytics dashboard
            </li>
            <li class="lp-plan-feature">
              <div class="lp-plan-feature-icon">
                <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              </div>
              12-hour withdrawal speed
            </li>
            <li class="lp-plan-feature">
              <div class="lp-plan-feature-icon">
                <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              </div>
              Elite plan access
            </li>
          </ul>
        </div>

        <!-- Plaid Elite Plan (featured/dark) -->
        <div class="lp-plan-card lp-plan-card--featured lp-reveal lp-reveal-delay-3">
          <div class="lp-plan-tier">Exclusive</div>
          <div class="lp-plan-price-row">
            <span class="lp-plan-sup">$</span>
            <span class="lp-plan-price">50K</span>
          </div>
          <div class="lp-plan-meta">minimum &nbsp;·&nbsp; 90-day term<br>0.90%–1.20% daily &nbsp;·&nbsp; 81–108% total</div>
          <a href="/register" class="lp-plan-cta lp-plan-cta--featured">Plaid Elite Plan</a>
          <div class="lp-plan-divider"></div>
          <ul class="lp-plan-features">
            <li class="lp-plan-feature">
              <div class="lp-plan-feature-icon">
                <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              </div>
              Compound growth model
            </li>
            <li class="lp-plan-feature">
              <div class="lp-plan-feature-icon">
                <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              </div>
              81%–108% total return
            </li>
            <li class="lp-plan-feature">
              <div class="lp-plan-feature-icon">
                <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              </div>
              Dedicated account manager
            </li>
            <li class="lp-plan-feature">
              <div class="lp-plan-feature-icon">
                <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              </div>
              1-hour priority withdrawal
            </li>
            <li class="lp-plan-feature">
              <div class="lp-plan-feature-icon">
                <svg viewBox="0 0 256 256" fill="currentColor" width="10" height="10"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              </div>
              Invitation pools access
            </li>
          </ul>
        </div>

      </div>
    </div>
  </section>


  <!-- ==============================================================
       5. TSLA CHART
       ============================================================== -->
  <section class="lp-stock lp-section" aria-label="TSLA Live Chart">
    <div class="lp-container">
      <div class="lp-stock-inner">

        <div class="lp-reveal">
          <div class="lp-section-eyebrow">Live Market Data</div>
          <h2 class="lp-section-title" style="text-align:left;">TSLA Performance</h2>
          <p class="lp-section-sub">Our returns are powered by real Tesla market dynamics. Track TSLA live — the engine behind your investment yield. We monitor market cycles daily to maximize every investor's position.</p>
          <a href="/investments" class="lp-cta-primary" style="margin-top:var(--space-6);display:inline-flex;">
            View All Plans
            <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
              <path d="M221.66,133.66l-72,72a8,8,0,0,1-11.32-11.32L196.69,136H40a8,8,0,0,1,0-16H196.69L138.34,61.66a8,8,0,0,1,11.32-11.32l72,72A8,8,0,0,1,221.66,133.66Z"/>
            </svg>
          </a>
        </div>

        <div class="lp-stock-chart-wrap lp-reveal lp-reveal-delay-2">
          <!-- TradingView Advanced Chart Widget -->
          <div class="tradingview-widget-container" style="height:100%;width:100%">
            <div class="tradingview-widget-container__widget" style="height:100%;width:100%"></div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
            {
              "autosize": true,
              "symbol": "NASDAQ:TSLA",
              "interval": "D",
              "timezone": "Etc/UTC",
              "theme": "light",
              "style": "2",
              "locale": "en",
              "allow_symbol_change": false,
              "calendar": false,
              "hide_top_toolbar": false,
              "hide_legend": false,
              "support_host": "https://www.tradingview.com"
            }
            </script>
          </div>
        </div>

      </div>
    </div>
  </section>


  <!-- ==============================================================
       6. MEMBERSHIP PLANS
       ============================================================== -->
  <section class="lp-membership lp-section" aria-label="Membership Plans">
    <div class="lp-container">

      <div class="lp-section-header lp-reveal">
        <div class="lp-section-eyebrow">Membership Tiers</div>
        <h2 class="lp-section-title">Unlock More. Earn More.</h2>
        <p class="lp-section-sub">Membership elevates your investment experience — faster withdrawals, higher commissions, and exclusive access to elite features.</p>
      </div>

      <div class="lp-mem-grid">

        <!-- Basic -->
        <div class="lp-mem-card lp-reveal">
          <div class="lp-mem-tier">Basic Member</div>
          <div class="lp-mem-price">$49<span style="font-size:var(--text-base);font-weight:400;color:rgba(255,255,255,0.4);">/mo</span></div>
          <a href="/register" class="lp-mem-cta">Get Started</a>
          <div class="lp-mem-divider"></div>
          <ul class="lp-mem-features">
            <li class="lp-mem-feature">
              <div class="lp-mem-feature-icon">&#10003;</div>
              Up to 2 active investments
            </li>
            <li class="lp-mem-feature">
              <div class="lp-mem-feature-icon">&#10003;</div>
              3% referral commission
            </li>
            <li class="lp-mem-feature">
              <div class="lp-mem-feature-icon">&#10003;</div>
              72-hour withdrawal speed
            </li>
            <li class="lp-mem-feature" style="opacity:0.4;">
              <div class="lp-mem-feature-icon">&#10003;</div>
              Portfolio analytics
            </li>
          </ul>
        </div>

        <!-- Silver -->
        <div class="lp-mem-card lp-reveal lp-reveal-delay-1">
          <div class="lp-mem-tier">Silver Member</div>
          <div class="lp-mem-price">$99<span style="font-size:var(--text-base);font-weight:400;color:rgba(255,255,255,0.4);">/mo</span></div>
          <a href="/register" class="lp-mem-cta">Get Started</a>
          <div class="lp-mem-divider"></div>
          <ul class="lp-mem-features">
            <li class="lp-mem-feature">
              <div class="lp-mem-feature-icon">&#10003;</div>
              Up to 5 active investments
            </li>
            <li class="lp-mem-feature">
              <div class="lp-mem-feature-icon">&#10003;</div>
              5% referral commission
            </li>
            <li class="lp-mem-feature">
              <div class="lp-mem-feature-icon">&#10003;</div>
              24-hour withdrawal speed
            </li>
            <li class="lp-mem-feature">
              <div class="lp-mem-feature-icon">&#10003;</div>
              Priority email support
            </li>
          </ul>
        </div>

        <!-- Gold -->
        <div class="lp-mem-card lp-reveal lp-reveal-delay-2" style="border-color:rgba(245,166,35,0.4);">
          <div class="lp-mem-tier" style="color:rgba(245,166,35,0.9);">Gold Member</div>
          <div class="lp-mem-price">$199<span style="font-size:var(--text-base);font-weight:400;color:rgba(255,255,255,0.4);">/mo</span></div>
          <a href="/register" class="lp-mem-cta" style="border-color:rgba(245,166,35,0.4);">Get Started</a>
          <div class="lp-mem-divider"></div>
          <ul class="lp-mem-features">
            <li class="lp-mem-feature">
              <div class="lp-mem-feature-icon">&#10003;</div>
              Up to 10 active investments
            </li>
            <li class="lp-mem-feature">
              <div class="lp-mem-feature-icon">&#10003;</div>
              7% referral commission
            </li>
            <li class="lp-mem-feature">
              <div class="lp-mem-feature-icon">&#10003;</div>
              12-hour withdrawal speed
            </li>
            <li class="lp-mem-feature">
              <div class="lp-mem-feature-icon">&#10003;</div>
              Analytics &amp; strategy dashboard
            </li>
          </ul>
        </div>

        <!-- Platinum -->
        <div class="lp-mem-card lp-reveal lp-reveal-delay-3" style="border-color:rgba(229,228,226,0.3);background:rgba(255,255,255,0.08);">
          <div class="lp-mem-tier" style="color:rgba(229,228,226,0.8);">Platinum Member</div>
          <div class="lp-mem-price">$499<span style="font-size:var(--text-base);font-weight:400;color:rgba(255,255,255,0.4);">/mo</span></div>
          <a href="/register" class="lp-mem-cta" style="border-color:rgba(229,228,226,0.3);background:var(--color-primary);border-width:0;">Get Started</a>
          <div class="lp-mem-divider"></div>
          <ul class="lp-mem-features">
            <li class="lp-mem-feature">
              <div class="lp-mem-feature-icon">&#10003;</div>
              Unlimited active investments
            </li>
            <li class="lp-mem-feature">
              <div class="lp-mem-feature-icon">&#10003;</div>
              10% referral commission
            </li>
            <li class="lp-mem-feature">
              <div class="lp-mem-feature-icon">&#10003;</div>
              1-hour priority withdrawal
            </li>
            <li class="lp-mem-feature">
              <div class="lp-mem-feature-icon">&#10003;</div>
              Personal account manager
            </li>
            <li class="lp-mem-feature">
              <div class="lp-mem-feature-icon">&#10003;</div>
              Quarterly strategy reports
            </li>
          </ul>
        </div>

      </div>
    </div>
  </section>


  <!-- ==============================================================
       7. HOW IT WORKS
       ============================================================== -->
  <section class="lp-how lp-section" aria-label="How it works">
    <div class="lp-container">

      <div class="lp-section-header lp-reveal">
        <div class="lp-section-eyebrow">Getting Started</div>
        <h2 class="lp-section-title">Invest in Three Steps</h2>
        <p class="lp-section-sub">From signup to earning — the entire process takes less than 10 minutes.</p>
      </div>

      <div class="lp-how-steps">

        <div class="lp-how-step lp-reveal">
          <div class="lp-how-num">1</div>
          <div class="lp-how-title">Create Your Account</div>
          <p class="lp-how-desc">Register in seconds. Verify your email, complete the 3-step onboarding, and your account is live immediately.</p>
        </div>

        <div class="lp-how-step lp-reveal lp-reveal-delay-1">
          <div class="lp-how-num">2</div>
          <div class="lp-how-title">Deposit &amp; Choose a Plan</div>
          <p class="lp-how-desc">Fund your wallet via Bitcoin, Ethereum, or USDT. Select the investment plan that matches your capital and goals.</p>
        </div>

        <div class="lp-how-step lp-reveal lp-reveal-delay-2">
          <div class="lp-how-num">3</div>
          <div class="lp-how-title">Watch Returns Grow</div>
          <p class="lp-how-desc">Daily profits are credited automatically. Track everything from your dashboard and withdraw on your schedule.</p>
        </div>

      </div>
    </div>
  </section>


  <!-- ==============================================================
       8. TESTIMONIALS CAROUSEL
       ============================================================== -->
  <section class="lp-testimonials lp-section" aria-label="Investor Testimonials">
    <div class="lp-container">

      <div class="lp-testimonials-header">
        <div>
          <div class="lp-section-eyebrow">Investor Stories</div>
          <h2 class="lp-section-title" style="text-align:left;margin-bottom:0;">What Our Investors Say</h2>
        </div>
        <div class="lp-testimonials-nav">
          <button class="lp-testimonials-nav-btn" id="lp-testimonial-prev" aria-label="Previous">
            <svg viewBox="0 0 256 256" fill="currentColor" width="18" height="18" aria-hidden="true">
              <path d="M165.66,202.34a8,8,0,0,1-11.32,11.32l-80-80a8,8,0,0,1,0-11.32l80-80a8,8,0,0,1,11.32,11.32L91.31,128Z"/>
            </svg>
          </button>
          <button class="lp-testimonials-nav-btn" id="lp-testimonial-next" aria-label="Next">
            <svg viewBox="0 0 256 256" fill="currentColor" width="18" height="18" aria-hidden="true">
              <path d="M181.66,133.66l-80,80a8,8,0,0,1-11.32-11.32L164.69,128,90.34,53.66a8,8,0,0,1,11.32-11.32l80,80A8,8,0,0,1,181.66,133.66Z"/>
            </svg>
          </button>
        </div>
      </div>

      <div class="lp-testimonials-carousel">
        <div class="lp-testimonials-track" id="lp-testimonials-track">

          <div class="lp-testimonial-card">
            <p class="lp-testimonial-quote">I've tried several crypto yield platforms. Averon Investment stands out — the daily credits are consistent, the dashboard is clean, and withdrawals always hit on time. Running the Drive Plan for three months now. Genuinely impressive.</p>
            <div class="lp-testimonial-author">
              <div class="lp-testimonial-avatar" style="background:#002914;">MR</div>
              <div>
                <div class="lp-testimonial-name">Marcus R.</div>
                <div class="lp-testimonial-meta">Drive Plan &nbsp;·&nbsp; Gold Member</div>
              </div>
            </div>
          </div>

          <div class="lp-testimonial-card">
            <p class="lp-testimonial-quote">Upgraded to Platinum last month and the 1-hour withdrawal speed alone is worth it. I moved $75K into the Plaid Elite plan and the compound model is doing exactly what it says. The account manager is responsive and knowledgeable too.</p>
            <div class="lp-testimonial-author">
              <div class="lp-testimonial-avatar" style="background:#C47A2B;">SK</div>
              <div>
                <div class="lp-testimonial-name">Sophia K.</div>
                <div class="lp-testimonial-meta">Plaid Elite Plan &nbsp;·&nbsp; Platinum Member</div>
              </div>
            </div>
          </div>

          <div class="lp-testimonial-card">
            <p class="lp-testimonial-quote">Started with the Launch Plan at $500. After the first 30-day term I rolled everything into Drive. The referral system is legitimately passive — I've earned $840 just from people I referred this quarter without any additional effort.</p>
            <div class="lp-testimonial-author">
              <div class="lp-testimonial-avatar" style="background:#2E7D32;">JT</div>
              <div>
                <div class="lp-testimonial-name">James T.</div>
                <div class="lp-testimonial-meta">Drive Plan &nbsp;·&nbsp; Silver Member</div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="lp-testimonials-dots" id="lp-testimonials-dots">
        <button class="lp-testimonial-dot active" data-index="0" aria-label="Testimonial 1"></button>
        <button class="lp-testimonial-dot" data-index="1" aria-label="Testimonial 2"></button>
        <button class="lp-testimonial-dot" data-index="2" aria-label="Testimonial 3"></button>
      </div>

    </div>
  </section>


  <!-- ==============================================================
       9. FINAL CTA
       ============================================================== -->
  <section class="lp-cta" aria-label="Call to action">
    <div class="lp-container">
      <h2 class="lp-cta-title">Start Earning Daily Returns Today</h2>
      <p class="lp-cta-sub">Join 12,400+ investors already growing their wealth on Averon Investment. Setup takes less than 5 minutes — no minimums on your first account.</p>
      <div class="lp-cta-actions">
        <a href="/register" class="lp-cta-primary">
          Create Free Account
          <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
            <path d="M221.66,133.66l-72,72a8,8,0,0,1-11.32-11.32L196.69,136H40a8,8,0,0,1,0-16H196.69L138.34,61.66a8,8,0,0,1,11.32-11.32l72,72A8,8,0,0,1,221.66,133.66Z"/>
          </svg>
        </a>
        <a href="/investments" class="lp-cta-secondary" style="border-color:rgba(255,255,255,0.25);color:rgba(255,255,255,0.8);">
          Explore Plans
        </a>
      </div>
      <p class="lp-cta-note">No minimum commitment. Cancel or withdraw anytime after your plan term.</p>
    </div>
  </section>

</main>

<?php include '../../includes/footer.php'; ?>

<script>
/* ── Scroll-reveal via IntersectionObserver ── */
(function () {
  var els = document.querySelectorAll('.lp-reveal');
  if (!els.length) return;
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (e.isIntersecting) {
        e.target.classList.add('lp-visible');
        io.unobserve(e.target);
      }
    });
  }, { threshold: 0.12 });
  els.forEach(function (el) { io.observe(el); });
})();

/* ── Testimonials carousel ── */
(function () {
  var track  = document.getElementById('lp-testimonials-track');
  var dots   = document.querySelectorAll('#lp-testimonials-dots .lp-testimonial-dot');
  var prevBtn = document.getElementById('lp-testimonial-prev');
  var nextBtn = document.getElementById('lp-testimonial-next');
  if (!track) return;

  var current = 0;
  var total   = dots.length;

  function goTo(n) {
    current = (n + total) % total;
    track.style.transform = 'translateX(-' + (current * 100) + '%)';
    dots.forEach(function (d, i) {
      d.classList.toggle('active', i === current);
    });
  }

  if (prevBtn) prevBtn.addEventListener('click', function () { goTo(current - 1); });
  if (nextBtn) nextBtn.addEventListener('click', function () { goTo(current + 1); });
  dots.forEach(function (d) {
    d.addEventListener('click', function () { goTo(parseInt(d.dataset.index, 10)); });
  });

  /* Auto-advance every 6 seconds */
  setInterval(function () { goTo(current + 1); }, 6000);
})();
</script>

<script src="/assets/js/main.js"></script>

</body>
</html>
