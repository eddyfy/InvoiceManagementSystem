<?php
use App\Config;
use App\Utils;
Utils::requireAuth();

/** @var array $invoice */
/** @var array $items */
/** @var array $errors */

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit <?php echo htmlspecialchars($invoice['invoice_number']); ?> — InvoiceManager</title>
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
.topnav{height:56px;background:var(--color-surface);border-bottom:1px solid var(--color-border);display:flex;align-items:center;justify-content:space-between;padding:0 1.5rem;position:sticky;top:0;z-index:100;box-shadow:0 1px 3px rgba(0,0,0,0.04);}
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

/* ── BREADCRUMB ── */
.breadcrumb{display:flex;align-items:center;gap:6px;font-size:13px;color:var(--color-text-secondary);margin-bottom:1rem;}
.breadcrumb a{color:var(--color-text-secondary);text-decoration:none;}
.breadcrumb a:hover{color:var(--color-text-primary);text-decoration:underline;}
.breadcrumb svg{width:12px;height:12px;stroke:var(--color-border-mid);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}

/* ── CARD ── */
.card{background:var(--color-surface);border:1px solid var(--color-border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:1.1rem;box-shadow:0 1px 3px rgba(0,0,0,0.04);}

/* ── SECTION TITLE ── */
.section-title{font-size:11px;font-weight:600;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:16px;}

/* ── PAYMENT DETAILS ── */
.payment-details{background:#f8fafc;border:1px solid var(--color-border);border-radius:var(--radius-md);padding:14px 18px;margin-top:28px;margin-bottom:18px;position:relative;}
.payment-details::before{content:"PAYMENT INFO";position:absolute;top:-11px;left:12px;background:var(--color-surface);padding:0 6px;font-size:11px;font-weight:600;color:var(--color-text-secondary);letter-spacing:0.5px;}
.payment-details p{margin:5px 0;font-size:14px;line-height:1.5;color:var(--color-text-secondary);}
.payment-details p:nth-child(2){font-size:17px;font-weight:600;color:var(--color-text-primary);letter-spacing:0.2px;}

/* ── STATUS SELECT ── */
.status-row{display:flex;align-items:center;gap:12px;margin-bottom:14px;}
.status-row label{font-size:13px;color:var(--color-text-secondary);font-weight:500;white-space:nowrap;margin:0;}

/* ── GRIDS ── */
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.grid3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;}

/* ── FORM ELEMENTS ── */
label{font-size:13px;color:var(--color-text-secondary);display:block;margin-bottom:5px;font-weight:500;}
input, select, textarea{width:100%;padding:9px 12px;border:1px solid var(--color-border);border-radius:var(--radius-md);font-size:13.5px;font-family:var(--font-sans);background:var(--color-surface);color:var(--color-text-primary);transition:border-color 0.15s,box-shadow 0.15s;}
input:focus, select:focus, textarea:focus{outline:none;border-color:var(--color-blue);box-shadow:0 0 0 3px var(--color-blue-soft);}

/* ── FIELD ERRORS ── */
.field-error{color:#ef4444;font-size:12px;margin-top:4px;display:block;}
input.error, textarea.error, select.error{border-color:#ef4444;}
input.error:focus, textarea.error:focus{box-shadow:0 0 0 3px rgba(239,68,68,0.15);}

/* ── TABLE ── */
table{width:100%;border-collapse:collapse;font-size:13.5px;margin-bottom:10px;}
th{font-size:11.5px;color:var(--color-text-secondary);font-weight:600;padding:10px 10px;text-align:left;border-bottom:1.5px solid var(--color-border);background:#f8fafc;text-transform:uppercase;letter-spacing:0.4px;}
td{padding:10px 10px;border-bottom:1px solid var(--color-border);vertical-align:middle;}
tbody tr:last-child td{border-bottom:none;}
tbody tr:hover{background:#f8fafc;}
td input{border:1px solid var(--color-border);padding:7px 10px;font-size:13.5px;font-family:var(--font-sans);transition:border-color 0.15s,box-shadow 0.15s;width:100%;}
td input:focus{border-color:var(--color-blue);box-shadow:0 0 0 3px var(--color-blue-soft);}

/* ── BUTTONS ── */
.add-btn{background:none;border:1px solid var(--color-border-mid);border-radius:var(--radius-md);padding:8px 16px;font-size:13px;font-family:var(--font-sans);cursor:pointer;color:var(--color-text-primary);margin-top:8px;transition:background 0.12s;}
.add-btn:hover{background:#f1f5f9;}
.remove-btn{background:none;border:1px solid var(--color-border);border-radius:6px;color:var(--color-text-secondary);cursor:pointer;font-size:13px;padding:5px 8px;display:flex;align-items:center;transition:background 0.12s,border-color 0.12s,color 0.12s;}
.remove-btn:hover{background:#fef2f2;border-color:#fca5a5;color:#ef4444;}
.btn-outline{background:none;border:1px solid var(--color-border-mid);border-radius:var(--radius-md);padding:9px 20px;font-size:13.5px;font-family:var(--font-sans);cursor:pointer;color:var(--color-text-primary);transition:background 0.15s;white-space:nowrap;display:inline-flex;align-items:center;gap:7px;text-decoration:none;}
.btn-outline:hover{background:#f1f5f9;}
.btn-primary{background:var(--color-accent);border:none;border-radius:var(--radius-md);padding:9px 22px;font-size:13.5px;font-family:var(--font-sans);cursor:pointer;color:white;font-weight:500;transition:background 0.15s;white-space:nowrap;}
.btn-primary:hover{background:var(--color-accent-hover);}
.btn-danger-outline{background:none;border:1px solid #fca5a5;border-radius:var(--radius-md);padding:9px 16px;font-size:13.5px;font-family:var(--font-sans);cursor:pointer;color:#dc2626;transition:background 0.15s;white-space:nowrap;}
.btn-danger-outline:hover{background:#fef2f2;}

/* ── TOTALS ── */
.totals{margin-left:auto;width:300px;margin-top:1.5rem;background:#f8fafc;padding:16px;border-radius:var(--radius-lg);border:1px solid var(--color-border);}
.total-row{display:flex;justify-content:space-between;font-size:13.5px;padding:7px 0;color:var(--color-text-secondary);}
.total-row.grand{font-size:16px;font-weight:600;color:var(--color-text-primary);border-top:2px solid var(--color-border);margin-top:8px;padding-top:12px;}
.totals input[type=number]{width:60px;border:1px solid var(--color-border);border-radius:4px;padding:2px 5px;font-size:12px;font-family:var(--font-sans);background:var(--color-surface);color:var(--color-text-primary);text-align:center;}
.totals input[type=number]:focus{border-color:var(--color-blue);box-shadow:0 0 0 2px var(--color-blue-soft);}

/* ── ACTIONS ── */
.actions{display:flex;gap:10px;justify-content:space-between;align-items:center;margin-top:1.25rem;flex-wrap:wrap;}
.actions-right{display:flex;gap:10px;flex-wrap:wrap;}

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
@media print{.topnav,.actions,.add-btn,.remove-btn,.toast,.modal-backdrop{display:none!important;}body{background:white;}.card{box-shadow:none;border:1px solid #ccc;}}

/* ── RESPONSIVE ── */
@media(max-width:640px){
  .grid2,.grid3{grid-template-columns:1fr;}
  .totals{width:100%;}
  .actions{flex-direction:column;align-items:stretch;}
  .actions-right{width:100%;}
  .btn-outline,.btn-primary,.btn-danger-outline{width:100%;justify-content:center;text-align:center;}
  .topnav{padding:0 1rem;}
  .page-header{flex-direction:column;}
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

<!-- Breadcrumb -->
<div class="wrap" style="margin-bottom:0; padding-bottom:0;">
  <div class="breadcrumb">
    <a href="<?php echo Config::get('baseProjectFolder'); ?>/dashboard">Dashboard</a>
    <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
    <!-- TODO: Replace hardcoded id with $invoice['id'] -->
    <a href="<?php echo Config::get('baseProjectFolder'); ?>/invoice/view/<?php echo $invoice['id']; ?>">
      <?php echo htmlspecialchars($invoice['invoice_number']); ?>
    </a>
    <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
    <span>Edit</span>
  </div>
</div>

<!-- FORM -->
<!-- TODO: Replace hardcoded id in action URL with $invoice['id'] -->
<form class="wrap" action="<?php echo Config::get('baseProjectFolder'); ?>/invoice/update/<?php echo $invoice['id']; ?>" method="POST">

  <!-- Spoofs PUT method the same way your delete does -->
  <input type="hidden" name="_method" value="PUT">
  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
  <input type="hidden" name="user_id"    value="<?php echo $_SESSION['user']['id'] ?? ''; ?>">
  <input type="hidden" name="subtotal"   id="h-subtotal"   value="<?php echo $invoice['subtotal']; ?>">
  <input type="hidden" name="tax_amount" id="h-tax-amt"    value="<?php echo $invoice['tax_amount']; ?>">
  <input type="hidden" name="grand_total" id="h-grand-total" value="<?php echo $invoice['grand_total']; ?>">

  <div class="page-header">
    <div class="page-header-left">
      <div class="page-title">Edit <?php echo htmlspecialchars($invoice['invoice_number']); ?></div>
      <div class="page-sub">Update invoice details below and save your changes.</div>
    </div>
  </div>

  <!-- CUSTOMER & INVOICE INFO -->
  <div class="card">
    <p class="section-title">Customer &amp; Invoice Info</p>

    <div class="payment-details">
      <p>Send payments to:</p>
      <p>0284413444</p>
      <p>WEMA BANK</p>
      <p>Oretade Olaoluwakitan</p>
    </div>

    <!-- Status field — unique to the edit form -->
    <div class="grid3" style="margin-bottom:14px">
      <div>
        <label>Invoice #</label>
        <input type="text" name="invoice_number"
          value="<?php echo htmlspecialchars($invoice['invoice_number']); ?>"
          class="<?php echo isset($errors['invoice_number']) ? 'error' : ''; ?>" />
        <?php echo Utils::fieldError($errors, 'invoice_number'); ?>
      </div>
      <div>
        <label>Date</label>
        <input type="date" name="invoice_date"
          value="<?php echo htmlspecialchars($invoice['invoice_date']); ?>"
          class="<?php echo isset($errors['invoice_date']) ? 'error' : ''; ?>" />
        <?php echo Utils::fieldError($errors, 'invoice_date'); ?>
      </div>
      <div>
        <label>Status</label>
        <select name="status" class="<?php echo isset($errors['status']) ? 'error' : ''; ?>">
          <option value="sent" <?php echo $invoice['status'] === 'sent' ? 'selected' : ''; ?>>Sent</option>
          <option value="draft" <?php echo $invoice['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
          <option value="paid"    <?php echo $invoice['status'] === 'paid'    ? 'selected' : ''; ?>>Paid</option>
          <option value="overdue" <?php echo $invoice['status'] === 'overdue' ? 'selected' : ''; ?>>Overdue</option>
        </select>
        <?php echo Utils::fieldError($errors, 'status'); ?>
      </div>
    </div>

    <div class="grid2">
      <div>
        <label>Customer name</label>
        <input type="text" name="customer_name"
          value="<?php echo htmlspecialchars($invoice['customer_name']); ?>"
          placeholder="e.g. John Doe"
          class="<?php echo isset($errors['customer_name']) ? 'error' : ''; ?>" />
        <?php echo Utils::fieldError($errors, 'customer_name'); ?>
      </div>
      <div>
        <label>Email</label>
        <input type="email" name="customer_email"
          value="<?php echo htmlspecialchars($invoice['customer_email']); ?>"
          placeholder="e.g. john.doe@example.com"
          class="<?php echo isset($errors['customer_email']) ? 'error' : ''; ?>" />
        <?php echo Utils::fieldError($errors, 'customer_email'); ?>
      </div>
    </div>
  </div>

  <!-- ITEMS -->
  <div class="card">
    <p class="section-title">Items</p>
    <?php if (!empty($errors['items_general'])): ?>
      <div class="field-error" style="margin-bottom:10px;">
        <?php echo Utils::fieldError($errors, 'items_general'); ?>
      </div>
    <?php endif; ?>

    <table id="items-table">
      <thead>
        <tr>
          <th style="width:38%">Item name</th>
          <th style="width:20%">Unit price (₦)</th>
          <th style="width:15%">Qty</th>
          <th style="width:20%">Subtotal (₦)</th>
          <th style="width:7%"></th>
        </tr>
      </thead>
      <tbody id="items-body"></tbody>
    </table>
    <button class="add-btn" type="button" onclick="addRow()">+ Add item</button>

    <div class="totals">
      <div class="total-row">
        <span>Subtotal</span>
        <span id="subtotal">₦<?php echo number_format($invoice['subtotal'], 2); ?></span>
      </div>
      <div class="total-row">
        <span>Tax <input type="number" id="tax-rate" name="tax_rate" value="<?php echo $invoice['tax_rate']; ?>" min="0" max="100" oninput="recalc()"> %</span>
        <span id="tax-amt">₦<?php echo number_format($invoice['tax_amount'], 2); ?></span>
      </div>
      <div class="total-row">
        <span>Discount <input type="number" id="discount" name="discount" value="<?php echo $invoice['discount']; ?>" min="0" oninput="recalc()"> ₦</span>
        <span id="discount-amt">-₦<?php echo number_format($invoice['discount'], 2); ?></span>
      </div>
      <div class="total-row grand">
        <span>Total</span>
        <span id="grand-total">₦<?php echo number_format($invoice['grand_total'], 2); ?></span>
      </div>
    </div>
  </div>

  <!-- NOTES -->
  <div class="card">
    <p class="section-title">Notes</p>
    <textarea name="notes" rows="3" placeholder="e.g. Thank you for your purchase!"><?php echo htmlspecialchars($invoice['notes']); ?></textarea>
  </div>

  <!-- FORM ACTIONS -->
  <div class="actions">
    <div>
      <!-- Delete button triggers the same delete modal pattern from dashboard -->
      <button type="button" class="btn-danger-outline" onclick="openDeleteInvoice()">Delete Invoice</button>
    </div>
    <div class="actions-right">
      <!-- TODO: Replace hardcoded id with $invoice['id'] -->
      <a class="btn-outline" href="<?php echo Config::get('baseProjectFolder'); ?>/invoice/view/<?php echo $invoice['id']; ?>">Discard changes</a>
      <button class="btn-primary" type="submit">Save changes</button>
    </div>
  </div>

</form>

<!-- DELETE INVOICE MODAL -->
<div class="modal-backdrop" id="delete-invoice-modal">
  <div class="modal">
    <h3>Delete invoice?</h3>
    <p>This will permanently remove <strong><?php echo htmlspecialchars($invoice['invoice_number']); ?></strong>. This cannot be undone.</p>
    <div class="modal-actions">
      <button class="btn-outline" onclick="closeModal('delete-invoice-modal')">Cancel</button>
      <!-- TODO: Replace hardcoded id with $invoice['id'] -->
      <form action="<?php echo Config::get('baseProjectFolder'); ?>/invoice/delete/<?php echo $invoice['id']; ?>" method="POST">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button class="btn-danger" type="submit">Yes, delete</button>
      </form>
    </div>
  </div>
</div>

<!-- LOGOUT MODAL -->
<div class="modal-backdrop" id="logout-modal">
  <div class="modal">
    <h3>Log out?</h3>
    <p>You'll be returned to the login page. Any unsaved changes will be lost.</p>
    <div class="modal-actions">
      <button class="btn-outline" onclick="closeModal('logout-modal')">Cancel</button>
      <button class="btn-danger" onclick="window.location.href='<?php echo Config::get('baseProjectFolder'); ?>/logout'">Yes, log out</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
  let rowId = 0;

  // Pre-populated items from PHP — injected as JSON for the JS to render
  const preloadedItems = <?php echo json_encode(array_values($items)); ?>;
  const itemErrors     = <?php echo json_encode((array)($errors['items'] ?? [])); ?>;

  function addRow(name = '', price = '', qty = 1, errors = {}) {
    const id = rowId++;
    const tr = document.createElement('tr');
    tr.id = 'row-' + id;
    tr.innerHTML = `
      <td>
        <input type="text" name="items[${id}][description]" value="${escHtml(name)}" placeholder="Item name" oninput="recalc()" />
        ${errors.description ? `<span class="field-error">${errors.description[0]}</span>` : ''}
      </td>
      <td>
        <input type="number" name="items[${id}][price]" value="${price}" placeholder="0.00" min="0" step="0.01" oninput="recalc()" />
        ${errors.price ? `<span class="field-error">${errors.price[0]}</span>` : ''}
      </td>
      <td>
        <input type="number" name="items[${id}][quantity]" value="${qty}" min="1" step="1" oninput="recalc()" />
        ${errors.quantity ? `<span class="field-error">${errors.quantity[0]}</span>` : ''}
      </td>
      <td id="sub-${id}" style="font-weight:500">₦0.00</td>
      <td><button class="remove-btn" type="button" onclick="removeRow(${id})">×</button></td>
    `;
    document.getElementById('items-body').appendChild(tr);
    recalc();
  }

  function removeRow(id) {
    const el = document.getElementById('row-' + id);
    if (el) el.remove();
    recalc();
  }

  function fmt(n) {
    return '₦' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  }

  function escHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
  }

  function recalc() {
    const rows = document.querySelectorAll('#items-body tr');
    let subtotal = 0;
    rows.forEach(tr => {
      const inputs = tr.querySelectorAll('input[type=number]');
      const price = parseFloat(inputs[0].value) || 0;
      const qty   = parseInt(inputs[1].value)   || 0;
      const sub   = price * qty;
      subtotal += sub;
      const subCell = tr.querySelector('td:nth-child(4)');
      if (subCell) subCell.textContent = fmt(sub);
    });
    const taxRate  = parseFloat(document.getElementById('tax-rate').value) || 0;
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const taxAmt   = subtotal * (taxRate / 100);
    const grand    = Math.max(0, subtotal + taxAmt - discount);

    document.getElementById('subtotal').textContent      = fmt(subtotal);
    document.getElementById('tax-amt').textContent       = fmt(taxAmt);
    document.getElementById('discount-amt').textContent  = '-' + fmt(discount);
    document.getElementById('grand-total').textContent   = fmt(grand);

    document.getElementById('h-subtotal').value    = subtotal.toFixed(2);
    document.getElementById('h-tax-amt').value     = taxAmt.toFixed(2);
    document.getElementById('h-grand-total').value = grand.toFixed(2);
  }

  function openLogout()        { document.getElementById('logout-modal').classList.add('open'); }
  function openDeleteInvoice() { document.getElementById('delete-invoice-modal').classList.add('open'); }
  function closeModal(id)      { document.getElementById(id).classList.remove('open'); }

  function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg; t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 2500);
  }

  document.querySelectorAll('.modal-backdrop').forEach(b => {
    b.addEventListener('click', e => { if (e.target === b) b.classList.remove('open'); });
  });

  document.getElementById('discount').addEventListener('blur', function() { if (this.value === '') this.value = 0; });
  document.getElementById('tax-rate').addEventListener('blur', function() { if (this.value === '') this.value = 0; });

  // Render preloaded items on page load
  preloadedItems.forEach((item, index) => {
    addRow(item.description, item.price, item.quantity, itemErrors[index] ?? {});
  });

  <?php if (isset($_SESSION['message'])): ?>
    showToast("<?php echo $_SESSION['message']; ?>");
    <?php unset($_SESSION['message']); ?>
  <?php endif; ?>
</script>
</body>
</html>
