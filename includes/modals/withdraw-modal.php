<!-- =====================================================================
     Withdraw Modal
     - Shows available balance, fee (1.5%), net amount received
     - Validates min $50 client-side
     - Displays membership-based processing time
     - POST → api/user-dashboard/create-withdrawal.php
     JS logic: initWithdrawModal() in assets/js/main.js
     ===================================================================== -->
<div class="modal-overlay" id="modal-withdraw" role="dialog" aria-modal="true" aria-labelledby="withdraw-modal-title">
  <div class="bottom-sheet">
    <div class="bottom-sheet-handle" aria-hidden="true"></div>

    <div class="bottom-sheet-header">
      <h3 id="withdraw-modal-title">Withdraw Funds</h3>
      <button
        type="button"
        class="modal-close"
        onclick="closeModal('modal-withdraw')"
        aria-label="Close withdraw modal"
      >
        <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
          <path d="M205.66,194.34a8,8,0,0,1-11.32,11.32L128,139.31,61.66,205.66a8,8,0,0,1-11.32-11.32L116.69,128,50.34,61.66A8,8,0,0,1,61.66,50.34L128,116.69l66.34-66.35a8,8,0,0,1,11.32,11.32L139.31,128Z"/>
        </svg>
      </button>
    </div>

    <div class="bottom-sheet-body">

      <!-- Available balance -->
      <div class="alert alert-info" role="status">
        <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
          <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm16-40a8,8,0,0,1-8,8,16,16,0,0,1-16-16V128a8,8,0,0,1,0-16,16,16,0,0,1,16,16v40A8,8,0,0,1,144,176ZM112,84a12,12,0,1,1,12,12A12,12,0,0,1,112,84Z"/>
        </svg>
        <span>Available balance: <strong id="withdraw-available-balance">$0.00</strong></span>
      </div>

      <!-- Amount input -->
      <div class="form-group">
        <label for="withdraw-amount" class="form-label">Amount (USD)</label>
        <div class="input-group">
          <span class="input-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
              <path d="M152,120H136V56h8a32,32,0,0,1,32,32,8,8,0,0,0,16,0,48.05,48.05,0,0,0-48-48h-8V24a8,8,0,0,0-16,0V40h-8a48,48,0,0,0,0,96h8v64H104a32,32,0,0,1-32-32,8,8,0,0,0-16,0,48.05,48.05,0,0,0,48,48h16v16a8,8,0,0,0,16,0V216h16a48,48,0,0,0,0-96Zm-32,0H104a32,32,0,0,1,0-64h16Zm32,80H136V136h16a32,32,0,0,1,0,64Z"/>
            </svg>
          </span>
          <input
            type="number"
            id="withdraw-amount"
            name="withdraw_amount"
            min="50"
            step="0.01"
            placeholder="Min $50"
            autocomplete="off"
          >
          <button type="button" class="btn btn-sm btn-ghost input-max-btn" id="withdraw-max-btn" aria-label="Use maximum available balance">Max</button>
        </div>
        <span class="form-hint">Minimum withdrawal: $50 &nbsp;·&nbsp; Fee: 1.5%</span>
        <span class="form-error hidden" id="withdraw-amount-error" role="alert"></span>
      </div>

      <!-- Fee summary -->
      <div class="withdraw-fee-summary" id="withdraw-fee-summary" aria-live="polite">
        <div class="withdraw-fee-row">
          <span class="withdraw-fee-label">Platform Fee (1.5%)</span>
          <span class="withdraw-fee-value" id="withdraw-fee-display">$0.00</span>
        </div>
        <div class="withdraw-fee-row withdraw-receive-row">
          <span class="withdraw-fee-label">You Receive</span>
          <strong class="withdraw-fee-value" id="withdraw-receive-display">$0.00</strong>
        </div>
      </div>

      <!-- Crypto selector -->
      <div class="form-group">
        <label for="withdraw-currency" class="form-label">Withdraw To</label>
        <select id="withdraw-currency" name="withdraw_currency" aria-label="Select cryptocurrency">
          <option value="BTC">Bitcoin (BTC)</option>
          <option value="ETH">Ethereum (ETH)</option>
          <option value="USDTTRC20">USDT — TRC-20</option>
          <option value="USDTERC20">USDT — ERC-20</option>
        </select>
      </div>

      <!-- Wallet address -->
      <div class="form-group">
        <label for="withdraw-wallet-address" class="form-label">Your Wallet Address</label>
        <input
          type="text"
          id="withdraw-wallet-address"
          name="withdraw_wallet_address"
          placeholder="Enter your crypto wallet address"
          autocomplete="off"
          spellcheck="false"
        >
        <span class="form-hint" id="withdraw-network-hint">Double-check the address and network before submitting.</span>
        <span class="form-error hidden" id="withdraw-address-error" role="alert"></span>
      </div>

      <!-- Processing time (driven by membership) -->
      <div class="alert alert-info" id="withdraw-processing-time" role="status">
        <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
          <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm64-88a8,8,0,0,1-8,8H128a8,8,0,0,1-8-8V72a8,8,0,0,1,16,0v56h48A8,8,0,0,1,192,128Z"/>
        </svg>
        <span>Processing time: <strong id="withdraw-speed-label">Up to 72 hours</strong> based on your membership plan.</span>
      </div>

    </div><!-- /.bottom-sheet-body -->

    <div class="bottom-sheet-footer">
      <button
        type="button"
        class="btn btn-primary btn-full btn-lg"
        id="withdraw-submit-btn"
      >
        Request Withdrawal
      </button>
    </div>

  </div><!-- /.bottom-sheet -->
</div><!-- /#modal-withdraw -->
