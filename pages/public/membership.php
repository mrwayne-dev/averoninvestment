<?php
$pageTitle = 'Membership Plans';
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
        <span class="section-eyebrow">Membership Tiers</span>
        <h1 class="section-title">Unlock Platform Privileges</h1>
        <p class="section-subtitle">Membership is your platform advantage — faster withdrawals, higher commissions, and exclusive access. It works alongside your investments, not instead of them.</p>
      </div>
    </div>
  </section>

  <!-- Membership vs Investment Distinction -->
  <section class="section section-surface">
    <div class="container">
      <div class="feature-grid feature-grid--2 fade-in">
        <div class="feature-card">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="28" height="28" aria-hidden="true"><path d="M216,72H180V52a28,28,0,0,0-56,0V72H76A20,20,0,0,0,56,92V200a20,20,0,0,0,20,20H216a20,20,0,0,0,20-20V92A20,20,0,0,0,216,72Zm-76-20a12,12,0,0,1,24,0V72H140ZM220,200a4,4,0,0,1-4,4H76a4,4,0,0,1-4-4V92a4,4,0,0,1,4-4H216a4,4,0,0,1,4,4Zm-86-52a10,10,0,1,1,10,10A10,10,0,0,1,134,148Z"/></svg>
          </div>
          <h3 class="feature-title">Investment Plans</h3>
          <p class="feature-desc">How you earn. Choose a capital amount and a term. Your money earns a fixed daily return for the duration of the plan. Capital is locked for the term.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="28" height="28" aria-hidden="true"><path d="M239.47,177.06,167.53,42.94a45.88,45.88,0,0,0-79.06,0L16.53,177.06a44,44,0,0,0,38.53,65H201A44,44,0,0,0,239.47,177.06ZM224.09,226a28,28,0,0,1-23.12,12H55a28,28,0,0,1-24.53-41.44l71.94-134.12a29.87,29.87,0,0,1,51.18,0l71.94,134.12A27.86,27.86,0,0,1,224.09,226ZM116,136V104a12,12,0,0,1,24,0v32a12,12,0,0,1-24,0Zm28,40a16,16,0,1,1-16-16A16,16,0,0,1,144,176Z"/></svg>
          </div>
          <h3 class="feature-title">Membership Plans</h3>
          <p class="feature-desc">How you operate on the platform. Membership is a monthly subscription that unlocks faster withdrawals, higher referral commissions, more investment slots, and premium support.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Membership Grid -->
  <section class="section section-white">
    <div class="container">
      <div class="section-header text-center fade-in">
        <span class="section-eyebrow">Choose Your Tier</span>
        <h2 class="section-title">Four Tiers. One Mission.</h2>
        <p class="section-subtitle">Billed monthly. Cancel anytime. Upgrade instantly.</p>
      </div>
      <div class="pricing-grid pricing-grid--4">

        <!-- Basic Member -->
        <div class="pricing-card stagger-item">
          <div class="pricing-card__tier">Basic Member</div>
          <p class="pricing-card__tagline">Starter platform access &amp; core benefits</p>
          <div class="pricing-card__price-block">
            <div class="pricing-card__price"><sup>$</sup>49</div>
            <div class="pricing-card__meta">per month · billed monthly</div>
          </div>
          <a href="/register" class="pricing-card__cta">Get Started</a>
          <div class="pricing-card__divider"></div>
          <ul class="pricing-card__features">
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Up to 2 active investments
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              72-hour withdrawals
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              3% referral commission
            </li>
            <li class="pricing-card__feature pricing-card__feature--disabled">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M165.66,101.66,139.31,128l26.35,26.34a8,8,0,0,1-11.32,11.32L128,139.31l-26.34,26.35a8,8,0,0,1-11.32-11.32L116.69,128,90.34,101.66a8,8,0,0,1,11.32-11.32L128,116.69l26.34-26.35a8,8,0,0,1,11.32,11.32ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              No analytics dashboard
            </li>
          </ul>
        </div>

        <!-- Silver Member -->
        <div class="pricing-card stagger-item">
          <div class="pricing-card__tier">Silver Member</div>
          <p class="pricing-card__tagline">Priority access &amp; faster withdrawals</p>
          <div class="pricing-card__price-block">
            <div class="pricing-card__price"><sup>$</sup>99</div>
            <div class="pricing-card__meta">per month · billed monthly</div>
          </div>
          <a href="/register" class="pricing-card__cta">Get Started</a>
          <div class="pricing-card__divider"></div>
          <ul class="pricing-card__features">
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Up to 5 active investments
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              24-hour withdrawals
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              5% referral commission
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Priority support
            </li>
          </ul>
        </div>

        <!-- Gold Member -->
        <div class="pricing-card pricing-card--featured stagger-item">
          <div class="pricing-card__tier">Gold Member</div>
          <p class="pricing-card__tagline">Analytics, elite plans &amp; dedicated support</p>
          <div class="pricing-card__price-block">
            <div class="pricing-card__price"><sup>$</sup>199</div>
            <div class="pricing-card__meta">per month · billed monthly</div>
          </div>
          <a href="/register" class="pricing-card__cta">Get Started</a>
          <div class="pricing-card__divider"></div>
          <ul class="pricing-card__features">
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Up to 10 active investments
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              12-hour withdrawals
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              7% referral commission
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Analytics dashboard
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Access to elite plans
            </li>
          </ul>
        </div>

        <!-- Platinum Member -->
        <div class="pricing-card stagger-item">
          <div class="pricing-card__tier">Platinum Member</div>
          <p class="pricing-card__tagline">Unlimited everything, personal manager</p>
          <div class="pricing-card__price-block">
            <div class="pricing-card__price"><sup>$</sup>499</div>
            <div class="pricing-card__meta">per month · billed monthly</div>
          </div>
          <a href="/register" class="pricing-card__cta">Get Started</a>
          <div class="pricing-card__divider"></div>
          <ul class="pricing-card__features">
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Unlimited active investments
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              1-hour withdrawals
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              10% referral commission
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Quarterly strategy reports
            </li>
            <li class="pricing-card__feature">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true"><path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"/></svg>
              Personal account manager
            </li>
          </ul>
        </div>

      </div>
    </div>
  </section>

  <!-- Benefits Breakdown -->
  <section class="section section-surface">
    <div class="container">
      <div class="section-header text-center fade-in">
        <span class="section-eyebrow">Why Upgrade?</span>
        <h2 class="section-title">The Difference Is Real</h2>
      </div>
      <div class="feature-grid feature-grid--3">
        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="28" height="28" aria-hidden="true"><path d="M236,128a108,108,0,1,1-108-108A108.12,108.12,0,0,1,236,128Zm-16,0a92,92,0,1,0-92,92A92.1,92.1,0,0,0,220,128Zm-84,36V128H120a8,8,0,0,1,0-16h24a8,8,0,0,1,8,8v44a8,8,0,0,1-16,0ZM116,76a16,16,0,1,1,16,16A16,16,0,0,1,116,76Z"/></svg>
          </div>
          <h3 class="feature-title">Faster Withdrawals</h3>
          <p class="feature-desc">Platinum members receive withdrawals in as little as 1 hour. Basic members wait up to 72 hours. Every tier upgrade cuts your wait time significantly.</p>
        </div>
        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="28" height="28" aria-hidden="true"><path d="M205.66,61.66a8,8,0,0,0-11.32,0L80,175.31,45.66,141A8,8,0,0,0,34.34,152.34l40,40a8,8,0,0,0,11.32,0l120-120A8,8,0,0,0,205.66,61.66Z"/></svg>
          </div>
          <h3 class="feature-title">Higher Referral Earnings</h3>
          <p class="feature-desc">Refer friends and earn 3–10% of every deposit they make. Platinum members earn the most from their network — passively, every time a referral deposits.</p>
        </div>
        <div class="feature-card stagger-item">
          <div class="feature-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="28" height="28" aria-hidden="true"><path d="M224,128a96,96,0,1,1-96-96A96.11,96.11,0,0,1,224,128Zm-16,0a80,80,0,1,0-80,80A80.09,80.09,0,0,0,208,128Zm-68-8H116a8,8,0,0,0,0,16h12v8a8,8,0,0,0,16,0v-8a24,24,0,0,0,0-48H128a8,8,0,0,1,0-16h28a8,8,0,0,0,0-16H144V48a8,8,0,0,0-16,0v8a24,24,0,0,0,0,48h16a8,8,0,0,1,0,16Z"/></svg>
          </div>
          <h3 class="feature-title">More Investment Slots</h3>
          <p class="feature-desc">Basic members can hold 2 active investments. Platinum members hold unlimited plans simultaneously — maximizing compound capital deployment.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta-section fade-in">
    <div class="container">
      <div class="cta-content">
        <h2 class="cta-title">Choose Your Membership</h2>
        <p class="cta-subtitle">All memberships are billed monthly. Upgrade or cancel at any time from your dashboard.</p>
        <div class="cta-actions">
          <a href="/register" class="btn btn-primary">Create Free Account</a>
          <a href="/investments" class="btn btn-secondary">View Investment Plans</a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php include '../../includes/footer.php'; ?>
<script src="/assets/js/main.js"></script>
</body>
</html>
