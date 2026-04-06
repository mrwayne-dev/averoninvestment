<?php
/* =====================================================================
   pages/admin/statistics.php
   Date-range metrics, charts, top investors, breakdown tables.
   ===================================================================== */
require_once '../../includes/admin-auth-guard.php';
require_once '../../includes/icons.php';
$pageTitle = 'Statistics — Admin';
$extraCss  = '<link rel="stylesheet" href="/assets/css/dashboard.css"><link rel="stylesheet" href="/assets/css/dashboard-responsive.css">';
$isAdmin   = true;
$today     = date('Y-m-d');
$monthAgo  = date('Y-m-d', strtotime('-30 days'));
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
    <h1 class="topbar-title">Statistics</h1>
  </div>
  <div class="dashboard-topbar-right">
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

  <!-- Date range picker -->
  <div class="stats-range-bar">
    <div class="admin-period-tabs" role="tablist">
      <button class="admin-period-tab" data-period="today">Today</button>
      <button class="admin-period-tab" data-period="7d">7 Days</button>
      <button class="admin-period-tab active" data-period="30d">30 Days</button>
      <button class="admin-period-tab" data-period="90d">90 Days</button>
      <button class="admin-period-tab" data-period="custom">Custom</button>
    </div>
    <div class="stats-custom-range hidden" id="custom-range-wrap">
      <input type="date" id="range-from" class="admin-date-input" value="<?= $monthAgo ?>">
      <span class="text-muted">to</span>
      <input type="date" id="range-to"   class="admin-date-input" value="<?= $today ?>">
      <button class="btn btn-primary btn-sm" id="apply-range-btn">Apply</button>
    </div>
  </div>

  <!-- Summary cards -->
  <section class="stats-grid admin-stats-grid" id="stats-cards">
    <article class="stat-overview-card stat-overview-card--primary">
      <div class="stat-overview-header"><span class="stat-overview-label">Revenue</span><div class="stat-overview-icon"><?= ph('currency-dollar',18) ?></div></div>
      <div class="stat-overview-value" id="ss-revenue">—</div>
      <div class="stat-overview-sub">Net (deposits − withdrawals − profits)</div>
    </article>
    <article class="stat-overview-card">
      <div class="stat-overview-header"><span class="stat-overview-label">Deposits</span><div class="stat-overview-icon"><?= ph('arrow-circle-down',18) ?></div></div>
      <div class="stat-overview-value" id="ss-deposits">—</div>
      <div class="stat-overview-sub">Confirmed in period</div>
    </article>
    <article class="stat-overview-card">
      <div class="stat-overview-header"><span class="stat-overview-label">Withdrawals</span><div class="stat-overview-icon"><?= ph('arrow-circle-up',18) ?></div></div>
      <div class="stat-overview-value" id="ss-withdrawals">—</div>
      <div class="stat-overview-sub">Completed in period</div>
    </article>
    <article class="stat-overview-card">
      <div class="stat-overview-header"><span class="stat-overview-label">Profits Paid</span><div class="stat-overview-icon"><?= ph('trend-up',18) ?></div></div>
      <div class="stat-overview-value" id="ss-profits">—</div>
      <div class="stat-overview-sub">Credited to users</div>
    </article>
    <article class="stat-overview-card">
      <div class="stat-overview-header"><span class="stat-overview-label">New Users</span><div class="stat-overview-icon"><?= ph('users',18) ?></div></div>
      <div class="stat-overview-value" id="ss-newusers">—</div>
      <div class="stat-overview-sub" id="ss-totalusers">— total</div>
    </article>
    <article class="stat-overview-card">
      <div class="stat-overview-header"><span class="stat-overview-label">New Investments</span><div class="stat-overview-icon"><?= ph('chart-line',18) ?></div></div>
      <div class="stat-overview-value" id="ss-investments">—</div>
      <div class="stat-overview-sub" id="ss-invested">— total invested</div>
    </article>
  </section>

  <!-- Chart -->
  <section class="dashboard-card" style="margin-bottom:var(--space-6)">
    <div class="dashboard-card-header">
      <h2 class="dashboard-card-title">Daily Breakdown</h2>
      <div class="admin-chart-legend">
        <span class="legend-dot legend-dot--deposits"></span>Deposits
        <span class="legend-dot legend-dot--withdrawals"></span>Withdrawals
        <span class="legend-dot legend-dot--profits"></span>Profits Paid
      </div>
    </div>
    <div style="height:300px;position:relative">
      <canvas id="stats-chart"></canvas>
    </div>
  </section>

  <!-- Two-column: Breakdown + Top investors -->
  <div class="dashboard-row" style="grid-template-columns:1fr 1fr">

    <!-- Transaction type breakdown -->
    <section class="dashboard-card">
      <div class="dashboard-card-header">
        <h2 class="dashboard-card-title">Transaction Breakdown</h2>
      </div>
      <table class="admin-table" id="breakdown-table">
        <thead><tr><th>Type</th><th>Count</th><th>Total</th></tr></thead>
        <tbody id="breakdown-tbody"><tr><td colspan="3" class="table-empty-msg">Loading&#8230;</td></tr></tbody>
      </table>
    </section>

    <!-- Top investors -->
    <section class="dashboard-card">
      <div class="dashboard-card-header">
        <h2 class="dashboard-card-title">Top Investors</h2>
      </div>
      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead><tr><th>#</th><th>Name</th><th>Invested</th><th>Balance</th></tr></thead>
          <tbody id="topinv-tbody"><tr><td colspan="4" class="table-empty-msg">Loading&#8230;</td></tr></tbody>
        </table>
      </div>
    </section>

  </div>

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
var period='30d';
var chartInst=null;
function fmt(n){return '$'+Number(n||0).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});}
function esc(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}

async function load(p,from,to){
  try{
    showLoader();
    var url='/api/admin-dashboard/get-statistics.php?period='+p;
    if(p==='custom'&&from&&to)url+='&from='+from+'&to='+to;
    var r=await fetch(url,{headers:{'X-Requested-With':'XMLHttpRequest'}});
    var j=await r.json();if(!j.success)throw new Error(j.message);
    render(j.data);
  }catch(e){showToast('Error: '+e.message,'error');}finally{hideLoader();}
}

function render(d){
  var s=d.summary;
  document.getElementById('ss-revenue').textContent=fmt(s.revenue);
  document.getElementById('ss-deposits').textContent=fmt(s.total_deposits);
  document.getElementById('ss-withdrawals').textContent=fmt(s.total_withdrawals);
  document.getElementById('ss-profits').textContent=fmt(s.total_profits);
  document.getElementById('ss-newusers').textContent=s.new_users;
  document.getElementById('ss-totalusers').textContent=s.total_users+' total';
  document.getElementById('ss-investments').textContent=s.active_investments;
  document.getElementById('ss-invested').textContent=fmt(s.total_invested)+' total';
  renderChart(d.chart);
  renderBreakdown(d.breakdown);
  renderTopInv(d.top_investors);
}

function renderChart(data){
  var canvas=document.getElementById('stats-chart');
  if(!canvas)return;
  var ctx=canvas.getContext('2d');
  function draw(){
    if(chartInst){chartInst.destroy();chartInst=null;}
    chartInst=new Chart(ctx,{
      type:'bar',
      data:{
        labels:data.map(function(r){return r.date.slice(5);}),
        datasets:[
          {label:'Deposits',data:data.map(function(r){return r.deposits;}),backgroundColor:'rgba(115,186,155,0.7)',borderRadius:3},
          {label:'Withdrawals',data:data.map(function(r){return r.withdrawals;}),backgroundColor:'rgba(196,122,43,0.65)',borderRadius:3},
          {label:'Profits',data:data.map(function(r){return r.profits;}),backgroundColor:'rgba(0,41,20,0.65)',borderRadius:3},
        ]
      },
      options:{
        responsive:true,maintainAspectRatio:false,
        interaction:{mode:'index',intersect:false},
        plugins:{legend:{display:false}},
        scales:{
          x:{grid:{display:false},ticks:{maxTicksLimit:12,font:{size:11}}},
          y:{grid:{color:'rgba(0,0,0,0.04)'},ticks:{font:{size:11},callback:function(v){return '$'+(v>=1000?(v/1000).toFixed(1)+'k':v);}}}
        }
      }
    });
  }
  if(typeof Chart==='undefined'){
    var s=document.createElement('script');s.src='https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js';s.onload=draw;document.head.appendChild(s);
  }else{draw();}
}

function renderBreakdown(items){
  var tb=document.getElementById('breakdown-tbody');
  if(!items||!items.length){tb.innerHTML='<tr><td colspan="3" class="table-empty-msg">No data</td></tr>';return;}
  tb.innerHTML=items.map(function(r){
    return '<tr><td>'+esc(r.type.replace(/_/g,' '))+'</td><td>'+r.count+'</td><td class="fmono">'+fmt(r.total)+'</td></tr>';
  }).join('');
}

function renderTopInv(items){
  var tb=document.getElementById('topinv-tbody');
  if(!items||!items.length){tb.innerHTML='<tr><td colspan="4" class="table-empty-msg">No data</td></tr>';return;}
  tb.innerHTML=items.map(function(inv,i){
    return '<tr><td class="text-muted">'+(i+1)+'</td>'+
      '<td><div style="display:flex;align-items:center;gap:8px"><div class="uav">'+((inv.name||'U')[0].toUpperCase())+'</div>'+esc(inv.name)+'</div></td>'+
      '<td class="fmono fw6">'+fmt(inv.invested_amount)+'</td>'+
      '<td class="fmono">'+fmt(inv.balance)+'</td>'+
      '</tr>';
  }).join('');
}

// Period tabs
document.querySelectorAll('.admin-period-tab').forEach(function(btn){
  btn.addEventListener('click',function(){
    document.querySelectorAll('.admin-period-tab').forEach(function(b){b.classList.remove('active');b.removeAttribute('aria-selected');});
    btn.classList.add('active');btn.setAttribute('aria-selected','true');
    period=btn.dataset.period;
    var customWrap=document.getElementById('custom-range-wrap');
    if(period==='custom'){customWrap.classList.remove('hidden');}
    else{customWrap.classList.add('hidden');load(period);}
  });
});
document.getElementById('apply-range-btn').addEventListener('click',function(){
  var from=document.getElementById('range-from').value;
  var to=document.getElementById('range-to').value;
  if(!from||!to){showToast('Please select both dates','error');return;}
  load('custom',from,to);
});

// Sidebar
var st=document.getElementById('sidebar-toggle'),sb=document.getElementById('dashboard-sidebar'),ov=document.getElementById('sidebar-overlay');
if(st&&sb){st.addEventListener('click',function(){sb.classList.toggle('open');if(ov)ov.classList.toggle('active');});if(ov)ov.addEventListener('click',function(){sb.classList.remove('open');ov.classList.remove('active');});}
var ub=document.getElementById('topbar-user-btn'),ud=document.getElementById('user-dropdown');
if(ub){ub.addEventListener('click',function(e){e.stopPropagation();ud.classList.toggle('open');});document.addEventListener('click',function(){ud.classList.remove('open');});}
document.addEventListener('click',function(e){var el=e.target.closest('[data-logout]');if(el){e.preventDefault();fetch(el.dataset.logout).finally(function(){window.location.href='/pages/public/login.php';});}});

document.addEventListener('DOMContentLoaded', function(){ load(period); });
})();
</script>
<style>
.stats-range-bar{display:flex;flex-direction:column;gap:var(--space-3);margin-bottom:var(--space-5);}
.stats-custom-range{display:flex;align-items:center;gap:var(--space-3);flex-wrap:wrap;}
.admin-date-input{padding:var(--space-2) var(--space-3);border:1px solid var(--border-color);border-radius:var(--radius-md);font-size:var(--text-sm);font-family:var(--font-sans);background:var(--bg-elevated);color:var(--text-primary);outline:none;}
.admin-date-input:focus{border-color:var(--color-primary);}
.hidden{display:none!important;}
.admin-stats-grid{grid-template-columns:repeat(3,1fr);}
.admin-period-tabs{display:flex;gap:var(--space-2);flex-wrap:wrap;}
.admin-period-tab{padding:var(--space-2) var(--space-4);border-radius:var(--radius-full);border:1px solid var(--border-color);background:var(--bg-elevated);font-size:var(--text-sm);font-weight:500;color:var(--text-secondary);cursor:pointer;transition:all var(--transition-fast);font-family:var(--font-sans);}
.admin-period-tab:hover{border-color:var(--color-primary);color:var(--color-primary);}
.admin-period-tab.active{background:var(--color-primary);color:#fff;border-color:var(--color-primary);}
.admin-chart-legend{display:flex;align-items:center;gap:var(--space-4);font-size:var(--text-xs);color:var(--text-muted);}
.legend-dot{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:4px;}
.legend-dot--deposits{background:#73BA9B;}.legend-dot--withdrawals{background:#C47A2B;}.legend-dot--profits{background:#002914;}
.fmono{font-family:var(--font-mono);font-size:var(--text-sm);}
.fw6{font-weight:600;}
.text-muted{color:var(--text-muted);font-size:var(--text-sm);}
.uav{width:28px;height:28px;border-radius:50%;background:var(--color-primary-light);color:var(--color-primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:var(--text-xs);flex-shrink:0;}
@media(max-width:900px){.admin-stats-grid{grid-template-columns:repeat(2,1fr);}#dashboard-row{grid-template-columns:1fr;}}
@media(max-width:500px){.admin-stats-grid{grid-template-columns:1fr;}}
</style>
</body>
</html>
