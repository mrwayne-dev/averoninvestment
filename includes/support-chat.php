<?php
/* =====================================================================
   includes/support-chat.php
   Floating support chat button + Telegram redirect modal.
   Included in footer.php (public pages) and mobile-dock.php (dashboard).

   Configure the Telegram URL via SUPPORT_TELEGRAM_URL in .env
   ===================================================================== */

// Ensure constants (and .env) are loaded — safe to call on pages that
// already loaded them since require_once is a no-op on repeat includes.
if (!defined('SUPPORT_TELEGRAM_URL')) {
    require_once dirname(__DIR__) . '/config/constants.php';
}

$_supportTgUrl = SUPPORT_TELEGRAM_URL;
?>

<!-- ════════════════════════════════════════════════════════════════
     FLOATING SUPPORT BUTTON
     Fixed bottom-right. Above mobile dock on small screens.
     ════════════════════════════════════════════════════════════════ -->
<button
  type="button"
  class="support-fab"
  id="support-fab"
  onclick="openModal('modal-support-chat')"
  aria-label="Open support chat"
  title="Chat with Support"
>
  <!-- Pulse ring (animated attention-getter) -->
  <span class="support-fab-pulse" aria-hidden="true"></span>

  <!-- Chat / speech bubble icon -->
  <svg viewBox="0 0 256 256" fill="currentColor" width="26" height="26" aria-hidden="true">
    <path d="M216,48H40A16,16,0,0,0,24,64V224a15.85,15.85,0,0,0,9.24,14.5A16.13,16.13,0,0,0,40,240a15.89,15.89,0,0,0,10.25-3.78.69.69,0,0,0,.13-.11L82.5,208H216a16,16,0,0,0,16-16V64A16,16,0,0,0,216,48ZM216,192H82.5a16,16,0,0,0-10.3,3.75l-.12.11L40,224V64H216ZM88,112a8,8,0,0,1,8-8h64a8,8,0,0,1,0,16H96A8,8,0,0,1,88,112Zm0,32a8,8,0,0,1,8-8h64a8,8,0,0,1,0,16H96A8,8,0,0,1,88,144Z"/>
  </svg>

  <!-- Notification dot (always visible) -->
  <span class="support-fab-dot" aria-hidden="true"></span>
</button>


<!-- ════════════════════════════════════════════════════════════════
     SUPPORT CHAT MODAL
     Uses the existing bottom-sheet / modal-overlay system.
     On desktop it is pinned to the bottom-right (not centred).
     ════════════════════════════════════════════════════════════════ -->
<div
  class="modal-overlay support-chat-overlay"
  id="modal-support-chat"
  role="dialog"
  aria-modal="true"
  aria-labelledby="support-chat-title"
>
  <div class="bottom-sheet support-chat-sheet">

    <!-- ── Chat Header ─────────────────────────────── -->
    <div class="support-chat-header">
      <div class="support-chat-avatar" aria-hidden="true">
        <svg viewBox="0 0 256 256" fill="currentColor" width="24" height="24" aria-hidden="true">
          <path d="M230.92,212c-15.23-26.33-38.7-45.21-66.09-54.16a72,72,0,1,0-73.66,0C63.78,166.78,40.31,185.66,25.08,212a8,8,0,1,0,13.85,8c18.84-32.56,52.14-52,89.07-52s70.23,19.44,89.07,52a8,8,0,1,0,13.85-8ZM72,96a56,56,0,1,1,56,56A56.06,56.06,0,0,1,72,96Z"/>
        </svg>
        <span class="support-avatar-online" aria-hidden="true"></span>
      </div>

      <div class="support-chat-meta">
        <div class="support-chat-name" id="support-chat-title">Averon Investment Support</div>
        <div class="support-chat-status">
          <span class="support-status-dot" aria-hidden="true"></span>
          Online · Usually replies instantly
        </div>
      </div>

      <button
        type="button"
        class="modal-close"
        onclick="closeModal('modal-support-chat')"
        aria-label="Close support chat"
      >
        <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
          <path d="M205.66,194.34a8,8,0,0,1-11.32,11.32L128,139.31,61.66,205.66a8,8,0,0,1-11.32-11.32L116.69,128,50.34,61.66A8,8,0,0,1,61.66,50.34L128,116.69l66.34-66.35a8,8,0,0,1,11.32,11.32L139.31,128Z"/>
        </svg>
      </button>
    </div>

    <!-- ── Chat Body — preview bubbles ────────────── -->
    <div class="support-chat-body">

      <div class="support-bubble-group">
        <div class="support-bubble support-bubble--in">
          <p>👋 Hi there! Welcome to <strong>Averon Investment</strong>.</p>
        </div>
        <div class="support-bubble support-bubble--in">
          <p>Have a question about your account, deposits, withdrawals, or investments? Our team is ready to help.</p>
        </div>
        <div class="support-bubble support-bubble--in">
          <p>Tap the button below to open a direct chat with us on <strong>Telegram</strong> — available 24 / 7.</p>
        </div>
        <div class="support-bubble-time">Support Team · now</div>
      </div>

    </div>

    <!-- ── Chat Footer — CTA ───────────────────────── -->
    <div class="support-chat-footer">
      <a
        href="<?= htmlspecialchars($_supportTgUrl, ENT_QUOTES, 'UTF-8') ?>"
        target="_blank"
        rel="noopener noreferrer"
        class="btn btn-primary btn-full support-tg-cta"
        onclick="closeModal('modal-support-chat')"
        aria-label="Open Telegram chat with Averon Investment Support"
      >
        <!-- Telegram send-plane icon -->
        <svg viewBox="0 0 256 256" fill="currentColor" width="20" height="20" aria-hidden="true">
          <path d="M228.88,26.19a9,9,0,0,0-9.16-1.57L17.06,103.93a14.22,14.22,0,0,0,2.43,27.21L72,141.45V200a15.92,15.92,0,0,0,10,14.83,15.91,15.91,0,0,0,17.51-3.73l25.32-26.26L165,220a15.88,15.88,0,0,0,10.51,4,16.3,16.3,0,0,0,5-.79,15.85,15.85,0,0,0,10.67-11.63L231.77,35A9,9,0,0,0,228.88,26.19Zm-29.65,172.5-47.48-37.36,77.27-84.44ZM88,200V155.86l24.48,19.27ZM216,40.44,88,136.16,40.07,115.88Z"/>
        </svg>
        Open Telegram Chat
      </a>
      <p class="support-chat-note">
        <svg viewBox="0 0 256 256" fill="currentColor" width="12" height="12" aria-hidden="true" style="vertical-align:middle;margin-right:3px">
          <path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32Zm0,176H48V48H72v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24Z"/>
        </svg>
        Opens in the Telegram app or web browser
      </p>
    </div>

  </div><!-- /.support-chat-sheet -->
</div><!-- /#modal-support-chat -->
