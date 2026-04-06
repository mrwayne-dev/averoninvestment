<!-- =====================================================================
     Deposit Modal — Two-stage crypto payment flow
     Stage 1: Amount + currency selection → POST api/payments/create-payment.php
     Stage 2: QR code, address, countdown timer, status polling
     JS logic: initDepositModal() in assets/js/main.js
     ===================================================================== -->
<div class="modal-overlay" id="modal-deposit" role="dialog" aria-modal="true" aria-labelledby="deposit-modal-title">
  <div class="bottom-sheet">
    <div class="bottom-sheet-handle" aria-hidden="true"></div>

    <!-- ── Stage 1: Configure Payment ─────────────────────────── -->
    <div id="deposit-stage-1">

      <div class="bottom-sheet-header">
        <h3 id="deposit-modal-title">Deposit Funds</h3>
        <button
          type="button"
          class="modal-close"
          onclick="closeModal('modal-deposit')"
          aria-label="Close deposit modal"
        >
          <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
            <path d="M205.66,194.34a8,8,0,0,1-11.32,11.32L128,139.31,61.66,205.66a8,8,0,0,1-11.32-11.32L116.69,128,50.34,61.66A8,8,0,0,1,61.66,50.34L128,116.69l66.34-66.35a8,8,0,0,1,11.32,11.32L139.31,128Z"/>
          </svg>
        </button>
      </div>

      <div class="bottom-sheet-body">

        <div class="form-group">
          <label for="deposit-amount" class="form-label">Amount (USD)</label>
          <div class="input-group">
            <span class="input-icon">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
                <path d="M152,120H136V56h8a32,32,0,0,1,32,32,8,8,0,0,0,16,0,48.05,48.05,0,0,0-48-48h-8V24a8,8,0,0,0-16,0V40h-8a48,48,0,0,0,0,96h8v64H104a32,32,0,0,1-32-32,8,8,0,0,0-16,0,48.05,48.05,0,0,0,48,48h16v16a8,8,0,0,0,16,0V216h16a48,48,0,0,0,0-96Zm-32,0H104a32,32,0,0,1,0-64h16Zm32,80H136V136h16a32,32,0,0,1,0,64Z"/>
              </svg>
            </span>
            <input
              type="number"
              id="deposit-amount"
              name="deposit_amount"
              min="50"
              max="500000"
              step="1"
              placeholder="Min $50"
              autocomplete="off"
            >
          </div>
          <span class="form-hint">Minimum: $50 &nbsp;·&nbsp; Maximum: $500,000</span>
        </div>

        <div class="form-group">
          <span class="form-label">Select Cryptocurrency</span>
          <div class="crypto-selector" id="deposit-crypto-selector" role="radiogroup" aria-label="Choose cryptocurrency">

            <label class="crypto-option selected" data-currency="BTC">
              <input type="radio" name="deposit_currency" value="BTC" checked class="sr-only">
              <svg width="28" height="28" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                <circle cx="16" cy="16" r="16" fill="#F7931A"/>
                <path d="M22.4 14.2c.3-2.1-1.3-3.2-3.5-3.9l.7-2.9-1.8-.4-.7 2.8-1.4-.4.7-2.8-1.8-.4-.7 2.9-1.1-.3-2.4-.6-.5 1.9s1.3.3 1.3.3c.7.2.9.7.8 1.1l-2 8c-.1.3-.4.8-1.1.6 0 0-1.3-.3-1.3-.3l-.9 2 2.3.6 1.3.3-.7 2.9 1.8.4.7-2.9 1.4.4-.7 2.9 1.8.4.7-2.9c3 .6 5.3.4 6.2-2.4.8-2.2-.1-3.4-1.6-4.3 1.1-.3 2-1 2.2-2.5zm-3.9 5.5c-.6 2.2-4.5 1-5.8.7l1-4.1c1.3.3 5.4 1 4.8 3.4zm.6-5.5c-.5 2-3.7 1-4.7.7l.9-3.7c1.1.3 4.4.8 3.8 3z" fill="white"/>
              </svg>
              <div>
                <div class="crypto-symbol">BTC</div>
                <div class="crypto-name">Bitcoin</div>
              </div>
            </label>

            <label class="crypto-option" data-currency="ETH">
              <input type="radio" name="deposit_currency" value="ETH" class="sr-only">
              <svg width="28" height="28" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                <circle cx="16" cy="16" r="16" fill="#627EEA"/>
                <path d="M16 6l-.1.3v13.1l.1.1 6.1-3.6L16 6z" fill="white" fill-opacity=".6"/>
                <path d="M16 6L9.9 15.9l6.1 3.6V6z" fill="white"/>
                <path d="M16 20.8l-.1.1v4.7l.1.3 6.1-8.6-6.1 3.5z" fill="white" fill-opacity=".6"/>
                <path d="M16 25.9v-5.1l-6.1-3.5 6.1 8.6z" fill="white"/>
              </svg>
              <div>
                <div class="crypto-symbol">ETH</div>
                <div class="crypto-name">Ethereum</div>
              </div>
            </label>

            <label class="crypto-option" data-currency="USDTTRC20">
              <input type="radio" name="deposit_currency" value="USDTTRC20" class="sr-only">
              <svg width="28" height="28" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                <circle cx="16" cy="16" r="16" fill="#26A17B"/>
                <path d="M17.9 14.6v-2.3h4.3V9.4H9.8v2.9h4.3v2.3C10.3 15 7.5 16.2 7.5 17.6c0 1.4 2.8 2.6 6.6 2.9V26h3.8v-5.5c3.8-.3 6.6-1.5 6.6-2.9 0-1.4-2.8-2.6-6.6-2.9v-.1zm0 4.5v.1c-.1 0-.2 0-.4.1-.8.1-1.6.2-2.5.2-.9 0-1.7-.1-2.5-.2-.2 0-.3-.1-.4-.1v-.1c0-.7 1.3-1.3 2.9-1.3s2.9.6 2.9 1.3z" fill="white"/>
              </svg>
              <div>
                <div class="crypto-symbol">USDT</div>
                <div class="crypto-name">TRC-20</div>
              </div>
            </label>

            <label class="crypto-option" data-currency="USDTERC20">
              <input type="radio" name="deposit_currency" value="USDTERC20" class="sr-only">
              <svg width="28" height="28" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                <circle cx="16" cy="16" r="16" fill="#2775CA"/>
                <path d="M17.9 14.6v-2.3h4.3V9.4H9.8v2.9h4.3v2.3C10.3 15 7.5 16.2 7.5 17.6c0 1.4 2.8 2.6 6.6 2.9V26h3.8v-5.5c3.8-.3 6.6-1.5 6.6-2.9 0-1.4-2.8-2.6-6.6-2.9v-.1zm0 4.5v.1c-.1 0-.2 0-.4.1-.8.1-1.6.2-2.5.2-.9 0-1.7-.1-2.5-.2-.2 0-.3-.1-.4-.1v-.1c0-.7 1.3-1.3 2.9-1.3s2.9.6 2.9 1.3z" fill="white"/>
              </svg>
              <div>
                <div class="crypto-symbol">USDT</div>
                <div class="crypto-name">ERC-20</div>
              </div>
            </label>

          </div>
        </div>

      </div><!-- /.bottom-sheet-body -->

      <div class="bottom-sheet-footer">
        <button
          type="button"
          class="btn btn-primary btn-full btn-lg"
          id="deposit-generate-btn"
        >
          Generate Payment Address
        </button>
      </div>

    </div><!-- /#deposit-stage-1 -->

    <!-- ── Stage 2: Redirecting to NowPayments ───────────────── -->
    <!-- Shown briefly while window.location.href redirect fires  -->
    <div id="deposit-stage-2" class="hidden">

      <div class="bottom-sheet-body" style="text-align:center;padding:var(--space-12) var(--space-6);">

        <!-- Spinning ring -->
        <div style="display:flex;justify-content:center;margin-bottom:var(--space-6);">
          <svg width="56" height="56" viewBox="0 0 88 88" style="animation:spin 0.9s linear infinite;" aria-hidden="true">
            <circle cx="44" cy="44" r="36" fill="none" stroke="var(--border-color)" stroke-width="6"/>
            <circle cx="44" cy="44" r="36" fill="none" stroke="var(--color-primary)" stroke-width="6"
                    stroke-dasharray="60 166" stroke-linecap="round"/>
          </svg>
        </div>

        <p style="font-size:var(--text-lg);font-weight:600;color:var(--text-primary);margin:0 0 var(--space-2);">
          Redirecting to NowPayments…
        </p>
        <p style="font-size:var(--text-sm);color:var(--text-secondary);margin:0;">
          You'll be taken to a secure checkout page to complete your payment.
          Your balance will update automatically once confirmed.
        </p>

      </div>

    </div><!-- /#deposit-stage-2 -->

  </div><!-- /.bottom-sheet -->
</div><!-- /#modal-deposit -->
