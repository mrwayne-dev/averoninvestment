<?php
/* =====================================================================
   pages/admin/membership.php  — Membership plans CRUD
   ===================================================================== */
require_once '../../includes/admin-auth-guard.php';
require_once '../../includes/icons.php';
$pageTitle = 'Membership Plans — Admin';
$extraCss  = '<link rel="stylesheet" href="/assets/css/dashboard.css"><link rel="stylesheet" href="/assets/css/dashboard-responsive.css">';
$isAdmin   = true;
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../../includes/head.php'; ?>
<body class="dashboard-body">
<?php include '../../includes/sidebar.php'; ?>
<div class="dashboard-layout">
<main class="dashboard-main" id="dashboard-main">

<header class="dashboard-topbar" role="banner">
  <div class="dashboard-topbar-left">
    <button class="sidebar-toggle" id="sidebar-toggle" type="button" aria-label="Toggle"><?= ph('list',20) ?></button>
    <h1 class="topbar-title">Membership Plans</h1>
  </div>
  <div class="dashboard-topbar-right">
    <button class="btn btn-primary btn-sm" id="create-plan-btn" type="button"><?= ph('plus',16) ?> New Plan</button>
    <div class="topbar-user-wrap">
      <button class="topbar-user-btn" id="topbar-user-btn" type="button" aria-haspopup="true">
        <span class="topbar-avatar"><?= mb_strtoupper(mb_substr($authUserName,0,1,'UTF-8'),'UTF-8') ?></span>
        <?= ph('caret-down',14) ?>
      </button>
      <div class="topbar-dropdown" id="user-dropdown" role="menu">
        <button type="button" class="topbar-dropdown-item topbar-dropdown-item--danger" data-logout="/api/auth/admin-logout.php"><?= ph('sign-out',16) ?> Sign Out</button>
      </div>
    </div>
  </div>
</header>

<div class="dashboard-content">
  <!-- Active members summary cards -->
  <div class="mem-summary-row" id="mem-summary-row">
    <!-- populated by JS -->
  </div>

  <section class="dashboard-card">
    <div class="dashboard-card-header">
      <h2 class="dashboard-card-title">All Membership Plans</h2>
    </div>
    <div class="admin-table-wrapper">
      <table class="admin-table">
        <thead><tr><th>Name</th><th>Price/mo</th><th>Max Investments</th><th>Withdraw Speed</th><th>Referral %</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody id="mplans-tbody"><tr><td colspan="7" class="table-empty-msg">Loading&#8230;</td></tr></tbody>
      </table>
    </div>
  </section>
</div>
</main>
</div>

<!-- Membership Plan Modal -->
<div class="modal-overlay" id="modal-mplan" role="dialog" aria-modal="true">
  <div class="bottom-sheet admin-modal" style="max-width:680px">
    <div class="bottom-sheet-handle"></div>
    <div class="bottom-sheet-header">
      <h3 id="mplan-title">Membership Plan</h3>
      <button class="modal-close" type="button" onclick="closeMModal()">&#x2715;</button>
    </div>
    <div class="bottom-sheet-body">
      <input type="hidden" id="mplan-id">
      <div class="admin-form-grid">
        <div class="form-group"><label class="form-label">Name <span class="req">*</span></label><input id="mp-name" class="form-input" type="text"></div>
        <div class="form-group"><label class="form-label">Price ($/month)</label><input id="mp-price" class="form-input" type="number" min="0" step="0.01"></div>
        <div class="form-group admin-fg-full"><label class="form-label">Description</label><textarea id="mp-desc" class="form-input" rows="2"></textarea></div>
        <div class="form-group"><label class="form-label">Duration (days)</label><input id="mp-dur" class="form-input" type="number" min="1" value="30"></div>
        <div class="form-group"><label class="form-label">Max Active Investments <small>(blank=unlimited)</small></label><input id="mp-maxinv" class="form-input" type="number" min="1"></div>
        <div class="form-group"><label class="form-label">Withdrawal Speed (hours)</label><input id="mp-wsh" class="form-input" type="number" min="1" value="72"></div>
        <div class="form-group"><label class="form-label">Referral Commission (%)</label><input id="mp-rcp" class="form-input" type="number" min="0" step="0.01" value="0"></div>
        <div class="form-group"><label class="form-label">Support Tier</label>
          <select id="mp-ps" class="form-input form-select">
            <option value="standard">Standard</option>
            <option value="priority">Priority</option>
            <option value="dedicated">Dedicated</option>
            <option value="manager">Account Manager</option>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Accent Color</label><div class="color-wrap"><input type="color" id="mp-cpicker" value="#A0A0A0"><input id="mp-color" class="form-input" type="text" value="#A0A0A0"></div></div>
        <div class="form-group"><label class="form-label">Badge Icon</label><input id="mp-badge" class="form-input" type="text" placeholder="e.g. user, medal, crown, diamond"></div>
        <div class="form-group"><label class="form-label">Sort Order</label><input id="mp-sort" class="form-input" type="number" min="0" value="0"></div>
        <div class="form-group"></div>
        <div class="form-group"><label class="chk-label"><input type="checkbox" id="mp-analytics"> Has Analytics</label></div>
        <div class="form-group"><label class="chk-label"><input type="checkbox" id="mp-strategy"> Strategy Reports</label></div>
        <div class="form-group"><label class="chk-label"><input type="checkbox" id="mp-elite"> Access Elite Plans</label></div>
        <div class="form-group"><label class="chk-label"><input type="checkbox" id="mp-pools"> Invitation Pools</label></div>
      </div>
      <div class="admin-form-actions">
        <button class="btn btn-ghost" onclick="closeMModal()">Cancel</button>
        <button class="btn btn-primary" id="save-mplan-btn">Save Plan</button>
      </div>
    </div>
  </div>
</div>

<?php include '../../includes/admin-mobile-dock.php'; ?>

<div id="global-loader" class="loader-overlay" style="display:none"><div class="loader-inner"><img src="/assets/images/logo/avernonlogo.png" alt="" aria-hidden="true" style="height:40px;width:auto;animation:logoPulse 1.5s ease-in-out infinite;"><div class="loader-spinner"></div></div></div>
<div id="toast-container" role="status" aria-live="polite"></div>
<script src="/assets/js/main.js" defer></script>
<script>
(function(){
'use strict';
var editId=null;
function esc(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}

async function loadPlans(){
  var tb=document.getElementById('mplans-tbody');
  tb.innerHTML='<tr><td colspan="7" class="table-empty-msg">Loading&#8230;</td></tr>';
  try{
    var r=await fetch('/api/admin-dashboard/manage-plans.php?plan_type=membership',{headers:{'X-Requested-With':'XMLHttpRequest'}});
    var j=await r.json();if(!j.success)throw new Error(j.message);
    var plans=j.data.plans;
    if(!plans.length){tb.innerHTML='<tr><td colspan="7" class="table-empty-msg">No membership plans found.</td></tr>';return;}
    // Summary cards
    var summaryRow=document.getElementById('mem-summary-row');
    if(summaryRow){
      summaryRow.innerHTML=plans.map(function(p){
        return '<div class="mem-card" style="border-top:3px solid '+esc(p.color_accent)+'">'+
          '<div class="mem-card-name">'+esc(p.name)+'</div>'+
          '<div class="mem-card-price">$'+Number(p.price).toFixed(2)+'/mo</div>'+
          '<div class="mem-card-sub">'+p.duration_days+' days · '+(p.max_active_investments||'&#8734;')+' max investments</div>'+
          '</div>';
      }).join('');
    }
    tb.innerHTML=plans.map(function(p){
      var maxI=p.max_active_investments||'Unlimited';
      var act=p.is_active?'<span class="badge badge--success">Active</span>':'<span class="badge badge--default">Hidden</span>';
      return '<tr>'+
        '<td><div style="display:flex;align-items:center;gap:8px"><div style="width:10px;height:10px;border-radius:50%;background:'+esc(p.color_accent)+';flex-shrink:0"></div><strong>'+esc(p.name)+'</strong></div></td>'+
        '<td class="fmono">$'+Number(p.price).toFixed(2)+'</td>'+
        '<td>'+maxI+'</td>'+
        '<td>'+p.withdrawal_speed_hours+'h</td>'+
        '<td>'+Number(p.referral_commission_pct).toFixed(1)+'%</td>'+
        '<td>'+act+'</td>'+
        '<td><div style="display:flex;gap:6px"><button class="btn btn-xs btn-ghost" onclick=\'editMPlan('+JSON.stringify(p).replace(/</g,'\\u003c')+')\'>Edit</button>'+
        '<button class="btn btn-xs btn-ghost" onclick="toggleMPlan('+p.id+')">'+(p.is_active?'Hide':'Show')+'</button>'+
        '<button class="btn btn-xs bdr" onclick="delMPlan('+p.id+')">Del</button></div></td>'+
        '</tr>';
    }).join('');
  }catch(e){tb.innerHTML='<tr><td colspan="7" class="table-empty-msg">'+esc(e.message)+'</td></tr>';}
}

function clearMForm(){
  ['mplan-id','mp-name','mp-price','mp-desc','mp-dur','mp-maxinv','mp-wsh','mp-rcp','mp-badge','mp-sort'].forEach(function(id){var el=document.getElementById(id);if(el)el.value='';});
  document.getElementById('mp-dur').value='30';document.getElementById('mp-wsh').value='72';document.getElementById('mp-rcp').value='0';document.getElementById('mp-sort').value='0';
  document.getElementById('mp-color').value='#A0A0A0';document.getElementById('mp-cpicker').value='#A0A0A0';
  document.getElementById('mp-ps').value='standard';
  ['mp-analytics','mp-strategy','mp-elite','mp-pools'].forEach(function(id){var el=document.getElementById(id);if(el)el.checked=false;});
  document.getElementById('mplan-title').textContent='New Membership Plan';
  document.getElementById('save-mplan-btn').textContent='Create Plan';
  editId=null;
}

window.editMPlan=function(p){
  editId=p.id;
  document.getElementById('mplan-id').value=p.id;document.getElementById('mp-name').value=p.name||'';
  document.getElementById('mp-price').value=p.price||'';document.getElementById('mp-desc').value=p.description||'';
  document.getElementById('mp-dur').value=p.duration_days||30;document.getElementById('mp-maxinv').value=p.max_active_investments||'';
  document.getElementById('mp-wsh').value=p.withdrawal_speed_hours||72;document.getElementById('mp-rcp').value=p.referral_commission_pct||0;
  document.getElementById('mp-ps').value=p.priority_support||'standard';
  document.getElementById('mp-color').value=p.color_accent||'#A0A0A0';document.getElementById('mp-cpicker').value=p.color_accent||'#A0A0A0';
  document.getElementById('mp-badge').value=p.badge_icon||'';document.getElementById('mp-sort').value=p.sort_order||0;
  document.getElementById('mp-analytics').checked=!!p.has_analytics;document.getElementById('mp-strategy').checked=!!p.has_strategy_reports;
  document.getElementById('mp-elite').checked=!!p.access_elite_plans;document.getElementById('mp-pools').checked=!!p.invitation_pools;
  document.getElementById('mplan-title').textContent='Edit Membership Plan';
  document.getElementById('save-mplan-btn').textContent='Save Changes';
  document.getElementById('modal-mplan').classList.add('active');
};

window.closeMModal=function(){document.getElementById('modal-mplan').classList.remove('active');clearMForm();};

window.toggleMPlan=async function(id){
  try{var r=await apiRequest('/api/admin-dashboard/manage-plans.php','POST',{action:'toggle_membership',plan_id:id});showToast(r.message,'success');loadPlans();}catch(e){}
};
window.delMPlan=async function(id){
  if(!confirm('Delete this membership plan?'))return;
  try{var r=await apiRequest('/api/admin-dashboard/manage-plans.php','POST',{action:'delete_membership',plan_id:id});showToast(r.message,'success');loadPlans();}catch(e){}
};

document.getElementById('save-mplan-btn').addEventListener('click',async function(){
  var body={
    action:editId?'update_membership':'create_membership',
    name:document.getElementById('mp-name').value.trim(),
    description:document.getElementById('mp-desc').value.trim(),
    price:document.getElementById('mp-price').value,
    duration_days:document.getElementById('mp-dur').value,
    max_active_investments:document.getElementById('mp-maxinv').value,
    withdrawal_speed_hours:document.getElementById('mp-wsh').value,
    referral_commission_pct:document.getElementById('mp-rcp').value,
    priority_support:document.getElementById('mp-ps').value,
    color_accent:document.getElementById('mp-color').value.trim(),
    badge_icon:document.getElementById('mp-badge').value.trim(),
    sort_order:document.getElementById('mp-sort').value,
    has_analytics:document.getElementById('mp-analytics').checked?1:0,
    has_strategy_reports:document.getElementById('mp-strategy').checked?1:0,
    access_elite_plans:document.getElementById('mp-elite').checked?1:0,
    invitation_pools:document.getElementById('mp-pools').checked?1:0,
  };
  if(editId)body.plan_id=editId;
  try{var r=await apiRequest('/api/admin-dashboard/manage-plans.php','POST',body);showToast(r.message,'success');closeMModal();loadPlans();}catch(e){}
});

document.getElementById('create-plan-btn').addEventListener('click',function(){clearMForm();document.getElementById('modal-mplan').classList.add('active');});
document.getElementById('modal-mplan').addEventListener('click',function(e){if(e.target===this)closeMModal();});
document.getElementById('mp-cpicker').addEventListener('input',function(){document.getElementById('mp-color').value=this.value;});
document.getElementById('mp-color').addEventListener('input',function(){if(/^#[0-9A-Fa-f]{6}$/.test(this.value))document.getElementById('mp-cpicker').value=this.value;});

var st=document.getElementById('sidebar-toggle'),sb=document.getElementById('dashboard-sidebar'),ov=document.getElementById('sidebar-overlay');
if(st&&sb){st.addEventListener('click',function(){sb.classList.toggle('open');if(ov)ov.classList.toggle('active');});if(ov)ov.addEventListener('click',function(){sb.classList.remove('open');ov.classList.remove('active');});}
var ub=document.getElementById('topbar-user-btn'),ud=document.getElementById('user-dropdown');
if(ub){ub.addEventListener('click',function(e){e.stopPropagation();ud.classList.toggle('open');});document.addEventListener('click',function(){ud.classList.remove('open');});}
document.addEventListener('click',function(e){var el=e.target.closest('[data-logout]');if(el){e.preventDefault();fetch(el.dataset.logout).finally(function(){window.location.href='/pages/public/login.php';});}});
loadPlans();
})();
</script>
<style>
.mem-summary-row{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:var(--space-4);margin-bottom:var(--space-5);}
.mem-card{background:var(--bg-elevated);border:1px solid var(--border-color);border-radius:var(--radius-lg);padding:var(--space-4);}
.mem-card-name{font-weight:700;font-size:var(--text-sm);color:var(--text-primary);margin-bottom:4px;}
.mem-card-price{font-size:var(--text-xl);font-weight:700;color:var(--text-primary);}
.mem-card-sub{font-size:var(--text-xs);color:var(--text-muted);margin-top:4px;}
.admin-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);}
.admin-fg-full{grid-column:1/-1;}
.admin-form-actions{display:flex;gap:var(--space-3);justify-content:flex-end;margin-top:var(--space-5);padding-top:var(--space-4);border-top:1px solid var(--border-color);}
.chk-label{display:flex;align-items:center;gap:var(--space-2);font-size:var(--text-sm);font-weight:500;cursor:pointer;}
.chk-label input[type=checkbox]{width:16px;height:16px;accent-color:var(--color-primary);}
.color-wrap{display:flex;gap:var(--space-2);align-items:center;}
.color-wrap input[type=color]{width:38px;height:38px;border:1px solid var(--border-color);border-radius:var(--radius-md);padding:2px;cursor:pointer;}
.color-wrap .form-input{flex:1;}
.fmono{font-family:var(--font-mono);font-size:var(--text-sm);}
.bdr{background:none;border:1px solid var(--color-danger);color:var(--color-danger);cursor:pointer;padding:3px 10px;font-size:var(--text-xs);border-radius:var(--radius-md);font-family:var(--font-sans);}
.bdr:hover{background:var(--color-danger);color:#fff;}
.req{color:var(--color-danger);}
@media(max-width:600px){.admin-form-grid{grid-template-columns:1fr;}.admin-fg-full{grid-column:auto;}}
</style>
</body>
</html>
