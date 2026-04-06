<!-- =====================================================================
     Start Investment Modal
     - Loads plans via GET api/user-dashboard/get-plans.php on open
     - Supports pre-selected plan via openModal('modal-start-investment', {planId: X})
     - Live profit calculator updates on amount/plan change
     - Checks membership investment limit before allowing submit
     - POST → api/user-dashboard/start-investment.php
     JS logic: initInvestmentModal() in assets/js/main.js
     ===================================================================== -->
<div class="modal-overlay" id="modal-start-investment" role="dialog" aria-modal="true" aria-labelledby="invest-modal-title">
  <div class="bottom-sheet">
    <div class="bottom-sheet-handle" aria-hidden="true"></div>

    <div class="bottom-sheet-header">
      <h3 id="invest-modal-title">Start Investment</h3>
      <button
        type="button"
        class="modal-close"
        onclick="closeModal('modal-start-investment')"
        aria-label="Close investment modal"
      >
        <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
          <path d="M205.66,194.34a8,8,0,0,1-11.32,11.32L128,139.31,61.66,205.66a8,8,0,0,1-11.32-11.32L116.69,128,50.34,61.66A8,8,0,0,1,61.66,50.34L128,116.69l66.34-66.35a8,8,0,0,1,11.32,11.32L139.31,128Z"/>
        </svg>
      </button>
    </div>

    <!-- Limit exceeded notice (shown instead of form when at plan cap) -->
    <div class="bottom-sheet-body hidden" id="invest-limit-exceeded">
      <div class="alert alert-warning" role="alert">
        <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
          <path d="M236.8,188.09,149.35,36.22a24.76,24.76,0,0,0-42.7,0L19.2,188.09a23.51,23.51,0,0,0,0,23.72A24.35,24.35,0,0,0,40.55,224h174.9a24.35,24.35,0,0,0,21.33-12.19A23.51,23.51,0,0,0,236.8,188.09ZM120,104a8,8,0,0,1,16,0v40a8,8,0,0,1-16,0Zm8,88a12,12,0,1,1,12-12A12,12,0,0,1,128,192Z"/>
        </svg>
        <div>
          <div class="alert-title">Investment Limit Reached</div>
          <p>Your current membership plan allows a maximum number of active investments. Upgrade to unlock more.</p>
        </div>
      </div>
      <div class="bottom-sheet-footer">
        <button type="button" class="btn btn-primary btn-full" id="invest-upgrade-btn">
          Upgrade Membership
        </button>
        <button type="button" class="btn btn-ghost btn-full btn-sm" onclick="closeModal('modal-start-investment')">
          Cancel
        </button>
      </div>
    </div>

    <!-- Investment form -->
    <div class="bottom-sheet-body" id="invest-form-body">

      <!-- Plan selector -->
      <div class="form-group">
        <label for="invest-plan-select" class="form-label">Select Plan</label>
        <select id="invest-plan-select" name="plan_id" aria-label="Investment plan">
          <option value="">Loading plans...</option>
        </select>
      </div>

      <!-- Plan info card (populated dynamically) -->
      <div class="invest-plan-card" id="invest-plan-card" aria-live="polite">
        <div class="invest-plan-card-inner" id="invest-plan-card-inner">
          <!-- JS populates this -->
        </div>
      </div>

      <!-- Amount input -->
      <div class="form-group">
        <label for="invest-amount" class="form-label">Amount (USD)</label>
        <div class="input-group">
          <span class="input-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
              <path d="M152,120H136V56h8a32,32,0,0,1,32,32,8,8,0,0,0,16,0,48.05,48.05,0,0,0-48-48h-8V24a8,8,0,0,0-16,0V40h-8a48,48,0,0,0,0,96h8v64H104a32,32,0,0,1-32-32,8,8,0,0,0-16,0,48.05,48.05,0,0,0,48,48h16v16a8,8,0,0,0,16,0V216h16a48,48,0,0,0,0-96Zm-32,0H104a32,32,0,0,1,0-64h16Zm32,80H136V136h16a32,32,0,0,1,0,64Z"/>
            </svg>
          </span>
          <input
            type="number"
            id="invest-amount"
            name="amount"
            min="0"
            step="1"
            placeholder="Enter amount"
            autocomplete="off"
          >
        </div>
        <span class="form-hint" id="invest-amount-hint">Select a plan to see minimum and maximum amounts.</span>
        <span class="form-error hidden" id="invest-amount-error" role="alert"></span>
      </div>

      <!-- Live calculator (hidden until plan + amount entered) -->
      <div class="invest-calculator hidden" id="invest-calculator" aria-live="polite">
        <div class="invest-calc-row">
          <span class="invest-calc-label">Est. Daily Profit</span>
          <span class="invest-calc-value" id="invest-calc-daily">—</span>
        </div>
        <div class="invest-calc-row">
          <span class="invest-calc-label">Total Return (range)</span>
          <span class="invest-calc-value" id="invest-calc-total">—</span>
        </div>
        <div class="invest-calc-divider" aria-hidden="true"></div>
        <div class="invest-calc-row">
          <span class="invest-calc-label">Profits Available From</span>
          <span class="invest-calc-value" id="invest-calc-profit-date">—</span>
        </div>
        <div class="invest-calc-row">
          <span class="invest-calc-label">Maturity Date</span>
          <span class="invest-calc-value" id="invest-calc-maturity">—</span>
        </div>
      </div>

    </div><!-- /#invest-form-body -->

    <div class="bottom-sheet-footer" id="invest-form-footer">
      <button
        type="button"
        class="btn btn-primary btn-full btn-lg"
        id="invest-submit-btn"
        disabled
      >
        Start Investment
      </button>
    </div>

  </div><!-- /.bottom-sheet -->
</div><!-- /#modal-start-investment -->
