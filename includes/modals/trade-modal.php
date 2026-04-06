<!-- =====================================================================
     Trade Modal — Simulated TSLA trading (display only, not real trading)
     - Shows live TSLA price from cached tesla_stocks table
     - Buy / Sell tabs with amount input and estimated shares
     - Records a display-only trade transaction
     - POST → api/user-dashboard/record-trade.php
     JS logic: initTradeModal() in assets/js/main.js
     ===================================================================== -->
<div class="modal-overlay" id="modal-trade" role="dialog" aria-modal="true" aria-labelledby="trade-modal-title">
  <div class="bottom-sheet">
    <div class="bottom-sheet-handle" aria-hidden="true"></div>

    <div class="bottom-sheet-header">
      <div class="trade-header-brand">
        <h3 id="trade-modal-title">Trade TSLA</h3>
        <div class="trade-price-display" aria-live="polite">
          <span class="trade-price-value" id="trade-tsla-price">$—</span>
          <span class="trade-price-change" id="trade-tsla-change" aria-label="Price change"></span>
        </div>
      </div>
      <button
        type="button"
        class="modal-close"
        onclick="closeModal('modal-trade')"
        aria-label="Close trade modal"
      >
        <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
          <path d="M205.66,194.34a8,8,0,0,1-11.32,11.32L128,139.31,61.66,205.66a8,8,0,0,1-11.32-11.32L116.69,128,50.34,61.66A8,8,0,0,1,61.66,50.34L128,116.69l66.34-66.35a8,8,0,0,1,11.32,11.32L139.31,128Z"/>
        </svg>
      </button>
    </div>

    <div class="bottom-sheet-body">

      <!-- Buy / Sell tab bar -->
      <div class="trade-tabs" role="tablist" aria-label="Trade direction">
        <button
          type="button"
          class="trade-tab active"
          id="trade-tab-buy"
          role="tab"
          aria-selected="true"
          aria-controls="trade-panel-buy"
          data-side="buy"
        >
          <svg viewBox="0 0 256 256" fill="currentColor" width="14" height="14" aria-hidden="true">
            <path d="M205.66,117.66a8,8,0,0,1-11.32,0L136,59.31V216a8,8,0,0,1-16,0V59.31L61.66,117.66a8,8,0,0,1-11.32-11.32l72-72a8,8,0,0,1,11.32,0l72,72A8,8,0,0,1,205.66,117.66Z"/>
          </svg>
          Buy
        </button>
        <button
          type="button"
          class="trade-tab"
          id="trade-tab-sell"
          role="tab"
          aria-selected="false"
          aria-controls="trade-panel-sell"
          data-side="sell"
        >
          <svg viewBox="0 0 256 256" fill="currentColor" width="14" height="14" aria-hidden="true">
            <path d="M205.66,149.66l-72,72a8,8,0,0,1-11.32,0l-72-72a8,8,0,0,1,11.32-11.32L120,196.69V40a8,8,0,0,1,16,0V196.69l58.34-58.35a8,8,0,0,1,11.32,11.32Z"/>
          </svg>
          Sell
        </button>
      </div>

      <!-- Trade panel -->
      <div id="trade-panel" role="tabpanel">

        <div class="form-group">
          <label for="trade-amount" class="form-label">Amount (USD)</label>
          <div class="input-group">
            <span class="input-icon">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
                <path d="M152,120H136V56h8a32,32,0,0,1,32,32,8,8,0,0,0,16,0,48.05,48.05,0,0,0-48-48h-8V24a8,8,0,0,0-16,0V40h-8a48,48,0,0,0,0,96h8v64H104a32,32,0,0,1-32-32,8,8,0,0,0-16,0,48.05,48.05,0,0,0,48,48h16v16a8,8,0,0,0,16,0V216h16a48,48,0,0,0,0-96Zm-32,0H104a32,32,0,0,1,0-64h16Zm32,80H136V136h16a32,32,0,0,1,0,64Z"/>
              </svg>
            </span>
            <input
              type="number"
              id="trade-amount"
              name="trade_amount"
              min="1"
              step="1"
              placeholder="Enter USD amount"
              autocomplete="off"
            >
          </div>
        </div>

        <!-- Estimated shares -->
        <div class="trade-estimate" id="trade-estimate" aria-live="polite">
          <span class="trade-estimate-label">Estimated Shares</span>
          <strong class="trade-estimate-value" id="trade-shares-estimate">—</strong>
        </div>

        <!-- Disclaimer -->
        <div class="alert alert-info trade-disclaimer" role="note">
          <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
            <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm16-40a8,8,0,0,1-8,8,16,16,0,0,1-16-16V128a8,8,0,0,1,0-16,16,16,0,0,1,16,16v40A8,8,0,0,1,144,176ZM112,84a12,12,0,1,1,12,12A12,12,0,0,1,112,84Z"/>
          </svg>
          <span>Simulated positions for platform engagement only. No real securities are bought or sold.</span>
        </div>

      </div><!-- /#trade-panel -->

    </div><!-- /.bottom-sheet-body -->

    <div class="bottom-sheet-footer">
      <button
        type="button"
        class="btn btn-primary btn-full btn-lg"
        id="trade-submit-btn"
      >
        <span id="trade-submit-label">Place Buy Order</span>
      </button>
    </div>

  </div><!-- /.bottom-sheet -->
</div><!-- /#modal-trade -->
