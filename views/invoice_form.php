<?php 
declare(strict_types=1);
use App\Config;
use App\Utils;

/** @var string $nextInvoiceNumber */
/** @var array $errors */
/** @var array $old */

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Invoice — InvoiceManager</title>
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

  /* aliases used by the form template */
  --color-background-primary: var(--color-surface);
  --color-background-secondary: #f1f5f9;
  --color-border-tertiary: var(--color-border);
  --color-border-secondary: var(--color-border-mid);
  --border-radius-md: var(--radius-md);
  --border-radius-lg: var(--radius-lg);
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

button.nav-link{
  background:none;border:none;color:var(--color-text-secondary);font-size:13.5px;font-weight:500;
  padding:7px 14px;border-radius:var(--radius-md);transition:background 0.12s,color 0.12s;cursor:pointer;
}
/* ── BREADCRUMB ── */
.breadcrumb{display:flex;align-items:center;gap:6px;font-size:13px;color:var(--color-text-secondary);margin-bottom:1rem;}
.breadcrumb a{color:var(--color-text-secondary);text-decoration:none;}
.breadcrumb a:hover{color:var(--color-text-primary);text-decoration:underline;}
.breadcrumb svg{width:12px;height:12px;stroke:var(--color-border-mid);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}

/* ── PAGE WRAPPER ── */
.wrap{max-width:860px;margin:2rem auto;padding:0 1rem;}

/* ── PAGE HEADER ── */
.page-header{margin-bottom:1.5rem;}
.page-title{font-size:19px;font-weight:600;color:var(--color-text-primary);letter-spacing:-0.3px;}
.page-sub{font-size:13px;color:var(--color-text-secondary);margin-top:3px;}

/* ── CARD ── */
.card{
  background:var(--color-surface);
  border:1px solid var(--color-border);
  border-radius:var(--radius-lg);
  padding:1.5rem;
  margin-bottom:1.1rem;
  box-shadow:0 1px 3px rgba(0,0,0,0.04);
}

/* ── SECTION TITLE ── */
.section-title{
  font-size:11px;font-weight:600;color:var(--color-text-secondary);
  text-transform:uppercase;letter-spacing:0.5px;margin-bottom:16px;
}

/* ── PAYMENT DETAILS ── */
.payment-details{
  background:#f8fafc;
  border:1px solid var(--color-border);
  border-radius:var(--radius-md);
  padding:14px 18px;
  margin-top:28px;
  margin-bottom:18px;
  position:relative;
}
.payment-details::before{
  content:"INVOICE FROM";
  position:absolute;top:-11px;left:12px;
  background:var(--color-surface);
  padding:0 6px;
  font-size:11px;font-weight:600;color:var(--color-text-secondary);letter-spacing:0.5px;
}
.payment-details p{margin:5px 0;font-size:14px;line-height:1.5;color:var(--color-text-secondary);}
.payment-details p:nth-child(2){font-size:17px;font-weight:600;color:var(--color-text-primary);letter-spacing:0.2px;}


/* ── GRIDS ── */
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.grid3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;}



/* ── FORM ELEMENTS ── */
label{font-size:13px;color:var(--color-text-secondary);display:block;margin-bottom:5px;font-weight:500;}

input, select, textarea{
  width:100%;
  padding:9px 12px;
  border:1px solid var(--color-border);
  border-radius:var(--radius-md);
  font-size:13.5px;
  font-family:var(--font-sans);
  background:var(--color-surface);
  color:var(--color-text-primary);
  transition:border-color 0.15s, box-shadow 0.15s;
}
input:focus, select:focus, textarea:focus{
  outline:none;
  border-color:var(--color-blue);
  box-shadow:0 0 0 3px var(--color-blue-soft);
}

/* ── FIELD ERRORS ── */
.field-error{color:#ef4444;font-size:12px;margin-top:4px;display:block;}
input.error, textarea.error{border-color:#ef4444;}
input.error:focus, textarea.error:focus{box-shadow:0 0 0 3px rgba(239,68,68,0.15);}

/* ── TABLE ── */
table{width:100%;border-collapse:collapse;font-size:13.5px;margin-bottom:10px;}
th{
  font-size:11.5px;color:var(--color-text-secondary);font-weight:600;
  padding:10px 10px;text-align:left;
  border-bottom:1.5px solid var(--color-border);
  background:#f8fafc;text-transform:uppercase;letter-spacing:0.4px;
}
td{padding:10px 10px;border-bottom:1px solid var(--color-border);vertical-align:middle;}
tbody tr:last-child td{border-bottom:none;}
tbody tr:hover{background:#f8fafc;}
td input{
  border:1px solid var(--color-border);
  padding:7px 10px;
  font-size:13.5px;
  font-family:var(--font-sans);
  transition:border-color 0.15s,box-shadow 0.15s;
}
td input:focus{border-color:var(--color-blue);box-shadow:0 0 0 3px var(--color-blue-soft);}

/* ── BUTTONS ── */
.add-btn{
  background:none;border:1px solid var(--color-border-mid);
  border-radius:var(--radius-md);padding:8px 16px;
  font-size:13px;font-family:var(--font-sans);cursor:pointer;
  color:var(--color-text-primary);margin-top:8px;
  transition:background 0.12s;
}
.add-btn:hover{background:#f1f5f9;}

.remove-btn{
  background:none;border:1px solid var(--color-border);border-radius:6px;
  color:var(--color-text-secondary);cursor:pointer;
  font-size:13px;padding:5px 8px;
  display:flex;align-items:center;
  transition:background 0.12s,border-color 0.12s,color 0.12s;
}
.remove-btn:hover{background:#fef2f2;border-color:#fca5a5;color:#ef4444;}

/* ── TOTALS ── */
.totals{
  margin-left:auto;width:300px;margin-top:1.5rem;
  background:#f8fafc;padding:16px;
  border-radius:var(--radius-lg);border:1px solid var(--color-border);
}
.total-row{display:flex;justify-content:space-between;font-size:13.5px;padding:7px 0;color:var(--color-text-secondary);}
.total-row.grand{
  font-size:16px;font-weight:600;color:var(--color-text-primary);
  border-top:2px solid var(--color-border);margin-top:8px;padding-top:12px;
}

/* ── MODAL ── */
.modal-backdrop{display:none;position:fixed;inset:0;background:rgba(15,23,42,0.4);z-index:300;align-items:center;justify-content:center;padding:1rem;}
.modal-backdrop.open{display:flex;}
.modal{background:var(--color-surface);border-radius:var(--radius-lg);padding:1.5rem;width:100%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,0.2);}
.modal h3{font-size:16px;font-weight:600;margin-bottom:8px;}
.modal p{font-size:13.5px;color:var(--color-text-secondary);margin-bottom:18px;line-height:1.6;}
.modal-actions{display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;}
.btn-danger{background:#dc2626;border:none;border-radius:var(--radius-md);padding:9px 18px;font-size:13.5px;font-family:var(--font-sans);cursor:pointer;color:white;font-weight:500;transition:background 0.15s;}
.btn-danger:hover{background:#b91c1c;}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 13px;
    margin-bottom: 13px;
}

.form-grid .field.full {
    grid-column: 1 / -1;
}

@media(max-width:640px) {
    .form-grid { grid-template-columns: 1fr; }
    .form-grid .field.full { grid-column: 1; }
}

#guest-business-section {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--color-border);
}

#guest-business-section .section-title {
    margin-top: 16px;
    margin-bottom: 12px;
}

#guest-business-section .section-title:first-child {
    margin-top: 0;
}

#business-toggle {
    margin-top: 0;
    margin-bottom: 0;
}

#guest-business-card {
    padding-top: 1rem;
}

/* inline number inputs inside totals */
.totals input[type=number]{
  width:44px;border:1px solid var(--color-border);border-radius:4px;
  padding:2px 5px;font-size:12px;font-family:var(--font-sans);
  background:var(--color-surface);color:var(--color-text-primary);
  text-align:center;
}
.totals input[type=number]:focus{border-color:var(--color-blue);box-shadow:0 0 0 2px var(--color-blue-soft);}
#discount-input{width:60px;}

/* ── ACTIONS ── */
.actions{display:flex;gap:10px;justify-content:flex-end;margin-top:1.25rem;}
.btn-outline{
  background:none;border:1px solid var(--color-border-mid);
  border-radius:var(--radius-md);padding:9px 20px;
  font-size:13.5px;font-family:var(--font-sans);cursor:pointer;
  color:var(--color-text-primary);transition:background 0.15s;white-space:nowrap;
}
.btn-outline:hover{background:#f1f5f9;}
.btn-primary{
  background:var(--color-accent);border:none;
  border-radius:var(--radius-md);padding:9px 22px;
  font-size:13.5px;font-family:var(--font-sans);cursor:pointer;
  color:white;font-weight:500;transition:background 0.15s;white-space:nowrap;
}
.btn-primary:hover{background:var(--color-accent-hover);}

/* ── TOAST ── */
.toast{
  display:none;position:fixed;bottom:20px;right:20px;left:20px;
  background:var(--color-accent);color:white;padding:12px 18px;
  border-radius:var(--radius-md);font-size:13.5px;
  box-shadow:0 10px 15px -3px rgba(0,0,0,0.15);z-index:999;
  font-family:var(--font-sans);text-align:center;
}

/* ── RESPONSIVE ── */
@media(max-width:640px){
  .grid2,.grid3{grid-template-columns:1fr;}
  .totals{width:100%;}
  .actions{flex-direction:column;}
  .btn-outline,.btn-primary{width:100%;justify-content:center;text-align:center;}
  .topnav{padding:0 1rem;}
}

/* ── PRINT ── */
@media print{
  .topnav,.actions,.page-header,.breadcrumb, .add-btn,.remove-btn,.toast{display:none!important;}
  body{background:white;}
  .card{box-shadow:none;border:1px solid #ccc;}
  .payment-details{background:white;border:1px dashed #999;}
}
  </style>
</head>
<body>

<!-- TOP NAV -->
<nav class="topnav">
  <a class="brand" href="<?php echo Config::get('baseProjectFolder'); ?>/dashboard">
    <div class="brand-logo">
      <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
    </div>
    <span class="brand-name">InvoiceManager</span>
  </a>
  <div class="nav-right">
    <?php if (isset($_SESSION['user'])): ?>
      <a class="nav-link" href="<?php echo Config::get('baseProjectFolder'); ?>/dashboard">Dashboard</a>
      <button class="nav-link danger logout" type="button" onclick="openLogout()">Logout</button>
      <!-- <a class="nav-link danger logout" href="<?php echo Config::get('baseProjectFolder'); ?>/logout">Logout</a> -->
    <?php else: ?>
      <a class="nav-link" href="<?php echo Config::get('baseProjectFolder'); ?>/login">Log in</a>
      <a class="nav-link" href="<?php echo Config::get('baseProjectFolder'); ?>/signup">Sign up</a>
    <?php endif; ?>
  </div>
</nav>

<?php if(isset($_SESSION['user'])): ?>
 <div class="wrap" style="margin-bottom:0; padding-bottom:0;">
      <div class="breadcrumb">
      <a href="<?php echo Config::get('baseProjectFolder'); ?>/dashboard">Dashboard</a>
      <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
      <span>New Invoice</span>
      </div>
  </div>
<?php endif; ?>

<form class="wrap" action="<?php echo Config::get('baseProjectFolder'); ?>/invoice" method="post">

  <div class="page-header">
    <div class="page-title">New Invoice</div>
    <div class="page-sub">Fill in the details below to create and save your invoice.</div>
  </div>

  <!-- CUSTOMER & INVOICE INFO -->
  <div class="card">
    <p class="section-title">Customer &amp; Invoice Info</p>


  <?php if(isset($_SESSION['user'])): ?>
      <div class="payment-details">
          <?php if($_SESSION['user']['has_business_details']): ?>
              
              <p style="font-size:11px;font-weight:600;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">From</p>

              <?php if(!empty($_SESSION['user']['business_name'])): ?>
                  <p><?php echo ucwords(htmlspecialchars((string)$_SESSION['user']['business_name'])); ?></p>
              <?php endif; ?>
              <?php if(!empty($_SESSION['user']['business_email'])): ?>
                  <p style="font-size:14px;"><?php echo htmlspecialchars((string)$_SESSION['user']['business_email']); ?></p>
              <?php endif; ?>
              <?php if(!empty($_SESSION['user']['business_phone'])): ?>
                  <p style="font-size:14px;"><?php echo htmlspecialchars((string)$_SESSION['user']['business_phone']); ?></p>
              <?php endif; ?>
              <?php if(!empty($_SESSION['user']['business_address'])): ?>
                  <p style="font-size:14px;"><?php echo ucwords(htmlspecialchars((string)$_SESSION['user']['business_address'])); ?></p>
              <?php endif; ?>

              <hr style="border:none;border-top:1px solid var(--color-border);margin:10px 0;">

              <p style="font-size:11px;font-weight:600;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Send payment to</p>
              <p><?php echo htmlspecialchars((string)($_SESSION['user']['bank_account_number'] ?? '')); ?></p>
              <p><?php echo strtoupper(htmlspecialchars((string)($_SESSION['user']['bank_name'] ?? ''))); ?></p>
              <p><?php echo ucwords(htmlspecialchars((string)($_SESSION['user']['bank_account_name'] ?? ''))); ?></p>

          <?php else: ?>
              <div style="display:flex;align-items:flex-start;gap:8px;padding:12px 14px;border:1px solid var(--color-border);border-radius:var(--radius-md);margin-top:4px;">
                  <svg style="width:14px;height:14px;stroke:var(--color-text-secondary);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;flex-shrink:0;margin-top:2px;" viewBox="0 0 24 24">
                      <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                  </svg>
                  <p style="font-size:13px;color:var(--color-text-secondary);line-height:1.5;margin:0;">
                      Business details not set up yet.
                      <a href="<?php echo Config::get('baseProjectFolder'); ?>/dashboard?section=profile#business-info-section" style="color:var(--color-text-primary);font-weight:500;text-decoration:underline;text-underline-offset:2px;">Set them up in your profile →</a>
                  </p>
              </div>
          <?php endif; ?>
      </div>
    <?php endif; ?>
    <?php if (!isset($_SESSION['user'])): ?>  <!--IF USER IS NOT LOGGED IN -->
      <div class="card">
          <button type="button" class="add-btn" onclick="toggleBusinessSection()" id="business-toggle">
              + Add your business details
          </button>
          <div id="guest-business-section" style="display:none; margin-top:16px;">
            <p class="section-title">Business Details</p>
            <div class="grid2" style="margin-bottom:14px">
                <div>
                    <label>Business name</label>
                    <input type="text" id="g-business-name" name="business_name"
                      value="<?php echo Utils::old($old, 'business_name', ''); ?>"
                      placeholder="e.g. Acme Ltd"
                      class="<?php echo isset($errors['business_name']) ? 'error' : ''; ?>"
                      oninput="saveBusinessToStorage()" required/>
                    <?php echo Utils::fieldError($errors,'public', null, 'business_name'); ?>
                </div>
                <div>
                    <label>Business email</label>
                    <input type="email" id="g-business-email" name="business_email"
                      value="<?php echo Utils::old($old, 'business_email', ''); ?>"
                      placeholder="e.g. info@acme.com"
                      class="<?php echo isset($errors['business_email']) ? 'error' : ''; ?>"
                      oninput="saveBusinessToStorage()" required/>
                    <?php echo Utils::fieldError($errors, 'public', null, 'business_email'); ?>
                </div>
                <div>
                    <label>Business phone (optional) </label>
                    <input type="tel" id="g-business-phone" name="business_phone" inputmode="numeric"
                      value="<?php echo Utils::old($old, 'business_phone', ''); ?>"
                      placeholder="e.g. 08012345678"
                      class="<?php echo isset($errors['business_phone']) ? 'error' : ''; ?>"
                      oninput="saveBusinessToStorage()"/>
                    <?php echo Utils::fieldError($errors, 'public', null, 'business_phone'); ?>
                </div>
                <div>
                    <label>Business address (optional)</label>
                    <input type="text" id="g-business-address" name="business_address"
                      value="<?php echo Utils::old($old, 'business_address', ''); ?>"
                      placeholder="e.g. 12 Marina Street, Lagos"
                      class="<?php echo isset($errors['business_address']) ? 'error' : ''; ?>"
                      oninput="saveBusinessToStorage()"/>
                    <?php echo Utils::fieldError($errors, 'public', null, 'business_address'); ?>
                </div>
            </div>
            <p class="section-title">Bank Details</p>
            <div class="grid2">
                <div>
                    <label>Account number</label>
                    <input type="tel" id="g-bank-account-number" name="bank_account_number" inputmode="numeric"
                      value="<?php echo Utils::old($old, 'bank_account_number', ''); ?>"
                      placeholder="e.g. 0123456789"
                      class="<?php echo isset($errors['bank_account_number']) ? 'error' : ''; ?>"
                      oninput="saveBusinessToStorage()" required/>
                    <?php echo Utils::fieldError($errors, 'public', null, 'bank_account_number'); ?>
                </div>
                <div>
                    <label>Account name</label>
                    <input type="text" id="g-bank-account-name" name="bank_account_name"
                      value="<?php echo Utils::old($old, 'bank_account_name', ''); ?>"
                      placeholder="e.g. Acme Ltd"
                      class="<?php echo isset($errors['bank_account_name']) ? 'error' : ''; ?>"
                      oninput="saveBusinessToStorage()" required/>
                    <?php echo Utils::fieldError($errors, 'public', null, 'bank_account_name'); ?>
                </div>
                <div>
                    <label>Bank name</label>
                    <input type="text" id="g-bank-name" name="bank_name"
                      value="<?php echo Utils::old($old, 'bank_name', ''); ?>"
                      placeholder="e.g. First Bank"
                      class="<?php echo isset($errors['bank_name']) ? 'error' : ''; ?>"
                      oninput="saveBusinessToStorage()" required/>
                    <?php echo Utils::fieldError($errors, 'public', null, 'bank_name'); ?>
                </div>
              </div>
          </div>  
        </div>
    <?php endif; ?>

    <div class="grid3" style="margin-bottom:14px">
      <div>
        <label>Invoice #</label>
        <input type="text" id="inv-num" name="invoice_number"
          value="<?php echo Utils::old($old, 'invoice_number', $nextInvoiceNumber); ?>"
          class="<?php echo isset($errors['invoice_number']) ? 'error' : ''; ?>" />
        <?php echo Utils::fieldError($errors, 'invoice_number'); ?>
      </div>
      <div>
        <label>Date</label>
        <input type="date" id="inv-date" name="invoice_date"
          value="<?php echo Utils::old($old, 'invoice_date'); ?>"
          class="<?php echo isset($errors['invoice_date']) ? 'error' : ''; ?>" required/>
        <?php echo Utils::fieldError($errors, 'invoice_date'); ?>
      </div>
    </div>

    <div class="grid2">
      <div>
        <label>Customer name</label>
        <input type="text" id="cust-name" name="customer_name"
          value="<?php echo Utils::old($old, 'customer_name'); ?>"
          placeholder="e.g. John Doe"
          class="<?php echo isset($errors['customer_name']) ? 'error' : ''; ?>" required/>
        <?php echo Utils::fieldError($errors, 'customer_name'); ?>
      </div>
      <div>
        <label>Email</label>
        <input type="email" id="cust-contact" name="customer_email"
          value="<?php echo Utils::old($old, 'customer_email'); ?>"
          placeholder="e.g. john.doe@example.com"
          class="<?php echo isset($errors['customer_email']) ? 'error' : ''; ?>" required/>
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
      <div class="total-row"><span>Subtotal</span><span id="subtotal">₦0.00</span></div>
      <div class="total-row">
        <span>Tax <input type="number" id="tax-rate" name="tax_rate" step="0.1" value="<?php echo Utils::old($old, 'tax_rate', '0'); ?>" min="0" max="100" oninput="recalc()"> %</span>
        <span id="tax-amt">₦0.00</span>
      </div>
      <div class="total-row">
        <span>Discount <input type="number" id="discount" name="discount" id="discount-input" value="<?php echo Utils::old($old, 'discount', '0'); ?>" min="0" oninput="recalc()"> ₦</span>
        <span id="discount-amt">₦0.00</span>
      </div>
      <div class="total-row grand"><span>Total</span><span id="grand-total">₦0.00</span></div>
    </div>
  </div>

  <!-- NOTES -->
  <div class="card">
    <p class="section-title">Notes</p>
    <textarea id="notes" name="notes" rows="3" placeholder="e.g. Thank you for your purchase!"><?php echo Utils::old($old, 'notes'); ?></textarea>
  </div>

  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
  <input type="hidden" name="user_id" value="<?php echo $_SESSION['user']['id'] ?? ''; ?>" />
  <input type="hidden" name="subtotal" id="h-subtotal" value="0">
  <input type="hidden" name="tax_amount" id="h-tax-amt" value="0">
  <input type="hidden" name="grand_total" id="h-grand-total" value="0">
  <input type="hidden" name="source" id="h-source" value="<?php echo isset($_SESSION['user']) ? 'user' : 'public'; ?>">
  <input type="hidden" name="business_section_visible" id="h-business-visible" value="false">

  <div class="actions">
    <!-- <button type="button" class="btn-outline" onclick="window.print()">Print / Export PDF</button> -->
     <button type="submit" formaction="<?php echo Config::get('baseProjectFolder'); ?>/invoice/pdf-public" formtarget="_blank" class="btn-outline">
        Print / Export PDF
    </button>
    <?php if (isset($_SESSION['user'])): ?>
      <button class="btn-primary" type="submit">Save Invoice</button>
    <?php else: ?>

      <button class="btn-primary" type="submit">Log in to save invoice</button>
      <?php $_SESSION['old'] = $_POST; // Preserve form data in session to repopulate after redirecting to login page ?>
      <?php $_SESSION['previous_page'] = "invoice_form"; ?>
    <?php endif; ?>
  </div>

</form>

<!-- MODALS -->
<div class="modal-backdrop" id="logout-modal">
  <div class="modal"><h3>Log out?</h3><p>You'll be returned to the login page. Any unsaved changes will be lost.</p>
  <div class="modal-actions"><button class="btn-outline" onclick="closeModal('logout-modal')">Cancel</button><button class="btn-danger" onclick="doLogout(); window.location.href='<?php echo Config::get('baseProjectFolder'); ?>/logout'">Yes, log out</button></div></div>
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
          <label>Business address</label>
          <input type="text" name="business_address" placeholder="e.g. 12 Marina Street, Lagos"
            value="<?php echo Utils::old($old, 'business_address', ''); ?>"
            class="<?php echo isset($errors['business_address']) ? 'error' : ''; ?>" />
          <?php echo Utils::fieldError($errors, 'modal', null, 'business_address'); ?>
        </div>

        <div class="field full <?php echo isset($errors['bank_account_name']) ? 'error' : ''; ?>">
          <label>Account name</label>
          <input type="text" name="bank_account_name" placeholder="e.g. Acme Ltd"
            value="<?php echo Utils::old($old, 'bank_account_name', ''); ?>"
            class="<?php echo isset($errors['bank_account_name']) ? 'error' : ''; ?>"/>
          <?php echo Utils::fieldError($errors, 'modal', null, 'bank_account_name'); ?>
        </div>

        <div class="field <?php echo isset($errors['bank_account_number']) ? 'error' : ''; ?>">
          <label>Account number</label>
          <input type="tel" name="bank_account_number" inputmode="numeric" placeholder="e.g. 0123456789"
            value="<?php echo Utils::old($old, 'bank_account_number', ''); ?>"
            class="<?php echo isset($errors['bank_account_number']) ? 'error' : ''; ?>"/>
          <?php echo Utils::fieldError($errors, 'modal', null, 'bank_account_number'); ?>
        </div>

        <div class="field <?php echo isset($errors['bank_name']) ? 'error' : ''; ?>">
          <label>Bank name</label>
          <input type="text" name="bank_name" placeholder="e.g. First Bank"
            value="<?php echo Utils::old($old, 'bank_name', ''); ?>"
            class="<?php echo isset($errors['bank_name']) ? 'error' : ''; ?>"/>
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

  console.log("here");
  document.getElementById('discount').addEventListener('blur', function() {
    if (this.value === '') this.value = 0;
  });

  document.getElementById('tax-rate').addEventListener('blur', function() {
    if (this.value === '') this.value = 0;
  });

  let rowId = 0;

  function addRow(name='', price='', qty=1, errors={}) {
    const id = rowId++;
    const tr = document.createElement('tr');
    tr.id = 'row-' + id;
    tr.innerHTML = `
      <td>
        <input type="text" name="items[${id}][description]" value="${name}" placeholder="Item name" oninput="recalc()" style="width:100%" required/>
        ${errors.description ? `<span class="field-error">${errors.description[0]}</span>` : ''}
      </td>
      <td>
        <input type="number" name="items[${id}][price]" value="${price}" placeholder="0.00" min="0" step="0.1" oninput="recalc()" style="width:100%" required/>
        ${errors.price ? `<span class="field-error">${errors.price[0]}</span>` : ''}
      </td>
      <td>
        <input type="number" name="items[${id}][quantity]" value="${qty}" min="1" step="1" oninput="recalc()" style="width:100%" required/>
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

  function recalc() {
    const rows = document.querySelectorAll('#items-body tr');
    let subtotal = 0;
    rows.forEach((tr, i) => {
      const inputs = tr.querySelectorAll('input');
      const price = parseFloat(inputs[1].value) || 0;
      const qty = parseInt(inputs[2].value) || 0;
      const sub = price * qty;
      subtotal += sub;
      const subCell = tr.querySelector('td:nth-child(4)');
      if (subCell) subCell.textContent = fmt(sub);
    });
    const taxRate = parseFloat(document.getElementById('tax-rate').value) || 0;
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const taxAmt = subtotal * (taxRate / 100);
    const grand = Math.max(0, subtotal + taxAmt - discount);
    document.getElementById('subtotal').textContent = fmt(subtotal);
    document.getElementById('tax-amt').textContent = fmt(taxAmt);
    document.getElementById('discount-amt').textContent = '-' + fmt(discount);
    document.getElementById('grand-total').textContent = fmt(grand);
    document.getElementById('h-subtotal').value = subtotal.toFixed(2);
    document.getElementById('h-tax-amt').value = taxAmt.toFixed(2);
    document.getElementById('h-grand-total').value = grand.toFixed(2);
  }

  function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 2500);
  }

  function openLogout(){
    document.getElementById('logout-modal').classList.add('open');
  }

  function closeModal(id){
    document.getElementById(id).classList.remove('open');
  }

  function doLogout(){
    closeModal('logout-modal');showToast('Logged out. Redirecting…');
  }

  const today = new Date().toISOString().split('T')[0];
  <?php if (empty($old['invoice_date'])): ?>
    document.getElementById('inv-date').value = new Date().toISOString().split('T')[0];
  <?php endif; ?>

  <?php
  $itemsJson  = json_encode(array_values((array)($old['items'] ?? [])));
  $errorsJson = json_encode((array)($errors['items'] ?? []));
  ?>

  <?php if (!empty($old['items'])): ?>
    const oldItems = <?php echo $itemsJson; ?>;
    // console.log("here", oldItems);
    const itemErrors = <?php echo $errorsJson; ?>;
    oldItems.forEach((item, index) => addRow(item.description, item.price, item.quantity, itemErrors[index] ?? {}));
    recalc();
  <?php else: ?>
    // addRow('', '', 1);
  <?php endif; ?>

 function toggleBusinessSection() {
    const section = document.getElementById('guest-business-section');
    const btn = document.getElementById('business-toggle');
    const isHidden = section.style.display === 'none';
    section.style.display = isHidden ? 'block' : 'none';
    btn.textContent = isHidden ? '− Hide business details' : '+ Add your business details';
    document.getElementById('h-business-visible').value = isHidden ? 'true' : 'false';
}

function saveBusinessToStorage() {
    const data = {
        business_name:       document.getElementById('g-business-name').value,
        business_email:      document.getElementById('g-business-email').value,
        business_phone:      document.getElementById('g-business-phone').value,
        business_address:    document.getElementById('g-business-address').value,
        bank_account_number: document.getElementById('g-bank-account-number').value,
        bank_account_name:   document.getElementById('g-bank-account-name').value,
        bank_name:           document.getElementById('g-bank-name').value,
    };
    localStorage.setItem('invoiceManager_business', JSON.stringify(data));
}

(function prefillGuestBusiness() {
    const saved = localStorage.getItem('invoiceManager_business');
    if (!saved) return;

    const data = JSON.parse(saved);
    const fields = {
        'g-business-name':       data.business_name,
        'g-business-email':      data.business_email,
        'g-business-phone':      data.business_phone,
        'g-business-address':    data.business_address,
        'g-bank-account-number': data.bank_account_number,
        'g-bank-account-name':   data.bank_account_name,  
        'g-bank-name':           data.bank_name,
    };

    let hasData = false;
    for (const [id, value] of Object.entries(fields)) {
        const el = document.getElementById(id);
        if (!el) continue;

        if (el.value) {
            // Already populated server-side (old() after a validation error) — don't overwrite.
            hasData = true;
            continue;
        }
        if (value) {
            el.value = value;
            hasData = true;
        }
    }

    // Auto-expand the section if there's saved data
    if (hasData) {
        document.getElementById('guest-business-section').style.display = 'block';
        document.getElementById('business-toggle').textContent = '− Hide business details';
        document.getElementById('h-business-visible').value = 'true';
    }
})();

// ── BUSINESS SETUP MODAL ──
<?php if (isset($_SESSION['user'])) : ?>
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
<?php endif; ?>
</script>

<?php if (isset($_SESSION['message'])): ?>
  <script>
    showToast("<?php echo $_SESSION['message']; ?>");
  </script>
  <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<?php if(!empty($errors['public'])): ?>
  <script>
    toggleBusinessSection();
  </script>
<?php endif; ?>
</body>
</html>
