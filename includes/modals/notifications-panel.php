<!-- =====================================================================
     Notifications Panel — full slide-in from the right
     - Triggered by a dedicated bell button (separate from the topbar dropdown)
     - Loads all 50 notifications with individual mark-read controls
     - Mark all read button
     - Uses notif-panel-* CSS classes (not modal-overlay)
     - JS logic: initNotificationsPanel() in assets/js/main.js
     ===================================================================== -->
<div
  class="notif-panel-overlay"
  id="notifications-panel"
  role="dialog"
  aria-modal="true"
  aria-labelledby="notif-panel-title"
  aria-hidden="true"
>
  <!-- Clicking the backdrop closes the panel -->
  <div class="notif-panel-backdrop" id="notif-panel-backdrop" aria-hidden="true"></div>

  <aside class="notif-panel" role="document">

    <!-- Panel header -->
    <div class="notif-panel-header">
      <h3 class="notif-panel-title" id="notif-panel-title">
        <svg viewBox="0 0 256 256" fill="currentColor" width="18" height="18" aria-hidden="true" style="margin-right:8px;vertical-align:-3px">
          <path d="M221.8,175.94C216.25,166.38,208,139.33,208,104a80,80,0,1,0-160,0c0,35.34-8.26,62.38-13.81,71.94A16,16,0,0,0,48,200H88.81a40,40,0,0,0,78.38,0H208a16,16,0,0,0,13.8-24.06ZM128,216a24,24,0,0,1-22.62-16h45.24A24,24,0,0,1,128,216ZM48,184c7.7-13.24,16-43.92,16-80a64,64,0,1,1,128,0c0,36.05,8.28,66.73,16,80Z"/>
        </svg>
        Notifications
      </h3>
      <div class="notif-panel-actions">
        <button
          type="button"
          class="btn btn-ghost btn-sm"
          id="notif-panel-mark-all"
          aria-label="Mark all notifications as read"
        >Mark all read</button>
        <button
          type="button"
          class="notif-panel-close-btn"
          id="notif-panel-close-btn"
          aria-label="Close notifications panel"
        >
          <svg viewBox="0 0 256 256" fill="currentColor" width="16" height="16" aria-hidden="true">
            <path d="M205.66,194.34a8,8,0,0,1-11.32,11.32L128,139.31,61.66,205.66a8,8,0,0,1-11.32-11.32L116.69,128,50.34,61.66A8,8,0,0,1,61.66,50.34L128,116.69l66.34-66.35a8,8,0,0,1,11.32,11.32L139.31,128Z"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Panel body: notification list -->
    <div class="notif-panel-body" id="notif-panel-list" aria-live="polite">
      <!-- Loading skeleton shown until JS replaces it -->
      <div class="notif-panel-loading" aria-label="Loading notifications">
        <div class="notif-panel-skeleton"></div>
        <div class="notif-panel-skeleton"></div>
        <div class="notif-panel-skeleton"></div>
        <div class="notif-panel-skeleton"></div>
        <div class="notif-panel-skeleton"></div>
      </div>
    </div>

  </aside>
</div><!-- /#notifications-panel -->
