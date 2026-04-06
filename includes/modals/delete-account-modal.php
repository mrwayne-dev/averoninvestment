<!-- =====================================================================
     Delete Account Confirmation Modal — 2-step flow
     Step 1: User must type "DELETE" exactly to enable Next button
     Step 2: User must enter current password to submit
     Backend: sends email to admin requesting manual deletion.
             Does NOT automatically delete the account.
     POST → api/user-dashboard/delete-account-request.php
     JS logic: initDeleteAccountModal() in assets/js/main.js
     ===================================================================== -->
<div class="modal-overlay" id="modal-delete-account" role="dialog" aria-modal="true" aria-labelledby="delete-acct-title">
  <div class="bottom-sheet">
    <div class="bottom-sheet-handle" aria-hidden="true"></div>

    <!-- ═══════════════════════════════════════════════════════
         STEP 1 — Confirm intent by typing "DELETE"
    ═══════════════════════════════════════════════════════ -->
    <div id="delete-step-1">

      <div class="bottom-sheet-header">
        <h3 id="delete-acct-title" style="color:var(--color-danger)">
          <svg viewBox="0 0 256 256" fill="currentColor" width="18" height="18" aria-hidden="true" style="margin-right:6px;vertical-align:-3px">
            <path d="M236.8,188.09,149.35,36.22a24.76,24.76,0,0,0-42.7,0L19.2,188.09a23.51,23.51,0,0,0,0,23.72A24.35,24.35,0,0,0,40.55,224h174.9a24.35,24.35,0,0,0,21.33-12.19A23.51,23.51,0,0,0,236.8,188.09ZM120,104a8,8,0,0,1,16,0v40a8,8,0,0,1-16,0Zm8,88a12,12,0,1,1,12-12A12,12,0,0,1,128,192Z"/>
          </svg>
          Delete Account
        </h3>
        <button
          type="button"
          class="modal-close"
          onclick="closeModal('modal-delete-account')"
          aria-label="Close delete account modal"
        >
          <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
            <path d="M205.66,194.34a8,8,0,0,1-11.32,11.32L128,139.31,61.66,205.66a8,8,0,0,1-11.32-11.32L116.69,128,50.34,61.66A8,8,0,0,1,61.66,50.34L128,116.69l66.34-66.35a8,8,0,0,1,11.32,11.32L139.31,128Z"/>
          </svg>
        </button>
      </div>

      <div class="bottom-sheet-body">

        <div class="alert alert-danger" role="alert">
          <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true" style="flex-shrink:0">
            <path d="M236.8,188.09,149.35,36.22a24.76,24.76,0,0,0-42.7,0L19.2,188.09a23.51,23.51,0,0,0,0,23.72A24.35,24.35,0,0,0,40.55,224h174.9a24.35,24.35,0,0,0,21.33-12.19A23.51,23.51,0,0,0,236.8,188.09ZM120,104a8,8,0,0,1,16,0v40a8,8,0,0,1-16,0Zm8,88a12,12,0,1,1,12-12A12,12,0,0,1,128,192Z"/>
          </svg>
          <div>
            <div class="alert-title">This action is irreversible</div>
            <p style="margin:4px 0 0;font-size:var(--text-sm)">
              Deleting your account will permanently erase all data, investments, and wallet balances.
              This request will be reviewed by our team before processing.
            </p>
          </div>
        </div>

        <div class="form-group" style="margin-top:var(--space-5)">
          <label for="delete-confirm-text" class="form-label">
            Type <strong style="font-family:var(--font-mono);letter-spacing:0.05em;color:var(--color-danger)">DELETE</strong> to continue
          </label>
          <input
            type="text"
            id="delete-confirm-text"
            placeholder="DELETE"
            autocomplete="off"
            spellcheck="false"
            autocorrect="off"
            autocapitalize="off"
            style="font-family:var(--font-mono);letter-spacing:0.05em;text-transform:uppercase"
          >
        </div>

      </div><!-- /.bottom-sheet-body -->

      <div class="bottom-sheet-footer">
        <button
          type="button"
          class="btn btn-danger btn-full"
          id="delete-next-btn"
          disabled
          aria-disabled="true"
        >
          Continue to Step 2
        </button>
        <button
          type="button"
          class="btn btn-ghost btn-full btn-sm"
          onclick="closeModal('modal-delete-account')"
          style="margin-top:var(--space-2)"
        >
          Cancel
        </button>
      </div>

    </div><!-- /#delete-step-1 -->

    <!-- ═══════════════════════════════════════════════════════
         STEP 2 — Enter password to submit deletion request
    ═══════════════════════════════════════════════════════ -->
    <div id="delete-step-2" class="hidden">

      <div class="bottom-sheet-header">
        <h3 style="color:var(--color-danger)">Confirm Password</h3>
        <button
          type="button"
          class="modal-close"
          onclick="closeModal('modal-delete-account')"
          aria-label="Close delete account modal"
        >
          <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
            <path d="M205.66,194.34a8,8,0,0,1-11.32,11.32L128,139.31,61.66,205.66a8,8,0,0,1-11.32-11.32L116.69,128,50.34,61.66A8,8,0,0,1,61.66,50.34L128,116.69l66.34-66.35a8,8,0,0,1,11.32,11.32L139.31,128Z"/>
          </svg>
        </button>
      </div>

      <div class="bottom-sheet-body">

        <div class="alert alert-warning" role="status">
          <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true" style="flex-shrink:0">
            <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm16-40a8,8,0,0,1-8,8,16,16,0,0,1-16-16V128a8,8,0,0,1,0-16,16,16,0,0,1,16,16v40A8,8,0,0,1,144,176ZM112,84a12,12,0,1,1,12,12A12,12,0,0,1,112,84Z"/>
          </svg>
          <span>Your request will be sent to our support team. Account deletion is processed within 72 hours.</span>
        </div>

        <div class="form-group" style="margin-top:var(--space-5)">
          <label for="delete-password" class="form-label">Current Password</label>
          <div class="input-group">
            <span class="input-icon">
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
                <path d="M208,80H176V56a48,48,0,0,0-96,0V80H48A16,16,0,0,0,32,96V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V96A16,16,0,0,0,208,80ZM96,56a32,32,0,0,1,64,0V80H96ZM208,208H48V96H208V208Zm-68-56a12,12,0,1,1-12-12A12,12,0,0,1,140,152Z"/>
              </svg>
            </span>
            <input
              type="password"
              id="delete-password"
              name="password"
              autocomplete="current-password"
              placeholder="Enter your current password"
            >
            <button
              type="button"
              class="input-eye-btn"
              data-toggle-password="delete-password"
              aria-label="Toggle password visibility"
            >
              <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
                <path d="M247.31,124.76c-.35-.79-8.82-19.58-27.65-38.41C194.57,61.26,162.88,48,128,48S61.43,61.26,36.34,86.35C17.51,105.18,9,124,8.69,124.76a8,8,0,0,0,0,6.5c.35.79,8.82,19.57,27.65,38.4C61.43,194.74,93.12,208,128,208s66.57-13.26,91.66-38.34c18.83-18.83,27.3-37.61,27.65-38.4A8,8,0,0,0,247.31,124.76ZM128,192c-30.78,0-57.67-11.19-79.93-33.25A133.47,133.47,0,0,1,25,128,133.33,133.33,0,0,1,48.07,97.25C70.33,75.19,97.22,64,128,64s57.67,11.19,79.93,33.25A133.46,133.46,0,0,1,231.05,128C223.84,141.46,192.43,192,128,192Zm0-112a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Z"/>
              </svg>
            </button>
          </div>
          <span class="form-error hidden" id="delete-password-error" role="alert"></span>
        </div>

      </div><!-- /.bottom-sheet-body -->

      <div class="bottom-sheet-footer">
        <button
          type="button"
          class="btn btn-danger btn-full btn-lg"
          id="delete-submit-btn"
        >
          <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true" style="margin-right:6px;vertical-align:-2px">
            <path d="M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16Zm-96-8a8,8,0,0,1,8-8h16a8,8,0,0,1,8,8v8H120Zm72,168H64V64H192ZM112,104v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Z"/>
          </svg>
          Submit Deletion Request
        </button>
        <button
          type="button"
          class="btn btn-ghost btn-full btn-sm"
          id="delete-back-btn"
          style="margin-top:var(--space-2)"
        >
          ← Back to Step 1
        </button>
      </div>

    </div><!-- /#delete-step-2 -->

  </div><!-- /.bottom-sheet -->
</div><!-- /#modal-delete-account -->
