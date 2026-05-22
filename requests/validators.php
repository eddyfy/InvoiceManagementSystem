<?php

declare(strict_types=1);
// root/requests/validators.php
function validateEmail(string $email, array &$errors): void {
    if (empty($email)) {
        $errors["email"][] = "Email is required.";
    } else {
        if (isLongerThan($email, 150)) {
            $errors["email"][] = "Email must be less than 150 characters.";
        }
        if (!isEmail($email)) {
            $errors["email"][] = "Invalid email format.";
        }
    }
}

function validatePassword(string $password, array &$errors): void{
         if(empty($password)){
                $errors["password"][] = "Password is required.";
            }else if(isShorterThan($password, 6)){
                $errors["password"][] = "Password must be at least 6 characters.";
            }else if(isLongerThan($password, 255)){
                $errors["password"][] = "Password must be less than 255 characters.";
            }
}

function validateConfirmPassword(string $confirm_password, string $password, array &$errors): void{
        if(empty($confirm_password)){
                $errors["confirm_password"][] = "Confirm Password is required.";
            }else if($confirm_password !== $password){
                $errors["confirm_password"][] = "Passwords do not match.";
            }

}

function validateFirstName(string $firstname, array &$errors): void{
      if (empty($firstname)) {
                $errors["firstname"][] = "First name is required.";
            }else{
                if (isLongerThan($firstname, 100)) {
                    $errors["firstname"][] = "First name must be less than 100 characters.";
                }
            }
}

function validateLastName(string $lastname, array &$errors): void{
       if (empty($lastname)) {
                $errors["lastname"][] = "Last name is required.";
            }else{
                if (isLongerThan($lastname, 100)) {
                    $errors["lastname"][] = "Last name must be less than 100 characters.";
                }
            }
}

function validateInvoiceNumber(string $invoice_number, array &$errors): void{
        if(empty($invoice_number)){
            $errors["invoice_number"][] = "Invoice number is required.";
        }
        
}

function validateInvoicedate(string $invoice_date, array &$errors): void{
        if(empty($invoice_date)){
            $errors["invoice_date"][] = "Invoice date is required.";
        }
}

function validateInvoiceItems(array $items, array &$errors): void{
             if(empty($items) || !is_array($items)){
                    $errors["items_general"][] = "At least one item is required.";
                    
                }else{
                    foreach($items as $index => $item){
                        if(empty($item['description'])){
                            $errors["items"][$index]['description'][] = "Description is required.";
                        }else if(isLongerThan($item['description'], 255)){
                            $errors["items"][$index]['description'][] = "Description must be less than 255 characters.";
                        }
                        if(empty($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] <= 0){
                            $errors["items"][$index]['quantity'][] = "Quantity must be a positive number.";
                        }
                        if(empty($item['price']) || !is_numeric($item['price']) || $item['price'] < 0){
                            $errors["items"][$index]['price'][] = "Price must be a non-negative number.";
                        }
                    }
                    foreach($items as $index => $item){
                        $item['description'] = strtolower(trim($item['description'] ?? ''));
                    }
                }   
}

function validateCustomerEmail(string $customer_email, array &$errors): void{
        if(empty($customer_email)){
            $errors["customer_email"][] = "Customer email is required.";
        }else{
            if(isLongerThan($customer_email, 150)){
                $errors["customer_email"][] = "Customer email must be less than 150 characters.";
            }
            if(!isEmail($customer_email)){
                $errors["customer_email"][] = "Invalid email format.";
            }
        }
}

function validateCustomerName(string $customer_name, array &$errors): void{
        if(empty($customer_name)){
            $errors["customer_name"][] = "Customer name is required.";
        }else{
            if(isLongerThan($customer_name, 255)){
                $errors["customer_name"][] = "Customer name must be less than 255 characters.";
            }
        }
}