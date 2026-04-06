<?php
/* =====================================================================
   pages/admin/users.php
   Admin user management — paginated table, search, filters,
   AJAX edit/suspend/delete actions with modals.
   ===================================================================== */
require_once '../../includes/admin-auth-guard.php';
require_once '../../includes/icons.php';

$pageTitle = 'Users — Admin';
$extraCss  = '
  <link rel="stylesheet" href="/assets/css/dashboard.css">
  <link rel="stylesheet" href="/assets/css/dashboard-responsive.css">
';
$isAdmin = true;
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../../includes/head.php'; ?>
<body class="dashboard-body">
<?php include '../../includes/sidebar.php'; ?>

<div class="dashboard-layout">
  <main class="dashboard-main" id="dashboard-main">

    <!-- TOPBAR -->
    <header class="dashboard-topbar" role="banner">
      <div class="dashboard-topbar-left">
        <button class="sidebar-toggle" id="sidebar-toggle" type="button" aria-label="Toggle navigation">
          <?= ph('list', 20) ?>
        </button>
        <h1 class="topbar-title">User Management</h1>
      </div>
      <div class="dashboard-topbar-right">
        <div class="topbar-user-wrap">
          <button class="topbar-user-btn" id="topbar-user-btn" type="button" aria-label="Account menu"
            aria-expanded="false" aria-controls="user-dropdown" aria-haspopup="true">
            <span class="topbar-avatar"><?= mb_strtoupper(mb_substr($authUserName, 0, 1, 'UTF-8'), 'UTF-8') ?></span>
            <span class="topbar-username-label"><?= $authUserName ?></span>
            <?= ph('caret-down', 14) ?>
          </button>
          <div class="topbar-dropdown" id="user-dropdown" role="menu">
            <button type="button" class="topbar-dropdown-item topbar-dropdown-item--danger"
              data-logout="/api/auth/admin-logout.php"><?= ph('sign-out', 16) ?> Sign Out</button>
          </div>
        </div>
      </div>
    </header>

    <div class="dashboard-content">

      <!-- Filters bar -->
      <div class="admin-filter-bar">
        <div class="admin-search-wrap">
          <span class="admin-search-icon"><?= ph('magnifying-glass', 16) ?></span>
          <input type="text" id="user-search" class="admin-search-input" placeholder="Search name, email…" autocomplete="off">
        </div>
        <select id="user-status-filter" class="admin-select">
          <option value="">All Statuses</option>
          <option value="active">Active</option>
          <option value="suspended">Suspended</option>
          <option value="banned">Banned</option>
          <option value="pending">Pending</option>
        </select>
        <select id="user-role-filter" class="admin-select">
          <option value="">All Roles</option>
          <option value="user">User</option>
          <option value="admin">Admin</option>
        </select>
        <button class="btn btn-ghost btn-sm" id="user-filter-reset">Reset</button>
      </div>

      <!-- Users table -->
      <section class="dashboard-card" aria-label="Users table">
        <div class="dashboard-card-header">
          <h2 class="dashboard-card-title">All Users</h2>
          <span class="admin-count-badge" id="users-total-count">— users</span>
        </div>
        <div class="admin-table-wrapper">
          <table class="admin-table" id="users-table">
            <thead>
              <tr>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Balance</th>
                <th>Invested</th>
                <th>Profit</th>
                <th>Joined</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="users-tbody">
              <tr><td colspan="9" class="table-empty-msg">Loading…</td></tr>
            </tbody>
          </table>
        </div>
        <div class="admin-pagination" id="users-pagination"></div>
      </section>

    </div>
  </main>
</div>

<?php include '../../includes/admin-mobile-dock.php'; ?>

<!-- Edit User Modal -->
<div class="modal-overlay" id="modal-edit-user" role="dialog" aria-modal="true" aria-labelledby="edit-user-title">
  <div class="bottom-sheet admin-modal">
    <div class="bottom-sheet-handle"></div>
    <div class="bottom-sheet-header">
      <h3 id="edit-user-title">Edit User</h3>
      <button class="modal-close" type="button" onclick="closeAdminModal('modal-edit-user')" aria-label="Close">&#x2715;</button>
    </div>
    <div class="bottom-sheet-body">
      <input type="hidden" id="edit-user-id">

      <div class="admin-modal-tabs" role="tablist">
        <button class="admin-modal-tab active" data-tab="profile" role="tab" aria-selected="true">Profile</button>
        <button class="admin-modal-tab" data-tab="balance" role="tab">Balance</button>
        <button class="admin-modal-tab" data-tab="status" role="tab">Status</button>
      </div>

      <div class="admin-tab-panel" id="tab-profile">
        <div class="form-group">
          <label class="form-label" for="edit-first-name">First Name</label>
          <input type="text" id="edit-first-name" class="form-input">
        </div>
        <div class="form-group">
          <label class="form-label" for="edit-last-name">Last Name</label>
          <input type="text" id="edit-last-name" class="form-input">
        </div>
        <div class="form-group">
          <label class="form-label" for="edit-email">Email</label>
          <input type="email" id="edit-email" class="form-input">
        </div>
        <button class="btn btn-primary btn-full" id="save-profile-btn" type="button">Save Profile</button>
      </div>

      <div class="admin-tab-panel hidden" id="tab-balance">
        <div class="form-group">
          <label class="form-label" for="edit-balance">Wallet Balance ($)</label>
          <input type="number" id="edit-balance" class="form-input" step="0.01" min="0">
        </div>
        <div class="form-group">
          <label class="form-label" for="edit-profit-balance">Profit Balance ($)</label>
          <input type="number" id="edit-profit-balance" class="form-input" step="0.01" min="0">
        </div>
        <div class="form-group">
          <label class="form-label" for="edit-invested-amount">Invested Amount ($)</label>
          <input type="number" id="edit-invested-amount" class="form-input" step="0.01" min="0">
        </div>
        <button class="btn btn-primary btn-full" id="save-balance-btn" type="button">Update Balance</button>
      </div>

      <div class="admin-tab-panel hidden" id="tab-status">
        <div class="form-group">
          <label class="form-label" for="edit-status">Account Status</label>
          <select id="edit-status" class="form-input form-select">
            <option value="active">Active</option>
            <option value="suspended">Suspended</option>
            <option value="banned">Banned</option>
          </select>
        </div>
        <button class="btn btn-primary btn-full" id="save-status-btn" type="button">Update Status</button>
        <hr class="admin-modal-divider">
        <button class="btn btn-danger btn-full" id="delete-user-btn" type="button">
          Delete Account (Irreversible)
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Loader + Toast -->
<div id="global-loader" class="loader-overlay" style="display:none" aria-hidden="true">
  <div class="loader-inner">
    <img src="/assets/images/logo/avernonlogo.png" alt="" aria-hidden="true" style="height:40px;width:auto;animation:logoPulse 1.5s ease-in-out infinite;">
    <div class="loader-spinner"></div>
  </div>
</div>
<div id="toast-container" role="status" aria-live="polite"></div>

<script src="/assets/js/main.js" defer></script>
<script>
(function () {
  'use strict';

  let currentPage = 1;
  let searchTimeout;
  let editingUserId = null;

  const pencilIcon = <?= json_encode(ph('pencil-simple', 14)) ?>;

  function fmtMoney(n) { return '$' + Number(n||0).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }
  function fmtDate(s)  { return s ? new Date(s).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}) : '—'; }
  function escHtml(s)  { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
  function statusBadge(s) {
    const m = { active:'success', suspended:'warning', banned:'danger', pending:'info' };
    return `<span class="badge badge--${m[s]||'default'}">${escHtml(s)}</span>`;
  }
  function roleBadge(r) {
    return r === 'admin' ? `<span class="badge badge--accent">Admin</span>` : `<span class="badge badge--default">User</span>`;
  }

  async function loadUsers(page) {
    page = page || 1;
    const search = document.getElementById('user-search').value.trim();
    const status = document.getElementById('user-status-filter').value;
    const role   = document.getElementById('user-role-filter').value;
    const params = new URLSearchParams({ page: page, search: search, status: status, role: role });
    const tbody  = document.getElementById('users-tbody');
    tbody.innerHTML = '<tr><td colspan="9" class="table-empty-msg">Loading\u2026</td></tr>';
    try {
      const res  = await fetch('/api/admin-dashboard/get-users.php?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const json = await res.json();
      if (!json.success) throw new Error(json.message);
      var data = json.data;
      currentPage = page;
      document.getElementById('users-total-count').textContent = data.total + ' user' + (data.total !== 1 ? 's' : '');
      if (!data.users || !data.users.length) {
        tbody.innerHTML = '<tr><td colspan="9" class="table-empty-msg">No users found</td></tr>';
        renderPagination(0, 1, 1);
        return;
      }
      tbody.innerHTML = data.users.map(function(u) {
        var bal  = parseFloat(u.balance)||0;
        var prof = parseFloat(u.profit_balance)||0;
        var inv  = parseFloat(u.invested_amount)||0;
        var fn   = escHtml(u.first_name||'');
        var ln   = escHtml(u.last_name||'');
        var em   = escHtml(u.email||'');
        return '<tr>' +
          '<td><div class="user-cell"><div class="user-avatar-sm">' + ((u.first_name||'U')[0].toUpperCase()) + '</div>' +
          '<span class="user-name-sm">' + fn + ' ' + ln + '</span></div></td>' +
          '<td class="text-muted">' + em + '</td>' +
          '<td>' + roleBadge(u.role) + '</td>' +
          '<td>' + statusBadge(u.status) + '</td>' +
          '<td class="font-mono">' + fmtMoney(bal) + '</td>' +
          '<td class="font-mono">' + fmtMoney(inv) + '</td>' +
          '<td class="font-mono color-success">' + fmtMoney(prof) + '</td>' +
          '<td class="text-muted">' + fmtDate(u.created_at) + '</td>' +
          '<td><button class="btn btn-xs btn-ghost" onclick="openEditUser(' + u.id + ',\'' + fn + '\',\'' + ln + '\',\'' + em + '\',\'' + escHtml(u.status) + '\',' + bal + ',' + prof + ',' + inv + ')">' + pencilIcon + ' Edit</button></td>' +
          '</tr>';
      }).join('');
      renderPagination(data.total, page, data.pages);
    } catch (err) {
      tbody.innerHTML = '<tr><td colspan="9" class="table-empty-msg">Error: ' + escHtml(err.message) + '</td></tr>';
    }
  }

  function renderPagination(total, page, pages) {
    var wrap = document.getElementById('users-pagination');
    if (!wrap) return;
    if (pages <= 1) { wrap.innerHTML = ''; return; }
    var html = '';
    if (page > 1) html += '<button class="admin-page-btn" onclick="loadUsers(' + (page-1) + ')">\u2039 Prev</button>';
    var start = Math.max(1, page - 2);
    var end   = Math.min(pages, page + 2);
    for (var i = start; i <= end; i++) {
      html += '<button class="admin-page-btn' + (i===page?' active':'') + '" onclick="loadUsers(' + i + ')">' + i + '</button>';
    }
    if (page < pages) html += '<button class="admin-page-btn" onclick="loadUsers(' + (page+1) + ')">Next \u203a</button>';
    wrap.innerHTML = html;
  }

  window.openEditUser = function(id, fn, ln, email, status, bal, profit, invested) {
    editingUserId = id;
    document.getElementById('edit-user-id').value          = id;
    document.getElementById('edit-first-name').value       = fn;
    document.getElementById('edit-last-name').value        = ln;
    document.getElementById('edit-email').value            = email;
    document.getElementById('edit-status').value           = status;
    document.getElementById('edit-balance').value          = bal.toFixed(2);
    document.getElementById('edit-profit-balance').value   = profit.toFixed(2);
    document.getElementById('edit-invested-amount').value  = invested.toFixed(2);
    showTab('profile');
    document.getElementById('modal-edit-user').classList.add('active');
  };

  window.closeAdminModal = function(id) {
    document.getElementById(id).classList.remove('active');
  };

  function showTab(name) {
    document.querySelectorAll('.admin-tab-panel').forEach(function(p) { p.classList.add('hidden'); });
    document.querySelectorAll('.admin-modal-tab').forEach(function(t) {
      t.classList.remove('active');
      t.removeAttribute('aria-selected');
    });
    var panel = document.getElementById('tab-' + name);
    if (panel) panel.classList.remove('hidden');
    var tab = document.querySelector('.admin-modal-tab[data-tab="' + name + '"]');
    if (tab) { tab.classList.add('active'); tab.setAttribute('aria-selected','true'); }
  }

  document.querySelectorAll('.admin-modal-tab').forEach(function(btn) {
    btn.addEventListener('click', function() { showTab(btn.dataset.tab); });
  });

  document.getElementById('save-profile-btn').addEventListener('click', async function() {
    try {
      var res = await apiRequest('/api/admin-dashboard/update-user.php', 'POST', {
        user_id: editingUserId, action: 'update_profile',
        first_name: document.getElementById('edit-first-name').value.trim(),
        last_name:  document.getElementById('edit-last-name').value.trim(),
        email:      document.getElementById('edit-email').value.trim(),
      });
      showToast(res.message, 'success');
      closeAdminModal('modal-edit-user');
      loadUsers(currentPage);
    } catch(e) {}
  });

  document.getElementById('save-balance-btn').addEventListener('click', async function() {
    try {
      var res = await apiRequest('/api/admin-dashboard/update-user.php', 'POST', {
        user_id: editingUserId, action: 'update_balance',
        balance:         parseFloat(document.getElementById('edit-balance').value)||0,
        profit_balance:  parseFloat(document.getElementById('edit-profit-balance').value)||0,
        invested_amount: parseFloat(document.getElementById('edit-invested-amount').value)||0,
      });
      showToast(res.message, 'success');
      closeAdminModal('modal-edit-user');
      loadUsers(currentPage);
    } catch(e) {}
  });

  document.getElementById('save-status-btn').addEventListener('click', async function() {
    try {
      var res = await apiRequest('/api/admin-dashboard/update-user.php', 'POST', {
        user_id: editingUserId, action: 'update_status',
        status: document.getElementById('edit-status').value,
      });
      showToast(res.message, 'success');
      closeAdminModal('modal-edit-user');
      loadUsers(currentPage);
    } catch(e) {}
  });

  document.getElementById('delete-user-btn').addEventListener('click', async function() {
    if (!confirm('Are you sure you want to permanently delete this account? This cannot be undone.')) return;
    try {
      var res = await apiRequest('/api/admin-dashboard/update-user.php', 'POST', {
        user_id: editingUserId, action: 'delete',
      });
      showToast(res.message, 'success');
      closeAdminModal('modal-edit-user');
      loadUsers(currentPage);
    } catch(e) {}
  });

  document.getElementById('modal-edit-user').addEventListener('click', function(e) {
    if (e.target === this) closeAdminModal('modal-edit-user');
  });

  document.getElementById('user-search').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() { loadUsers(1); }, 400);
  });
  document.getElementById('user-status-filter').addEventListener('change', function() { loadUsers(1); });
  document.getElementById('user-role-filter').addEventListener('change',   function() { loadUsers(1); });
  document.getElementById('user-filter-reset').addEventListener('click',   function() {
    document.getElementById('user-search').value         = '';
    document.getElementById('user-status-filter').value = '';
    document.getElementById('user-role-filter').value   = '';
    loadUsers(1);
  });

  var sidebarToggle = document.getElementById('sidebar-toggle');
  var sidebar       = document.getElementById('dashboard-sidebar');
  var overlay       = document.getElementById('sidebar-overlay');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', function() {
      sidebar.classList.toggle('open');
      if (overlay) overlay.classList.toggle('active');
    });
    if (overlay) overlay.addEventListener('click', function() {
      sidebar.classList.remove('open');
      overlay.classList.remove('active');
    });
  }

  var userBtn  = document.getElementById('topbar-user-btn');
  var userDrop = document.getElementById('user-dropdown');
  if (userBtn) {
    userBtn.addEventListener('click', function(e) { e.stopPropagation(); userDrop.classList.toggle('open'); });
    document.addEventListener('click', function() { userDrop.classList.remove('open'); });
  }
  document.addEventListener('click', function(e) {
    var el = e.target.closest('[data-logout]');
    if (el) { e.preventDefault(); fetch(el.dataset.logout).finally(function() { window.location.href = '/login'; }); }
  });

  loadUsers(1);
})();
</script>

<style>
.admin-filter-bar { display:flex; gap:var(--space-3); margin-bottom:var(--space-5); flex-wrap:wrap; align-items:center; }
.admin-search-wrap { position:relative; flex:1; min-width:200px; }
.admin-search-icon { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-muted); pointer-events:none; display:flex; align-items:center; justify-content:center; line-height:0; width:16px; height:16px; }
.admin-search-icon svg { display:block; }
.admin-search-input { width:100%; padding:var(--space-2) var(--space-3) var(--space-2) 36px; border:1px solid var(--border-color); border-radius:var(--radius-md); font-size:var(--text-sm); font-family:var(--font-sans); background:var(--bg-elevated); color:var(--text-primary); outline:none; transition:border-color var(--transition-fast); }
.admin-search-input:focus { border-color:var(--color-primary); }
.admin-select { padding:var(--space-2) var(--space-3); border:1px solid var(--border-color); border-radius:var(--radius-md); font-size:var(--text-sm); font-family:var(--font-sans); background:var(--bg-elevated); color:var(--text-primary); cursor:pointer; outline:none; }
.admin-count-badge { font-size:var(--text-sm); color:var(--text-muted); background:var(--bg-muted); padding:3px 10px; border-radius:var(--radius-full); }
.admin-pagination { display:flex; gap:var(--space-2); justify-content:center; padding:var(--space-4) 0 var(--space-2); flex-wrap:wrap; }
.admin-page-btn { padding:var(--space-2) var(--space-3); border:1px solid var(--border-color); border-radius:var(--radius-md); background:var(--bg-elevated); color:var(--text-secondary); font-size:var(--text-sm); cursor:pointer; font-family:var(--font-sans); transition:all var(--transition-fast); }
.admin-page-btn:hover,.admin-page-btn.active { border-color:var(--color-primary); color:var(--color-primary); background:var(--color-primary-light); }
.admin-modal { max-width:540px; }
.admin-modal-tabs { display:flex; gap:var(--space-2); border-bottom:1px solid var(--border-color); margin-bottom:var(--space-5); }
.admin-modal-tab { padding:var(--space-2) var(--space-4); border:none; background:none; font-size:var(--text-sm); font-weight:600; color:var(--text-muted); cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-1px; font-family:var(--font-sans); transition:all var(--transition-fast); }
.admin-modal-tab.active { color:var(--color-primary); border-bottom-color:var(--color-primary); }
.admin-tab-panel { display:flex; flex-direction:column; gap:var(--space-4); }
.admin-tab-panel.hidden { display:none; }
.admin-modal-divider { border:none; border-top:1px solid var(--border-color); margin:var(--space-4) 0; }
.user-cell { display:flex; align-items:center; gap:var(--space-2); }
.user-avatar-sm { width:30px; height:30px; border-radius:50%; background:var(--color-primary-light); color:var(--color-primary); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:var(--text-xs); flex-shrink:0; }
.user-name-sm { font-size:var(--text-sm); font-weight:600; }
.font-mono { font-family:var(--font-mono); font-size:var(--text-sm); }
.color-success { color:var(--color-success); }
.text-muted { color:var(--text-muted); font-size:var(--text-sm); }
.btn-xs { padding:3px 10px; font-size:var(--text-xs); }
.btn-full { width:100%; }
.btn-danger { background:var(--color-danger); color:#fff; border:none; cursor:pointer; padding:var(--space-3) var(--space-5); border-radius:var(--radius-md); font-size:var(--text-sm); font-weight:600; font-family:var(--font-sans); transition:opacity var(--transition-fast); }
.btn-danger:hover { opacity:0.85; }
.badge--accent { background:#FDF0E0; color:#C05300; }
.badge--default { background:var(--bg-muted); color:var(--text-muted); }
</style>
</body>
</html>
