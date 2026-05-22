<?php
// root/views/invoice_view.php
require_once "./autoloader.php";
require_once "./utils.php";
requireAuth();

/** @var array $invoice */
// var_dump($invoice);
/** @var array $items */

// Status badge helper
$statusMap = [
    'paid'    => ['label' => 'Paid',    'class' => 'badge-paid'],
    'sent' => ['label' => 'Sent', 'class' => 'badge-sent'],
    'overdue' => ['label' => 'Overdue', 'class' => 'badge-overdue'],
];
$statusInfo = $statusMap[$invoice['status']] ?? ['label' => 'Draft', 'class' => ''];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice <?php echo htmlspecialchars($invoice['invoice_number']); ?> — InvoiceManager</title>
  <style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

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
}

html,body{min-height:100%;font-family:var(--font-sans);background:var(--color-bg);color:var(--color-text-primary);}

/* ── TOP NAV ── */
.topnav{
  height:56px;background:var(--color-surface);border-bottom:1px solid var(--color-border);
  display:flex;align-items:center;justify-content:space-between;
  padding:0 1.5rem;position:sticky;top:0;z-index:100;
  box-shadow:0 1px 3px rgba(0,0,0,0.04);
}
.brand{display:flex;align-items:center;gap:10px;text-decoration:none;}
.brand-logo{width:34px;height:34px;background:var(--color-accent);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.brand-logo svg{width:17px;height:17px;stroke:white;fill:none;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round;}
.brand-name{font-size:15px;font-weight:600;color:var(--color-text-primary);letter-spacing:-0.3px;}
.nav-right{display:flex;align-items:center;gap:8px;}
.nav-link{font-size:13.5px;font-weight:500;color:var(--color-text-secondary);text-decoration:none;padding:7px 14px;border-radius:var(--radius-md);transition:background 0.12s,color 0.12s;}
.nav-link:hover{background:#f1f5f9;color:var(--color-text-primary);}
.nav-link.danger{color:#ef4444;}
.nav-link.danger:hover{background:#fef2f2;}
button.nav-link{background:none;border:none;cursor:pointer;font-family:var(--font-sans);}

/* ── PAGE WRAPPER ── */
.wrap{max-width:860px;margin:2rem auto;padding:0 1rem;}

/* ── PAGE HEADER ── */
.page-header{
  display:flex;align-items:flex-start;justify-content:space-between;
  margin-bottom:1.5rem;gap:12px;flex-wrap:wrap;
}
.page-header-left .page-title{font-size:19px;font-weight:600;letter-spacing:-0.3px;}
.page-header-left .page-sub{font-size:13px;color:var(--color-text-secondary);margin-top:3px;}
.page-header-right{display:flex;gap:8px;flex-wrap:wrap;align-items:center;}

/* ── BUTTONS ── */
.btn-outline{
  background:none;border:1px solid var(--color-border-mid);border-radius:var(--radius-md);
  padding:9px 16px;font-size:13.5px;font-family:var(--font-sans);cursor:pointer;
  color:var(--color-text-primary);transition:background 0.15s;white-space:nowrap;
  display:inline-flex;align-items:center;gap:7px;text-decoration:none;
}
.btn-outline:hover{background:#f1f5f9;}
.btn-primary{
  background:var(--color-accent);border:none;border-radius:var(--radius-md);
  padding:9px 18px;font-size:13.5px;font-family:var(--font-sans);cursor:pointer;
  color:white;font-weight:500;transition:background 0.15s;white-space:nowrap;
  display:inline-flex;align-items:center;gap:7px;text-decoration:none;
}
.btn-primary:hover{background:var(--color-accent-hover);}
.btn-icon svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}

/* ── CARD ── */
.card{
  background:var(--color-surface);border:1px solid var(--color-border);
  border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.1rem;
  box-shadow:0 1px 3px rgba(0,0,0,0.04);
}

/* ── INVOICE HEADER CARD ── */
.invoice-header{
  display:flex;justify-content:space-between;align-items:flex-start;
  gap:20px;flex-wrap:wrap;margin-bottom:1.5rem;
}
.invoice-brand{display:flex;align-items:center;gap:10px;}
.invoice-brand-logo{width:42px;height:42px;background:var(--color-accent);border-radius:10px;display:flex;align-items:center;justify-content:center;}
.invoice-brand-logo svg{width:20px;height:20px;stroke:white;fill:none;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round;}
.invoice-brand-name{font-size:16px;font-weight:700;color:var(--color-text-primary);letter-spacing:-0.3px;}
.invoice-brand-sub{font-size:12px;color:var(--color-text-secondary);margin-top:1px;}

.invoice-meta{text-align:right;}
.invoice-number{font-size:22px;font-weight:700;color:var(--color-text-primary);letter-spacing:-0.5px;}
.invoice-date{font-size:13px;color:var(--color-text-secondary);margin-top:4px;}

/* ── BADGE ── */
.badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:99px;font-size:12px;font-weight:600;letter-spacing:0.2px;margin-top:6px;}
.badge-paid{background:#dcfce7;color:#16a34a;}
.badge-sent{background:#fef9c3;color:#ca8a04;}
.badge-overdue{background:#fee2e2;color:#dc2626;}

/* ── DIVIDER ── */
.divider{border:none;border-top:1px solid var(--color-border);margin:1.25rem 0;}

/* ── PARTY GRID ── */
.party-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;}
.party-block p.party-label{font-size:11px;font-weight:600;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;}
.party-block p.party-name{font-size:15px;font-weight:600;color:var(--color-text-primary);}
.party-block p.party-detail{font-size:13px;color:var(--color-text-secondary);margin-top:2px;}

/* ── PAYMENT BOX ── */
.payment-box{
  background:#f8fafc;border:1px solid var(--color-border);border-radius:var(--radius-md);
  padding:14px 18px;position:relative;
}
.payment-box::before{
  content:"PAYMENT INFO";position:absolute;top:-11px;left:12px;
  background:var(--color-surface);padding:0 6px;
  font-size:11px;font-weight:600;color:var(--color-text-secondary);letter-spacing:0.5px;
}
.payment-box p{margin:4px 0;font-size:13px;color:var(--color-text-secondary);}
.payment-box .account-number{font-size:18px;font-weight:700;color:var(--color-text-primary);letter-spacing:0.5px;margin:4px 0;}

/* ── SECTION TITLE ── */
.section-title{font-size:11px;font-weight:600;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:14px;}

/* ── TABLE ── */
table{width:100%;border-collapse:collapse;font-size:13.5px;margin-bottom:10px;}
th{font-size:11.5px;color:var(--color-text-secondary);font-weight:600;padding:10px 12px;text-align:left;border-bottom:1.5px solid var(--color-border);background:#f8fafc;text-transform:uppercase;letter-spacing:0.4px;}
th:not(:first-child){text-align:right;}
td{padding:12px 12px;border-bottom:1px solid var(--color-border);vertical-align:middle;}
td:not(:first-child){text-align:right;}
tbody tr:last-child td{border-bottom:none;}
tbody tr:hover{background:#f8fafc;}
.item-name{font-weight:500;}
.item-meta{font-size:12px;color:var(--color-text-secondary);margin-top:2px;}

/* ── TOTALS ── */
.totals-wrap{display:flex;justify-content:flex-end;margin-top:1.25rem;}
.totals{
  width:300px;background:#f8fafc;padding:16px;
  border-radius:var(--radius-lg);border:1px solid var(--color-border);
}
.total-row{display:flex;justify-content:space-between;font-size:13.5px;padding:6px 0;color:var(--color-text-secondary);}
.total-row span:last-child{font-variant-numeric:tabular-nums;}
.total-row.grand{font-size:16px;font-weight:700;color:var(--color-text-primary);border-top:2px solid var(--color-border);margin-top:8px;padding-top:12px;}

/* ── NOTES ── */
.notes-box{background:#f8fafc;border-radius:var(--radius-md);padding:14px 16px;font-size:13.5px;color:var(--color-text-secondary);line-height:1.6;}

/* ── MODAL ── */
.modal-backdrop{display:none;position:fixed;inset:0;background:rgba(15,23,42,0.4);z-index:300;align-items:center;justify-content:center;padding:1rem;}
.modal-backdrop.open{display:flex;}
.modal{background:var(--color-surface);border-radius:var(--radius-lg);padding:1.5rem;width:100%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,0.2);}
.modal h3{font-size:16px;font-weight:600;margin-bottom:8px;}
.modal p{font-size:13.5px;color:var(--color-text-secondary);margin-bottom:18px;line-height:1.6;}
.modal-actions{display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;}
.btn-danger{background:#dc2626;border:none;border-radius:var(--radius-md);padding:9px 18px;font-size:13.5px;font-family:var(--font-sans);cursor:pointer;color:white;font-weight:500;transition:background 0.15s;}
.btn-danger:hover{background:#b91c1c;}

/* ── TOAST ── */
.toast{display:none;position:fixed;bottom:20px;right:20px;left:20px;background:var(--color-accent);color:white;padding:12px 18px;border-radius:var(--radius-md);font-size:13.5px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.15);z-index:999;font-family:var(--font-sans);text-align:center;}

/* ── PRINT ── */
@media print{
  .topnav,.page-header-right,.page-header-left, .toast,.modal-backdrop{display:none!important;}
  body{background:white;}
  .wrap{margin:0;max-width:100%;padding:0;}
  .card{box-shadow:none;border:1px solid #ddd;break-inside:avoid;}
}

/* ── RESPONSIVE ── */
@media(max-width:640px){
  .party-grid{grid-template-columns:1fr;}
  .invoice-header{flex-direction:column;}
  .invoice-meta{text-align:left;}
  .totals{width:100%;}
  .page-header{flex-direction:column;}
  .page-header-right{width:100%;}
  .btn-outline,.btn-primary{flex:1;justify-content:center;}
}
  </style>
</head>
<body>

<!-- TOP NAV -->
<nav class="topnav">
  <a class="brand" href="<?php echo Config::get('baseProjectFolder'); ?>/">
    <div class="brand-logo">
      <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
    </div>
    <span class="brand-name">InvoiceManager</span>
  </a>
  <div class="nav-right">
    <a class="nav-link" href="<?php echo Config::get('baseProjectFolder'); ?>/dashboard">Dashboard</a>
    <button class="nav-link danger" type="button" onclick="openLogout()">Logout</button>
  </div>
</nav>

<div class="wrap">

  <!-- PAGE HEADER (hidden on print) -->
  <div class="page-header">
    <div class="page-header-left">
      <div class="page-title">Invoice <?php echo htmlspecialchars($invoice['invoice_number']); ?></div>
      <div class="page-sub">Viewing invoice details — read only.</div>
    </div>
    <div class="page-header-right">
      <button class="btn-outline" onclick="window.print()">
        <svg class="btn-icon" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Print / PDF
      </button>
      <!-- TODO: Replace hardcoded id with $invoice['id'] -->
      <a class="btn-primary" href="<?php echo Config::get('baseProjectFolder'); ?>/invoice/edit/<?php echo $invoice['id']; ?>">
        <svg class="btn-icon" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit Invoice
      </a>
    </div>
  </div>

  <!-- INVOICE DOCUMENT -->
  <div class="card">

    <!-- Invoice header: brand + invoice number -->
    <div class="invoice-header">
      <div class="invoice-brand">
        <div class="invoice-brand-logo">
          <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div>
          <div class="invoice-brand-name">InvoiceManager</div>
          <div class="invoice-brand-sub">invoicemanager.com</div>
        </div>
      </div>
      <div class="invoice-meta">
        <div class="invoice-number"><?php echo htmlspecialchars($invoice['invoice_number']); ?></div>
        <div class="invoice-date">Date: <?php echo date('F d, Y', strtotime($invoice['invoice_date'])); ?></div>
        <span class="badge <?php echo $statusInfo['class']; ?>"><?php echo $statusInfo['label']; ?></span>
      </div>
    </div>

    <hr class="divider">

    <!-- From / To / Payment info -->
    <div class="party-grid">
      <div class="party-block">
        <p class="party-label">From</p>
        <p class="party-name"><?php echo htmlspecialchars($_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['lastname']); ?></p>
        <p class="party-detail"><?php echo htmlspecialchars($_SESSION['user']['email']); ?></p>
      </div>
      <div class="party-block">
        <p class="party-label">Bill To</p>
        <p class="party-name"><?php echo htmlspecialchars($invoice['customer_name']); ?></p>
        <p class="party-detail"><?php echo htmlspecialchars($invoice['customer_email']); ?></p>
      </div>
    </div>

    <div class="payment-box">
      <p>Send payments to:</p>
      <p class="account-number">0284413444</p>
      <p>WEMA BANK &mdash; Oretade Olaoluwakitan</p>
    </div>

    <hr class="divider">

    <!-- Line items -->
    <p class="section-title">Items</p>
    <table>
      <thead>
        <tr>
          <th style="width:45%">Description</th>
          <th style="width:20%">Unit Price</th>
          <th style="width:15%">Qty</th>
          <th style="width:20%">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
          <td><div class="item-name"><?php echo htmlspecialchars($item['description']); ?></div></td>
          <td>₦<?php echo number_format($item['price'], 2); ?></td>
          <td><?php echo (int) $item['quantity']; ?></td>
          <td style="font-weight:500">₦<?php echo number_format($item['subtotal'], 2); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Totals -->
    <div class="totals-wrap">
      <div class="totals">
        <div class="total-row">
          <span>Subtotal</span>
          <span>₦<?php echo number_format($invoice['subtotal'], 2); ?></span>
        </div>
        <div class="total-row">
          <span>Tax (<?php echo $invoice['tax_rate']; ?>%)</span>
          <span>₦<?php echo number_format($invoice['tax_amount'], 2); ?></span>
        </div>
        <div class="total-row">
          <span>Discount</span>
          <span>-₦<?php echo number_format($invoice['discount'], 2); ?></span>
        </div>
        <div class="total-row grand">
          <span>Total</span>
          <span>₦<?php echo number_format($invoice['grand_total'], 2); ?></span>
        </div>
      </div>
    </div>

    <?php if (!empty($invoice['notes'])): ?>
    <hr class="divider">
    <p class="section-title">Notes</p>
    <div class="notes-box"><?php echo nl2br(htmlspecialchars($invoice['notes'])); ?></div>
    <?php endif; ?>

  </div><!-- /.card -->

</div><!-- /.wrap -->

<!-- LOGOUT MODAL -->
<div class="modal-backdrop" id="logout-modal">
  <div class="modal">
    <h3>Log out?</h3>
    <p>You'll be returned to the login page.</p>
    <div class="modal-actions">
      <button class="btn-outline" onclick="closeModal('logout-modal')">Cancel</button>
      <button class="btn-danger" onclick="window.location.href='<?php echo Config::get('baseProjectFolder'); ?>/logout'">Yes, log out</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
function openLogout(){ document.getElementById('logout-modal').classList.add('open'); }
function closeModal(id){ document.getElementById(id).classList.remove('open'); }
function showToast(msg){
  const t = document.getElementById('toast');
  t.textContent = msg; t.style.display = 'block';
  setTimeout(() => t.style.display = 'none', 2500);
}
document.querySelectorAll('.modal-backdrop').forEach(b => {
  b.addEventListener('click', e => { if (e.target === b) b.classList.remove('open'); });
});

<?php if (isset($_SESSION['message'])): ?>
  showToast("<?php echo $_SESSION['message']; ?>");
  <?php unset($_SESSION['message']); ?>
<?php endif; ?>
</script>
</body>
</html>
