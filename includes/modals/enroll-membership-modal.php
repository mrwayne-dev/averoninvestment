<!-- =====================================================================
     Enroll Membership Modal
     - Accepts pre-filled plan data via openModal('modal-enroll-membership', {plan})
     - Checks wallet balance vs plan price; disables pay button if insufficient
     - "Deposit First" closes this modal and opens deposit modal
     - POST → api/user-dashboard/enroll-membership.php
     JS logic: initMembershipModal() in assets/js/main.js
     ===================================================================== -->
<div class="modal-overlay" id="modal-enroll-membership" role="dialog" aria-modal="true" aria-labelledby="membership-modal-title">
  <div class="bottom-sheet">
    <div class="bottom-sheet-handle" aria-hidden="true"></div>

    <div class="bottom-sheet-header">
      <h3 id="membership-modal-title">Enroll Membership</h3>
      <!-- title is updated dynamically by JS when managing an active plan -->
      <button
        type="button"
        class="modal-close"
        onclick="closeModal('modal-enroll-membership')"
        aria-label="Close membership modal"
      >
        <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
          <path d="M205.66,194.34a8,8,0,0,1-11.32,11.32L128,139.31,61.66,205.66a8,8,0,0,1-11.32-11.32L116.69,128,50.34,61.66A8,8,0,0,1,61.66,50.34L128,116.69l66.34-66.35a8,8,0,0,1,11.32,11.32L139.31,128Z"/>
        </svg>
      </button>
    </div>

    <div class="bottom-sheet-body">

      <!-- Plan detail card -->
      <div class="membership-plan-card" id="membership-plan-card" aria-live="polite">
        <div class="membership-plan-header">
          <div>
            <div class="membership-plan-name" id="membership-plan-name">—</div>
            <div class="membership-plan-price" id="membership-plan-price">—</div>
          </div>
          <div class="membership-plan-badge" id="membership-plan-badge" aria-hidden="true">
            <!-- badge icon injected by JS -->
          </div>
        </div>
        <ul class="membership-plan-benefits" id="membership-plan-benefits" aria-label="Plan benefits">
          <!-- JS populates benefit items -->
        </ul>
        <div class="membership-plan-meta-row">
          <div class="membership-plan-meta-item">
            <span class="membership-plan-meta-label">Withdrawal Speed</span>
            <strong class="membership-plan-meta-value" id="membership-withdrawal-speed">—</strong>
          </div>
          <div class="membership-plan-meta-item">
            <span class="membership-plan-meta-label">Max Investments</span>
            <strong class="membership-plan-meta-value" id="membership-max-investments">—</strong>
          </div>
        </div>
      </div>

      <!-- Wallet balance check -->
      <div class="membership-balance-check" id="membership-balance-check" aria-live="polite">
        <div class="withdraw-fee-row">
          <span class="withdraw-fee-label">Your Wallet Balance</span>
          <span class="withdraw-fee-value" id="membership-wallet-balance">$0.00</span>
        </div>
        <div class="withdraw-fee-row">
          <span class="withdraw-fee-label">Plan Price</span>
          <span class="withdraw-fee-value" id="membership-plan-cost">$0.00</span>
        </div>
      </div>

      <!-- Insufficient balance warning -->
      <div class="alert alert-warning hidden" id="membership-insufficient-alert" role="alert">
        <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
          <path d="M236.8,188.09,149.35,36.22a24.76,24.76,0,0,0-42.7,0L19.2,188.09a23.51,23.51,0,0,0,0,23.72A24.35,24.35,0,0,0,40.55,224h174.9a24.35,24.35,0,0,0,21.33-12.19A23.51,23.51,0,0,0,236.8,188.09ZM120,104a8,8,0,0,1,16,0v40a8,8,0,0,1-16,0Zm8,88a12,12,0,1,1,12-12A12,12,0,0,1,128,192Z"/>
        </svg>
        <span>Insufficient balance. You need <strong id="membership-shortfall">$0.00</strong> more to enroll.</span>
      </div>

    </div><!-- /.bottom-sheet-body -->

    <div class="bottom-sheet-footer">
      <!-- Shown when enrolling a NEW plan -->
      <button
        type="button"
        class="btn btn-primary btn-full btn-lg"
        id="membership-pay-btn"
      >
        Pay <span id="membership-pay-label">$0.00</span> from Wallet
      </button>
      <button
        type="button"
        class="btn btn-secondary btn-full"
        id="membership-deposit-first-btn"
      >
        Deposit First
      </button>
      <!-- Shown when viewing the ACTIVE plan (manage mode) -->
      <button
        type="button"
        class="btn btn-primary btn-full btn-lg hidden"
        id="membership-upgrade-btn"
      >
        Upgrade Plan
      </button>
      <button
        type="button"
        class="btn btn-ghost btn-full hidden"
        id="membership-manage-close-btn"
        onclick="closeModal('modal-enroll-membership')"
      >
        Close
      </button>
    </div>

  </div><!-- /.bottom-sheet -->
</div><!-- /#modal-enroll-membership -->
