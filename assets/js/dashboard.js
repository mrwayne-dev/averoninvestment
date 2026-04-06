/* ============================================================
   Averon Investment — dashboard.js
   Dashboard: polling, sidebar, nav, logout
   ============================================================ */

// ─── Polling ─────────────────────────────────────────────────
let _pollingTimer = null;

async function pollOverview() {
  try {
    const res  = await fetch('/api/user-dashboard/get-overview.php', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    });
    const json = await res.json();
    if (json.success && json.data) {
      if (json.data.wallet)                             updateWalletUI(json.data.wallet);
      if (json.data.notifications_count !== undefined)  updateNotificationBadge(json.data.notifications_count);
      if (Array.isArray(json.data.notifications))       renderNotificationList(json.data.notifications);
      if (Array.isArray(json.data.recent_transactions)) renderRecentTransactions(json.data.recent_transactions);
      if (Array.isArray(json.data.active_investments))  renderDashboardInvestments(json.data.active_investments);
    }
  } catch (_) {
    // silent fail — polling must never surface errors to user
  }
}

function startPolling() {
  pollOverview(); // immediate first call
  _pollingTimer = setInterval(pollOverview, 30000);
}

function stopPolling() {
  if (_pollingTimer) clearInterval(_pollingTimer);
}

// ─── Cached balance (updated on every poll; read by membership modal) ─
let _lastKnownBalance = 0;

// ─── Active membership plan id (from get-overview; 0 = no membership) ─
let _activeMembershipPlanId = 0;

// ─── Cached membership plans map (id → plan object) ──────────────────
let _membershipPlansMap = {};

/**
 * Called by "Enroll Now" buttons generated inside renderMembershipPlans().
 * Looks up the full plan from cache and opens the modal with correct shape.
 */
function openMembershipEnroll(planId) {
  const plan = _membershipPlansMap[planId];
  if (!plan) return;
  openModal('modal-enroll-membership', {
    plan:          plan,
    balance:       _lastKnownBalance,
    benefits:      plan.benefits || [],
    isCurrentPlan: _activeMembershipPlanId === parseInt(planId, 10),
  });
}

// ─── Wallet UI ───────────────────────────────────────────────
function updateWalletUI(wallet) {
  if (!wallet) return;

  // Keep caches in sync so membership modal always has fresh data
  _lastKnownBalance       = parseFloat(wallet.balance || 0);
  _activeMembershipPlanId = wallet.membership_plan_id ? parseInt(wallet.membership_plan_id, 10) : 0;

  const map = {
    '[data-wallet-balance]':  formatCurrency(wallet.balance),
    '[data-wallet-profit]':   formatCurrency(wallet.profit_balance),
    '[data-wallet-invested]': formatCurrency(wallet.invested_amount),
  };

  for (const [selector, value] of Object.entries(map)) {
    document.querySelectorAll(selector).forEach(el => { el.textContent = value; });
  }

  // Active plans count
  if (wallet.active_plans !== undefined) {
    document.querySelectorAll('[data-stat="active_plans"]').forEach((el) => {
      el.textContent = wallet.active_plans;
    });
  }

  // Membership tier + expiry
  if (wallet.membership_tier !== undefined) {
    document.querySelectorAll('[data-membership="tier"]').forEach((el) => {
      el.textContent = wallet.membership_tier || 'No Membership';
    });
    document.querySelectorAll('[data-membership="expiry"]').forEach((el) => {
      el.textContent = wallet.membership_expiry ? `Expires ${formatDate(wallet.membership_expiry)}` : '—';
    });
    // Show/hide upgrade CTA
    const ctaBtn = document.getElementById('membership-cta-btn');
    if (ctaBtn) {
      ctaBtn.textContent = wallet.membership_tier ? 'Manage' : 'Upgrade';
    }
  }
}

// ─── Notification Badge ───────────────────────────────────────
function updateNotificationBadge(count) {
  const badge = document.getElementById('notif-badge');
  if (!badge) return;
  if (count > 0) {
    badge.textContent  = count > 99 ? '99+' : String(count);
    badge.style.display = 'flex';
  } else {
    badge.style.display = 'none';
  }
}

// ─── XSS-safe string helper ───────────────────────────────────
function escapeHtml(str) {
  if (str == null) return '';
  return String(str)
    .replace(/&/g,  '&amp;')
    .replace(/</g,  '&lt;')
    .replace(/>/g,  '&gt;')
    .replace(/"/g,  '&quot;')
    .replace(/'/g,  '&#39;');
}

// ─── Notification list renderer ───────────────────────────────
function renderNotificationList(notifications) {
  const list = document.getElementById('notif-list');
  if (!list) return;

  if (!notifications || notifications.length === 0) {
    list.innerHTML = '<p class="topbar-dropdown-empty">No new notifications</p>';
    return;
  }

  list.innerHTML = notifications.map((n) => `
    <div class="notif-item${n.is_read ? '' : ' notif-item--unread'}">
      <div class="notif-item-title">${escapeHtml(n.title)}</div>
      <div class="notif-item-message">${escapeHtml(n.message)}</div>
      <div class="notif-item-time">${timeAgo(n.created_at)}</div>
    </div>
  `).join('');
}

// ─── Recent transactions renderer ─────────────────────────────
function renderRecentTransactions(transactions) {
  const tbody    = document.getElementById('recent-transactions-body');
  const emptyRow = document.getElementById('recent-transactions-empty');
  if (!tbody) return;

  // Remove previously rendered dynamic rows (keep empty row in DOM)
  tbody.querySelectorAll('[data-tx-row]').forEach((r) => r.remove());

  if (!transactions || transactions.length === 0) {
    if (emptyRow) emptyRow.style.display = '';
    return;
  }

  if (emptyRow) emptyRow.style.display = 'none';

  const rows = transactions.map((tx) => {
    const sign      = tx.type === 'withdrawal' || tx.type === 'fee' ? '−' : '+';
    const typeLabel = escapeHtml(tx.type.replace(/_/g, ' '));
    const statusLabel = escapeHtml(tx.status);
    const ref       = escapeHtml(tx.reference || '—');
    const date      = formatDate(tx.created_at);
    const amount    = formatCurrency(tx.amount);

    return `
      <tr data-tx-row>
        <td>${date}</td>
        <td>
          <span class="tx-type-badge tx-type-badge--${escapeHtml(tx.type)}">${typeLabel}</span>
        </td>
        <td>${sign}${amount}</td>
        <td>
          <span class="status-badge status-badge--${escapeHtml(tx.status)}">${statusLabel}</span>
        </td>
        <td class="col-hide-mobile">${ref}</td>
      </tr>`;
  }).join('');

  tbody.insertAdjacentHTML('afterbegin', rows);
}

// ─── Active investments (dashboard table) ─────────────────────
function renderDashboardInvestments(investments) {
  const list  = document.getElementById('active-investments-list');
  const empty = document.getElementById('active-investments-empty');
  if (!list) return; // not on the dashboard page

  // Clear previously rendered table wrapper (preserve the static empty-state child)
  list.querySelectorAll('[data-inv-card]').forEach((c) => c.remove());

  if (!investments || investments.length === 0) {
    if (empty) empty.style.display = '';
    return;
  }

  if (empty) empty.style.display = 'none';

  const rows = investments.map((inv) => {
    const elapsed = inv.days_elapsed  || 0;
    const total   = inv.duration_days || 1;
    const pct     = Math.min(100, Math.round((elapsed / total) * 100));
    const endDate = inv.end_date ? formatDate(inv.end_date) : '—';
    return `
      <tr>
        <td>
          <span style="font-weight:600;color:var(--text-primary);font-size:var(--text-sm)">${escapeHtml(inv.plan_name)}</span>
          <div style="font-size:var(--text-xs);color:var(--text-muted);margin-top:2px">Ends ${endDate}</div>
        </td>
        <td style="font-weight:700;font-variant-numeric:tabular-nums">${formatCurrency(inv.amount)}</td>
        <td style="min-width:120px">
          <div class="progress-bar" role="progressbar" aria-valuenow="${pct}" aria-valuemin="0" aria-valuemax="100" style="margin-bottom:4px">
            <div class="progress-bar-fill" style="width:${pct}%"></div>
          </div>
          <div class="progress-bar-label">${elapsed} / ${total} days</div>
        </td>
        <td style="font-weight:600;color:var(--color-success);font-variant-numeric:tabular-nums">${formatCurrency(inv.profit_earned)}</td>
        <td><span class="status-badge status-badge--active">Active</span></td>
      </tr>`;
  }).join('');

  const table = `
    <div class="admin-table-wrapper" data-inv-card>
      <div class="admin-table-scroll">
        <table class="admin-table" aria-label="Active investments">
          <thead>
            <tr>
              <th scope="col">Plan</th>
              <th scope="col">Amount</th>
              <th scope="col">Progress</th>
              <th scope="col">Profit Earned</th>
              <th scope="col">Status</th>
            </tr>
          </thead>
          <tbody>${rows}</tbody>
        </table>
      </div>
    </div>`;

  list.insertAdjacentHTML('afterbegin', table);
}

// ─── Sidebar Toggle ──────────────────────────────────────────
function initSidebarToggle() {
  const toggleBtn = document.getElementById('sidebar-toggle');
  const sidebar   = document.getElementById('dashboard-sidebar');
  const overlay   = document.getElementById('sidebar-overlay');

  if (!toggleBtn || !sidebar) return;

  function openSidebar() {
    sidebar.classList.add('open');
    if (overlay) overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
  });

  if (overlay) {
    overlay.addEventListener('click', closeSidebar);
  }

  // Close sidebar on ESC
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && sidebar.classList.contains('open')) closeSidebar();
  });
}

// ─── Active Nav Highlighting ──────────────────────────────────
function initActiveNav() {
  // Normalise current path — strip trailing slash for comparison
  const currentPath = window.location.pathname.replace(/\/$/, '') || '/';

  document.querySelectorAll('[data-nav-link]').forEach((el) => {
    let href = (el.getAttribute('href') || el.dataset.navLink || '').replace(/\/$/, '');
    if (!href) return;

    // Strip .php extension if any remain (backward-compat during migration)
    href = href.replace(/\.php$/, '');

    // Exact match — avoids false positives like /dashboard matching /dashboard/wallet
    if (href && href === currentPath) {
      el.classList.add('active');
      const li = el.closest('li');
      if (li) li.classList.add('active');
    }
  });
}

// ─── Logout ──────────────────────────────────────────────────
function initLogoutButton() {
  document.querySelectorAll('[data-logout]').forEach((btn) => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      try {
        const data = await apiRequest(btn.dataset.logout || '/api/auth/user-logout.php', 'POST');
        window.location.href = (data.data && data.data.redirect) || '/login';
      } catch (_) {
        window.location.href = '/login';
      }
    });
  });
}

// ─── Topbar Dropdown Toggles ─────────────────────────────────
function initTopbarDropdowns() {
  // Generic toggle: button[aria-controls] → dropdown[id]
  function makeToggle(btnId, dropdownId) {
    const btn      = document.getElementById(btnId);
    const dropdown = document.getElementById(dropdownId);
    if (!btn || !dropdown) return;

    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isOpen = dropdown.classList.contains('open');

      // Close all open dropdowns first
      document.querySelectorAll('.topbar-dropdown.open').forEach((d) => {
        d.classList.remove('open');
        const b = document.querySelector(`[aria-controls="${d.id}"]`);
        if (b) b.setAttribute('aria-expanded', 'false');
      });

      if (!isOpen) {
        dropdown.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
      }
    });
  }

  makeToggle('notif-btn',      'notif-dropdown');
  makeToggle('topbar-user-btn', 'user-dropdown');

  // Close dropdowns when clicking outside
  document.addEventListener('click', () => {
    document.querySelectorAll('.topbar-dropdown.open').forEach((d) => {
      d.classList.remove('open');
      const b = document.querySelector(`[aria-controls="${d.id}"]`);
      if (b) b.setAttribute('aria-expanded', 'false');
    });
  });

  // Close on ESC
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    document.querySelectorAll('.topbar-dropdown.open').forEach((d) => {
      d.classList.remove('open');
      const b = document.querySelector(`[aria-controls="${d.id}"]`);
      if (b) b.setAttribute('aria-expanded', 'false');
    });
  });

  // Mark-all-read button
  const markAllBtn = document.getElementById('notif-mark-all-read');
  if (markAllBtn) {
    markAllBtn.addEventListener('click', async () => {
      try {
        await apiRequest('/api/user-dashboard/mark-notifications-read.php', 'POST');
        updateNotificationBadge(0);
        const list = document.getElementById('notif-list');
        if (list) {
          list.innerHTML = '<p class="topbar-dropdown-empty">No new notifications</p>';
        }
      } catch (_) { /* silent */ }
    });
  }
}

// ─── Membership CTA button ────────────────────────────────────
function initMembershipCta() {
  const ctaBtn = document.getElementById('membership-cta-btn');
  if (!ctaBtn) return;
  ctaBtn.addEventListener('click', async () => {
    // Case 1: plan already in cache (membership page loaded it) — open immediately
    if (_activeMembershipPlanId && _membershipPlansMap[_activeMembershipPlanId]) {
      openMembershipEnroll(_activeMembershipPlanId);
      return;
    }

    // Case 2: we know the plan id but plans aren't cached (dashboard page) — fetch first
    if (_activeMembershipPlanId) {
      try {
        const res  = await fetch('/api/user-dashboard/get-membership-plans.php', {
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin',
        });
        const json = await res.json();
        if (json.success && Array.isArray(json.data?.plans)) {
          json.data.plans.forEach((p) => { _membershipPlansMap[p.id] = p; });
        }
      } catch (_) { /* ignore — fall through */ }

      if (_membershipPlansMap[_activeMembershipPlanId]) {
        openMembershipEnroll(_activeMembershipPlanId);
        return;
      }
    }

    // Case 3: no active membership — open empty modal (user sees Upgrade state)
    openModal('modal-enroll-membership', { plan: null, balance: _lastKnownBalance, isCurrentPlan: false });
  });
}

// ─── Wallet Page ─────────────────────────────────────────────
function initWalletPage() {
  if (!document.getElementById('wallet-tx-body')) return;

  let allTransactions = [];
  let currentFilter   = 'all';
  let searchQuery     = '';

  async function loadTransactions() {
    try {
      const res  = await fetch('/api/user-dashboard/get-transactions.php', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      const json = await res.json();
      if (json.success && Array.isArray(json.data?.transactions)) {
        allTransactions = json.data.transactions;
        renderFiltered();
      }
    } catch (_) {}
  }

  function renderFiltered() {
    let filtered = allTransactions;
    if (currentFilter !== 'all') {
      filtered = filtered.filter((tx) => tx.type === currentFilter);
    }
    if (searchQuery) {
      const q = searchQuery.toLowerCase();
      filtered = filtered.filter((tx) =>
        (tx.reference || '').toLowerCase().includes(q) ||
        (tx.type || '').toLowerCase().includes(q) ||
        String(tx.amount).includes(q)
      );
    }
    renderWalletTable(filtered);
  }

  function renderWalletTable(transactions) {
    const tbody    = document.getElementById('wallet-tx-body');
    const emptyRow = document.getElementById('wallet-tx-empty');
    if (!tbody) return;

    tbody.querySelectorAll('[data-tx-row]').forEach((r) => r.remove());

    if (!transactions || !transactions.length) {
      if (emptyRow) emptyRow.style.display = '';
      return;
    }
    if (emptyRow) emptyRow.style.display = 'none';

    const rows = transactions.map((tx) => {
      const sign      = (tx.type === 'withdrawal' || tx.type === 'fee') ? '−' : '+';
      const typeLabel = escapeHtml(tx.type.replace(/_/g, ' '));
      return `
        <tr data-tx-row>
          <td>${formatDate(tx.created_at)}</td>
          <td><span class="tx-type-badge tx-type-badge--${escapeHtml(tx.type)}">${typeLabel}</span></td>
          <td style="font-weight:700;font-variant-numeric:tabular-nums">${sign}${formatCurrency(tx.amount)}</td>
          <td><span class="status-badge status-badge--${escapeHtml(tx.status)}">${escapeHtml(tx.status)}</span></td>
          <td class="col-hide-mobile td-mono">${escapeHtml(tx.reference || '—')}</td>
          <td class="col-hide-mobile">${escapeHtml(tx.description || '—')}</td>
        </tr>`;
    }).join('');

    tbody.insertAdjacentHTML('afterbegin', rows);
  }

  // Filter tabs
  document.querySelectorAll('[data-filter]').forEach((btn) => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('[data-filter]').forEach((b) => {
        b.classList.remove('active');
        b.setAttribute('aria-selected', 'false');
      });
      btn.classList.add('active');
      btn.setAttribute('aria-selected', 'true');
      currentFilter = btn.dataset.filter;
      renderFiltered();
    });
  });

  // Search
  const searchInput = document.getElementById('wallet-tx-search');
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      searchQuery = searchInput.value.trim();
      renderFiltered();
    });
  }

  // CSV export
  const csvBtn = document.getElementById('wallet-csv-btn');
  if (csvBtn) {
    csvBtn.addEventListener('click', () => {
      let filtered = allTransactions;
      if (currentFilter !== 'all') filtered = filtered.filter((tx) => tx.type === currentFilter);
      if (searchQuery) {
        const q = searchQuery.toLowerCase();
        filtered = filtered.filter((tx) =>
          (tx.reference || '').toLowerCase().includes(q) ||
          (tx.type || '').toLowerCase().includes(q) ||
          String(tx.amount).includes(q)
        );
      }
      exportToCSV(filtered, 'transactions.csv');
    });
  }

  loadTransactions();
}


// ─── Investments Page ─────────────────────────────────────────
function initInvestmentsPage() {
  if (!document.getElementById('investment-plans-grid')) return;

  // Load available plans
  async function loadPlans() {
    try {
      const res  = await fetch('/api/user-dashboard/get-plans.php', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      const json = await res.json();
      if (json.success && Array.isArray(json.data?.plans)) {
        renderPlans(json.data.plans);
      }
    } catch (_) {
      const grid = document.getElementById('investment-plans-grid');
      if (grid) grid.innerHTML = '<div class="dashboard-empty-state"><p>Could not load plans.</p></div>';
    }
  }

  function renderPlans(plans) {
    const grid = document.getElementById('investment-plans-grid');
    if (!grid) return;

    if (!plans.length) {
      grid.innerHTML = '<div class="dashboard-empty-state"><p>No investment plans available.</p></div>';
      return;
    }

    grid.innerHTML = plans.map((plan) => {
      const color   = escapeHtml(plan.color_accent || '#3E6AE1');
      const minAmt  = formatCurrency(plan.min_amount);
      const maxAmt  = plan.max_amount ? formatCurrency(plan.max_amount) : 'Unlimited';
      const planData = JSON.stringify({
        plan_id:    plan.id,
        plan_name:  plan.name,
        min_amount: plan.min_amount,
        max_amount: plan.max_amount || null,
        color:      plan.color_accent,
      });
      return `
        <article class="investment-plan-card" style="--plan-color:${color}">
          <div class="plan-card-badge">${escapeHtml(plan.badge_label || '')}</div>
          <div class="plan-card-name">${escapeHtml(plan.name)}</div>
          <div class="plan-card-yield">
            ${escapeHtml(plan.daily_yield_min)}–${escapeHtml(plan.daily_yield_max)}%<span>/day</span>
          </div>
          <div class="plan-card-meta">
            <div class="plan-meta-row">
              <span class="plan-meta-key">Min. Investment</span>
              <span class="plan-meta-value">${minAmt}</span>
            </div>
            <div class="plan-meta-row">
              <span class="plan-meta-key">Max. Investment</span>
              <span class="plan-meta-value">${maxAmt}</span>
            </div>
            <div class="plan-meta-row">
              <span class="plan-meta-key">Duration</span>
              <span class="plan-meta-value">${escapeHtml(String(plan.duration_days))} days</span>
            </div>
            <div class="plan-meta-row">
              <span class="plan-meta-key">Total Yield</span>
              <span class="plan-meta-value">${escapeHtml(plan.total_yield_min)}–${escapeHtml(plan.total_yield_max)}%</span>
            </div>
          </div>
          <button
            type="button"
            class="btn btn-primary btn-full"
            onclick='openModal("modal-start-investment", ${planData})'
          >Start Plan</button>
        </article>`;
    }).join('');
  }

  // Load user's active investments
  async function loadMyInvestments() {
    try {
      const res  = await fetch('/api/user-dashboard/get-my-investments.php', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      const json = await res.json();
      if (json.success) {
        renderMyInvestments(json.data?.investments || []);
      }
    } catch (_) {}
  }

  function renderMyInvestments(investments) {
    const tbody = document.getElementById('my-investments-body');
    const empty = document.getElementById('my-investments-empty');
    if (!tbody) return;

    tbody.querySelectorAll('[data-inv-row]').forEach((r) => r.remove());

    if (!investments.length) {
      if (empty) empty.style.display = '';
      return;
    }
    if (empty) empty.style.display = 'none';

    const rows = investments.map((inv) => {
      const elapsed = parseInt(inv.days_elapsed)  || 0;
      const total   = parseInt(inv.duration_days) || 1;
      const pct     = Math.min(100, Math.round((elapsed / total) * 100));
      return `
        <tr data-inv-row>
          <td class="td-primary">${escapeHtml(inv.plan_name)}</td>
          <td style="font-variant-numeric:tabular-nums">${formatCurrency(inv.amount)}</td>
          <td>${formatDate(inv.start_date)}</td>
          <td>
            <div style="min-width:100px">
              <div class="progress-bar">
                <div class="progress-bar-fill" style="width:${pct}%"></div>
              </div>
              <div class="progress-bar-label">${elapsed}/${total} days</div>
            </div>
          </td>
          <td class="col-hide-mobile" style="font-variant-numeric:tabular-nums">
            ${formatCurrency(inv.profit_earned || 0)}
          </td>
          <td>
            <span class="status-badge status-badge--${escapeHtml(inv.status)}">
              ${escapeHtml(inv.status)}
            </span>
          </td>
        </tr>`;
    }).join('');

    tbody.insertAdjacentHTML('afterbegin', rows);
  }

  loadPlans();
  loadMyInvestments();
}


// ─── Membership Page ──────────────────────────────────────────
function initMembershipPage() {
  if (!document.getElementById('membership-plans-grid')) return;

  async function loadMembershipPlans() {
    try {
      const res  = await fetch('/api/user-dashboard/get-membership-plans.php', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      const json = await res.json();
      if (json.success && Array.isArray(json.data?.plans)) {
        renderMembershipPlans(json.data.plans, json.data.current_plan_id);
      }
    } catch (_) {
      const grid = document.getElementById('membership-plans-grid');
      if (grid) grid.innerHTML = '<div class="dashboard-empty-state"><p>Could not load plans.</p></div>';
    }
  }

  function renderMembershipPlans(plans, currentPlanId) {
    const grid = document.getElementById('membership-plans-grid');
    if (!grid) return;

    const loadingState = document.getElementById('membership-plans-loading');
    if (loadingState) loadingState.remove();

    // Cache plans by id so openMembershipEnroll() can look them up at click time
    _membershipPlansMap = {};
    plans.forEach((p) => { _membershipPlansMap[p.id] = p; });

    grid.innerHTML = plans.map((plan) => {
      const isCurrent = currentPlanId && String(currentPlanId) === String(plan.id);
      const color     = escapeHtml(plan.color_accent || '#A0A0A0');
      // Use openMembershipEnroll(id) — it reads _lastKnownBalance at click time,
      // so the modal always shows the up-to-date wallet balance.
      const ctaHtml = isCurrent
        ? `<div class="btn btn-full" style="background:var(--color-success);color:white;text-align:center;padding:10px;border-radius:var(--radius-md);font-size:var(--text-sm);font-weight:700">
             ✓ Current Plan
           </div>`
        : `<button type="button" class="btn btn-primary btn-full" onclick="openMembershipEnroll(${plan.id})">
             Enroll Now
           </button>`;

      return `
        <article class="investment-plan-card${isCurrent ? ' selected' : ''}" style="--plan-color:${color}">
          <div class="plan-card-badge">${escapeHtml(plan.name)}</div>
          <div class="plan-card-yield">
            $${parseFloat(plan.price).toFixed(2)}<span>/month</span>
          </div>
          <div class="plan-card-meta">
            <div class="plan-meta-row">
              <span class="plan-meta-key">Max Investments</span>
              <span class="plan-meta-value">${plan.max_active_investments ?? 'Unlimited'}</span>
            </div>
            <div class="plan-meta-row">
              <span class="plan-meta-key">Withdrawal Speed</span>
              <span class="plan-meta-value">${escapeHtml(String(plan.withdrawal_speed_hours))}h</span>
            </div>
          </div>
          ${ctaHtml}
        </article>`;
    }).join('');
  }

  loadMembershipPlans();
}


// ─── Payment History Page ─────────────────────────────────────
function initPaymentHistoryPage() {
  if (!document.getElementById('history-tx-body')) return;

  let currentPage = 1;
  const perPage   = 20;
  let totalCount  = 0;
  const filters   = { type: 'all', status: 'all', search: '', date_from: '', date_to: '' };

  async function loadHistory() {
    const params = new URLSearchParams({
      page:      currentPage,
      per_page:  perPage,
      type:      filters.type,
      status:    filters.status,
      search:    filters.search,
      date_from: filters.date_from,
      date_to:   filters.date_to,
    });

    try {
      showLoader();
      const res  = await fetch(`/api/user-dashboard/get-transactions.php?${params}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      const json = await res.json();
      if (json.success) {
        totalCount = json.data?.total || 0;
        renderHistoryTable(json.data?.transactions || []);
        renderHistoryPagination();
      }
    } catch (_) {} finally {
      hideLoader();
    }
  }

  function renderHistoryTable(transactions) {
    const tbody = document.getElementById('history-tx-body');
    const empty = document.getElementById('history-tx-empty');
    if (!tbody) return;

    tbody.querySelectorAll('[data-tx-row]').forEach((r) => r.remove());

    if (!transactions.length) {
      if (empty) empty.style.display = '';
      return;
    }
    if (empty) empty.style.display = 'none';

    const rows = transactions.map((tx) => {
      const sign      = (tx.type === 'withdrawal' || tx.type === 'fee') ? '−' : '+';
      const typeLabel = escapeHtml(tx.type.replace(/_/g, ' '));
      return `
        <tr data-tx-row>
          <td>${formatDate(tx.created_at)}</td>
          <td><span class="tx-type-badge tx-type-badge--${escapeHtml(tx.type)}">${typeLabel}</span></td>
          <td style="font-weight:700;font-variant-numeric:tabular-nums">${sign}${formatCurrency(tx.amount)}</td>
          <td><span class="status-badge status-badge--${escapeHtml(tx.status)}">${escapeHtml(tx.status)}</span></td>
          <td class="col-hide-mobile td-mono">${escapeHtml(tx.reference || '—')}</td>
          <td class="col-hide-mobile">${escapeHtml(tx.description || '—')}</td>
        </tr>`;
    }).join('');

    tbody.insertAdjacentHTML('afterbegin', rows);
  }

  function renderHistoryPagination() {
    const totalPages = Math.ceil(totalCount / perPage);
    const info       = document.getElementById('history-pagination-info');
    const controls   = document.getElementById('history-pagination-controls');

    if (info) {
      const start = totalCount ? (currentPage - 1) * perPage + 1 : 0;
      const end   = Math.min(currentPage * perPage, totalCount);
      info.textContent = totalCount
        ? `Showing ${start}–${end} of ${totalCount}`
        : 'No results found';
    }

    if (!controls) return;
    controls.innerHTML = '';

    const makeBtn = (label, page, disabled, isActive) => {
      const btn = document.createElement('button');
      btn.className  = `page-btn${isActive ? ' active' : ''}`;
      btn.textContent = label;
      btn.disabled    = disabled;
      if (!disabled) {
        btn.addEventListener('click', () => {
          currentPage = page;
          loadHistory();
        });
      }
      return btn;
    };

    controls.appendChild(makeBtn('←', currentPage - 1, currentPage <= 1, false));

    const startPage = Math.max(1, currentPage - 2);
    const endPage   = Math.min(totalPages, startPage + 4);
    for (let i = startPage; i <= endPage; i++) {
      controls.appendChild(makeBtn(i, i, false, i === currentPage));
    }

    controls.appendChild(makeBtn('→', currentPage + 1, currentPage >= totalPages, false));
  }

  // Bind filter controls
  const typeEl   = document.getElementById('history-filter-type');
  const statusEl = document.getElementById('history-filter-status');
  const fromEl   = document.getElementById('history-date-from');
  const toEl     = document.getElementById('history-date-to');
  const searchEl = document.getElementById('history-search');
  const csvBtn   = document.getElementById('history-csv-btn');

  if (typeEl)   typeEl.addEventListener('change',   () => { filters.type      = typeEl.value;   currentPage = 1; loadHistory(); });
  if (statusEl) statusEl.addEventListener('change', () => { filters.status    = statusEl.value; currentPage = 1; loadHistory(); });
  if (fromEl)   fromEl.addEventListener('change',   () => { filters.date_from = fromEl.value;   currentPage = 1; loadHistory(); });
  if (toEl)     toEl.addEventListener('change',     () => { filters.date_to   = toEl.value;     currentPage = 1; loadHistory(); });

  let _historySearchTimer;
  if (searchEl) {
    searchEl.addEventListener('input', () => {
      clearTimeout(_historySearchTimer);
      _historySearchTimer = setTimeout(() => {
        filters.search = searchEl.value.trim();
        currentPage    = 1;
        loadHistory();
      }, 400);
    });
  }

  // CSV export — fetch full result set
  if (csvBtn) {
    csvBtn.addEventListener('click', async () => {
      const params = new URLSearchParams({ ...filters, per_page: 9999, page: 1 });
      try {
        showLoader();
        const res  = await fetch(`/api/user-dashboard/get-transactions.php?${params}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin',
        });
        const json = await res.json();
        if (json.success) {
          exportToCSV(json.data?.transactions || [], 'payment-history.csv');
        }
      } catch (_) {} finally {
        hideLoader();
      }
    });
  }

  loadHistory();
}


// ─── Account Page ─────────────────────────────────────────────
function initAccountPage() {
  if (!document.getElementById('profile-form')) return;

  // Load profile
  async function loadProfile() {
    try {
      const res  = await fetch('/api/user-dashboard/get-profile.php', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      const json = await res.json();
      if (json.success && json.data) {
        populateProfile(json.data);
      }
    } catch (_) {}
  }

  function populateProfile(profile) {
    // Form fields
    ['first_name', 'last_name', 'email', 'phone', 'country', 'language'].forEach((field) => {
      const el = document.querySelector(`[name="${field}"]`);
      if (el && profile[field] != null) el.value = profile[field];
    });

    // Apply the user's saved language preference (from DB) if no localStorage override
    if (profile.language && typeof setLanguage === 'function') {
      const storedLang = localStorage.getItem('averon_lang');
      if (!storedLang) setLanguage(profile.language);
    }

    // Profile hero card
    const fullName = `${profile.first_name || ''} ${profile.last_name || ''}`.trim();
    const nameEl   = document.getElementById('profile-full-name');
    const emailEl  = document.getElementById('profile-email-display');
    const avatar   = document.getElementById('profile-avatar-large');

    if (nameEl)  nameEl.textContent  = fullName || escapeHtml(profile.first_name || '');
    if (emailEl) emailEl.textContent = profile.email || '—';
    if (avatar)  avatar.textContent  = (profile.first_name || 'U').charAt(0).toUpperCase();

    // Member since tag
    const sinceEl = document.getElementById('profile-member-since');
    if (sinceEl && profile.member_since) {
      sinceEl.innerHTML = `<svg viewBox="0 0 256 256" fill="currentColor" width="12" height="12" aria-hidden="true"><path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM48,48H72v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48ZM208,208H48V96H208V208Z"/></svg> Member since ${formatDate(profile.member_since)}`;
    }

    // Notification settings
    if (profile.settings) {
      Object.keys(profile.settings).forEach((key) => {
        const toggle = document.querySelector(`[name="notif_${key}"]`);
        if (toggle) toggle.checked = !!profile.settings[key];
      });
    }
  }

  // Profile form submit
  const profileForm = document.getElementById('profile-form');
  if (profileForm) {
    profileForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        await apiRequest('/api/user-dashboard/update-profile.php', 'POST', formToJSON(profileForm));
        showToast('Profile updated successfully!', 'success');
      } catch (_) {}
    });
  }

  // Password form submit
  const passwordForm = document.getElementById('password-form');
  if (passwordForm) {
    const newPwdInput   = passwordForm.querySelector('[name="new_password"]');
    const strengthBar   = document.getElementById('pwd-strength-bar');
    const strengthText  = document.getElementById('pwd-strength-text');

    if (newPwdInput && strengthBar) {
      const levels = ['', 'Very Weak', 'Weak', 'Fair', 'Strong', 'Very Strong'];
      const colors = ['', '#E31937', '#FF6B35', '#FFB300', '#00C851', '#00A843'];

      newPwdInput.addEventListener('input', () => {
        const strength = getPasswordStrength(newPwdInput.value);
        strengthBar.style.width      = `${(strength / 5) * 100}%`;
        strengthBar.style.background = colors[strength];
        strengthBar.setAttribute('aria-valuenow', (strength / 5) * 100);
        if (strengthText) strengthText.textContent = strength > 0 ? levels[strength] : '';
      });
    }

    passwordForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const data = formToJSON(passwordForm);
      if (data.new_password !== data.confirm_password) {
        showToast('New passwords do not match', 'error');
        return;
      }
      try {
        await apiRequest('/api/user-dashboard/change-password.php', 'POST', data);
        showToast('Password changed successfully!', 'success');
        passwordForm.reset();
        if (strengthBar)  { strengthBar.style.width = '0'; }
        if (strengthText) { strengthText.textContent = ''; }
      } catch (_) {}
    });
  }

  // Notification toggles — save state in localStorage (for show only, no backend)
  document.querySelectorAll('.notif-toggle').forEach((toggle) => {
    // Restore saved state
    const saved = localStorage.getItem('notif_' + toggle.name);
    if (saved !== null) toggle.checked = saved === 'true';

    toggle.addEventListener('change', () => {
      localStorage.setItem('notif_' + toggle.name, toggle.checked);
    });
  });

  loadProfile();
}


// ─── Password strength helper ─────────────────────────────────
function getPasswordStrength(pwd) {
  if (!pwd) return 0;
  let score = 0;
  if (pwd.length >= 8)           score++;
  if (pwd.length >= 12)          score++;
  if (/[A-Z]/.test(pwd))         score++;
  if (/[0-9]/.test(pwd))         score++;
  if (/[^A-Za-z0-9]/.test(pwd)) score++;
  return score;
}


// ─── CSV export helper ────────────────────────────────────────
function exportToCSV(data, filename) {
  if (!data || !data.length) { showToast('No data to export', 'info'); return; }

  const headers = ['Date', 'Type', 'Amount', 'Status', 'Reference', 'Note'];
  const rows    = data.map((tx) => [
    formatDate(tx.created_at),
    tx.type || '',
    tx.amount || '',
    tx.status || '',
    tx.reference || '',
    tx.description || '',
  ]);

  const csv  = [headers, ...rows]
    .map((row) => row.map((v) => `"${String(v).replace(/"/g, '""')}"`).join(','))
    .join('\n');

  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const url  = URL.createObjectURL(blob);
  const a    = document.createElement('a');
  a.href     = url;
  a.download = filename;
  a.click();
  URL.revokeObjectURL(url);
}


// ─── Init ────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  startPolling();
  initSidebarToggle();
  initActiveNav();
  initLogoutButton();
  initTopbarDropdowns();
  initMembershipCta();
  // Page-specific init (each function self-guards by checking for a key element)
  initWalletPage();
  initInvestmentsPage();
  initMembershipPage();
  initPaymentHistoryPage();
  initAccountPage();
});
