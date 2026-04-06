<?php
$pageTitle = 'Help Center';
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
        <span class="section-eyebrow">Help Center</span>
        <h1 class="section-title">Frequently Asked Questions</h1>
        <p class="section-subtitle">Find answers to the most common questions about Averon Investment. Can't find what you're looking for? <a href="/contact" class="link">Contact our support team.</a></p>
      </div>
    </div>
  </section>

  <!-- FAQ Sections -->
  <section class="section section-surface">
    <div class="container">
      <div class="faq-layout">

        <!-- Account & Registration -->
        <div class="faq-section fade-in">
          <h2 class="faq-section-title">Account &amp; Registration</h2>

          <details class="faq-item">
            <summary class="faq-question">How do I create an account?</summary>
            <div class="faq-answer">
              <p>Creating an account is free and takes under 2 minutes. Go to the <a href="/register" class="link">Sign Up page</a>, complete the 3-step registration form, and verify your email address with the 6-digit code we send you. Once verified, your account is activated and you can fund your wallet immediately.</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">I didn't receive my verification code. What should I do?</summary>
            <div class="faq-answer">
              <p>Check your spam or junk folder first — verification emails sometimes land there. If you still don't see it, wait 2 minutes and use the Resend Code option on the verification page. Codes expire after 15 minutes, so request a fresh one if needed. Make sure you entered the correct email address during registration.</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">Can I change my email address?</summary>
            <div class="faq-answer">
              <p>Email changes are processed by our support team for security reasons. Contact us via <a href="/contact" class="link">the contact form</a> or email support@averon-investment.com with your account details and the new email address you'd like to use.</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">How do I reset my password?</summary>
            <div class="faq-answer">
              <p>Click "Forgot password?" on the login page or visit <a href="/forgot-password" class="link">this link</a>. Enter your registered email address and we'll send you a password reset link. The link expires after 1 hour. If you don't receive it, check spam or contact support.</p>
            </div>
          </details>
        </div>

        <!-- Deposits -->
        <div class="faq-section fade-in">
          <h2 class="faq-section-title">Deposits &amp; Funding</h2>

          <details class="faq-item">
            <summary class="faq-question">What cryptocurrencies can I deposit?</summary>
            <div class="faq-answer">
              <p>We accept four cryptocurrencies: Bitcoin (BTC), Ethereum (ETH), USDT on the TRC-20 network (Tron), and USDT on the ERC-20 network (Ethereum). Select your preferred currency when making a deposit to receive the correct wallet address.</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">How long does a deposit take to confirm?</summary>
            <div class="faq-answer">
              <p>Confirmation times vary by blockchain. USDT TRC-20 is typically the fastest (1–2 minutes). ETH and USDT ERC-20 usually confirm within 5–15 minutes. BTC can take 10–60 minutes depending on network congestion. Once confirmed on-chain, your wallet balance updates automatically.</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">Is there a minimum deposit amount?</summary>
            <div class="faq-answer">
              <p>The minimum deposit depends on the investment plan you wish to activate. The Launch Plan requires a minimum of $100. Drive Plan requires $1,000. Performance Plan requires $10,000. Plaid Elite requires $50,000. You can also hold uninvested balance in your wallet.</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">My deposit is confirmed on-chain but not showing in my wallet. What should I do?</summary>
            <div class="faq-answer">
              <p>In rare cases, IPN (webhook) notifications can be delayed by up to 30 minutes. Wait and refresh your dashboard. If your balance doesn't update after 1 hour, contact support with your transaction hash (TXID) so we can manually verify and credit your deposit.</p>
            </div>
          </details>
        </div>

        <!-- Investments -->
        <div class="faq-section fade-in">
          <h2 class="faq-section-title">Investment Plans</h2>

          <details class="faq-item">
            <summary class="faq-question">How are daily profits calculated?</summary>
            <div class="faq-answer">
              <p>Daily profits are calculated as a percentage of your invested capital, using the midpoint of the plan's daily yield range. For example, the Launch Plan yields 0.20–0.30% daily — so the average is 0.25% per day. On a $1,000 investment, that's $2.50 per day. Profits are credited automatically every 24 hours.</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">Can I withdraw my capital before the plan ends?</summary>
            <div class="faq-answer">
              <p>Capital is locked for the full term of the plan (30–90 days depending on the plan). Early withdrawal of capital is not available. This structure allows us to generate the returns promised. You can withdraw earned profits after the plan's profit withdrawal window opens.</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">What is the difference between simple and compound compounding?</summary>
            <div class="faq-answer">
              <p>Simple compounding means profits are calculated on your original capital only — they don't roll back into the principal. Compound compounding (used in the Plaid Elite Plan) means daily profits are reinvested, so tomorrow's yield is calculated on a slightly larger amount. Compounding grows your balance faster over 90 days.</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">What happens when my plan ends?</summary>
            <div class="faq-answer">
              <p>When a plan's term completes, your capital is returned to your wallet balance and any remaining profits are credited to your profit balance. You can then withdraw, reinvest, or start a new plan immediately.</p>
            </div>
          </details>
        </div>

        <!-- Withdrawals -->
        <div class="faq-section fade-in">
          <h2 class="faq-section-title">Withdrawals</h2>

          <details class="faq-item">
            <summary class="faq-question">How do I request a withdrawal?</summary>
            <div class="faq-answer">
              <p>Go to your Wallet page in the dashboard and click "Withdraw". Enter the amount and your crypto wallet address. Withdrawals are processed within the timeframe specified by your membership tier — from 1 hour (Platinum) to 72 hours (Basic).</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">Is there a minimum withdrawal amount?</summary>
            <div class="faq-answer">
              <p>The minimum withdrawal amount is $50 USD equivalent. This helps cover network transaction fees while ensuring your withdrawal is worth processing.</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">Why is my withdrawal taking longer than expected?</summary>
            <div class="faq-answer">
              <p>Processing times depend on your membership tier. If processing time has exceeded your tier's limit, please contact support with your withdrawal request ID. We may request additional KYC verification for large withdrawals to ensure account security.</p>
            </div>
          </details>
        </div>

        <!-- Membership -->
        <div class="faq-section fade-in">
          <h2 class="faq-section-title">Membership</h2>

          <details class="faq-item">
            <summary class="faq-question">Do I need a membership to invest?</summary>
            <div class="faq-answer">
              <p>No. You can invest and earn daily returns without any membership. Membership is optional and provides platform advantages: faster withdrawals, more investment slots, higher referral commissions, and priority support. Think of it as an upgrade to how you experience the platform.</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">Can I upgrade or downgrade my membership?</summary>
            <div class="faq-answer">
              <p>Yes. You can upgrade your membership at any time from your dashboard, and the new tier activates immediately. Downgrading takes effect at the end of your current billing cycle.</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">How is membership billed?</summary>
            <div class="faq-answer">
              <p>Membership fees are deducted from your platform wallet balance at the start of each 30-day cycle. Ensure your wallet has sufficient balance before renewal to avoid interruption in your membership benefits.</p>
            </div>
          </details>
        </div>

        <!-- Security -->
        <div class="faq-section fade-in">
          <h2 class="faq-section-title">Security</h2>

          <details class="faq-item">
            <summary class="faq-question">How does Averon Investment keep my funds safe?</summary>
            <div class="faq-answer">
              <p>All communications are encrypted via TLS. User sessions use secure HTTP-only cookies with CSRF protection. Passwords are hashed using bcrypt with a high cost factor. Withdrawal requests are verified against your registered withdrawal address. We never store raw payment credentials.</p>
            </div>
          </details>

          <details class="faq-item">
            <summary class="faq-question">What should I do if I suspect unauthorized access to my account?</summary>
            <div class="faq-answer">
              <p>Reset your password immediately via the <a href="/forgot-password" class="link">forgot password page</a>. Then contact our support team via Telegram or email so we can review your account activity and lock any suspicious sessions.</p>
            </div>
          </details>
        </div>

      </div>
    </div>
  </section>

  <!-- Contact Banner -->
  <section class="section section-white fade-in">
    <div class="container">
      <div class="section-header text-center">
        <h2 class="section-title">Still Have Questions?</h2>
        <p class="section-subtitle">Our support team is available Monday through Saturday. We'll get back to you within 24 hours.</p>
        <div class="hero-actions">
          <a href="/contact" class="btn btn-primary">Contact Support</a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php include '../../includes/footer.php'; ?>
<script src="/assets/js/main.js"></script>
</body>
</html>
