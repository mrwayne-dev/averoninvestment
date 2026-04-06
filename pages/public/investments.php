<?php
$pageTitle = 'Investment Plans';
if (session_status() === PHP_SESSION_NONE) session_start();
$isLoggedIn = isset($_SESSION['user_id']);
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
        <span class="section-eyebrow">Investment Plans</span>
        <h1 class="section-title">Grow Your Wealth Daily</h1>
        <p class="section-subtitle">Choose a plan that fits your capital. Every plan delivers daily returns — from the moment you invest.</p>
      </div>
    </div>
  </section>

  <!-- Plans Grid -->
  <section class="section section-surface">
    <div class="container">
      <div class="pricing-grid pricing-grid--4">

        <!-- Launch Plan -->
        <div class="pricing-card stagger-item">
          <div class="pricing-card__tier">Launch Plan</div>
          <p class="pricing-card__tagline">$100 – $999 · entry-level returns</p>
          <div class="pricing-card__price-block">
            <div class="pricing-card__price">0.20–0.30%</div>
            <div class="pricing-card__meta">daily return · 6%–9% total · 30-day term</div>
          </div>
          <a href="/register" class="pricing-card__cta">Get Started</a>
          <div class="pricing-card__divider"></div>
          <ul class="pricing-card__features">
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Daily profit credits
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Simple compounding
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Crypto deposits accepted
            </li>
            <li class="pricing-card__feature pricing-card__feature--disabled">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M165.66,101.66,139.31,128l26.35,26.34a8,8,0,0,1-11.32,11.32L128,139.31l-26.34,26.35a8,8,0,0,1-11.32-11.32L116.69,128,90.34,101.66a8,8,0,0,1,11.32-11.32L128,116.69l26.34-26.35a8,8,0,0,1,11.32,11.32ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              No dedicated manager
            </li>
          </ul>
        </div>

        <!-- Drive Plan -->
        <div class="pricing-card stagger-item">
          <div class="pricing-card__tier">Drive Plan</div>
          <p class="pricing-card__tagline">$1,000 – $9,999 · scale your returns</p>
          <div class="pricing-card__price-block">
            <div class="pricing-card__price">0.35–0.50%</div>
            <div class="pricing-card__meta">daily return · 15%–22% total · 45-day term</div>
          </div>
          <a href="/register" class="pricing-card__cta">Get Started</a>
          <div class="pricing-card__divider"></div>
          <ul class="pricing-card__features">
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Daily profit credits
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Priority email support
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Profits after day 25
            </li>
            <li class="pricing-card__feature pricing-card__feature--disabled">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M165.66,101.66,139.31,128l26.35,26.34a8,8,0,0,1-11.32,11.32L128,139.31l-26.34,26.35a8,8,0,0,1-11.32-11.32L116.69,128,90.34,101.66a8,8,0,0,1,11.32-11.32L128,116.69l26.34-26.35a8,8,0,0,1,11.32,11.32ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              No dedicated manager
            </li>
          </ul>
        </div>

        <!-- Performance Plan -->
        <div class="pricing-card stagger-item">
          <div class="pricing-card__tier">Performance Plan</div>
          <p class="pricing-card__tagline">$10,000 – $49,999 · analytics &amp; elite access</p>
          <div class="pricing-card__price-block">
            <div class="pricing-card__price">0.60–0.80%</div>
            <div class="pricing-card__meta">daily return · 36%–48% total · 60-day term</div>
          </div>
          <a href="/register" class="pricing-card__cta">Get Started</a>
          <div class="pricing-card__divider"></div>
          <ul class="pricing-card__features">
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Daily profit credits
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Portfolio analytics dashboard
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Access to elite plans
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              12-hour withdrawal speed
            </li>
          </ul>
        </div>

        <!-- Plaid Elite Plan -->
        <div class="pricing-card pricing-card--featured stagger-item">
          <div class="pricing-card__tier">Plaid Elite Plan</div>
          <p class="pricing-card__tagline">$50,000+ · institutional-grade compound growth</p>
          <div class="pricing-card__price-block">
            <div class="pricing-card__price">0.90–1.20%</div>
            <div class="pricing-card__meta">daily return · 81%–108% total · 90-day term</div>
          </div>
          <a href="/register" class="pricing-card__cta">Get Started</a>
          <div class="pricing-card__divider"></div>
          <ul class="pricing-card__features">
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Compound growth model
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Dedicated account manager
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              1-hour priority withdrawal
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Invitation pools access
            </li>
          </ul>
        </div>

      </div>
    </div>
  </section>

  <!-- How Daily Profits Work -->
  <section class="section section-white">
    <div class="container">
      <div class="section-header text-center fade-in">
        <span class="section-eyebrow">How It Works</span>
        <h2 class="section-title">Your Money Works Every Day</h2>
        <p class="section-subtitle">From deposit to daily profit — here's the full cycle.</p>
      </div>
      <div class="how-steps">
        <div class="how-step stagger-item">
          <div class="how-step-number">01</div>
          <div class="how-step-content">
            <h3 class="how-step-title">Fund Your Wallet</h3>
            <p class="how-step-desc">Deposit crypto (BTC, ETH, USDT) into your Averon Investment wallet. Funds are confirmed on-chain and credited instantly.</p>
          </div>
        </div>
        <div class="how-step stagger-item">
          <div class="how-step-number">02</div>
          <div class="how-step-content">
            <h3 class="how-step-title">Activate a Plan</h3>
            <p class="how-step-desc">Choose a plan that matches your capital. Your investment is locked for the term duration and starts earning from day one.</p>
          </div>
        </div>
        <div class="how-step stagger-item">
          <div class="how-step-number">03</div>
          <div class="how-step-content">
            <h3 class="how-step-title">Earn Daily Returns</h3>
            <p class="how-step-desc">Profits are credited to your profit balance every 24 hours. Watch your balance grow in real time on your dashboard.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Deposit Currencies -->
  <section class="section section-surface">
    <div class="container">
      <div class="section-header text-center fade-in">
        <span class="section-eyebrow">Supported Currencies</span>
        <h2 class="section-title">Invest With Your Crypto</h2>
        <p class="section-subtitle">All deposits are processed securely via NOWPayments with real-time on-chain confirmation.</p>
      </div>
      <div class="feature-grid feature-grid--4">
        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="28" height="28" aria-hidden="true"><path d="M247.41,160.19a8,8,0,0,0-4.8-5.12l-30-11.17L224,128a8,8,0,0,0-4.42-10.58L176,100.94V72a8,8,0,0,0-4.42-7.16l-40-20a8,8,0,0,0-7.16,0l-40,20A8,8,0,0,0,80,72v28.94L36.42,117.42A8,8,0,0,0,32,128l11.39,15.9L13.39,155.07a8,8,0,0,0-4.8,10.12l16,48a8,8,0,0,0,10.12,4.8l112-37.33,112,37.33A8,8,0,0,0,256,216a8.13,8.13,0,0,0,2.53-.41,8,8,0,0,0,5.07-4.8l16-48A8,8,0,0,0,247.41,160.19ZM96,79.06l32-16,32,16V99.44L128,85.81,96,99.44ZM128,103.19l35.32,13.06L128,143.56,92.68,116.25ZM34.93,163.35l20.78-7.76,49.82,69.12Zm186.14,0L200.47,224.71l49.82-69.12Z"/></svg>
          </div>
          <h3 class="feature-title">Bitcoin (BTC)</h3>
          <p class="feature-desc">The original digital store of value. Deposit BTC directly from any compatible wallet.</p>
        </div>
        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="28" height="28" aria-hidden="true"><path d="M127.89,11a117,117,0,1,0,117,117A117.13,117.13,0,0,0,127.89,11Zm0,218a101,101,0,1,1,101-101A101.11,101.11,0,0,1,127.89,229Zm40-135.47a8,8,0,0,1-11.31-.22L136,72.31V168a8,8,0,0,1-16,0V72.31L99.42,93.31a8,8,0,1,1-11.53-11.09l32-33.33a8,8,0,0,1,11.53,0l32,33.33A8,8,0,0,1,167.89,93.53Zm0,64a8,8,0,0,1-11.31-.22L136,136.31V168a8,8,0,0,1-16,0V136.31l-20.58,21a8,8,0,1,1-11.53-11.09l32-33.33a8,8,0,0,1,11.53,0l32,33.33A8,8,0,0,1,167.89,157.53Z"/></svg>
          </div>
          <h3 class="feature-title">Ethereum (ETH)</h3>
          <p class="feature-desc">Fast, widely supported ERC-20 compatible deposits confirmed on the Ethereum network.</p>
        </div>
        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="28" height="28" aria-hidden="true"><path d="M152,120H136V104a8,8,0,0,0-16,0v16H104a8,8,0,0,0,0,16h16v16a8,8,0,0,0,16,0V136h16a8,8,0,0,0,0-16Zm72,8A96,96,0,1,1,128,32,96.11,96.11,0,0,1,224,128Zm-16,0a80,80,0,1,0-80,80A80.09,80.09,0,0,0,208,128Z"/></svg>
          </div>
          <h3 class="feature-title">USDT (TRC-20)</h3>
          <p class="feature-desc">Tron-based USDT with ultra-low fees. Ideal for frequent deposits and larger amounts.</p>
        </div>
        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="28" height="28" aria-hidden="true"><path d="M152,120H136V104a8,8,0,0,0-16,0v16H104a8,8,0,0,0,0,16h16v16a8,8,0,0,0,16,0V136h16a8,8,0,0,0,0-16Zm72,8A96,96,0,1,1,128,32,96.11,96.11,0,0,1,224,128Zm-16,0a80,80,0,1,0-80,80A80.09,80.09,0,0,0,208,128Z"/></svg>
          </div>
          <h3 class="feature-title">USDT (ERC-20)</h3>
          <p class="feature-desc">Ethereum-based USDT offering broad compatibility with major exchanges and wallets.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta-section fade-in">
    <div class="container">
      <div class="cta-content">
        <h2 class="cta-title">Ready to Start Earning?</h2>
        <p class="cta-subtitle">Create a free account in minutes. Your first deposit starts earning on day one.</p>
        <div class="cta-actions">
          <a href="/register" class="btn btn-primary">Open Free Account</a>
          <a href="/membership" class="btn btn-secondary">View Memberships</a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php include '../../includes/footer.php'; ?>
<script src="/assets/js/main.js"></script>
</body>
</html>
