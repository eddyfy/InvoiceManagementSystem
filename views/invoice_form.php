<?php 
declare(strict_types=1);
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
// var_dump($config);
// $config = new Config(); // Create a new instance of the Config class to access configuration settings
$_SESSION['csrf_token'] = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);

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
      <div><label>Invoice #</label><input type="text" id="inv-num" name="invoice_number" value="INV-001" /></div>
      <div><label>Date</label><input type="date" id="inv-date" name="invoice_date" /></div>
      <!-- <div><label>Due date</label><input type="date" id="due-date" /></div> -->
    </div>
    <div class="grid2">
      <div><label>Customer name</label><input type="text" id="cust-name" name="customer_name" placeholder="e.g. John Doe" required /></div>
      <div><label>Email</label><input type="email" id="cust-contact" name="customer_email" placeholder="e.g. john.doe@example.com" required /></div>
    </div>
  </div>
  
  <div class="card">
    <p class="section-title">Items</p>
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
        <span>Tax <input type="number" id="tax-rate" name="tax-rate" value="0" min="0" max="100" style="width:44px;border:0.5px solid var(--color-border-tertiary);border-radius:4px;padding:2px 4px;font-size:12px;background:var(--color-background-primary);color:var(--color-text-primary);" oninput="recalc()"> %</span>
        <span id="tax-amt">₦0.00</span>
      </div>
      <div class="total-row">
        <span>Discount <input type="number" id="discount" name="discount" value="0" min="0" style="width:60px;border:0.5px solid var(--color-border-tertiary);border-radius:4px;padding:2px 4px;font-size:12px;background:var(--color-background-primary);color:var(--color-text-primary);" oninput="recalc()"> ₦</span>
        <span id="discount-amt">₦0.00</span>
      </div>
      <div class="total-row grand"><span>Total</span><span id="grand-total">₦0.00</span></div>
    </div>
  </div>

  <div class="card">
    <p class="section-title">Notes</p>
    <textarea id="notes" name="notes" rows="2" style="width:100%;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);padding:8px;font-size:13px;font-family:var(--font-sans);background:var(--color-background-primary);color:var(--color-text-primary);" placeholder="e.g. Thank you for your purchase!"></textarea>
  </div>
  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
  <div class="actions">
    <button class="btn-outline" onclick="window.print()">Print / export PDF</button>
    <?php if (isset($_SESSION['user'])): ?>
      <button class="btn-primary" type="submit">Save invoice</button>
    <?php else: ?>
      <button class="btn-primary" type="submit">Log in to save invoice</button>
    <?php endif; ?>
    

  </div>

</form>
<?php if(isset($_SESSION['user'])): ?>
    <a href="<?php echo Config::get('baseProjectFolder'); ?>/logout">Logout</a>
<?php endif; ?>
<div class="toast" id="toast"></div>

<script>
let rowId = 0;

function addRow(name='', price='', qty=1) {
  const id = rowId++;
  const tr = document.createElement('tr');
  tr.id = 'row-' + id;
  tr.innerHTML = `
    <td><input type="text" name="items[${id}][description]" value="${name}" placeholder="Item name" oninput="recalc()" style="width:100%" /></td>
    <td><input type="number" name="items[${id}][price]" value="${price}" placeholder="0.00" min="0" step="0.1" oninput="recalc()" style="width:100%" /></td>
    <td><input type="number" name="items[${id}][quantity]" value="${qty}" min="1" step="1" oninput="recalc()" style="width:100%" /></td>
    <td id="sub-${id}" style="font-weight:500">₦0.00</td>
    <td><button class="remove-btn" onclick="removeRow(${id})">×</button></td>
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
document.getElementById('inv-date').value = today;
const due = new Date(); due.setDate(due.getDate() + 7);
document.getElementById('due-date').value = due.toISOString().split('T')[0];

addRow('', '', 1);
</script>
<?php if (isset($_SESSION['message'])): ?>
  <script>
    showToast("<?php echo $_SESSION['message']; ?>");
  </script>
  <?php unset($_SESSION['message']); // Clear the message after displaying it ?>
<?php endif; ?>
</body>
</html>
