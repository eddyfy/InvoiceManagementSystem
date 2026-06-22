<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Config;
use App\DBH;
use App\Requests\ValidateInvoice;
use Exception;
use PDOException;
use Dompdf\Dompdf;
use Dompdf\Options;

class InvoiceController{
    public function showInvoiceForm(): void {

        $_SESSION['csrf_token'] = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);

        // Get the next invoice number for the logged in user
        $nextInvoiceNumber = 'INV-001'; // fallback default
        
        if (isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['id'];
            $stmt = DBH::getConnection()->prepare(
            "SELECT MAX(CAST(SUBSTRING_INDEX(invoice_number, '-', -1) AS UNSIGNED)) 
            FROM invoices 
            WHERE user_id = ?"
            );
            $stmt->execute([$userId]);
            $max = $stmt->fetchColumn();
            $nextInvoiceNumber = 'INV-' . sprintf('%03d', ($max ?? 0) + 1);
        }

        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['invoice_draft']['invoice_number']);
        $old = $_SESSION['old'] ?? $_SESSION['invoice_draft'] ?? [];
        // var_dump($old);
       
        unset($_SESSION['errors'], $_SESSION['old']);
        require './views/invoice_form.php'; // Include the invoice form view to display it to the user
    }

    public function createInvoice(): void {
            if (!isset($_SESSION['user'])) {
                $_SESSION['invoice_draft'] = $_POST;
                $_SESSION['previous_page'] = 'invoice_form';
                header('Location: ' . Config::get('baseProjectFolder') . '/login');
                exit();
            }

            $validated = ValidateInvoice::validate();

            $invoiceModel = new Invoice();
            $invoiceItemModel = new InvoiceItem();
            try{
                $invoiceModel->pdo->beginTransaction(); // Start a database transaction to ensure data integrity during invoice creation
                $invoice = $invoiceModel->create([
                    'user_id' => $validated['user_id'],
                    'invoice_number' => $validated['invoice_number'],
                    'invoice_date' => $validated['invoice_date'],
                    'customer_name' => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'],
                    'subtotal' => $validated['subtotal'],
                    'tax_rate' => $validated['tax_rate'],
                    'tax_amount' => $validated['tax_amount'],
                    'discount' => $validated['discount'],
                    'grand_total' => $validated['grand_total'],
                    'notes' => $validated['notes']
                ]); 
                
                $items = array_map(function($item) use ($invoice){
                    return [
                        'invoice_id' => $invoice->id,
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ];
                }, $validated['items']); // Prepare the invoice items data by mapping the validated items to include the generated invoice ID
                foreach($items as $item){
                    $invoiceItemModel->create($item);
                } // Call the create method of the InvoiceItem model to add each item to the databse 
                
                $invoiceModel->pdo->commit(); //commit the database transaction to save changesif succesfull
                $_SESSION['message'] = "Invoice created successfully."; // Store a success message in the session to display on the dashboard
                header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
                exit();
            }catch(Exception $e){
                $invoiceModel->pdo->rollBack(); // Roll back the transaction if an error occurs during invoice creation to maintain data integrity
                $message = match ($e->getMessage()) {
                    'duplicate' => 'An invoice with that number already exists.',
                    default     => 'Something went wrong. Please try again.',
                };
                error_log("Error creating invoice: " . $e->getMessage()); 
                echo $message; // Handle any errors that occur during invoice creation
            }
    }

    public function showInvoice(array $params):void{
        $invoiceId = (int) ($params['id']);
        $invoiceModel = new Invoice();
        try{
            $invoice = $invoiceModel->getById($invoiceId, $_SESSION['user']['id']);
            if(!$invoice){
                $_SESSION['message'] = "Invoice not found.";
                header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
                exit;
            }
            $invoiceItems = new InvoiceItem();
            $items = $invoiceItems->getByInvoiceId($invoiceId);
        }catch(Exception $e){
            error_log("Error fetching invoice: " . $e->getMessage());
            echo "An error occurred while fetching the invoice.";
        }
        
        require './views/invoice_view.php'; // Include the invoice view to display the invoice details to the user
    }

    public function showEditForm(array $params):void{
        $invoiceId = (int) ($params['id']);
        $invoiceModel = new Invoice();
        $invoice = null;
        $items = [];
        try{
            $invoice = $invoiceModel->getById($invoiceId, $_SESSION['user']['id']);
            if(!$invoice){
                $_SESSION['message'] = "Invoice not found.";
                header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
                exit;
            }
            $invoiceItems = new InvoiceItem();
            $items = $invoiceItems->getByInvoiceId($invoiceId);
        }catch(Exception $e){
            error_log("Error fetching invoice: " . $e->getMessage());
            echo "An error occurred while fetching the invoice.";
        }


        $_SESSION['csrf_token'] = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);

        // Merge session errors/old input on validation failure 
        $errors = $_SESSION['errors'] ?? [];
        $old    = $_SESSION['old']    ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        // If there are old values from a failed submission, override $invoice and $items
        if (!empty($old)) {
            $invoice = array_merge($invoice, $old);
            if (!empty($old['items'])) {
                $items = array_values($old['items']);
            }
        }

        require './views/invoice_edit.php'; // Include the invoice edit form to allow the user to edit the invoice details
    }

    public function updateInvoice(array $params):void{
        
        $invoiceId = (int) ($params['id']);
        $validated = ValidateInvoice::validate("/invoice/edit/{$invoiceId}"); // Validate the invoice data and redirect back to the edit form if there are validation errors
        $status = $_POST['status'] ?? 'draft';
        $invoiceModel = new Invoice();
        $invoiceItemModel = new InvoiceItem();
        $invoiceModel->pdo->beginTransaction();
        try{
            $invoiceModel->update($invoiceId, $_SESSION['user']['id'], [
                'invoice_number' => $validated['invoice_number'],
                'invoice_date' => $validated['invoice_date'],
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'subtotal' => $validated['subtotal'],
                'tax_rate' => $validated['tax_rate'],
                'tax_amount' => $validated['tax_amount'],
                'discount' => $validated['discount'],
                'grand_total' => $validated['grand_total'],
                'notes' => $validated['notes'],
                'status' => $status
            ]);
            $invoiceItemModel->deleteByInvoiceId($invoiceId); // Delete existing items before adding updated items to handle item updates and deletions
            $items = array_map(function($item) use ($invoiceId){
                return [    
                    'invoice_id' => $invoiceId,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ];
            }, $validated['items']);
            foreach($items as $item){
                $invoiceItemModel->create($item);   
            }
            $invoiceModel->pdo->commit();
        } catch (PDOException $e) {
            $invoiceModel->pdo->rollback();
            error_log("Error updating invoice: " . $e->getMessage());
            echo "An error occurred while updating the invoice.";
        }
        $_SESSION['message'] = "Invoice with id " . $invoiceId . " updated successfully."; // Store a success message in the session to display on the dashboard after successful update
        header('Location: ' . Config::get('baseProjectFolder') . '/dashboard');
        exit();
    }
    
    public function deleteInvoice(array $params):void{
        $invoiceId = (int) ($params['id']);
        echo "Deleting invoice with ID: " . $params['id']; // Output the invoice ID for demonstration purposes (replace with actual invoice deletion logic)
        $invoiceModel = new Invoice();
        try{
            $invoiceModel->deleteById($invoiceId, $_SESSION['user']['id']);
        }catch(Exception $e){
            error_log("Error deleting invoice: " . $e->getMessage());
            echo "An error occurred while deleting the invoice.";
        }
        $_SESSION['message'] = "Invoice deleted successfully."; // Store a success message in the session to display on the dashboard after successful deletion
        header('Location: ' . Config::get('baseProjectFolder') . '/dashboard'); // Redirect back to the dashboard after deleting the invoice
        exit();
    }

    public function downloadPdf(array $params):void {

       

        $invoiceId = (int) $params['id'];
        $userId = $_SESSION['user']['id'];

        $invoiceModel = new Invoice();
        try{
            $invoice = $invoiceModel->getById($invoiceId, $userId); // IDOR-safe, matches your existing pattern
        }catch(Exception $e){
            error_log("Error fetching invoice for PDF: " . $e->getMessage());
            echo "An error occurred while fetching the invoice.";
            return;
        }
    
        if (!$invoice) {
            die("Invoice not found.");
        }

        $itemModel = new InvoiceItem();
        $items = $itemModel->getByInvoiceId($invoiceId);

        $user = $_SESSION['user'];

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $dompdf = new Dompdf($options);

        ob_start();
        require __DIR__ . '/../views/pdf/invoice_pdf.php'; // $invoice, $items, $user in scope here
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("invoice-{$invoice['invoice_number']}.pdf", ['Attachment' => false]);
    }


    public function downloadPdfPublic(): void {
        // print_r($_SESSION['errors']);
        $validated = ValidateInvoice::validate();

        // Build $invoice array matching the DB row shape
        $invoice = [
            'invoice_number' => $validated['invoice_number'] ?? '',
            'invoice_date'   => $validated['invoice_date'] ?? date('Y-m-d'),
            'customer_name'  => $validated['customer_name'] ?? '',
            'customer_email' => $validated['customer_email'] ?? '',
            'subtotal'       => $validated['subtotal'] ?? 0,
            'tax_rate'       => $validated['tax_rate'] ?? 0,
            'tax_amount'     => $validated['tax_amount'] ?? 0,
            'discount'       => $validated['discount'] ?? 0,
            'grand_total'    => $validated['grand_total'] ?? 0,
            'notes'          => $validated['notes'] ?? '',
            'status'         => 'draft', // guests haven't saved/sent anything yet
        ];

        // Build $items array matching the DB row shape
        $items = [];
        foreach (($_POST['items'] ?? []) as $item) {
            $price = (float)($item['price'] ?? 0);
            $qty   = (int)($item['quantity'] ?? 0);
            $items[] = [
                'description' => $item['description'] ?? '',
                'quantity'    => $qty,
                'price'       => $price,
                'subtotal'    => $price * $qty,
            ];
        }

        // Build $user array — from session if logged in, otherwise from guest POST fields
        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
        } else {
            $user = [
                'firstname'            => '',
                'lastname'              => '',
                'email'                 => $_POST['business_email'] ?? '',
                'has_business_details'  => !empty($_POST['business_name']),
                'business_name'         => $_POST['business_name'] ?? '',
                'business_email'        => $_POST['business_email'] ?? '',
                'business_phone'        => $_POST['business_phone'] ?? '',
                'business_address'      => $_POST['business_address'] ?? '',
                'bank_account_name'     => $_POST['bank_account_name'] ?? '',
                'bank_account_number'   => $_POST['bank_account_number'] ?? '',
                'bank_name'             => $_POST['bank_name'] ?? '',
            ];
        }

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $dompdf = new Dompdf($options);

        ob_start();
        require __DIR__ . '/../views/pdf/invoice_pdf.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("invoice-preview.pdf", ['Attachment' => false]);
    }

} 