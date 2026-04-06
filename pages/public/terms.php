<?php
$pageTitle = 'Terms of Service';
$lastUpdated = 'March 1, 2026';
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

  <section class="section section-white">
    <div class="container">
      <div class="legal-wrap fade-in">

        <div class="legal-header">
          <h1 class="legal-title">Terms of Service</h1>
          <p class="legal-meta">Last updated: <?= htmlspecialchars($lastUpdated) ?></p>
        </div>

        <div class="legal-body">

          <p class="legal-intro">Please read these Terms of Service ("Terms") carefully before using the Averon Investment platform. By creating an account or using any of our services, you agree to be bound by these Terms.</p>

          <h2>1. Acceptance of Terms</h2>
          <p>By accessing or using Averon Investment ("Platform", "we", "us", or "our"), you confirm that you are at least 18 years of age, have the legal capacity to enter into binding agreements, and agree to comply with these Terms. If you do not agree to any part of these Terms, you may not use the Platform.</p>

          <h2>2. Platform Description</h2>
          <p>Averon Investment is a digital investment platform that allows users to deposit cryptocurrency assets and participate in fixed-term investment plans that generate daily returns. The Platform also offers optional membership tiers that provide enhanced platform privileges including faster withdrawals, referral commissions, and additional investment slots.</p>

          <h2>3. Account Registration</h2>
          <p>To access investment features, you must create an account by providing accurate and complete information. You are responsible for maintaining the confidentiality of your account credentials and for all activity that occurs under your account. You must notify us immediately at support@averon-investment.com if you suspect unauthorized use of your account.</p>
          <p>We reserve the right to suspend or terminate accounts that provide false information, violate these Terms, or engage in fraudulent activity.</p>

          <h2>4. Investment Plans</h2>
          <p>Investment plans are fixed-term contracts. By activating a plan, you acknowledge that:</p>
          <ul>
            <li>Capital is locked for the full term duration and cannot be withdrawn early.</li>
            <li>Daily profit rates are expressed as a range and the actual credited rate may vary within the stated range.</li>
            <li>Profit withdrawals become available only after the profit withdrawal window opens for each specific plan.</li>
            <li>Past performance does not guarantee future results.</li>
            <li>Investment involves risk and you may not recover your full capital in extreme market conditions.</li>
          </ul>

          <h2>5. Deposits and Withdrawals</h2>
          <p>All deposits are made via supported cryptocurrency networks. You are responsible for sending the correct currency to the correct address. Funds sent to wrong addresses cannot be recovered. Withdrawals are processed within the timeframe specified by your active membership tier. The Platform reserves the right to require identity verification (KYC) for large withdrawal requests as part of anti-money laundering compliance.</p>

          <h2>6. Membership Plans</h2>
          <p>Membership is a monthly subscription that provides platform privileges. Membership fees are non-refundable once the billing cycle has begun. Membership does not guarantee investment returns. Benefits are as described at the time of enrollment and may be updated with 30 days' notice.</p>

          <h2>7. Referral Program</h2>
          <p>The referral program allows you to earn commissions when referred users deposit funds. Referral commissions are paid as a percentage of deposits, as defined by your membership tier. Referral abuse, including self-referral or use of fake accounts, will result in account termination and forfeiture of all referral earnings.</p>

          <h2>8. Prohibited Conduct</h2>
          <p>You may not use the Platform to:</p>
          <ul>
            <li>Violate any applicable law or regulation, including anti-money laundering (AML) and know-your-customer (KYC) requirements.</li>
            <li>Engage in fraudulent, deceptive, or manipulative activity.</li>
            <li>Attempt to access the Platform by unauthorized means or circumvent security measures.</li>
            <li>Use automated tools, bots, or scripts to interact with the Platform without prior written permission.</li>
            <li>Harass, threaten, or abuse other users or Platform staff.</li>
          </ul>

          <h2>9. Risk Disclosure</h2>
          <p>Cryptocurrency investment carries significant risk. The value of digital assets is volatile. You acknowledge that you invest at your own risk and that Averon Investment does not guarantee profits or the preservation of capital. You should only invest what you can afford to lose. This Platform is not a licensed financial advisor and nothing on this Platform constitutes financial advice.</p>

          <h2>10. Limitation of Liability</h2>
          <p>To the maximum extent permitted by law, Averon Investment shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of the Platform, including but not limited to loss of profits, loss of data, or service interruption. Our total liability to you shall not exceed the total fees paid by you to the Platform in the 30 days preceding the claim.</p>

          <h2>11. Intellectual Property</h2>
          <p>All content on the Platform, including logos, text, graphics, and software, is the property of Averon Investment or its licensors and is protected by copyright and trademark law. You may not reproduce, distribute, or create derivative works without our express written consent.</p>

          <h2>12. Modifications to Terms</h2>
          <p>We reserve the right to update these Terms at any time. Changes will be communicated via email and posted on this page with an updated date. Continued use of the Platform after changes are posted constitutes your acceptance of the updated Terms.</p>

          <h2>13. Governing Law</h2>
          <p>These Terms are governed by and construed in accordance with applicable international digital commerce law. Any disputes shall be resolved through binding arbitration before resorting to litigation.</p>

          <h2>14. Contact</h2>
          <p>If you have questions about these Terms, please contact us at <a href="mailto:legal@averon-investment.com" class="link">legal@averon-investment.com</a> or via the <a href="/contact" class="link">Contact page</a>.</p>

        </div>

      </div>
    </div>
  </section>

</main>

<?php include '../../includes/footer.php'; ?>
<script src="/assets/js/main.js"></script>
</body>
</html>
