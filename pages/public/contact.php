<?php
$pageTitle = 'Contact Us';
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
        <span class="section-eyebrow">Get In Touch</span>
        <h1 class="section-title">We're Here to Help</h1>
        <p class="section-subtitle">Have a question about your account, an investment, or our platform? Reach out and our team will respond promptly.</p>
      </div>
    </div>
  </section>

  <!-- Contact Layout -->
  <section class="section section-surface">
    <div class="container">
      <div class="contact-layout">

        <!-- Form -->
        <div class="contact-form-wrap fade-in">
          <form id="contact-form" class="form-stack" novalidate>

            <div class="form-group">
              <label class="form-label" for="contact-name">Full Name</label>
              <input type="text" id="contact-name" name="name" class="form-control"
                     placeholder="Your full name" required autocomplete="name">
            </div>

            <div class="form-group">
              <label class="form-label" for="contact-email">Email Address</label>
              <input type="email" id="contact-email" name="email" class="form-control"
                     placeholder="you@example.com" required autocomplete="email">
            </div>

            <div class="form-group">
              <label class="form-label" for="contact-subject">Subject</label>
              <select id="contact-subject" name="subject" class="form-control" required>
                <option value="">Select a subject...</option>
                <option value="account">Account &amp; Login</option>
                <option value="deposit">Deposits &amp; Payments</option>
                <option value="withdrawal">Withdrawal Requests</option>
                <option value="investment">Investment Plans</option>
                <option value="membership">Membership</option>
                <option value="referral">Referral Program</option>
                <option value="technical">Technical Issue</option>
                <option value="other">Other</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="contact-message">Message</label>
              <textarea id="contact-message" name="message" class="form-control form-textarea"
                        placeholder="Describe your question or issue in detail..." required rows="5"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-full" id="contact-submit">Send Message</button>

          </form>
        </div>

        <!-- Info Panel -->
        <div class="contact-info stagger-item">

          <div class="contact-info-card">
            <div class="contact-info-icon">
              <svg viewBox="0 0 256 256" fill="currentColor" width="24" height="24" aria-hidden="true"><path d="M220,40H36A16,16,0,0,0,20,56V192a16,16,0,0,0,16,16H220a16,16,0,0,0,16-16V56A16,16,0,0,0,220,40Zm-12,16L128,110.81,48,56ZM220,192H36V70.19l88,57.62a8,8,0,0,0,8.1,0L220,70.19V192Z"/></svg>
            </div>
            <div>
              <p class="contact-info-label">Email Support</p>
              <p class="contact-info-value">support@averon-investment.com</p>
              <p class="contact-info-note">Responses within 24 hours</p>
            </div>
          </div>

          <div class="contact-info-card">
            <div class="contact-info-icon">
              <svg viewBox="0 0 256 256" fill="currentColor" width="24" height="24" aria-hidden="true"><path d="M228.88,26.19a9,9,0,0,0-9.16-1.57L17.06,103.93a14.22,14.22,0,0,0,2.43,27.21L72,141.45V200a15.92,15.92,0,0,0,10,14.83,15.91,15.91,0,0,0,17.51-3.73l25.32-26.26L165,220a15.88,15.88,0,0,0,10.51,4,16.3,16.3,0,0,0,5-.79,15.85,15.85,0,0,0,10.67-11.63L231.77,35A9,9,0,0,0,228.88,26.19Z"/></svg>
            </div>
            <div>
              <p class="contact-info-label">Telegram</p>
              <p class="contact-info-value">@AveronInvestSupport</p>
              <p class="contact-info-note">Fastest response channel</p>
            </div>
          </div>

          <div class="contact-info-card">
            <div class="contact-info-icon">
              <svg viewBox="0 0 256 256" fill="currentColor" width="24" height="24" aria-hidden="true"><path d="M236,128a108,108,0,1,1-108-108A108.12,108.12,0,0,1,236,128Zm-16,0a92,92,0,1,0-92,92A92.1,92.1,0,0,0,220,128Zm-84,36V128H120a8,8,0,0,1,0-16h24a8,8,0,0,1,8,8v44a8,8,0,0,1-16,0ZM116,76a16,16,0,1,1,16,16A16,16,0,0,1,116,76Z"/></svg>
            </div>
            <div>
              <p class="contact-info-label">Support Hours</p>
              <p class="contact-info-value">Monday – Saturday</p>
              <p class="contact-info-note">9:00 AM – 9:00 PM UTC</p>
            </div>
          </div>

          <div class="contact-info-card">
            <div class="contact-info-icon">
              <svg viewBox="0 0 256 256" fill="currentColor" width="24" height="24" aria-hidden="true"><path d="M224,128a96,96,0,1,1-96-96A96.11,96.11,0,0,1,224,128Zm-16,0a80,80,0,1,0-80,80A80.09,80.09,0,0,0,208,128Zm-68-8H116a8,8,0,0,0,0,16h12v8a8,8,0,0,0,16,0v-8a24,24,0,0,0,0-48H128a8,8,0,0,1,0-16h28a8,8,0,0,0,0-16H144V48a8,8,0,0,0-16,0v8a24,24,0,0,0,0,48h16a8,8,0,0,1,0,16Z"/></svg>
            </div>
            <div>
              <p class="contact-info-label">Help Center</p>
              <p class="contact-info-value"><a href="/support" class="link">Browse FAQs</a></p>
              <p class="contact-info-note">Common questions answered instantly</p>
            </div>
          </div>

        </div>

      </div>
    </div>
  </section>

</main>

<?php include '../../includes/footer.php'; ?>
<script src="/assets/js/main.js"></script>
<script src="/assets/js/contact.js"></script>
</body>
</html>
