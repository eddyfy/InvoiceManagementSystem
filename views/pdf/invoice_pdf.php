<?php
/** @var array $invoice */
/** @var array $items */
/** @var array $user */

$statusMap = [
    'paid'    => ['label' => 'Paid',    'color' => '#16a34a', 'bg' => '#dcfce7'],
    'sent'    => ['label' => 'Sent',    'color' => '#ca8a04', 'bg' => '#fef9c3'],
    'overdue' => ['label' => 'Overdue', 'color' => '#dc2626', 'bg' => '#fee2e2'],
];
$statusInfo = $statusMap[$invoice['status']] ?? ['label' => 'Draft', 'color' => '#64748b', 'bg' => '#f1f5f9'];

$hasBusiness = !empty($user['has_business_details']);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    /* dompdf supports a limited CSS subset — no flexbox, no grid, table-based layout instead */
    @page {
        margin: 30px 36px;
    }
    * { box-sizing: border-box; }
    body {
        font-family: 'DejaVu Sans', sans-serif; /* DejaVu Sans ships with dompdf and supports ₦ and most unicode */
        color: #0f172a;
        font-size: 12px;
        line-height: 1.5;
    }
    table { width: 100%; border-collapse: collapse; }
    .header-table td { vertical-align: top; }
    .brand-name { font-size: 16px; font-weight: bold; color: #0f172a; }
    .brand-sub { font-size: 10px; color: #64748b; margin-top: 2px; }
    .invoice-number { font-size: 20px; font-weight: bold; color: #0f172a; text-align: right; }
    .invoice-date { font-size: 11px; color: #64748b; text-align: right; margin-top: 4px; }
    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: bold;
        color: <?php echo $statusInfo['color']; ?>;
        background: <?php echo $statusInfo['bg']; ?>;
        margin-top: 6px;
    }
    .divider { border: none; border-top: 1px solid #e2e8f0; margin: 18px 0; }
    .party-table td { vertical-align: top; width: 50%; padding-right: 20px; }
    .party-label { font-size: 9px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
    .party-name { font-size: 13px; font-weight: bold; color: #0f172a; margin-top: 4px; }
    .party-detail { font-size: 11px; color: #64748b; margin-top: 2px; }

    .payment-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 12px 16px;
        margin-top: 16px;
    }
    .payment-box .label { font-size: 9px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .payment-box .account-number { font-size: 15px; font-weight: bold; color: #0f172a; margin-top: 4px; }
    .payment-box p { margin: 3px 0; font-size: 11px; color: #64748b; }

    .section-title { font-size: 9px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; margin-top: 20px; }

    .items-table { margin-top: 8px; }
    .items-table th {
        font-size: 9px; color: #64748b; font-weight: bold;
        padding: 8px 10px; text-align: left;
        border-bottom: 1.5px solid #e2e8f0;
        background: #f8fafc; text-transform: uppercase; letter-spacing: 0.4px;
    }
    .items-table th.num, .items-table td.num { text-align: right; }
    .items-table td { padding: 9px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }

    .totals-table { width: 260px; margin-left: auto; margin-top: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; }
    .totals-table td { padding: 6px 14px; font-size: 11px; color: #64748b; }
    .totals-table td.val { text-align: right; }
    .totals-table tr.grand td { font-size: 14px; font-weight: bold; color: #0f172a; border-top: 1.5px solid #e2e8f0; padding-top: 10px; }

    .notes-box { background: #f8fafc; border-radius: 6px; padding: 12px 14px; font-size: 11px; color: #64748b; margin-top: 8px; }

    .footer-note { text-align: center; font-size: 9px; color: #94a3b8; margin-top: 30px; }
</style>
</head>
<body>

    <!-- HEADER: brand + invoice number -->
    <table class="header-table">
        <tr>
            <td style="width:60%;">
                <div class="brand-name">InvoiceManager</div>
                <div class="brand-sub"><?php echo $_SERVER['HTTP_HOST']; ?></div>
            </td>
            <td style="width:40%;">
                <div class="invoice-number"><?php echo htmlspecialchars($invoice['invoice_number']); ?></div>
                <div class="invoice-date">Date: <?php echo date('F d, Y', strtotime($invoice['invoice_date'])); ?></div>
                <div style="text-align:right;"><span class="badge"><?php echo $statusInfo['label']; ?></span></div>
            </td>
        </tr>
    </table>

    <hr class="divider">

    <!-- FROM / BILL TO -->
    <table class="party-table">
        <tr>
            <td>
                <div class="party-label">From</div>
                <?php if ($hasBusiness && !empty($user['business_name'])): ?>
                    <div class="party-name"><?php echo htmlspecialchars(ucwords((string)$user['business_name'])); ?></div>
                <?php else: ?>
                    <div class="party-name"><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></div>
                <?php endif; ?>

                <?php if ($hasBusiness && !empty($user['business_email'])): ?>
                    <div class="party-detail"><?php echo htmlspecialchars((string)$user['business_email']); ?></div>
                <?php else: ?>
                    <div class="party-detail"><?php echo htmlspecialchars($user['email']); ?></div>
                <?php endif; ?>

                <?php if ($hasBusiness && !empty($user['business_phone'])): ?>
                    <div class="party-detail"><?php echo htmlspecialchars((string)$user['business_phone']); ?></div>
                <?php endif; ?>
                <?php if ($hasBusiness && !empty($user['business_address'])): ?>
                    <div class="party-detail"><?php echo htmlspecialchars(ucwords((string)$user['business_address'])); ?></div>
                <?php endif; ?>
            </td>
            <td>
                <div class="party-label">Bill To</div>
                <div class="party-name"><?php echo htmlspecialchars($invoice['customer_name']); ?></div>
                <div class="party-detail"><?php echo htmlspecialchars($invoice['customer_email']); ?></div>
            </td>
        </tr>
    </table>

    <!-- PAYMENT INFO -->
    <?php if ($hasBusiness): ?>
    <div class="payment-box">
        <div class="label">Send payment to</div>
        <div class="account-number"><?php echo htmlspecialchars((string)($user['bank_account_number'] ?? '')); ?></div>
        <p><?php echo htmlspecialchars(strtoupper((string)($user['bank_name'] ?? ''))); ?> &mdash; <?php echo htmlspecialchars(ucwords((string)($user['bank_account_name'] ?? ''))); ?></p>
    </div>
    <?php endif; ?>

    <!-- ITEMS -->
    <div class="section-title">Items</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:45%">Description</th>
                <th class="num" style="width:20%">Unit Price</th>
                <th class="num" style="width:15%">Qty</th>
                <th class="num" style="width:20%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['description']); ?></td>
                <td class="num">&#8358;<?php echo number_format($item['price'], 2); ?></td>
                <td class="num"><?php echo (int)$item['quantity']; ?></td>
                <td class="num"><?php echo number_format($item['subtotal'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- TOTALS -->
    <table class="totals-table">
        <tr>
            <td>Subtotal</td>
            <td class="val">&#8358;<?php echo number_format($invoice['subtotal'], 2); ?></td>
        </tr>
        <tr>
            <td>Tax (<?php echo $invoice['tax_rate']; ?>%)</td>
            <td class="val">&#8358;<?php echo number_format($invoice['tax_amount'], 2); ?></td>
        </tr>
        <tr>
            <td>Discount</td>
            <td class="val">-&#8358;<?php echo number_format($invoice['discount'], 2); ?></td>
        </tr>
        <tr class="grand">
            <td>Total</td>
            <td class="val">&#8358;<?php echo number_format($invoice['grand_total'], 2); ?></td>
        </tr>
    </table>

    <?php if (!empty($invoice['notes'])): ?>
        <div class="section-title">Notes</div>
        <div class="notes-box"><?php echo nl2br(htmlspecialchars($invoice['notes'])); ?></div>
    <?php endif; ?>

    <div class="footer-note">Generated by InvoiceManager &middot; <?php echo date('F d, Y \a\t g:i A'); ?></div>

</body>
</html>