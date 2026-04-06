<!-- =====================================================================
     Transfer Modal
     - Shows available balance (injected on open via registerModalHandler)
     - Recipient email + amount inputs with live summary
     - No transfer fees (internal ledger transfer)
     - POST → api/user-dashboard/transfer.php
     JS logic: initTransferModal() in assets/js/main.js
     ===================================================================== -->
<div class="modal-overlay" id="modal-transfer" role="dialog" aria-modal="true" aria-labelledby="transfer-modal-title">
  <div class="bottom-sheet">
    <div class="bottom-sheet-handle" aria-hidden="true"></div>

    <div class="bottom-sheet-header">
      <h3 id="transfer-modal-title">Transfer Funds</h3>
      <button
        type="button"
        class="modal-close"
        onclick="closeModal('modal-transfer')"
        aria-label="Close transfer modal"
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
        <span>Available balance: <strong id="transfer-available-balance">$0.00</strong></span>
      </div>

      <!-- Recipient email -->
      <div class="form-group">
        <label for="transfer-recipient" class="form-label">Recipient Email</label>
        <div class="input-group">
          <span class="input-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
              <path d="M224,48H32a8,8,0,0,0-8,8V192a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A8,8,0,0,0,224,48Zm-96,85.15L61.68,64H194.32ZM98.71,128,40,181.81V74.19Zm11.84,10.85,12,11.05a8,8,0,0,0,10.82,0l12-11.05,58,53.15H52.57ZM157.29,128,216,74.18V181.82Z"/>
            </svg>
          </span>
          <input
            type="email"
            id="transfer-recipient"
            name="recipient_email"
            placeholder="recipient@example.com"
            autocomplete="email"
            inputmode="email"
          >
        </div>
        <span class="form-error hidden" id="transfer-recipient-error" role="alert"></span>
      </div>

      <!-- Amount -->
      <div class="form-group">
        <label for="transfer-amount" class="form-label">Amount (USD)</label>
        <div class="input-group">
          <span class="input-icon">
            <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
              <path d="M152,120H136V56h8a32,32,0,0,1,32,32,8,8,0,0,0,16,0,48.05,48.05,0,0,0-48-48h-8V24a8,8,0,0,0-16,0V40h-8a48,48,0,0,0,0,96h8v64H104a32,32,0,0,1-32-32,8,8,0,0,0-16,0,48.05,48.05,0,0,0,48,48h16v16a8,8,0,0,0,16,0V216h16a48,48,0,0,0,0-96Zm-32,0H104a32,32,0,0,1,0-64h16Zm32,80H136V136h16a32,32,0,0,1,0,64Z"/>
            </svg>
          </span>
          <input
            type="number"
            id="transfer-amount"
            name="amount"
            min="1"
            step="0.01"
            placeholder="0.00"
            inputmode="decimal"
            autocomplete="off"
          >
        </div>
        <span class="form-hint">Minimum transfer: $1.00 &nbsp;·&nbsp; No fees for internal transfers</span>
        <span class="form-error hidden" id="transfer-amount-error" role="alert"></span>
      </div>

      <!-- Live transfer summary (shown when both fields have valid input) -->
      <div class="withdraw-fee-summary hidden" id="transfer-summary" aria-live="polite">
        <div class="withdraw-fee-row">
          <span class="withdraw-fee-label">Transfer Amount</span>
          <span class="withdraw-fee-value" id="transfer-summary-amount">$0.00</span>
        </div>
        <div class="withdraw-fee-row">
          <span class="withdraw-fee-label">Recipient</span>
          <span class="withdraw-fee-value" id="transfer-summary-recipient" style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">—</span>
        </div>
        <div class="withdraw-fee-row withdraw-receive-row">
          <span class="withdraw-fee-label">Platform Fee</span>
          <strong class="withdraw-fee-value" style="color:var(--color-success);">$0.00 (Free)</strong>
        </div>
      </div>

    </div><!-- /.bottom-sheet-body -->

    <div class="bottom-sheet-footer">
      <button
        type="button"
        class="btn btn-primary btn-full btn-lg"
        id="transfer-submit-btn"
      >
        <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true" style="margin-right:6px;vertical-align:-2px">
          <path d="M237.66,133.66l-32,32a8,8,0,0,1-11.32-11.32L212.69,136H40a8,8,0,0,1,0-16H212.69L194.34,101.66a8,8,0,0,1,11.32-11.32l32,32A8,8,0,0,1,237.66,133.66Z"/>
        </svg>
        Transfer Funds
      </button>
    </div>

  </div><!-- /.bottom-sheet -->
</div><!-- /#modal-transfer -->
