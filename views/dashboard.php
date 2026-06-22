<?php

use App\Utils;
use App\Config;
Utils::requireAuth(); // Ensure the user is authenticated before accessing the dashboard
/** @var array $stats */
/** @var array $recentInvoices */
/** @var array $allInvoices */
/** @var array $errors */
/** @var array $old */
/** @var int $totalPages */
/** @var int $page */

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>InvoiceManager - Dashboard
</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');
*{box-sizing:border-box;margin:0;padding:0;}
:root{
  --font-sans:'Inter',sans-serif;
  --color-text-primary:#0f172a;
  --color-text-secondary:#64748b;
  --color-bg:#f8fafc;
  --color-surface:#ffffff;
  --color-border:#e2e8f0;
  --color-border-mid:#cbd5e1;
  --color-accent:#1e2937;
  --color-accent-hover:#334155;
  --color-blue:#3b82f6;
  --color-blue-soft:rgba(59,130,246,0.12);
  --radius-md:8px;
  --radius-lg:12px;
  --sidebar-w:220px;
  --topnav-h:56px;
}
body{font-family:var(--font-sans);background:var(--color-bg);color:var(--color-text-primary);min-height:100vh;display:flex;flex-direction:column;}
.field-error {
    color: #ef4444;
    font-size: 12px;
    margin-top: 4px;
    display: block;
}

input.error, textarea.error {
    border-color: #ef4444;
}
input.error:focus, textarea.error:focus {
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
}
/* ── TOP NAV ── */
.topnav{
  height:var(--topnav-h);background:var(--color-surface);border-bottom:1px solid var(--color-border);
  display:flex;align-items:center;justify-content:space-between;
  padding:0 1.25rem;position:sticky;top:0;z-index:200;
  box-shadow:0 1px 3px rgba(0,0,0,0.04);
}
.brand{display:flex;align-items:center;gap:10px;}
.brand-logo{width:34px;height:34px;background:var(--color-accent);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.brand-logo svg{width:17px;height:17px;stroke:white;fill:none;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round;}
.brand-name{font-size:15px;font-weight:600;color:var(--color-text-primary);letter-spacing:-0.3px;}
.nav-right{display:flex;align-items:center;gap:10px;}
.avatar{width:32px;height:32px;border-radius:50%;background:var(--color-accent);color:white;font-size:13px;font-weight:600;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;}
.nav-username{font-size:13px;color:var(--color-text-secondary);font-weight:500;}

/* hamburger */
.hamburger{display:none;background:none;border:none;cursor:pointer;padding:6px;border-radius:var(--radius-md);color:var(--color-text-primary);}
.hamburger svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;}
.hamburger:hover{background:#f1f5f9;}

/* ── LAYOUT ── */
.layout{display:flex;flex:1;min-height:0;}

/* ── OVERLAY ── */
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(15,23,42,0.35);z-index:149;}
.sidebar-overlay.open{display:block;}

/* ── SIDEBAR ── */
.sidebar{
  width:var(--sidebar-w);background:var(--color-surface);border-right:1px solid var(--color-border);
  padding:1.25rem 0.75rem;display:flex;flex-direction:column;gap:2px;
  position:sticky;top:var(--topnav-h);height:calc(100vh - var(--topnav-h));overflow-y:auto;
  transition:transform 0.25s ease;flex-shrink:0;
}
.nav-item{
  display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:var(--radius-md);
  font-size:13.5px;font-weight:500;color:var(--color-text-secondary);cursor:pointer;
  transition:background 0.12s,color 0.12s;border:none;background:none;width:100%;text-align:left;
}
.nav-item svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;flex-shrink:0;}
.nav-item:hover{background:#f1f5f9;color:var(--color-text-primary);}
.nav-item.active{background:#f1f5f9;color:var(--color-accent);font-weight:600;}
.nav-item.active svg{stroke:var(--color-accent);}
.sidebar-spacer{flex:1;}
.logout-btn{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:var(--radius-md);font-size:13.5px;font-weight:500;color:#ef4444;cursor:pointer;border:none;background:none;width:100%;text-align:left;transition:background 0.12s;}
.logout-btn svg{width:16px;height:16px;stroke:#ef4444;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;}
.logout-btn:hover{background:#fef2f2;}

/* ── MAIN ── */
.main{flex:1;padding:1.5rem;overflow-y:auto;min-width:0;}

/* ── SECTION ── */
.section{display:none;}
.section.active{display:block;}

/* ── PAGE HEADER ── */
.page-header{margin-bottom:1.25rem;}
.page-title{font-size:19px;font-weight:600;color:var(--color-text-primary);letter-spacing:-0.3px;}
.page-sub{font-size:13px;color:var(--color-text-secondary);margin-top:3px;}

/* ── STAT CARDS ── */
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:1.25rem;}
.stat-card{background:var(--color-surface);border:1px solid var(--color-border);border-radius:var(--radius-lg);padding:1.1rem 1.1rem 0.9rem;box-shadow:0 1px 3px rgba(0,0,0,0.04);}
.stat-label{font-size:11px;font-weight:600;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;}
.stat-value{font-size:22px;font-weight:700;color:var(--color-text-primary);letter-spacing:-0.5px;}
.stat-sub{font-size:11.5px;color:var(--color-text-secondary);margin-top:3px;}

/* ── CARD ── */
.card{background:var(--color-surface);border:1px solid var(--color-border);border-radius:var(--radius-lg);padding:1.25rem;box-shadow:0 1px 3px rgba(0,0,0,0.04);margin-bottom:1.1rem;}
.section-title{font-size:12px;font-weight:600;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:14px;}

/* ── TABLE TOOLBAR ── */
.table-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:10px;}
.toolbar-left{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.toolbar-right{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.search-wrap{position:relative;}
.search-wrap input[type=text]{padding:8px 12px 8px 34px;border:1px solid var(--color-border);border-radius:var(--radius-md);font-size:13px;font-family:var(--font-sans);background:var(--color-bg);color:var(--color-text-primary);width:200px;transition:border-color 0.15s,box-shadow 0.15s;}
.search-wrap input[type=text]:focus{outline:none;border-color:var(--color-blue);box-shadow:0 0 0 3px var(--color-blue-soft);}
.search-icon{position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;stroke:var(--color-text-secondary);fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;pointer-events:none;}


/* ── BUTTONS ── */
.btn-primary{background:var(--color-accent);border:none;border-radius:var(--radius-md);padding:9px 16px;font-size:13.5px;font-family:var(--font-sans);cursor:pointer;color:white;font-weight:500;transition:background 0.15s;white-space:nowrap;}
.btn-primary:hover{background:var(--color-accent-hover);}
.btn-outline{background:none;border:1px solid var(--color-border-mid);border-radius:var(--radius-md);padding:9px 14px;font-size:13.5px;font-family:var(--font-sans);cursor:pointer;color:var(--color-text-primary);transition:background 0.15s;white-space:nowrap;}
.btn-outline:hover{background:#f1f5f9;}

/* ── TABLE ── */
.table-wrap{width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch;}
table{width:100%;border-collapse:collapse;font-size:13.5px;min-width:560px;}
th{font-size:11.5px;color:var(--color-text-secondary);font-weight:600;padding:10px 12px;text-align:left;border-bottom:1.5px solid var(--color-border);background:#f8fafc;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;}
td{padding:11px 12px;border-bottom:1px solid var(--color-border);vertical-align:middle;color:var(--color-text-primary);}
tr:last-child td{border-bottom:none;}
tbody tr{transition:background 0.1s;}
tbody tr:hover{background:#f8fafc;}

/* hide less-important columns on small screens */
/* hidden via media query */

.badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:99px;font-size:11.5px;font-weight:600;letter-spacing:0.2px;}
.badge-paid{background:#dcfce7;color:#16a34a;}
.badge-sent{background:#fef9c3;color:#ca8a04;}
.badge-overdue{background:#fee2e2;color:#dc2626;}

.action-btns{display:flex;gap:6px;}
.icon-btn{background:none;border:1px solid var(--color-border);border-radius:6px;padding:5px 7px;cursor:pointer;display:flex;align-items:center;transition:background 0.12s,border-color 0.12s;}
.icon-btn svg{width:13px;height:13px;stroke:var(--color-text-secondary);fill:none;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round;}
.icon-btn:hover{background:#f1f5f9;border-color:var(--color-border-mid);}
.icon-btn.danger:hover{background:#fef2f2;border-color:#fca5a5;}
.icon-btn.danger:hover svg{stroke:#ef4444;}

/* ── PROFILE ── */
.profile-header{display:flex;align-items:center;gap:16px;margin-bottom:1.5rem;flex-wrap:wrap;}
.avatar-lg{width:60px;height:60px;border-radius:50%;background:var(--color-accent);color:white;font-size:20px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.profile-meta h2{font-size:17px;font-weight:600;letter-spacing:-0.3px;}
.profile-meta p{font-size:13px;color:var(--color-text-secondary);margin-top:2px;}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:13px;margin-bottom:13px;}
.field{display:flex;flex-direction:column;gap:5px;}
.field.full{grid-column:1/-1;}
label{font-size:13px;font-weight:500;color:var(--color-text-secondary);}
input[type=text],input[type=email],input[type=tel],input[type=password],select.styled{
  padding:9px 12px;border:1px solid var(--color-border);border-radius:var(--radius-md);
  font-size:13.5px;font-family:var(--font-sans);background:var(--color-surface);color:var(--color-text-primary);
  width:100%;transition:border-color 0.15s,box-shadow 0.15s;
}
input:focus,select.styled:focus{outline:none;border-color:var(--color-blue);box-shadow:0 0 0 3px var(--color-blue-soft);}
.form-actions{display:flex;gap:10px;justify-content:flex-end;margin-top:4px;flex-wrap:wrap;}
.danger-zone{border:1px solid #fca5a5;border-radius:var(--radius-lg);padding:1.1rem 1.25rem;background:#fff5f5;margin-top:1.1rem;}
.danger-title{font-size:12px;font-weight:600;color:#dc2626;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:5px;}
.danger-desc{font-size:13px;color:#64748b;margin-bottom:12px;line-height:1.6;}
.btn-danger{background:#dc2626;border:none;border-radius:var(--radius-md);padding:9px 18px;font-size:13.5px;font-family:var(--font-sans);cursor:pointer;color:white;font-weight:500;transition:background 0.15s;}
.btn-danger:hover{background:#b91c1c;}
.updateInfo {
  margin-bottom: 1.25rem;
}

/* ── TOAST ── */
.toast{display:none;position:fixed;bottom:20px;right:20px;left:20px;background:var(--color-accent);color:white;padding:12px 18px;border-radius:var(--radius-md);font-size:13.5px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.15);z-index:999;font-family:var(--font-sans);text-align:center;}

/* ── MODAL ── */
.modal-backdrop{display:none;position:fixed;inset:0;background:rgba(15,23,42,0.4);z-index:300;align-items:center;justify-content:center;padding:1rem;}
.modal-backdrop.open{display:flex;}
.modal{background:var(--color-surface);border-radius:var(--radius-lg);padding:1.5rem;width:100%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,0.2);}
.modal h3{font-size:16px;font-weight:600;margin-bottom:8px;}
.modal p{font-size:13.5px;color:var(--color-text-secondary);margin-bottom:18px;line-height:1.6;}
.modal-actions{display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;}

/* empty */
.empty-state{text-align:center;padding:2.5rem 1rem;color:var(--color-text-secondary);}
.empty-state svg{width:36px;height:36px;stroke:var(--color-border-mid);fill:none;stroke-width:1.5;stroke-linecap:round;stroke-linejoin:round;margin-bottom:10px;}
.empty-state p{font-size:14px;}

/* ── BOTTOM NAV (mobile only) ── */
.bottom-nav{display:none;position:fixed;bottom:0;left:0;right:0;background:var(--color-surface);border-top:1px solid var(--color-border);z-index:150;padding:6px 0 max(6px, env(safe-area-inset-bottom));}
.bottom-nav-inner{display:flex;justify-content:space-around;}
.bnav-item{display:flex;flex-direction:column;align-items:center;gap:3px;padding:6px 12px;border:none;background:none;cursor:pointer;color:var(--color-text-secondary);font-size:10.5px;font-family:var(--font-sans);font-weight:500;border-radius:var(--radius-md);transition:color 0.12s;}
.bnav-item svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;}
.bnav-item.active{color:var(--color-accent);}
.bnav-item.active svg{stroke:var(--color-accent);}
.bnav-logout{color:#ef4444;}
.bnav-logout svg{stroke:#ef4444;}

/* pagination */
.pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 1.25rem;
    flex-wrap: wrap;
}

.pagination a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: var(--radius-md);
    font-size: 13px;
    font-family: var(--font-sans);
    font-weight: 500;
    color: var(--color-text-secondary);
    border: 1px solid var(--color-border);
    background: var(--color-surface);
    text-decoration: none;
    transition: background 0.12s, color 0.12s, border-color 0.12s;
}

.pagination a:hover {
    background: #f1f5f9;
    color: var(--color-text-primary);
    border-color: var(--color-border-mid);
}

.pagination a.active {
    background: var(--color-accent);
    color: white;
    border-color: var(--color-accent);
    font-weight: 600;
}

.pagination a.prev,
.pagination a.next {
    width: auto;
    padding: 0 12px;
    gap: 5px;
    font-size: 13px;
}

.pagination .dots {
    font-size: 13px;
    color: var(--color-text-secondary);
    padding: 0 4px;
}

/* ── RESPONSIVE ── */
@media(max-width:768px){
  .hamburger{display:flex;}
  .nav-username{display:none;}

  .sidebar{
    position:fixed;top:0;left:0;height:100vh;z-index:150;
    transform:translateX(-100%);box-shadow:4px 0 20px rgba(0,0,0,0.12);
    padding-top:calc(var(--topnav-h) + 1rem);
  }
  .sidebar.open{transform:translateX(0);}

  .main{padding:1rem;padding-bottom:80px;} /* space for bottom nav */
  .bottom-nav{display:block;}

  .stats{grid-template-columns:1fr 1fr;gap:10px;}
  .stats .stat-card:last-child{grid-column:1/-1;}

  .stat-value{font-size:20px;}

  .table-toolbar{flex-direction:column;align-items:stretch;}
  .toolbar-left,.toolbar-right{width:100%;justify-content:space-between;}
  .search-wrap{flex:1;}
  .search-wrap input{width:100%;}

  .col-email{display:none;}
  .col-date{display:none;}

  .form-grid{grid-template-columns:1fr;}
  .field.full{grid-column:1;}

  .toast{left:16px;right:16px;bottom:74px;text-align:center;}

  .profile-header{flex-direction:column;align-items:flex-start;gap:12px;}
}

@media(max-width:400px){
  .stats{grid-template-columns:1fr;}
  .stats .stat-card:last-child{grid-column:1;}
  .brand-name{display:none;}
  .pagination a {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }

  .pagination a.prev, .pagination a.next {
        padding: 0 8px;
        font-size: 12px;
    }
}
</style>
</head>
<body>

<!-- TOP NAV -->

<nav class="topnav">
  <div class="brand">
    <button class="hamburger" id="hamburger-btn" onclick="toggleSidebar()" aria-label="Menu">
      <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <div class="brand-logo">
      <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
    </div>
    <span class="brand-name">InvoiceManager</span>
  </div>
  <div class="nav-right">
    <span class="nav-username"><?php echo $_SESSION['user']['firstname'] ?? 'User'; ?> <?php echo $_SESSION['user']['lastname'] ?? 'Username'; ?></span>
    <div class="avatar" onclick="showSection('profile')"><?php echo substr($_SESSION['user']['firstname'], 0, 1); ?><?php echo substr($_SESSION['user']['lastname'], 0, 1); ?></div>
  </div>
</nav>

<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

<div class="layout">
  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <button class="nav-item active" id="nav-dashboard" onclick="showSection('dashboard')">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>Dashboard
    </button>
    <button class="nav-item" id="nav-invoices" onclick="showSection('invoices')">
      <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Invoices
    </button>
    <button class="nav-item" id="nav-new" onclick="showToast('Navigating to invoice form…'); window.location.href='<?php echo Config::get('baseProjectFolder'); ?>/invoice'">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>New Invoice
    </button>
    <button class="nav-item" id="nav-profile" onclick="showSection('profile')">
      <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>My Profile
    </button>
    <div class="sidebar-spacer"></div>
    <button class="logout-btn" onclick="openLogout()">
      <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Logout
    </button>
  </aside>

  <!-- MAIN -->
  <main class="main">

    <!-- DASHBOARD -->
    <div class="section active" id="section-dashboard">
      <div class="page-header">
      <div class="page-title" id="greeting">Good morning, <?php echo htmlspecialchars($_SESSION['user']['firstname']); ?> 👋</div>        <div class="page-sub">Here's an overview of your invoicing activity.</div>
      </div>
      <div class="stats">
        <div class="stat-card"><div class="stat-label">Total Invoices</div><div class="stat-value"><?php echo $stats['total_invoices']; ?></div><div class="stat-sub">All time</div></div>
        <div class="stat-card"><div class="stat-label">Revenue</div><div class="stat-value">₦<?php echo $stats['revenue']? number_format($stats['revenue'], 2) : '0.00'; ?></div><div class="stat-sub">Paid invoices</div></div>
        <div class="stat-card"><div class="stat-label">Outstanding</div><div class="stat-value">₦<?php echo $stats['outstanding']? number_format($stats['outstanding'], 2) : '0.00'; ?></div><div class="stat-sub"><?php echo $stats['unpaid_count']; ?> unpaid</div></div>
      </div>
      <div class="card">
        <p class="section-title">Recent Invoices</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Invoice #</th><th>Customer</th><th class="col-date">Date</th><th>Amount</th><th>Status</th><th></th></tr></thead>
            <tbody>
              <?php if (empty($recentInvoices)): ?>
                <tr><td colspan="6" style="text-align:center;color:var(--color-text-secondary);padding:20px;">No recent invoices found.</td></tr>   
              <?php else: ?>

                  <?php foreach($recentInvoices as $invoice): ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($invoice['invoice_number']); ?></strong></td>
                    <td><?php echo htmlspecialchars($invoice['customer_name']); ?></td>
                    <td class="col-date" style="color:var(--color-text-secondary)"><?php echo date('M d, Y', strtotime($invoice['invoice_date'])); ?></td>
                    <td>₦<?php echo number_format($invoice['grand_total'], 2); ?></td>
                    <td>
                      <?php 
                        $statusClass = '';
                        if ($invoice['status'] === 'paid') $statusClass = 'badge-paid';
                        elseif ($invoice['status'] === 'sent') $statusClass = 'badge-sent';
                        elseif ($invoice['status'] === 'overdue') $statusClass = 'badge-overdue';
                      ?>
                      <span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($invoice['status']); ?></span>
                    </td>
                    <td>
                      <div class="action-btns">
                        <button class="icon-btn" onclick="showToast('Viewing invoice <?php echo htmlspecialchars($invoice['invoice_number']); ?>'); window.location.href='<?php echo Config::get('baseProjectFolder'); ?>/invoice/view/<?php echo $invoice['id']; ?>'">
                          <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>

            </tbody>
          </table>
        </div>
        <div style="margin-top:12px;text-align:right"><button class="btn-outline" onclick="showSection('invoices');">View all invoices →</button></div>
      </div>
    </div>

    <!-- INVOICES -->
    <div class="section" id="section-invoices">
      <div class="page-header">
        <div class="page-title">Invoices</div>
        <div class="page-sub">All your saved invoices in one place.</div>
      </div>
      <div class="card">
        <div class="table-toolbar">
          <div class="toolbar-left">
            <div class="search-wrap">
              <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" id="search-input" placeholder="Search invoices…" oninput="filterInvoices()"/>
            </div>
          </div>
          <div class="toolbar-right">
            <select class="styled" id="status-filter" onchange="filterInvoices()" style="padding:8px 12px;border:1px solid var(--color-border);border-radius:var(--radius-md);font-size:13px;font-family:var(--font-sans);background:var(--color-bg);color:var(--color-text-primary);">
              <option value="">All statuses</option>
              <option value="Draft">Drafts</option>
              <option value="Paid">Paid</option>
              <option value="Sent">Sent</option>
              <option value="Overdue">Overdue</option>
            </select>
            <button class="btn-primary" onclick="showToast('Navigating to invoice form…'); window.location.href='<?php echo Config::get('baseProjectFolder'); ?>/invoice'">+ New Invoice</button>
          </div>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Invoice #</th><th>Customer</th><th class="col-email">Email</th><th class="col-date">Date</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="invoice-tbody"></tbody>
          </table>
        </div>
        <div class="pagination">
          <?php if ($page > 1): ?>
              <a href="?section=invoices&page=<?= $page - 1 ?>" class="prev">← Prev</a>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <?php if ($i === 1 || $i === $totalPages || abs($i - $page) <= 1): ?>
                  <a href="?section=invoices&page=<?= $i ?>" <?= $i === $page ? 'class="active"' : '' ?>><?= $i ?></a>
              <?php elseif (abs($i - $page) === 2): ?>
                  <span class="dots">…</span>
              <?php endif; ?>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
              <a href="?section=invoices&page=<?= $page + 1 ?>" class="next">Next →</a>
          <?php endif; ?>
        </div>
        <div id="invoice-empty" class="empty-state" style="display:none">
          <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          <p>No invoices match your search.</p>
        </div>
      </div>
    </div>

    <!-- PROFILE -->
    <div class="section" id="section-profile"> 
          <div class="page-header">
            <div class="page-title">My Profile</div>
            <div class="page-sub">View and update your account information.</div>
          </div>
      <div class="card">
        <div class="profile-header">
            <div class="avatar-lg" id="avatar-initials"><?php echo substr($_SESSION['user']['firstname'], 0, 1); ?><?php echo substr($_SESSION['user']['lastname'], 0, 1); ?></div>
            <div class="profile-meta">
              <h2 id="display-name"><?php echo $_SESSION['user']['firstname'] ?: 'User'; ?> <?php echo $_SESSION['user']['lastname'] ?: 'Username'; ?></h2>
              <p id="display-email"><?php echo $_SESSION['user']['email'] ?: 'user@example.com'; ?></p>
            </div>
        </div>
        <p class="section-title">Personal Information</p>
        
        <form  action="<?php echo Config::get('baseProjectFolder'); ?>/profile/update-personal-info" method="POST" id="profile-form" class="updateInfo card">
            <div class="form-grid" >

              <div class="field <?php echo isset($errors['profile']['firstname']) ? 'error' : ''; ?>"><label>First name</label><input type="text" name="firstname" id="fname" value="<?php echo Utils::old($old, 'firstname', htmlspecialchars((string)($_SESSION['user']['firstname'] ?? ''))); ?>" required/> <?php echo Utils::fieldError($errors, 'profile', null, 'firstname'); ?> </div>
             
              <div class="field <?php echo isset($errors['profile']['lastname']) ? 'error' : ''; ?>"><label>Last name</label><input type="text" name="lastname" id="lname" value="<?php echo Utils::old($old, 'lastname', htmlspecialchars((string)($_SESSION['user']['lastname'] ?? ''))); ?>" required/><?php echo Utils::fieldError($errors, 'profile', null, 'lastname'); ?></div>
              
              <div class="field <?php echo isset($errors['profile']['email']) ? 'error' : ''; ?>"><label>Email address</label><input type="email" name="email" id="email" value="<?php echo Utils::old($old, 'email', htmlspecialchars((string)($_SESSION['user']['email'] ?? ''))); ?>" required/> <?php echo Utils::fieldError($errors, 'profile', null, 'email'); ?></div>

              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>

              <!-- <div class="field full"><label>Business / Bank name</label><input type="text" id="bank" value="WEMA BANK"/></div> -->
            </div>
            <div class="form-actions">
              <button type="reset" class="btn-outline" onclick="showToast('Changes discarded.')">Cancel</button>
              <button type="submit" class="btn-primary">Save changes</button>
            </div>
        </form>
      <form action="<?php echo Config::get('baseProjectFolder'); ?>/profile/business-details" method="POST" class="card">
      <p class="section-title" id="business-info-section">Business Information</p>
      <div class="form-grid" >

          <div class="field full <?php echo isset($errors['profile']['business_name']) ? 'error' : ''; ?>">
              <label>Business name</label>
              <input type="text" name="business_name" placeholder="e.g. Acme Ltd"
                  value="<?php echo Utils::old($old, 'business_name', ucwords(htmlspecialchars((string)($_SESSION['user']['business_name'] ?? '')))); ?>" required/>
              <?php echo Utils::fieldError($errors, 'profile', null, 'business_name'); ?>
          </div>

          <div class="field <?php echo isset($errors['profile']['business_email']) ? 'error' : ''; ?>">
              <label>Business email</label>
              <input type="email" name="business_email" placeholder="e.g. info@acme.com"
                  value="<?php echo Utils::old($old, 'business_email', htmlspecialchars((string)($_SESSION['user']['business_email'] ?? ''))); ?>" required/>
              <?php echo Utils::fieldError($errors, 'profile', null, 'business_email'); ?>
          </div>

          <div class="field <?php echo isset($errors['profile']['business_phone']) ? 'error' : ''; ?>">
              <label>Business phone(optional)</label>
              <input type="tel" name="business_phone" inputmode="numeric" placeholder="e.g. 08012345678"
                  value="<?php echo Utils::old($old, 'business_phone', htmlspecialchars((string)($_SESSION['user']['business_phone'] ?? '')) ?? ''); ?>"/>
              <?php echo Utils::fieldError($errors, 'profile', null, 'business_phone'); ?>
          </div>

          <div class="field full <?php echo isset($errors['profile']['business_address']) ? 'error' : ''; ?>">
              <label>Business address (optional)</label>
              <input type="text" name="business_address" placeholder="e.g. 12 Marina Street, Lagos"
                  value="<?php echo Utils::old($old, 'business_address', ucwords(htmlspecialchars((string)($_SESSION['user']['business_address'] ?? '')))); ?>"/>
              <?php echo Utils::fieldError($errors, 'profile', null, 'business_address'); ?>
          </div>

          <div class="field full <?php echo isset($errors['profile']['bank_account_number']) ? 'error' : ''; ?>"><label>Account number</label><input type="text" name="bank_account_number" id="phone"  inputmode="numeric" pattern="[0-9]*" autocomplete="off" value="<?php echo Utils::old($old, 'bank_account_number', htmlspecialchars((string)($_SESSION['user']['bank_account_number'] ?? ''))); ?>" required/> <?php echo Utils::fieldError($errors, 'profile', null, 'bank_account_number'); ?></div>

          <div class="field full <?php echo isset($errors['profile']['bank_account_name']) ? 'error' : ''; ?>"><label>Account name</label><input type="text" name="bank_account_name" id="account-name" value="<?php echo ucwords(Utils::old($old, 'bank_account_name', htmlspecialchars((string)($_SESSION['user']['bank_account_name'] ?? '')))); ?>" required/> <?php echo Utils::fieldError($errors, 'profile', null, 'bank_account_name'); ?></div>

          <div class="field full <?php echo isset($errors['profile']['bank_name']) ? 'error' : ''; ?>"><label>Bank name</label><input type="text" name="bank_name" id="bank" value="<?php echo ucwords(Utils::old($old, 'bank_name', htmlspecialchars((string)($_SESSION['user']['bank_name'] ?? '')))); ?>" required/> <?php echo Utils::fieldError($errors, 'profile', null, 'bank_name'); ?></div>

          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>

          <input type="hidden" name="source" value="profile"/>
      </div>
      <div class="form-actions">
          <button type="reset" class="btn-outline">Cancel</button>
          <button type="submit" class="btn-primary">Save business details</button>
      </div>
      </form>
          <form action="<?php echo Config::get('baseProjectFolder'); ?>/profile/change-password" method="POST" class="card">
              <p class="section-title">Change Password</p>
              <div class="form-grid">
                <div class="field full <?php echo isset($errors['profile']['current_password']) ? 'error' : ''; ?>"><label>Current password</label><input type="password" name="current_password" id="pw-current" placeholder="••••••••"/><?php echo Utils::fieldError($errors, 'profile', null, 'current_password'); ?> </div>
                <div class="field <?php echo isset($errors['profile']['new_password']) ? 'error' : ''; ?>"><label>New password</label><input type="password" name="new_password" id="pw-new" placeholder="••••••••"/><?php echo Utils::fieldError($errors, 'profile', null, 'new_password'); ?> </div>
                <div class="field <?php echo isset($errors['profile']['confirm_password']) ? 'error' : ''; ?>"><label>Confirm new password</label><input type="password" name="confirm_password" id="pw-confirm" placeholder="••••••••"/><?php echo Utils::fieldError($errors, 'profile', null, 'confirm_password'); ?> </div>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
              </div> 
              <div class="form-actions"><button class="btn-primary" type="submit" >Update password</button></div>
            </div>
          </form>
          <div class="danger-zone">
            <div class="danger-title">Danger Zone</div>
            <div class="danger-desc">Permanently delete your account and all associated invoices. This action cannot be undone.</div>
            <button class="btn-danger" onclick="openDeleteAccount()">Delete my account</button>
          </div>
     

  </main>
</div>

<!-- BOTTOM NAV (mobile) -->
<nav class="bottom-nav">
  <div class="bottom-nav-inner">
    <button class="bnav-item active" id="bnav-dashboard" onclick="showSection('dashboard')">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>Home
    </button>
    <button class="bnav-item" id="bnav-invoices" onclick="showSection('invoices')">
      <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Invoices
    </button>
    <button class="bnav-item" onclick="showToast('Navigating to invoice form…'); window.location.href='<?php echo Config::get('baseProjectFolder'); ?>/invoice'"> 
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>New
    </button>
    <button class="bnav-item" id="bnav-profile" onclick="showSection('profile')">
      <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Profile
    </button>
    <button class="bnav-item bnav-logout" onclick="openLogout()">
      <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Logout
    </button>
  </div>
</nav>

<!-- MODALS -->
<div class="modal-backdrop" id="logout-modal">
  <div class="modal"><h3>Log out?</h3><p>You'll be returned to the login page. Any unsaved changes will be lost.</p>
  <div class="modal-actions"><button class="btn-outline" onclick="closeModal('logout-modal')">Cancel</button><button class="btn-danger" onclick="doLogout(); window.location.href='<?php echo Config::get('baseProjectFolder'); ?>/logout'">Yes, log out</button></div></div>
</div>
<div class="modal-backdrop" id="delete-invoice-modal">
  <div class="modal"><h3>Delete invoice?</h3><p>This will permanently remove the invoice. This cannot be undone.</p>
  <div class="modal-actions">
    <button class="btn-outline" onclick="closeModal('delete-invoice-modal')">Cancel</button>
    <form id="delete-invoice-form" action="" method="POST">
      <input type="hidden" name="_method" value="DELETE">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      <button class="btn-danger" type="submit">Delete</button>
    </form>
  </div></div>
</div>
<div class="modal-backdrop" id="delete-account-modal">
  <div class="modal">
    <h3>Delete account?</h3>
    <p>All your data and invoices will be permanently deleted. Are you absolutely sure?</p>
    <div class="modal-actions">
      <button class="btn-outline" onclick="closeModal('delete-account-modal')">Cancel</button>
      <form action="<?php echo Config::get('baseProjectFolder'); ?>/profile/delete" method="POST">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button class="btn-danger" type="submit">Yes, delete everything</button>
      </form>
    </div>
  </div>
</div>


<!-- BUSINESS SETUP MODAL -->
<div class="modal-backdrop" id="business-setup-modal">
  <div class="modal" style="max-width:460px;">
    <h3>Set up your business details</h3>
    <p>Add your business details so they appear on your invoices. You can always update this later in your profile.</p>
    
    <form action="<?php echo Config::get('baseProjectFolder'); ?>/profile/business-details" method="POST">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>

      <div class="form-grid" style="margin-bottom:0;">
        
        <div class="field full <?php echo isset($errors['business_name']) ? 'error' : ''; ?>">
          <label>Business name</label>
          <input type="text" name="business_name" placeholder="e.g. Acme Ltd"
            value="<?php echo Utils::old($old, 'business_name', ''); ?>"
            class="<?php echo isset($errors['business_name']) ? 'error' : ''; ?>" required/>
          <?php echo Utils::fieldError($errors, 'modal', null, 'business_name'); ?>
        </div>

        <div class="field <?php echo isset($errors['business_email']) ? 'error' : ''; ?>">
          <label>Business email</label>
          <input type="email" name="business_email" placeholder="e.g. info@acme.com"
            value="<?php echo Utils::old($old, 'business_email', ''); ?>"
            class="<?php echo isset($errors['business_email']) ? 'error' : ''; ?>" required/>
          <?php echo Utils::fieldError($errors, 'modal', null, 'business_email'); ?>
        </div>

        <div class="field <?php echo isset($errors['business_phone']) ? 'error' : ''; ?>">
          <label>Business phone (optional)</label>
          <input type="tel" name="business_phone" inputmode="numeric" placeholder="e.g. 08012345678"
            value="<?php echo Utils::old($old, 'business_phone', ''); ?>"
            class="<?php echo isset($errors['business_phone']) ? 'error' : ''; ?>"/>
          <?php echo Utils::fieldError($errors, 'modal', null, 'business_phone'); ?>
        </div>

        <div class="field full <?php echo isset($errors['business_address']) ? 'error' : ''; ?>">
          <label>Business address (optional)</label>
          <input type="text" name="business_address" placeholder="e.g. 12 Marina Street, Lagos"
            value="<?php echo Utils::old($old, 'business_address', ''); ?>"
            class="<?php echo isset($errors['business_address']) ? 'error' : ''; ?>" />
          <?php echo Utils::fieldError($errors, 'modal', null, 'business_address'); ?>
        </div>

        <div class="field full <?php echo isset($errors['bank_account_name']) ? 'error' : ''; ?>">
          <label>Account name</label>
          <input type="text" name="bank_account_name" placeholder="e.g. Acme Ltd"
            value="<?php echo Utils::old($old, 'bank_account_name', ''); ?>"
            class="<?php echo isset($errors['bank_account_name']) ? 'error' : ''; ?>" required/>
          <?php echo Utils::fieldError($errors, 'modal', null, 'bank_account_name'); ?>
        </div>

        <div class="field <?php echo isset($errors['bank_account_number']) ? 'error' : ''; ?>">
          <label>Account number</label>
          <input type="tel" name="bank_account_number" inputmode="numeric" placeholder="e.g. 0123456789"
            value="<?php echo Utils::old($old, 'bank_account_number', ''); ?>"
            class="<?php echo isset($errors['bank_account_number']) ? 'error' : ''; ?>" required/>
          <?php echo Utils::fieldError($errors, 'modal', null, 'bank_account_number'); ?>
        </div>

        <div class="field <?php echo isset($errors['bank_name']) ? 'error' : ''; ?>">
          <label>Bank name</label>
          <input type="text" name="bank_name" placeholder="e.g. First Bank"
            value="<?php echo Utils::old($old, 'bank_name', ''); ?>"
            class="<?php echo isset($errors['bank_name']) ? 'error' : ''; ?>" required/>
          <?php echo Utils::fieldError($errors, 'modal', null, 'bank_name'); ?>
        </div>
          <input type="hidden" name="source" value="modal"/>

      </div>

      <!-- Don't Show Again Checkbox -->
      <div style="margin: 16px 0;">
        <label style="font-size:13px; color:#64748b; cursor:pointer; user-select:none;">
          <input type="checkbox" id="dont-show-again" style="margin-right:8px;">
          Don't show this again
        </label>
      </div>

      <div class="modal-actions" style="margin-top:10px;">
        <button type="button" class="btn-outline" onclick="closeBusinessModal()">Set up later</button>
        <button type="submit" class="btn-primary">Save details</button>
      </div>
    </form>

  </div>
</div>

<div class="toast" id="toast"></div>
   
<script>
  const baseUrl = '<?php echo Config::get('baseProjectFolder'); ?>'; 
  const firstName = "<?php echo htmlspecialchars($_SESSION['user']['firstname']); ?>";
  const lastName = "<?php echo htmlspecialchars($_SESSION['user']['lastname']); ?>";
  const hour = new Date().getHours();
  let greeting;

  if (hour < 12) {
    greeting = 'Good morning';
  } else if (hour < 17) {
    greeting = 'Good afternoon';
  } else {
    greeting = 'Good evening';
  }

  document.getElementById('greeting').textContent = `${greeting}, ${firstName} 👋`;
const invoices = <?php echo json_encode(array_map(function($invoice) {
    return [
        'id' => $invoice['id'],
        'invoice_number' => $invoice['invoice_number'],
        'customer_name' => $invoice['customer_name'],
        'customer_email' => $invoice['customer_email'],
        'invoice_date' => date('M d, Y', strtotime($invoice['invoice_date'])),
        'grand_total' => '₦' . number_format($invoice['grand_total'], 2),
        'status' => ucfirst($invoice['status'])
    ];
}, $allInvoices)); ?>;
let deleteTarget=null;

// ── SIDEBAR ──
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sidebar-overlay').classList.toggle('open');
}
function closeSidebar(){
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebar-overlay').classList.remove('open');
}

// ── NAVIGATION ──
function showSection(name){
  document.querySelectorAll('.section').forEach(s=>s.classList.remove('active'));
  document.querySelectorAll('.nav-item,.bnav-item').forEach(n=>n.classList.remove('active'));
  document.getElementById('section-'+name).classList.add('active');
  const navEl=document.getElementById('nav-'+name);
  const bnavEl=document.getElementById('bnav-'+name);
  if(navEl) navEl.classList.add('active');
  if(bnavEl) bnavEl.classList.add('active');
  if(name==='invoices') renderInvoices(invoices);
  closeSidebar();
  window.scrollTo({top:0,behavior:'smooth'});
}

// ── INVOICES ──
function badgeClass(s){return s==='Paid'?'badge-paid':s==='Sent'?'badge-sent':'badge-overdue';}
// function renderInvoices(data){
//   const tbody=document.getElementById('invoice-tbody');
//   const empty=document.getElementById('invoice-empty');
//   if(!data.length){tbody.innerHTML='';empty.style.display='block';return;}
//   empty.style.display='none';
//   tbody.innerHTML=data.map(inv=>`
//     <tr>
//       <td><strong>${inv.invoice_number}</strong></td>
//       <td>${inv.customer_name}</td>
//       <td class="col-email" style="color:var(--color-text-secondary)">${inv.customer_email}</td>
//       <td class="col-date" style="color:var(--color-text-secondary)">${inv.invoice_date}</td>
//       <td style="font-weight:500">${inv.grand_total}</td>
//       <td><span class="badge ${badgeClass(inv.status)}">${inv.status}</span></td>
//       <td><div class="action-btns">
//         <button class="icon-btn" title="View" onclick="window.location.href='<?php echo Config::get('baseProjectFolder'); ?>/invoice/view/${inv.id}'">
//           <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
//         </button>
//         <button class="icon-btn" title="Edit"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
//         <button class="icon-btn danger" title="Delete" onclick="openDeleteInvoice('${inv.id}')"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
//       </div></td>
//     </tr>
//   `).join('');
// }

function renderInvoices(data){
  const tbody=document.getElementById('invoice-tbody');
  const empty=document.getElementById('invoice-empty');
  if(!data.length){tbody.innerHTML='';empty.style.display='block';return;}
  empty.style.display='none';
  tbody.innerHTML=data.map(inv=>`
    <tr>
      <td><strong>${inv.invoice_number}</strong></td>
      <td>${inv.customer_name}</td>
      <td class="col-email" style="color:var(--color-text-secondary)">${inv.customer_email}</td>
      <td class="col-date" style="color:var(--color-text-secondary)">${inv.invoice_date}</td>
      <td style="font-weight:500">${inv.grand_total}</td>
      <td><span class="badge ${badgeClass(inv.status)}">${inv.status}</span></td>
      <td><div class="action-btns">
        <button class="icon-btn" title="View" onclick="window.location.href='${baseUrl}/invoice/view/${inv.id}'">
          <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
        <button class="icon-btn" title="Edit" onclick="window.location.href='${baseUrl}/invoice/edit/${inv.id}'">
          <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </button>
        <button class="icon-btn danger" title="Delete" onclick="openDeleteInvoice(${inv.id})">
          <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div></td>
    </tr>
  `).join('');
}

function filterInvoices(){
  const q=document.getElementById('search-input').value.toLowerCase();
  const s=document.getElementById('status-filter').value;
  renderInvoices(invoices.filter(inv=>{
    const mq=inv.invoice_number.toLowerCase().includes(q)||inv.customer_name.toLowerCase().includes(q)||inv.customer_email.toLowerCase().includes(q);
    return mq&&(!s||inv.status===s);
  }));
}

// ── PROFILE ──
function saveProfile(){
  const fn=document.getElementById('fname').value.trim();
  const ln=document.getElementById('lname').value.trim();
  const em=document.getElementById('email').value.trim();
  document.getElementById('display-name').textContent=fn+' '+ln;
  document.getElementById('display-email').textContent=em;
  document.getElementById('avatar-initials').textContent=(fn[0]||'')+(ln[0]||'');
  document.querySelector('.nav-username').textContent=fn;
  document.querySelector('.avatar').textContent=(fn[0]||'')+(ln[0]||'');
  showToast('Profile updated successfully!');
}
function changePassword(){
  const nw=document.getElementById('pw-new').value;
  const cf=document.getElementById('pw-confirm').value;
  if(!nw){showToast('Please enter a new password.');return;}
  if(nw!==cf){showToast('Passwords do not match.');return;}
  ['pw-current','pw-new','pw-confirm'].forEach(id=>document.getElementById(id).value='');
  showToast('Password updated!');
}

// ── MODALS ──
function openLogout(){document.getElementById('logout-modal').classList.add('open');}
function openDeleteInvoice(id) {
  deleteTarget = id;
  document.getElementById('delete-invoice-form').action = '<?php echo Config::get('baseProjectFolder'); ?>/invoice/delete/' + id;
  document.getElementById('delete-invoice-modal').classList.add('open');
}
function openDeleteAccount(){document.getElementById('delete-account-modal').classList.add('open');}
function closeModal(id){document.getElementById(id).classList.remove('open');}
function doLogout(){closeModal('logout-modal');showToast('Logged out. Redirecting…');}
// function confirmDeleteInvoice(){
//   const i=invoices.findIndex(x=>x.id===deleteTarget);
//   if(i>-1) invoices.splice(i,1);
//   renderInvoices(invoices);
//   closeModal('delete-invoice-modal');
//   showToast('Invoice deleted.');
//   deleteTarget=null;
// }
document.querySelectorAll('.modal-backdrop').forEach(b=>{
  b.addEventListener('click',e=>{if(e.target===b) b.classList.remove('open');});
});

// ── TOAST ──
function showToast(msg){
  const t=document.getElementById('toast');
  t.textContent=msg;t.style.display='block';
  setTimeout(()=>t.style.display='none',2500);
}
async function deleteAccount() {
  console.log("Deleting account...");
  const response = await fetch('<?php echo Config::get('baseProjectFolder'); ?>/profile/delete', {
    method: 'DELETE',
  });

  if (response.ok) {
    window.location.href = '<?php echo Config::get('baseProjectFolder'); ?>/login';
  } else {
    alert('Something went wrong. Please try again.');
  }
}



// ── BUSINESS SETUP MODAL ──
(function initBusinessModal() {
    const userId = <?php echo json_encode($_SESSION['user']['id'] ?? ''); ?>;
    const hasBusinessDetails = <?php echo json_encode((bool)($_SESSION['user']['has_business_details'] ?? false)); ?>;
    const hasErrors = <?php echo json_encode(!empty($errors['modal'])); ?>;

    if (hasBusinessDetails) {
        localStorage.removeItem('invoiceManager_business'); // ← clear if already set up
        return;
    }

    // Prefill from localStorage — runs regardless of which path shows the modal
    const saved = localStorage.getItem('invoiceManager_business');
    if (saved) {
        try {
            const data = JSON.parse(saved);
            for (const [name, value] of Object.entries(data)) {
                const el = document.querySelector(`#business-setup-modal [name="${name}"]`);
                if (el && value) el.value = value;
            }
        } catch(e) {}
    }

    if (hasErrors) {
        setTimeout(() => {
            document.getElementById('business-setup-modal').classList.add('open');
        }, 600);
        return;
    }

    const dontShowKey = 'businessModalDontShow_' + userId;
    if (localStorage.getItem(dontShowKey) === 'true') return;

    const skipKey = 'businessModalSkipped_' + userId;
    if (sessionStorage.getItem(skipKey) === 'true') return;

    setTimeout(() => {
        document.getElementById('business-setup-modal').classList.add('open');
    }, 800);
})();

// Updated close function
function closeBusinessModal() {
    const modal = document.getElementById('business-setup-modal');
    const dontShowCheckbox = document.getElementById('dont-show-again');
    
    modal.classList.remove('open');

    const userId = <?php echo json_encode($_SESSION['user']['id'] ?? ''); ?>;
    const dontShowKey = 'businessModalDontShow_' + userId;
    const skipKey = 'businessModalSkipped_' + userId;

    // Mark as skipped for THIS user only
    sessionStorage.setItem(skipKey, 'true');

    // If "Don't show again" is checked → save permanently
    if (dontShowCheckbox && dontShowCheckbox.checked) {
        localStorage.setItem(dontShowKey, 'true');
    }
}

// Open section from URL query param (e.g. ?section=profile)
(function checkUrlSection() {
  const params = new URLSearchParams(window.location.search);
  const section = params.get('section');
  if (section && document.getElementById('section-' + section)) {
    showSection(section);
  }
})();
</script>
<?php if(isset($_SESSION['message'])): ?>
  <script>showToast("<?php echo $_SESSION['message']; ?>");</script>
  <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<?php if(!empty($errors['profile'])): ?>
  <script>
    showSection('profile');
  </script>
<?php endif; ?>
</body>
</html>
