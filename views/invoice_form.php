<?php 
declare(strict_types=1);
require_once "./autoloader.php";
require_once "./utils.php";
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
// var_dump($config);
// $config = new Config(); // Create a new instance of the Config class to access configuration settings
$_SESSION['csrf_token'] = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
// Get the next invoice number for the logged in user
$nextInvoiceNumber = 'INV-001'; // fallback default

if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];
    $stmt = DBH::getConnection()->prepare("SELECT COUNT(*) FROM invoices WHERE user_id = ?");
    $stmt->execute([$userId]);
    $count = $stmt->fetchColumn();
    $nextInvoiceNumber = 'INV-' . sprintf('%03d', $count + 1);
}

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

// // Helper to get old input value with fallback
// function old(array $old, string $key, string $fallback = ''): string {
//     return htmlspecialchars($old[$key] ?? $fallback);
// }

// // Helper to display field error
// function fieldError(array $errors, string $key, ?int $index = null, ?string $subKey = null): string {
//     if ($index !== null && $subKey !== null) {
//         $message = $errors[$key][$index][$subKey][0] ?? null;
//     } else {
//         $message = $errors[$key][0] ?? null;
//     }

//     if ($message) {
//         return '<span class="field-error">' . htmlspecialchars($message) . '</span>';
//     }
//     return '';
// }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?php echo Config::get('baseProjectFolder'); ?>/public/index.css">
  <title>Document</title>
</head>
<body>

<form class="wrap" action="<?php echo Config::get('baseProjectFolder'); ?>/invoice" method="post">

  <div class="card">
    <p class="section-title">Customer & invoice info</p>
    
    <div class="payment-details">
      <p>Send payments to:</p>
      <p>0284413444</p>
      <p>WEMA BANK</p>
      <p>Oretade Olaoluwakitan</p>
    </div>
  
    <br>
  
    <div class="grid3" style="margin-bottom:12px">
       <div>
          <label>Invoice #</label>
            <input type="text" id="inv-num" name="invoice_number" 
              value="<?php echo old($old, 'invoice_number', $nextInvoiceNumber); ?>"
              class="<?php echo isset($errors['invoice_number']) ? 'error' : ''; ?>" />
              <?php echo fieldError($errors, 'invoice_number'); ?>
        </div>
        <div>
          <label>Date</label>
          <input type="date" id="inv-date" name="invoice_date"
            value="<?php echo old($old, 'invoice_date'); ?>"
            class="<?php echo isset($errors['invoice_date']) ? 'error' : ''; ?>" />
            <?php echo fieldError($errors, 'invoice_date'); ?>
      </div>
      <!-- <div><label>Due date</label><input type="date" id="due-date" /></div> -->
    </div>
    <div class="grid2">
      <div>
        <label>Customer name</label>
        <input type="text" id="cust-name" name="customer_name"
          value="<?php echo old($old, 'customer_name'); ?>"
          placeholder="e.g. John Doe"
          class="<?php echo isset($errors['customer_name']) ? 'error' : ''; ?>" />
          <?php echo fieldError($errors, 'customer_name'); ?>
      </div>
      <div>
        <label>Email</label>
        <input type="email" id="cust-contact" name="customer_email"
          value="<?php echo old($old, 'customer_email'); ?>"
          placeholder="e.g. john.doe@example.com"
          class="<?php echo isset($errors['customer_email']) ? 'error' : ''; ?>" />
          <?php echo fieldError($errors, 'customer_email'); ?>
      </div>
    </div>
  </div>
  
  <div class="card">
    <p class="section-title">Items</p>
    <?php if (!empty($errors['items_general'])): ?>
      <div class="field-error" style="margin-bottom: 10px;">
        <?php echo fieldError($errors, 'items_general'); ?>
      </div>
    <?php endif; ?>
    <table id="items-table">
      <thead>
        <tr>
          <th style="width:35%">Item name</th>
          <th style="width:20%">Unit price (₦)</th>
          <th style="width:15%">Qty</th>
          <th style="width:20%">Subtotal (₦)</th>
          <th style="width:10%"></th>
        </tr>
      </thead>
      <tbody id="items-body"></tbody>
    </table>
    <button class="add-btn" type="button" onclick="addRow()">+ Add item</button>

    <div class="totals">
      <div class="total-row"><span>Subtotal</span><span id="subtotal">₦0.00</span></div>
      <div class="total-row">
        <span>Tax <input type="number" id="tax-rate" name="tax_rate"  value="<?php echo old($old, 'tax_rate', '0'); ?>" min="0" max="100" style="width:44px;border:0.5px solid var(--color-border-tertiary);border-radius:4px;padding:2px 4px;font-size:12px;background:var(--color-background-primary);color:var(--color-text-primary);" oninput="recalc()"> %</span>
        <span id="tax-amt">₦0.00</span>
      </div>
      <div class="total-row">
        <span>Discount <input type="number" id="discount" name="discount" value="<?php echo old($old, 'discount', '0'); ?>" min="0" style="width:60px;border:0.5px solid var(--color-border-tertiary);border-radius:4px;padding:2px 4px;font-size:12px;background:var(--color-background-primary);color:var(--color-text-primary);" oninput="recalc()"> ₦</span>
        <span id="discount-amt">₦0.00</span>
      </div>
      <div class="total-row grand"><span>Total</span><span id="grand-total">₦0.00</span></div>
  
    </div>
  </div>

  <div class="card">
    <p class="section-title">Notes</p>
    <textarea id="notes" name="notes" rows="2" style="width:100%;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);padding:8px;font-size:13px;font-family:var(--font-sans);background:var(--color-background-primary);color:var(--color-text-primary);" placeholder="e.g. Thank you for your purchase!"><?php echo old($old, 'notes'); ?></textarea>
  </div>
  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
  <input type="hidden" name="user_id" value="<?php echo $_SESSION['user']['id'] ?? ''; ?>" />
  <input type="hidden" name="subtotal" id="h-subtotal" value="0">
  <input type="hidden" name="tax_amount" id="h-tax-amt" value="0">
  <input type="hidden" name="grand_total" id="h-grand-total" value="0">
  <div class="actions">
    <button type="button" class="btn-outline" onclick="window.print()">Print / export PDF</button>
    <?php if (isset($_SESSION['user'])): ?>
      <button class="btn-primary" type="submit">Save invoice</button>
    <?php else: ?>
      <button class="btn-primary" type="submit">Log in to save invoice</button>
      <?php $_SESSION['previous_page'] = "invoice_form"; ?>
    <?php endif; ?>
    

  </div>

</form>
<?php if(isset($_SESSION['user'])): ?>
    <a class="logout" href="<?php echo Config::get('baseProjectFolder'); ?>/logout">Logout</a>
<?php endif; ?>
<div class="toast" id="toast"></div>

<script>
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
  // tr.innerHTML = `
  //   <td><input type="text" name="items[${id}][description]" value="${name}" placeholder="Item name" oninput="recalc()" style="width:100%" /></td>
  //   <td><input type="number" name="items[${id}][price]" value="${price}" placeholder="0.00" min="0" step="0.1" oninput="recalc()" style="width:100%" /></td>
  //   <td><input type="number" name="items[${id}][quantity]" value="${qty}" min="1" step="1" oninput="recalc()" style="width:100%" /></td>
  //   <td id="sub-${id}" style="font-weight:500">₦0.00</td>
  //   <td><button class="remove-btn" onclick="removeRow(${id})">×</button></td>
  // `;
    tr.innerHTML = `
    <td>
      <input type="text" name="items[${id}][description]" value="${name}" placeholder="Item name" oninput="recalc()" style="width:100%" />
      ${errors.description ? `<span class="field-error">${errors.description[0]}</span>` : ''}
    </td>
    <td>
      <input type="number" name="items[${id}][price]" value="${price}" placeholder="0.00" min="0" step="0.1" oninput="recalc()" style="width:100%" />
      ${errors.price ? `<span class="field-error">${errors.price[0]}</span>` : ''}
    </td>
    <td>
      <input type="number" name="items[${id}][quantity]" value="${qty}" min="1" step="1" oninput="recalc()" style="width:100%" />
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

// function saveInvoice() {
//   const rows = document.querySelectorAll('#items-body tr');
//   const items = []; 
//   rows.forEach(tr => {
//     const inputs = tr.querySelectorAll('input');
//     items.push({
//       name: inputs[0].value,
//       price: parseFloat(inputs[1].value) || 0,
//       qty: parseInt(inputs[2].value) || 0
//     });
//   });

//   const invoice = {
//     number: document.getElementById('inv-num').value,
//     date: document.getElementById('inv-date').value,
//     dueDate: document.getElementById('due-date').value,
//     customer: { name: document.getElementById('cust-name').value, contact: document.getElementById('cust-contact').value },
//     items,
//     taxRate: parseFloat(document.getElementById('tax-rate').value) || 0,
//     discount: parseFloat(document.getElementById('discount').value) || 0,
//     notes: document.getElementById('notes').value,
//     total: document.getElementById('grand-total').textContent,
//     savedAt: new Date().toISOString()
//   };

//   const existing = JSON.parse(localStorage.getItem('invoices') || '[]');
//   console.log(items);
//   existing.push(invoice);
//   localStorage.setItem('invoices', JSON.stringify(existing));
//   showToast('Invoice saved!');
// }

const today = new Date().toISOString().split('T')[0];
<?php if (empty($old['invoice_date'])): ?>
  document.getElementById('inv-date').value = new Date().toISOString().split('T')[0];
<?php endif; ?>
// const due = new Date(); due.setDate(due.getDate() + 7);
// document.getElementById('due-date').value = due.toISOString().split('T')[0];

  <?php if (!empty($old['items'])): ?>
  
    const oldItems = <?php echo json_encode(array_values($old['items'])); ?>;
    // oldItems.forEach(item => addRow(item.description, item.price, item.quantity));
    const itemErrors = <?php echo json_encode($errors['items'] ?? []); ?>;
    oldItems.forEach((item, index) => addRow(item.description, item.price, item.quantity, itemErrors[index] ?? {}));
    recalc();

  <?php else: ?>
    // addRow('', '', 1);
  <?php endif; ?>

</script>
<?php if (isset($_SESSION['message'])): ?>
  <script>
    showToast("<?php echo $_SESSION['message']; ?>");
  </script>
  <?php unset($_SESSION['message']); // Clear the message after displaying it ?>
<?php endif; ?>
</body>
</html>
