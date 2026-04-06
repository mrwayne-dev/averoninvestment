<?php
/* =====================================================================
   pages/admin/investments.php  — Investment plans CRUD
   ===================================================================== */
require_once '../../includes/admin-auth-guard.php';
require_once '../../includes/icons.php';
$pageTitle = 'Investment Plans — Admin';
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
    <button class="sidebar-toggle" id="sidebar-toggle" type="button" aria-label="Toggle"><?= ph('list', 20) ?></button>
    <h1 class="topbar-title">Investment Plans</h1>
  </div>
  <div class="dashboard-topbar-right">
    <button class="btn btn-primary btn-sm" id="create-plan-btn" type="button"><?= ph('plus', 16) ?> New Plan</button>
    <div class="topbar-user-wrap">
      <button class="topbar-user-btn" id="topbar-user-btn" type="button" aria-haspopup="true">
        <span class="topbar-avatar"><?= mb_strtoupper(mb_substr($authUserName,0,1,'UTF-8'),'UTF-8') ?></span>
        <?= ph('caret-down', 14) ?>
      </button>
      <div class="topbar-dropdown" id="user-dropdown" role="menu">
        <button type="button" class="topbar-dropdown-item topbar-dropdown-item--danger" data-logout="/api/auth/admin-logout.php"><?= ph('sign-out',16) ?> Sign Out</button>
      </div>
    </div>
  </div>
</header>

<div class="dashboard-content">
  <section class="dashboard-card">
    <div class="dashboard-card-header">
      <h2 class="dashboard-card-title">All Investment Plans</h2>
    </div>
    <div class="admin-table-wrapper">
      <table class="admin-table">
        <thead><tr><th>Name</th><th>Min / Max</th><th>Duration</th><th>Daily Yield</th><th>Total Yield</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody id="plans-tbody"><tr><td colspan="8" class="table-empty-msg">Loading&#8230;</td></tr></tbody>
      </table>
    </div>
  </section>
</div>
</main>
</div>

<!-- Plan Modal -->
<div class="modal-overlay" id="modal-plan" role="dialog" aria-modal="true">
  <div class="bottom-sheet admin-modal" style="max-width:700px">
    <div class="bottom-sheet-handle"></div>
    <div class="bottom-sheet-header">
      <h3 id="plan-modal-title">Investment Plan</h3>
      <button class="modal-close" type="button" onclick="closePlanModal()">&#x2715;</button>
    </div>
    <div class="bottom-sheet-body">
      <input type="hidden" id="plan-id">
      <div class="admin-form-grid">
        <div class="form-group"><label class="form-label">Name <span class="req">*</span></label><input id="plan-name" class="form-input" type="text"></div>
        <div class="form-group"><label class="form-label">Badge Label</label><input id="plan-badge" class="form-input" type="text"></div>
        <div class="form-group admin-fg-full"><label class="form-label">Description</label><textarea id="plan-desc" class="form-input" rows="2"></textarea></div>
        <div class="form-group"><label class="form-label">Accent Color</label><div class="color-wrap"><input type="color" id="plan-cpicker" value="#2196F3"><input id="plan-color" class="form-input" type="text" value="#2196F3"></div></div>
        <div class="form-group"><label class="form-label">Min Amount ($)</label><input id="plan-min" class="form-input" type="number" min="0" step="0.01"></div>
        <div class="form-group"><label class="form-label">Max Amount ($) <small>(blank=unlimited)</small></label><input id="plan-max" class="form-input" type="number" min="0" step="0.01"></div>
        <div class="form-group"><label class="form-label">Duration (days)</label><input id="plan-duration" class="form-input" type="number" min="1"></div>
        <div class="form-group"><label class="form-label">Profit Withdraw After (days)</label><input id="plan-pwa" class="form-input" type="number" min="0"></div>
        <div class="form-group"><label class="form-label">Daily Yield Min (%)</label><input id="plan-dymin" class="form-input" type="number" min="0" step="0.01"></div>
        <div class="form-group"><label class="form-label">Daily Yield Max (%)</label><input id="plan-dymax" class="form-input" type="number" min="0" step="0.01"></div>
        <div class="form-group"><label class="form-label">Total Yield Min (%)</label><input id="plan-tymin" class="form-input" type="number" min="0" step="0.01"></div>
        <div class="form-group"><label class="form-label">Total Yield Max (%)</label><input id="plan-tymax" class="form-input" type="number" min="0" step="0.01"></div>
        <div class="form-group"><label class="form-label">Compounding</label><select id="plan-comp" class="form-input form-select"><option value="simple">Simple</option><option value="compound">Compound</option></select></div>
        <div class="form-group"><label class="form-label">Sort Order</label><input id="plan-sort" class="form-input" type="number" min="0" value="0"></div>
        <div class="form-group"><label class="chk-label"><input type="checkbox" id="plan-locked" checked> Capital Locked</label></div>
        <div class="form-group"><label class="chk-label"><input type="checkbox" id="plan-mgr"> Dedicated Manager</label></div>
      </div>
      <div class="admin-form-actions">
        <button class="btn btn-ghost" onclick="closePlanModal()">Cancel</button>
        <button class="btn btn-primary" id="save-plan-btn">Save Plan</button>
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
function fmt(n){return '$'+Number(n||0).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});}

async function loadPlans(){
  var tb=document.getElementById('plans-tbody');
  tb.innerHTML='<tr><td colspan="8" class="table-empty-msg">Loading&#8230;</td></tr>';
  try{
    var r=await fetch('/api/admin-dashboard/manage-plans.php?plan_type=investment',{headers:{'X-Requested-With':'XMLHttpRequest'}});
    var j=await r.json();if(!j.success)throw new Error(j.message);
    var plans=j.data.plans;
    if(!plans.length){tb.innerHTML='<tr><td colspan="8" class="table-empty-msg">No plans found.</td></tr>';return;}
    tb.innerHTML=plans.map(function(p){
      var mx=p.max_amount?fmt(p.max_amount):'&#8734;';
      var act=p.is_active?'<span class="badge badge--success">Active</span>':'<span class="badge badge--default">Hidden</span>';
      return '<tr>'+
        '<td><div style="display:flex;align-items:center;gap:8px"><div style="width:10px;height:10px;border-radius:50%;background:'+esc(p.color_accent)+';flex-shrink:0"></div><strong>'+esc(p.name)+'</strong>'+(p.badge_label?' <span class="badge badge--default">'+esc(p.badge_label)+'</span>':'')+'</div></td>'+
        '<td class="fmono">'+fmt(p.min_amount)+' / '+mx+'</td>'+
        '<td>'+p.duration_days+'d</td>'+
        '<td>'+p.daily_yield_min+'%&ndash;'+p.daily_yield_max+'%</td>'+
        '<td>'+p.total_yield_min+'%&ndash;'+p.total_yield_max+'%</td>'+
        '<td><span class="badge badge--default">'+esc(p.compounding_type)+'</span></td>'+
        '<td>'+act+'</td>'+
        '<td><div style="display:flex;gap:6px"><button class="btn btn-xs btn-ghost" onclick=\'editPlan('+JSON.stringify(p).replace(/</g,'\\u003c')+')\'>Edit</button>'+
        '<button class="btn btn-xs btn-ghost" onclick="togglePlan('+p.id+')">'+(p.is_active?'Hide':'Show')+'</button>'+
        '<button class="btn btn-xs bdr" onclick="delPlan('+p.id+')">Del</button></div></td>'+
        '</tr>';
    }).join('');
  }catch(e){tb.innerHTML='<tr><td colspan="8" class="table-empty-msg">'+esc(e.message)+'</td></tr>';}
}

function clearForm(){
  ['plan-id','plan-name','plan-badge','plan-desc','plan-min','plan-max','plan-duration','plan-pwa','plan-dymin','plan-dymax','plan-tymin','plan-tymax','plan-sort'].forEach(function(id){var el=document.getElementById(id);if(el)el.value='';});
  document.getElementById('plan-color').value='#2196F3';document.getElementById('plan-cpicker').value='#2196F3';
  document.getElementById('plan-comp').value='simple';document.getElementById('plan-sort').value='0';
  document.getElementById('plan-locked').checked=true;document.getElementById('plan-mgr').checked=false;
  document.getElementById('plan-modal-title').textContent='New Investment Plan';
  document.getElementById('save-plan-btn').textContent='Create Plan';
  editId=null;
}

window.editPlan=function(p){
  editId=p.id;
  document.getElementById('plan-id').value=p.id;document.getElementById('plan-name').value=p.name||'';
  document.getElementById('plan-badge').value=p.badge_label||'';document.getElementById('plan-desc').value=p.description||'';
  document.getElementById('plan-color').value=p.color_accent||'#2196F3';document.getElementById('plan-cpicker').value=p.color_accent||'#2196F3';
  document.getElementById('plan-min').value=p.min_amount||'';document.getElementById('plan-max').value=p.max_amount||'';
  document.getElementById('plan-duration').value=p.duration_days||'';document.getElementById('plan-pwa').value=p.profit_withdrawal_after_days||'';
  document.getElementById('plan-dymin').value=p.daily_yield_min||'';document.getElementById('plan-dymax').value=p.daily_yield_max||'';
  document.getElementById('plan-tymin').value=p.total_yield_min||'';document.getElementById('plan-tymax').value=p.total_yield_max||'';
  document.getElementById('plan-comp').value=p.compounding_type||'simple';document.getElementById('plan-sort').value=p.sort_order||0;
  document.getElementById('plan-locked').checked=!!p.capital_locked;document.getElementById('plan-mgr').checked=!!p.dedicated_manager;
  document.getElementById('plan-modal-title').textContent='Edit Investment Plan';
  document.getElementById('save-plan-btn').textContent='Save Changes';
  document.getElementById('modal-plan').classList.add('active');
};

window.closePlanModal=function(){document.getElementById('modal-plan').classList.remove('active');clearForm();};

window.togglePlan=async function(id){
  try{var r=await apiRequest('/api/admin-dashboard/manage-plans.php','POST',{action:'toggle_investment',plan_id:id});showToast(r.message,'success');loadPlans();}catch(e){}
};
window.delPlan=async function(id){
  if(!confirm('Delete this plan?'))return;
  try{var r=await apiRequest('/api/admin-dashboard/manage-plans.php','POST',{action:'delete_investment',plan_id:id});showToast(r.message,'success');loadPlans();}catch(e){}
};

document.getElementById('save-plan-btn').addEventListener('click',async function(){
  var body={
    action:editId?'update_investment':'create_investment',
    name:document.getElementById('plan-name').value.trim(),
    badge_label:document.getElementById('plan-badge').value.trim(),
    description:document.getElementById('plan-desc').value.trim(),
    color_accent:document.getElementById('plan-color').value.trim(),
    min_amount:document.getElementById('plan-min').value,
    max_amount:document.getElementById('plan-max').value,
    duration_days:document.getElementById('plan-duration').value,
    profit_withdrawal_after_days:document.getElementById('plan-pwa').value,
    daily_yield_min:document.getElementById('plan-dymin').value,
    daily_yield_max:document.getElementById('plan-dymax').value,
    total_yield_min:document.getElementById('plan-tymin').value,
    total_yield_max:document.getElementById('plan-tymax').value,
    compounding_type:document.getElementById('plan-comp').value,
    sort_order:document.getElementById('plan-sort').value,
    capital_locked:document.getElementById('plan-locked').checked?1:0,
    dedicated_manager:document.getElementById('plan-mgr').checked?1:0,
  };
  if(editId)body.plan_id=editId;
  try{var r=await apiRequest('/api/admin-dashboard/manage-plans.php','POST',body);showToast(r.message,'success');closePlanModal();loadPlans();}catch(e){}
});

document.getElementById('create-plan-btn').addEventListener('click',function(){clearForm();document.getElementById('modal-plan').classList.add('active');});
document.getElementById('modal-plan').addEventListener('click',function(e){if(e.target===this)closePlanModal();});
document.getElementById('plan-cpicker').addEventListener('input',function(){document.getElementById('plan-color').value=this.value;});
document.getElementById('plan-color').addEventListener('input',function(){if(/^#[0-9A-Fa-f]{6}$/.test(this.value))document.getElementById('plan-cpicker').value=this.value;});

var st=document.getElementById('sidebar-toggle'),sb=document.getElementById('dashboard-sidebar'),ov=document.getElementById('sidebar-overlay');
if(st&&sb){st.addEventListener('click',function(){sb.classList.toggle('open');if(ov)ov.classList.toggle('active');});if(ov)ov.addEventListener('click',function(){sb.classList.remove('open');ov.classList.remove('active');});}
var ub=document.getElementById('topbar-user-btn'),ud=document.getElementById('user-dropdown');
if(ub){ub.addEventListener('click',function(e){e.stopPropagation();ud.classList.toggle('open');});document.addEventListener('click',function(){ud.classList.remove('open');});}
document.addEventListener('click',function(e){var el=e.target.closest('[data-logout]');if(el){e.preventDefault();fetch(el.dataset.logout).finally(function(){window.location.href='/pages/public/login.php';});}});
loadPlans();
})();
</script>
<style>
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
