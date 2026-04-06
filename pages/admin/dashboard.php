<?php
/* =====================================================================
   pages/admin/dashboard.php
   Admin dashboard — stat cards, revenue chart, recent transactions,
   activity feed. All live data loaded via AJAX from get-statistics.php.
   ===================================================================== */
require_once '../../includes/admin-auth-guard.php';
require_once '../../includes/icons.php';

$pageTitle = 'Admin Dashboard';
$extraCss  = '
  <link rel="stylesheet" href="/assets/css/dashboard.css">
  <link rel="stylesheet" href="/assets/css/dashboard-responsive.css">
';

$isAdmin     = true;
$todayDisplay = date('l, F j, Y');
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../../includes/head.php'; ?>

<body class="dashboard-body">

<?php include '../../includes/sidebar.php'; ?>

<div class="dashboard-layout">
  <main class="dashboard-main" id="dashboard-main">

    <!-- ══ TOPBAR ════════════════════════════════════════════════ -->
    <header class="dashboard-topbar" role="banner">
      <div class="dashboard-topbar-left">
        <button class="sidebar-toggle" id="sidebar-toggle" type="button"
          aria-label="Toggle navigation" aria-expanded="false" aria-controls="dashboard-sidebar">
          <?= ph('list', 20) ?>
        </button>
        <div class="topbar-welcome">
          <h1 class="topbar-title">Admin Overview</h1>
          <p class="topbar-date"><?= $todayDisplay ?></p>
        </div>
      </div>
      <div class="dashboard-topbar-right">
        <button class="topbar-icon-btn" id="admin-refresh-btn" type="button" aria-label="Refresh stats" title="Refresh">
          <?= ph('arrow-clockwise', 18) ?>
        </button>
        <div class="topbar-user-wrap">
          <button class="topbar-user-btn" id="topbar-user-btn" type="button"
            aria-label="Account menu" aria-expanded="false" aria-controls="user-dropdown" aria-haspopup="true">
            <span class="topbar-avatar" aria-hidden="true">
              <?= mb_strtoupper(mb_substr($authUserName, 0, 1, 'UTF-8'), 'UTF-8') ?>
            </span>
            <span class="topbar-username-label"><?= $authUserName ?></span>
            <?= ph('caret-down', 14) ?>
          </button>
          <div class="topbar-dropdown" id="user-dropdown" role="menu" aria-label="Account menu">
            <button type="button" class="topbar-dropdown-item topbar-dropdown-item--danger"
              role="menuitem" data-logout="/api/auth/admin-logout.php">
              <?= ph('sign-out', 16) ?>
              Sign Out
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- ══ PAGE CONTENT ══════════════════════════════════════════ -->
    <div class="dashboard-content">

      <!-- Period selector -->
      <div class="admin-period-bar">
        <div class="admin-period-label">Showing data for:</div>
        <div class="admin-period-tabs" role="tablist" aria-label="Time period">
          <button class="admin-period-tab" role="tab" data-period="today">Today</button>
          <button class="admin-period-tab" role="tab" data-period="7d">7 Days</button>
          <button class="admin-period-tab active" role="tab" data-period="30d" aria-selected="true">30 Days</button>
          <button class="admin-period-tab" role="tab" data-period="90d">90 Days</button>
        </div>
      </div>

      <!-- ── 1. STAT CARDS ──────────────────────────────────────── -->
      <section class="stats-grid admin-stats-grid" aria-label="Platform overview" id="admin-stat-cards">

        <article class="stat-overview-card stat-overview-card--primary">
          <div class="stat-overview-header">
            <span class="stat-overview-label">Total Revenue</span>
            <div class="stat-overview-icon"><?= ph('currency-dollar', 18) ?></div>
          </div>
          <div class="stat-overview-value" id="stat-revenue">—</div>
          <div class="stat-overview-sub" id="stat-revenue-sub">Loading…</div>
        </article>

        <article class="stat-overview-card">
          <div class="stat-overview-header">
            <span class="stat-overview-label">Total Deposits</span>
            <div class="stat-overview-icon"><?= ph('arrow-circle-down', 18) ?></div>
          </div>
          <div class="stat-overview-value" id="stat-deposits">—</div>
          <div class="stat-overview-sub" id="stat-deposits-sub">in selected period</div>
        </article>

        <article class="stat-overview-card">
          <div class="stat-overview-header">
            <span class="stat-overview-label">Withdrawals</span>
            <div class="stat-overview-icon"><?= ph('arrow-circle-up', 18) ?></div>
          </div>
          <div class="stat-overview-value" id="stat-withdrawals">—</div>
          <div class="stat-overview-sub" id="stat-withdrawals-sub">in selected period</div>
        </article>

        <article class="stat-overview-card">
          <div class="stat-overview-header">
            <span class="stat-overview-label">New Users</span>
            <div class="stat-overview-icon"><?= ph('users', 18) ?></div>
          </div>
          <div class="stat-overview-value" id="stat-new-users">—</div>
          <div class="stat-overview-sub" id="stat-total-users">— total users</div>
        </article>

        <article class="stat-overview-card">
          <div class="stat-overview-header">
            <span class="stat-overview-label">Active Investments</span>
            <div class="stat-overview-icon"><?= ph('chart-line-up', 18) ?></div>
          </div>
          <div class="stat-overview-value" id="stat-investments">—</div>
          <div class="stat-overview-sub" id="stat-invested-sub">— total invested</div>
        </article>

        <article class="stat-overview-card">
          <div class="stat-overview-header">
            <span class="stat-overview-label">Platform Wallet</span>
            <div class="stat-overview-icon"><?= ph('bank', 18) ?></div>
          </div>
          <div class="stat-overview-value" id="stat-balance">—</div>
          <div class="stat-overview-sub">All user balances combined</div>
        </article>

      </section>

      <!-- ── 2. QUICK ACTIONS ───────────────────────────────────── -->
      <section class="admin-quick-actions" aria-label="Quick actions">
        <a href="/admin/users" class="admin-qa-card">
          <div class="admin-qa-icon"><?= ph('users', 22) ?></div>
          <span>Manage Users</span>
        </a>
        <a href="/admin/transactions" class="admin-qa-card">
          <div class="admin-qa-icon"><?= ph('currency-dollar', 22) ?></div>
          <span>Transactions</span>
        </a>
        <a href="/admin/investments" class="admin-qa-card">
          <div class="admin-qa-icon"><?= ph('chart-line', 22) ?></div>
          <span>Inv. Plans</span>
        </a>
        <a href="/admin/membership" class="admin-qa-card">
          <div class="admin-qa-icon"><?= ph('crown', 22) ?></div>
          <span>Memberships</span>
        </a>
        <a href="/admin/statistics" class="admin-qa-card">
          <div class="admin-qa-icon"><?= ph('chart-bar', 22) ?></div>
          <span>Statistics</span>
        </a>
      </section>

      <!-- ── 3. CHART + ACTIVITY ROW ────────────────────────────── -->
      <div class="dashboard-row admin-chart-row">

        <!-- Revenue chart -->
        <section class="dashboard-card admin-chart-card" aria-label="Revenue chart">
          <div class="dashboard-card-header">
            <h2 class="dashboard-card-title">Revenue Overview</h2>
            <div class="admin-chart-legend">
              <span class="legend-dot legend-dot--deposits"></span>Deposits
              <span class="legend-dot legend-dot--withdrawals"></span>Withdrawals
              <span class="legend-dot legend-dot--profits"></span>Profits Paid
            </div>
          </div>
          <div class="admin-chart-wrap">
            <canvas id="admin-revenue-chart"></canvas>
          </div>
        </section>

        <!-- Activity feed -->
        <section class="dashboard-card admin-activity-card" aria-label="Recent activity">
          <div class="dashboard-card-header">
            <h2 class="dashboard-card-title">Recent Activity</h2>
            <a href="/admin/transactions" class="btn btn-ghost btn-sm">View all</a>
          </div>
          <div class="activity-feed" id="admin-activity-feed" aria-live="polite">
            <p class="table-empty-msg">Loading…</p>
          </div>
        </section>

      </div><!-- /.dashboard-row -->

      <!-- ── 4. RECENT TRANSACTIONS TABLE ──────────────────────── -->
      <section class="dashboard-card" aria-label="Recent transactions">
        <div class="dashboard-card-header">
          <h2 class="dashboard-card-title">Recent Transactions</h2>
          <a href="/admin/transactions" class="btn btn-ghost btn-sm">View all</a>
        </div>
        <div class="admin-table-wrapper">
          <table class="admin-table" id="admin-recent-tx-table">
            <thead>
              <tr>
                <th>User</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="admin-recent-tx-body">
              <tr><td colspan="6" class="table-empty-msg">Loading…</td></tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- ── 5. TOP INVESTORS ────────────────────────────────────── -->
      <section class="dashboard-card" aria-label="Top investors">
        <div class="dashboard-card-header">
          <h2 class="dashboard-card-title">Top Investors</h2>
        </div>
        <div class="admin-table-wrapper">
          <table class="admin-table" id="admin-top-investors-table">
            <thead>
              <tr>
                <th>#</th>
                <th>User</th>
                <th>Invested</th>
                <th>Wallet Balance</th>
                <th>Profit Balance</th>
                <th>Active Investments</th>
              </tr>
            </thead>
            <tbody id="admin-top-investors-body">
              <tr><td colspan="6" class="table-empty-msg">Loading…</td></tr>
            </tbody>
          </table>
        </div>
      </section>

    </div><!-- /.dashboard-content -->
  </main>
</div>

<?php include '../../includes/admin-mobile-dock.php'; ?>

<!-- Loader + Toast (required per CLAUDE.md) -->
<div id="global-loader" class="loader-overlay" style="display:none" aria-hidden="true">
  <div class="loader-inner">
    <img src="/assets/images/logo/avernonlogo.png" alt="" aria-hidden="true" style="height:40px;width:auto;animation:logoPulse 1.5s ease-in-out infinite;">
    <div class="loader-spinner"></div>
  </div>
</div>
<div id="toast-container" role="status" aria-live="polite"></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script src="/assets/js/main.js" defer></script>
<script src="/assets/js/dashboard.js" defer></script>
<script>
/* ── Admin Dashboard JS ──────────────────────────────────── */
(function () {
  'use strict';

  let currentPeriod = '30d';
  let chartInstance = null;

  // ── Format helpers ────────────────────────────────────────
  function fmtMoney(n) {
    return '$' + Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }
  function fmtNum(n) {
    return Number(n || 0).toLocaleString('en-US');
  }
  function fmtDate(str) {
    if (!str) return '—';
    return new Date(str).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  }
  function statusBadge(s) {
    const map = { confirmed: 'success', completed: 'success', pending: 'warning', failed: 'danger', processing: 'info' };
    const cls = map[s] || 'default';
    return `<span class="badge badge--${cls}">${s}</span>`;
  }
  function typeBadge(t) {
    const icons = { deposit: '↓', withdrawal: '↑', profit: '+', referral_bonus: '★', membership_fee: '♦' };
    return `<span class="tx-type-chip tx-type-chip--${t}">${icons[t] || ''} ${t.replace('_',' ')}</span>`;
  }

  // ── Load stats from API ───────────────────────────────────
  async function loadStats(period) {
    try {
      showLoader();
      const res  = await fetch(`/api/admin-dashboard/get-statistics.php?period=${period}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const json = await res.json();
      if (!json.success) throw new Error(json.message);
      renderStats(json.data);
    } catch (err) {
      showToast('Failed to load statistics: ' + err.message, 'error');
    } finally {
      hideLoader();
    }
  }

  // ── Render stat cards ─────────────────────────────────────
  function renderStats(d) {
    const s = d.summary;
    document.getElementById('stat-revenue').textContent      = fmtMoney(s.revenue);
    document.getElementById('stat-revenue-sub').textContent  = `Deposits − Withdrawals − Profits`;
    document.getElementById('stat-deposits').textContent     = fmtMoney(s.total_deposits);
    document.getElementById('stat-withdrawals').textContent  = fmtMoney(s.total_withdrawals);
    document.getElementById('stat-new-users').textContent    = fmtNum(s.new_users);
    document.getElementById('stat-total-users').textContent  = fmtNum(s.total_users) + ' total users';
    document.getElementById('stat-investments').textContent  = fmtNum(s.active_investments);
    document.getElementById('stat-invested-sub').textContent = fmtMoney(s.total_invested) + ' total invested';
    document.getElementById('stat-balance').textContent      = fmtMoney(s.total_balance);

    renderChart(d.chart);
    renderActivity(d.recent_activity);
    renderRecentTx(d.recent_activity);
    renderTopInvestors(d.top_investors);
  }

  // ── Chart (canvas, no external lib needed — lightweight) ──
  function renderChart(chartData) {
    const canvas = document.getElementById('admin-revenue-chart');
    if (!canvas || typeof Chart === 'undefined') return;
    const ctx = canvas.getContext('2d');
    drawChart(ctx, chartData, canvas);
  }

  function drawChart(ctx, chartData, canvas) {
    const labels     = chartData.map(r => r.date.slice(5));
    const deposits   = chartData.map(r => r.deposits);
    const withdrawals= chartData.map(r => r.withdrawals);
    const profits    = chartData.map(r => r.profits);

    if (chartInstance) { chartInstance.destroy(); chartInstance = null; }

    chartInstance = new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [
          {
            label: 'Deposits',
            data: deposits,
            borderColor: '#73BA9B',
            backgroundColor: 'rgba(62,106,225,0.08)',
            fill: true,
            tension: 0.4,
            pointRadius: 2,
          },
          {
            label: 'Withdrawals',
            data: withdrawals,
            borderColor: '#C47A2B',
            backgroundColor: 'rgba(196,122,43,0.06)',
            fill: false,
            tension: 0.4,
            pointRadius: 2,
          },
          {
            label: 'Profits Paid',
            data: profits,
            borderColor: '#002914',
            backgroundColor: 'rgba(46,125,50,0.06)',
            fill: false,
            tension: 0.4,
            pointRadius: 2,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false }, ticks: { maxTicksLimit: 10, font: { size: 11 } } },
          y: {
            grid: { color: 'rgba(0,0,0,0.04)' },
            ticks: {
              font: { size: 11 },
              callback: v => '$' + (v >= 1000 ? (v/1000).toFixed(1)+'k' : v),
            },
          },
        },
      },
    });
  }

  // ── Activity feed ─────────────────────────────────────────
  function renderActivity(items) {
    const feed = document.getElementById('admin-activity-feed');
    if (!feed) return;
    if (!items || !items.length) {
      feed.innerHTML = '<p class="table-empty-msg">No recent activity</p>';
      return;
    }
    feed.innerHTML = items.slice(0, 8).map(a => `
      <div class="activity-item">
        <div class="activity-avatar">${(a.name||'U')[0].toUpperCase()}</div>
        <div class="activity-body">
          <div class="activity-text"><strong>${escHtml(a.name)}</strong> — ${a.type.replace('_',' ')}</div>
          <div class="activity-meta">${fmtMoney(a.amount)} · ${fmtDate(a.created_at)}</div>
        </div>
        <div class="activity-badge">${statusBadge(a.status)}</div>
      </div>
    `).join('');
  }

  // ── Recent transactions table ─────────────────────────────
  function renderRecentTx(items) {
    const tbody = document.getElementById('admin-recent-tx-body');
    if (!tbody) return;
    if (!items || !items.length) {
      tbody.innerHTML = '<tr><td colspan="6" class="table-empty-msg">No transactions found</td></tr>';
      return;
    }
    tbody.innerHTML = items.map(tx => `
      <tr>
        <td>
          <div class="user-cell">
            <div class="user-avatar-sm">${(tx.name||'U')[0].toUpperCase()}</div>
            <div>
              <div class="user-name-sm">${escHtml(tx.name)}</div>
              <div class="user-email-sm">${escHtml(tx.email||'')}</div>
            </div>
          </div>
        </td>
        <td>${typeBadge(tx.type)}</td>
        <td class="font-mono">${fmtMoney(tx.amount)}</td>
        <td>${statusBadge(tx.status)}</td>
        <td class="text-muted">${fmtDate(tx.created_at)}</td>
        <td>
          ${tx.status === 'pending' && tx.type === 'deposit' ? `
            <div class="tx-action-group">
              <button class="btn btn-xs btn-primary" onclick="adminApproveTx(${tx.id},'approve_deposit')">Approve</button>
              <button class="btn btn-xs btn-danger"  onclick="adminApproveTx(${tx.id},'reject_deposit')">Reject</button>
            </div>
          ` : tx.status === 'pending' && tx.type === 'withdrawal' ? `
            <div class="tx-action-group">
              <button class="btn btn-xs btn-primary" onclick="adminApproveTx(${tx.id},'approve_withdrawal')">Approve</button>
              <button class="btn btn-xs btn-danger"  onclick="adminApproveTx(${tx.id},'reject_withdrawal')">Reject</button>
            </div>
          ` : `<span class="tx-action-done">${tx.status === 'confirmed' || tx.status === 'completed' ? '&#10003; Done' : '&mdash;'}</span>`}
        </td>
      </tr>
    `).join('');
  }

  // ── Top investors table ───────────────────────────────────
  function renderTopInvestors(items) {
    const tbody = document.getElementById('admin-top-investors-body');
    if (!tbody) return;
    if (!items || !items.length) {
      tbody.innerHTML = '<tr><td colspan="6" class="table-empty-msg">No investors found</td></tr>';
      return;
    }
    tbody.innerHTML = items.map((inv, i) => `
      <tr>
        <td class="text-muted">${i + 1}</td>
        <td>
          <div class="user-cell">
            <div class="user-avatar-sm">${(inv.name||'U')[0].toUpperCase()}</div>
            <span class="user-name-sm">${escHtml(inv.name)}</span>
          </div>
        </td>
        <td class="font-mono fw-600">${fmtMoney(inv.invested_amount)}</td>
        <td class="font-mono">${fmtMoney(inv.balance)}</td>
        <td class="font-mono color-success">${fmtMoney(inv.profit_balance)}</td>
        <td>${inv.total_investments}</td>
      </tr>
    `).join('');
  }

  // ── Escape HTML helper ────────────────────────────────────
  function escHtml(s) {
    if (!s) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  // ── Approve/Reject a transaction ──────────────────────────
  window.adminApproveTx = async function(txId, action) {
    if (!confirm(`Are you sure you want to ${action.replace('_',' ')} this transaction?`)) return;
    try {
      const res  = await apiRequest('/api/admin-dashboard/manage-transaction.php', 'POST', { transaction_id: txId, action });
      showToast(res.message || 'Done', 'success');
      loadStats(currentPeriod);
    } catch (_) {}
  };

  // ── Period tab switching ──────────────────────────────────
  document.querySelectorAll('.admin-period-tab').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.admin-period-tab').forEach(b => {
        b.classList.remove('active');
        b.removeAttribute('aria-selected');
      });
      btn.classList.add('active');
      btn.setAttribute('aria-selected', 'true');
      currentPeriod = btn.dataset.period;
      loadStats(currentPeriod);
    });
  });

  // ── Refresh button ────────────────────────────────────────
  const refreshBtn = document.getElementById('admin-refresh-btn');
  if (refreshBtn) refreshBtn.addEventListener('click', () => loadStats(currentPeriod));

  // ── Sidebar toggle ────────────────────────────────────────
  const sidebarToggle  = document.getElementById('sidebar-toggle');
  const sidebar        = document.getElementById('dashboard-sidebar');
  const overlay        = document.getElementById('sidebar-overlay');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
      overlay && overlay.classList.toggle('active');
    });
    overlay && overlay.addEventListener('click', () => {
      sidebar.classList.remove('open');
      overlay.classList.remove('active');
    });
  }

  // Logout delegation + initial load
  document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (e) => {
      const el = e.target.closest('[data-logout]');
      if (el) {
        e.preventDefault();
        fetch(el.dataset.logout).finally(() => { window.location.href = '/login'; });
      }
    });

    // Initial load
    loadStats(currentPeriod);
  });

})();
</script>

<style>
/* ── Admin Dashboard Styles ──────────────────────────────── */
.admin-period-bar {
  display: flex;
  align-items: center;
  gap: var(--space-4);
  margin-bottom: var(--space-5);
  flex-wrap: wrap;
}
.admin-period-label { font-size: var(--text-sm); color: var(--text-muted); }
.admin-period-tabs  { display: flex; gap: var(--space-2); flex-wrap: wrap; }
.admin-period-tab {
  padding: var(--space-2) var(--space-4);
  border-radius: var(--radius-full);
  border: 1px solid var(--border-color);
  background: var(--bg-elevated);
  font-size: var(--text-sm);
  font-weight: 500;
  color: var(--text-secondary);
  cursor: pointer;
  transition: all var(--transition-fast);
  font-family: var(--font-sans);
}
.admin-period-tab:hover   { border-color: var(--color-primary); color: var(--color-primary); }
.admin-period-tab.active  { background: var(--color-primary); color: #fff; border-color: var(--color-primary); }

.admin-stats-grid { grid-template-columns: repeat(3, 1fr); }

.admin-quick-actions {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: var(--space-4);
  margin-bottom: var(--space-6);
}
.admin-qa-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
  padding: var(--space-5) var(--space-3);
  background: var(--bg-elevated);
  border: 1px solid var(--border-color);
  border-radius: var(--radius-lg);
  text-decoration: none;
  color: var(--text-secondary);
  font-size: var(--text-sm);
  font-weight: 600;
  transition: all var(--transition-fast);
  text-align: center;
}
.admin-qa-card:hover { border-color: var(--color-primary); color: var(--color-primary); box-shadow: var(--shadow-sm); }
.admin-qa-icon { color: var(--color-primary); }

.admin-chart-row { grid-template-columns: 1fr 360px; }
.admin-chart-card { flex: 1; }
.admin-chart-wrap { height: 260px; position: relative; }

.admin-chart-legend {
  display: flex;
  align-items: center;
  gap: var(--space-4);
  font-size: var(--text-xs);
  color: var(--text-muted);
}
.legend-dot {
  display: inline-block;
  width: 8px; height: 8px;
  border-radius: 50%;
  margin-right: 4px;
}
.legend-dot--deposits    { background: #73BA9B; }
.legend-dot--withdrawals { background: #BA2D0B; }
.legend-dot--profits     { background: #002914; }

.admin-activity-card { max-width: 360px; min-width: 280px; }
.activity-feed       { display: flex; flex-direction: column; gap: var(--space-3); }
.activity-item       { display: flex; align-items: flex-start; gap: var(--space-3); padding-bottom: var(--space-3); border-bottom: 1px solid var(--border-color); }
.activity-item:last-child { border-bottom: none; padding-bottom: 0; }
.activity-avatar     { width: 32px; height: 32px; border-radius: 50%; background: var(--color-primary-light); color: var(--color-primary); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: var(--text-sm); flex-shrink: 0; }
.activity-body       { flex: 1; min-width: 0; }
.activity-text       { font-size: var(--text-sm); color: var(--text-primary); line-height: 1.4; }
.activity-meta       { font-size: var(--text-xs); color: var(--text-muted); margin-top: 2px; }
.activity-badge      { flex-shrink: 0; }

.tx-type-chip { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: var(--radius-full); font-size: var(--text-xs); font-weight: 600; text-transform: capitalize; }
.tx-type-chip--deposit     { background: #EAF5EF; color: #73BA9B; }
.tx-type-chip--withdrawal  { background: #FFF0F0; color: #D32F2F; }
.tx-type-chip--profit      { background: #E8F5E9; color: #2E7D32; }
.tx-type-chip--referral_bonus   { background: #FFF8E1; color: #F5A623; }
.tx-type-chip--membership_fee   { background: #F3E5F5; color: #7B1FA2; }

.user-cell       { display: flex; align-items: center; gap: var(--space-2); }
.user-avatar-sm  { width: 30px; height: 30px; border-radius: 50%; background: var(--color-primary-light); color: var(--color-primary); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: var(--text-xs); flex-shrink: 0; }
.user-name-sm    { font-size: var(--text-sm); font-weight: 600; color: var(--text-primary); }
.user-email-sm   { font-size: var(--text-xs); color: var(--text-muted); }

.font-mono  { font-family: var(--font-mono); }
.fw-600     { font-weight: 600; }
.color-success { color: var(--color-success); }
.text-muted { color: var(--text-muted); font-size: var(--text-sm); }

.btn-xs { padding: 3px 10px; font-size: var(--text-xs); }
.btn-danger { background: var(--color-danger); color: #fff; border: none; }
.btn-danger:hover { background: var(--color-danger); opacity: 0.85; }
.tx-action-group { display: flex; gap: 6px; flex-wrap: wrap; }
.tx-action-done { font-size: var(--text-xs); color: var(--text-muted); }

@media (max-width: 1100px) {
  .admin-stats-grid     { grid-template-columns: repeat(2, 1fr); }
  .admin-quick-actions  { grid-template-columns: repeat(3, 1fr); }
  .admin-chart-row      { grid-template-columns: 1fr; }
  .admin-activity-card  { max-width: 100%; }
}
/* Mobile: quick actions become a horizontally scrollable strip */
@media (max-width: 767px) {
  .admin-quick-actions {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-bottom: var(--space-2);
    gap: var(--space-3);
  }
  .admin-quick-actions::-webkit-scrollbar { display: none; }
  .admin-qa-card {
    min-width: 88px;
    flex-shrink: 0;
    padding: var(--space-4) var(--space-3);
  }
}
@media (max-width: 600px) {
  .admin-stats-grid { grid-template-columns: 1fr; }
}
</style>

</body>
</html>
