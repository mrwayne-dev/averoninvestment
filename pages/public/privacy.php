<?php
$pageTitle = 'Privacy Policy';
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
          <h1 class="legal-title">Privacy Policy</h1>
          <p class="legal-meta">Last updated: <?= htmlspecialchars($lastUpdated) ?></p>
        </div>

        <div class="legal-body">

          <p class="legal-intro">Averon Investment ("we", "us", or "our") is committed to protecting your privacy. This Privacy Policy explains what information we collect, how we use it, and your rights regarding your personal data.</p>

          <h2>1. Information We Collect</h2>
          <p>We collect the following categories of information:</p>
          <ul>
            <li><strong>Account Information:</strong> First name, last name, email address, country/region, and language preference collected during registration.</li>
            <li><strong>Authentication Data:</strong> Hashed passwords and session tokens. We never store plain-text passwords.</li>
            <li><strong>Transaction Data:</strong> Deposit amounts, cryptocurrency addresses, withdrawal requests, investment plan details, and profit records.</li>
            <li><strong>Communication Data:</strong> Messages sent via our contact form, including name, email, and message content.</li>
            <li><strong>Technical Data:</strong> IP address, browser type, operating system, and access timestamps — collected automatically for security and fraud prevention.</li>
          </ul>

          <h2>2. How We Use Your Information</h2>
          <p>We use collected information to:</p>
          <ul>
            <li>Create and manage your account.</li>
            <li>Process deposits, investments, profit credits, and withdrawals.</li>
            <li>Send transactional emails (account verification, password resets, deposit confirmations, withdrawal updates).</li>
            <li>Send optional platform updates and market insights (you can opt out at any time).</li>
            <li>Detect and prevent fraud, unauthorized access, and other illegal activity.</li>
            <li>Comply with legal obligations including anti-money laundering (AML) requirements.</li>
          </ul>

          <h2>3. Cryptocurrency Addresses</h2>
          <p>When you make a deposit or withdrawal, cryptocurrency wallet addresses are collected and associated with your account. These addresses are stored to fulfill your transactions and for compliance purposes. We do not share wallet addresses with third parties except as required by law or to process your payment via our payment processor (NOWPayments).</p>

          <h2>4. Third-Party Services</h2>
          <p>We use a limited number of third-party services to operate the Platform:</p>
          <ul>
            <li><strong>NOWPayments:</strong> Handles cryptocurrency payment processing. Their privacy policy governs how they handle payment data.</li>
            <li><strong>SMTP Email Provider:</strong> Used to send transactional emails. Email content is transmitted securely.</li>
          </ul>
          <p>We do not sell, rent, or trade your personal information to any third party for marketing purposes.</p>

          <h2>5. Data Retention</h2>
          <p>We retain your account data for as long as your account is active. Transaction records are retained for a minimum of 5 years to comply with financial regulations. If you request account deletion, we will delete your personal data within 30 days, except where retention is required by law.</p>

          <h2>6. Cookies and Session Data</h2>
          <p>We use session cookies to maintain your login state. These cookies are HTTP-only and not accessible via JavaScript. We do not use advertising cookies or third-party tracking pixels. You can disable cookies in your browser settings, but this may prevent you from using certain Platform features.</p>

          <h2>7. Security</h2>
          <p>We implement industry-standard security measures to protect your data:</p>
          <ul>
            <li>All data transmitted between your browser and our servers is encrypted via TLS/HTTPS.</li>
            <li>Passwords are hashed using bcrypt with a high cost factor.</li>
            <li>Sessions use secure, HTTP-only cookies with CSRF protection.</li>
            <li>Access to user data is restricted to authorized personnel only.</li>
          </ul>
          <p>Despite our efforts, no system is 100% secure. If you discover a security vulnerability, please report it to security@averon-investment.com.</p>

          <h2>8. Your Rights</h2>
          <p>Depending on your location, you may have the following rights regarding your personal data:</p>
          <ul>
            <li><strong>Access:</strong> Request a copy of the personal data we hold about you.</li>
            <li><strong>Correction:</strong> Request correction of inaccurate or incomplete data.</li>
            <li><strong>Deletion:</strong> Request deletion of your personal data (subject to legal retention requirements).</li>
            <li><strong>Portability:</strong> Request your data in a machine-readable format.</li>
            <li><strong>Opt-Out:</strong> Unsubscribe from marketing emails at any time using the unsubscribe link in any email we send.</li>
          </ul>
          <p>To exercise any of these rights, contact us at <a href="mailto:privacy@averon-investment.com" class="link">privacy@averon-investment.com</a>.</p>

          <h2>9. Children's Privacy</h2>
          <p>Averon Investment is not directed at persons under the age of 18. We do not knowingly collect personal information from minors. If we become aware that a minor has created an account, we will terminate it and delete associated data immediately.</p>

          <h2>10. Changes to This Policy</h2>
          <p>We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated date and communicated via email to registered users where required by law. Continued use of the Platform after changes are posted constitutes your acceptance.</p>

          <h2>11. Contact</h2>
          <p>If you have questions or concerns about this Privacy Policy or how we handle your data, contact us at <a href="mailto:privacy@averon-investment.com" class="link">privacy@averon-investment.com</a> or via the <a href="/contact" class="link">Contact page</a>.</p>

        </div>

      </div>
    </div>
  </section>

</main>

<?php include '../../includes/footer.php'; ?>
<script src="/assets/js/main.js"></script>
</body>
</html>
