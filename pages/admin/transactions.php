<?php
/* =====================================================================
   pages/admin/transactions.php
   Full transaction history — filters, approve/reject actions,
   CSV export.
   ===================================================================== */
require_once '../../includes/admin-auth-guard.php';
require_once '../../includes/icons.php';
$pageTitle = 'Transactions — Admin';
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
    <h1 class="topbar-title">Transactions</h1>
  </div>
  <div class="dashboard-topbar-right">
    <button class="btn btn-ghost btn-sm" id="export-csv-btn" type="button"><?= ph('download-simple',16) ?> Export CSV</button>
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

  <!-- Filter bar -->
  <div class="admin-filter-bar">
    <div class="admin-search-wrap">
      <span class="admin-search-icon"><?= ph('magnifying-glass',16) ?></span>
      <input type="text" id="tx-search" class="admin-search-input" placeholder="Search user, reference&#8230;" autocomplete="off">
    </div>
    <select id="tx-type-filter" class="admin-select">
      <option value="">All Types</option>
      <option value="deposit">Deposit</option>
      <option value="withdrawal">Withdrawal</option>
      <option value="profit">Profit</option>
      <option value="referral_bonus">Referral Bonus</option>
      <option value="membership_fee">Membership Fee</option>
    </select>
    <select id="tx-status-filter" class="admin-select">
      <option value="">All Statuses</option>
      <option value="pending">Pending</option>
      <option value="confirmed">Confirmed</option>
      <option value="completed">Completed</option>
      <option value="processing">Processing</option>
      <option value="failed">Failed</option>
    </select>
    <input type="date" id="tx-from" class="admin-date-input">
    <input type="date" id="tx-to"   class="admin-date-input">
    <button class="btn btn-ghost btn-sm" id="tx-filter-reset">Reset</button>
  </div>

  <!-- Pending actions banner (populated by JS) -->
  <div id="pending-banner" class="pending-banner hidden"></div>

  <!-- Transactions table -->
  <section class="dashboard-card">
    <div class="dashboard-card-header">
      <h2 class="dashboard-card-title">All Transactions</h2>
      <span class="admin-count-badge" id="tx-total-count">— transactions</span>
    </div>
    <div class="admin-table-wrapper">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>User</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Reference</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="tx-tbody">
          <tr><td colspan="8" class="table-empty-msg">Loading&#8230;</td></tr>
        </tbody>
      </table>
    </div>
    <div class="admin-pagination" id="tx-pagination"></div>
  </section>

</div>
</main>
</div>

<?php include '../../includes/admin-mobile-dock.php'; ?>

<div id="global-loader" class="loader-overlay" style="display:none"><div class="loader-inner"><img src="/assets/images/logo/avernonlogo.png" alt="" aria-hidden="true" style="height:40px;width:auto;animation:logoPulse 1.5s ease-in-out infinite;"><div class="loader-spinner"></div></div></div>
<div id="toast-container" role="status" aria-live="polite"></div>
<script src="/assets/js/main.js" defer></script>
<script>
(function(){
'use strict';
var currentPage=1;
var searchTO;

function esc(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
function fmt(n){return '$'+Number(n||0).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});}
function fmtDate(s){return s?new Date(s).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric',hour:'2-digit',minute:'2-digit'}):'—';}

function statusBadge(s){
  var m={confirmed:'success',completed:'success',pending:'warning',failed:'danger',processing:'info'};
  return '<span class="badge badge--'+(m[s]||'default')+'">'+esc(s)+'</span>';
}
function typeBadge(t){
  var icons={deposit:'&#8595;',withdrawal:'&#8593;',profit:'+',referral_bonus:'&#9733;',membership_fee:'&#9670;'};
  return '<span class="tx-chip tx-chip--'+t.replace('_','-')+'">'+(icons[t]||'')+'&nbsp;'+esc(t.replace(/_/g,' '))+'</span>';
}

async function loadTx(page){
  page=page||1;
  var search=document.getElementById('tx-search').value.trim();
  var type=document.getElementById('tx-type-filter').value;
  var status=document.getElementById('tx-status-filter').value;
  var from=document.getElementById('tx-from').value;
  var to=document.getElementById('tx-to').value;
  var params=new URLSearchParams({page:page,search:search,type:type,status:status,from:from,to:to});
  var tb=document.getElementById('tx-tbody');
  tb.innerHTML='<tr><td colspan="8" class="table-empty-msg">Loading&#8230;</td></tr>';
  try{
    var r=await fetch('/api/admin-dashboard/manage-transaction.php?'+params.toString(),{headers:{'X-Requested-With':'XMLHttpRequest'}});
    var j=await r.json();if(!j.success)throw new Error(j.message);
    var data=j.data;
    currentPage=page;
    document.getElementById('tx-total-count').textContent=data.total+' transaction'+(data.total!==1?'s':'');
    if(!data.transactions.length){
      tb.innerHTML='<tr><td colspan="8" class="table-empty-msg">No transactions found</td></tr>';
      renderPagination(0,1,1);
      return;
    }
    // Count pending for banner
    var pending=data.transactions.filter(function(t){return t.status==='pending';});
    var banner=document.getElementById('pending-banner');
    if(pending.length&&(!status||status==='pending')){
      banner.classList.remove('hidden');
      banner.innerHTML='<strong>'+pending.length+' pending transaction'+(pending.length!==1?'s':'')+' need your attention.</strong> Review and approve or reject below.';
    }else{banner.classList.add('hidden');}

    tb.innerHTML=data.transactions.map(function(tx){
      var actionBtns='';
      if(tx.status==='pending'&&tx.type==='deposit'){
        actionBtns='<button class="btn btn-xs btn-primary" onclick="doTxAction('+tx.id+',\'approve_deposit\')">Approve</button>'+
                   '<button class="btn btn-xs bdr" onclick="doTxAction('+tx.id+',\'reject_deposit\')">Reject</button>';
      }else if(tx.status==='pending'&&tx.type==='withdrawal'){
        actionBtns='<button class="btn btn-xs btn-primary" onclick="doTxAction('+tx.id+',\'approve_withdrawal\')">Approve</button>'+
                   '<button class="btn btn-xs bdr" onclick="doTxAction('+tx.id+',\'reject_withdrawal\')">Reject</button>';
      }else if(tx.status==='confirmed'||tx.status==='completed'){
        actionBtns='<span class="tx-done-label">&#10003;&nbsp;Done</span>';
      }else{
        actionBtns='<span class="tx-dash-label">&mdash;</span>';
      }
      var name=esc((tx.name||'').trim()||tx.email||'—');
      return '<tr class="'+(tx.status==='pending'?'tx-row-pending':'')+'">'+
        '<td class="text-muted">#'+tx.id+'</td>'+
        '<td><div class="ucell"><div class="uav">'+((tx.name||'U')[0].toUpperCase())+'</div>'+
        '<div><div class="uname">'+name+'</div><div class="uemail">'+esc(tx.email||'')+'</div></div></div></td>'+
        '<td>'+typeBadge(tx.type)+'</td>'+
        '<td class="fmono fw6">'+fmt(tx.amount)+'</td>'+
        '<td>'+statusBadge(tx.status)+'</td>'+
        '<td class="text-muted ref-cell">'+(tx.reference?esc(tx.reference.slice(0,16))+'&#8230;':'—')+'</td>'+
        '<td class="text-muted">'+fmtDate(tx.created_at)+'</td>'+
        '<td><div style="display:flex;gap:6px;flex-wrap:wrap">'+actionBtns+'</div></td>'+
        '</tr>';
    }).join('');
    renderPagination(data.total,page,data.pages);
  }catch(e){
    tb.innerHTML='<tr><td colspan="8" class="table-empty-msg">Error: '+esc(e.message)+'</td></tr>';
  }
}

function renderPagination(total,page,pages){
  var wrap=document.getElementById('tx-pagination');
  if(!wrap)return;
  if(pages<=1){wrap.innerHTML='';return;}
  var html='';
  if(page>1)html+='<button class="admin-page-btn" onclick="loadTx('+(page-1)+')">\u2039 Prev</button>';
  var start=Math.max(1,page-2),end=Math.min(pages,page+2);
  for(var i=start;i<=end;i++){html+='<button class="admin-page-btn'+(i===page?' active':'') +'" onclick="loadTx('+i+')">'+i+'</button>';}
  if(page<pages)html+='<button class="admin-page-btn" onclick="loadTx('+(page+1)+')">Next \u203a</button>';
  wrap.innerHTML=html;
}

window.doTxAction=async function(txId,action){
  var label=action.replace(/_/g,' ');
  if(!confirm('Are you sure you want to '+label+' transaction #'+txId+'?'))return;
  try{
    var r=await apiRequest('/api/admin-dashboard/manage-transaction.php','POST',{transaction_id:txId,action:action});
    showToast(r.message,'success');
    loadTx(currentPage);
  }catch(e){}
};

// CSV export
document.getElementById('export-csv-btn').addEventListener('click',async function(){
  var search=document.getElementById('tx-search').value.trim();
  var type=document.getElementById('tx-type-filter').value;
  var status=document.getElementById('tx-status-filter').value;
  var from=document.getElementById('tx-from').value;
  var to=document.getElementById('tx-to').value;
  try{
    showLoader();
    var r=await fetch('/api/admin-dashboard/manage-transaction.php?page=1&limit=9999&search='+encodeURIComponent(search)+'&type='+type+'&status='+status+'&from='+from+'&to='+to,{headers:{'X-Requested-With':'XMLHttpRequest'}});
    var j=await r.json();
    if(!j.success)throw new Error(j.message);
    var rows=j.data.transactions;
    var header=['ID','Name','Email','Type','Amount','Status','Reference','Date'];
    var csv=header.join(',')+'\n'+rows.map(function(tx){
      return [tx.id,'"'+(tx.name||'').replace(/"/g,'""')+'"','"'+(tx.email||'').replace(/"/g,'""')+'"',tx.type,tx.amount,tx.status,'"'+(tx.reference||'').replace(/"/g,'""')+'"',tx.created_at].join(',');
    }).join('\n');
    var blob=new Blob([csv],{type:'text/csv'});
    var url=URL.createObjectURL(blob);
    var a=document.createElement('a');a.href=url;a.download='transactions-'+new Date().toISOString().slice(0,10)+'.csv';a.click();
    URL.revokeObjectURL(url);
    showToast('CSV exported successfully','success');
  }catch(e){showToast('Export failed: '+e.message,'error');}finally{hideLoader();}
});

// Filters
document.getElementById('tx-search').addEventListener('input',function(){clearTimeout(searchTO);searchTO=setTimeout(function(){loadTx(1);},400);});
['tx-type-filter','tx-status-filter','tx-from','tx-to'].forEach(function(id){document.getElementById(id).addEventListener('change',function(){loadTx(1);});});
document.getElementById('tx-filter-reset').addEventListener('click',function(){
  ['tx-search','tx-from','tx-to'].forEach(function(id){document.getElementById(id).value='';});
  ['tx-type-filter','tx-status-filter'].forEach(function(id){document.getElementById(id).value='';});
  loadTx(1);
});

// Sidebar
var st=document.getElementById('sidebar-toggle'),sb=document.getElementById('dashboard-sidebar'),ov=document.getElementById('sidebar-overlay');
if(st&&sb){st.addEventListener('click',function(){sb.classList.toggle('open');if(ov)ov.classList.toggle('active');});if(ov)ov.addEventListener('click',function(){sb.classList.remove('open');ov.classList.remove('active');});}
var ub=document.getElementById('topbar-user-btn'),ud=document.getElementById('user-dropdown');
if(ub){ub.addEventListener('click',function(e){e.stopPropagation();ud.classList.toggle('open');});document.addEventListener('click',function(){ud.classList.remove('open');});}
document.addEventListener('click',function(e){var el=e.target.closest('[data-logout]');if(el){e.preventDefault();fetch(el.dataset.logout).finally(function(){window.location.href='/pages/public/login.php';});}});

document.addEventListener('DOMContentLoaded', function(){ loadTx(1); });
})();
</script>

<style>
.admin-filter-bar{display:flex;gap:var(--space-3);margin-bottom:var(--space-5);flex-wrap:wrap;align-items:center;}
.admin-search-wrap{position:relative;flex:1;min-width:200px;}
.admin-search-icon{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none;display:flex;align-items:center;justify-content:center;line-height:0;width:16px;height:16px;}
.admin-search-icon svg{display:block;}
.admin-search-input{width:100%;padding:var(--space-2) var(--space-3) var(--space-2) 36px;border:1px solid var(--border-color);border-radius:var(--radius-md);font-size:var(--text-sm);font-family:var(--font-sans);background:var(--bg-elevated);color:var(--text-primary);outline:none;transition:border-color var(--transition-fast);}
.admin-search-input:focus{border-color:var(--color-primary);}
.tx-done-label{font-size:var(--text-xs);color:var(--color-success);font-weight:600;}
.tx-dash-label{font-size:var(--text-sm);color:var(--text-muted);}
.admin-select,.admin-date-input{padding:var(--space-2) var(--space-3);border:1px solid var(--border-color);border-radius:var(--radius-md);font-size:var(--text-sm);font-family:var(--font-sans);background:var(--bg-elevated);color:var(--text-primary);cursor:pointer;outline:none;}
.admin-count-badge{font-size:var(--text-sm);color:var(--text-muted);background:var(--bg-muted);padding:3px 10px;border-radius:var(--radius-full);}
.admin-pagination{display:flex;gap:var(--space-2);justify-content:center;padding:var(--space-4) 0 var(--space-2);flex-wrap:wrap;}
.admin-page-btn{padding:var(--space-2) var(--space-3);border:1px solid var(--border-color);border-radius:var(--radius-md);background:var(--bg-elevated);color:var(--text-secondary);font-size:var(--text-sm);cursor:pointer;font-family:var(--font-sans);transition:all var(--transition-fast);}
.admin-page-btn:hover,.admin-page-btn.active{border-color:var(--color-primary);color:var(--color-primary);background:var(--color-primary-light);}
.pending-banner{padding:var(--space-3) var(--space-4);background:#FFF8E1;border:1px solid #FFD740;border-radius:var(--radius-md);font-size:var(--text-sm);color:#7A5F00;margin-bottom:var(--space-4);}
.hidden{display:none!important;}
.tx-row-pending{background:rgba(245,166,35,0.04);}
.tx-chip{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:var(--radius-full);font-size:var(--text-xs);font-weight:600;text-transform:capitalize;}
.tx-chip--deposit{background:#E8F0FF;color:#3E6AE1;}
.tx-chip--withdrawal{background:#FFF0F0;color:#D32F2F;}
.tx-chip--profit{background:#E8F5E9;color:#2E7D32;}
.tx-chip--referral-bonus{background:#FFF8E1;color:#C05300;}
.tx-chip--membership-fee{background:#F3E5F5;color:#7B1FA2;}
.ucell{display:flex;align-items:center;gap:var(--space-2);}
.uav{width:28px;height:28px;border-radius:50%;background:var(--color-primary-light);color:var(--color-primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:var(--text-xs);flex-shrink:0;}
.uname{font-size:var(--text-sm);font-weight:600;}
.uemail{font-size:var(--text-xs);color:var(--text-muted);}
.ref-cell{font-family:var(--font-mono);font-size:var(--text-xs);max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.fmono{font-family:var(--font-mono);font-size:var(--text-sm);}
.fw6{font-weight:600;}
.text-muted{color:var(--text-muted);font-size:var(--text-sm);}
.btn-xs{padding:3px 10px;font-size:var(--text-xs);}
.bdr{background:none;border:1px solid var(--color-danger);color:var(--color-danger);cursor:pointer;padding:3px 10px;font-size:var(--text-xs);border-radius:var(--radius-md);font-family:var(--font-sans);transition:all var(--transition-fast);}
.bdr:hover{background:var(--color-danger);color:#fff;}
</style>
</body>
</html>
