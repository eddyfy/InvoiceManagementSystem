<?php

declare(strict_types=1);
namespace App\Requests;
use App\Utils;

class Validators{
    public static function validateEmail(string $email, array &$errors): void {
        if (empty($email)) {
            $errors["email"][] = "Email is required.";
        } else {
            if (Utils::isLongerThan($email, 150)) {
                $errors["email"][] = "Email must be less than 150 characters.";
            }
            if (!Utils::isEmail($email)) {
                $errors["email"][] = "Invalid email format.";
            }
        }
    }

    public static function validatePassword(string $password, array &$errors): void{
            if(empty($password)){
                    $errors["password"][] = "Password is required.";
                }else if(Utils::isShorterThan($password, 6)){
                    $errors["password"][] = "Password must be at least 6 characters.";
                }else if(Utils::isLongerThan($password, 255)){
                    $errors["password"][] = "Password must be less than 255 characters.";
                }
    }

    public static function validateConfirmPassword(string $confirm_password, string $password, array &$errors): void{
            if(empty($confirm_password)){
                    $errors["confirm_password"][] = "Confirm Password is required.";
                }else if($confirm_password !== $password){
                    $errors["confirm_password"][] = "Passwords do not match.";
                }

    }

    public static function validateFirstName(string $firstname, array &$errors): void{
        if (empty($firstname)) {
                    $errors["firstname"][] = "First name is required.";
                }else{
                    if (Utils::isLongerThan($firstname, 100)) {
                        $errors["firstname"][] = "First name must be less than 100 characters.";
                    }
                }
    }

    public static function validateLastName(string $lastname, array &$errors): void{
        if (empty($lastname)) {
                    $errors["lastname"][] = "Last name is required.";
                }else{
                    if (Utils::isLongerThan($lastname, 100)) {
                        $errors["lastname"][] = "Last name must be less than 100 characters.";
                    }
                }
    }

    public static function validateInvoiceNumber(string $invoice_number, array &$errors): void{
            if(empty($invoice_number)){
                $errors["invoice_number"][] = "Invoice number is required.";
            }
            
    }

    public static function validateInvoicedate(string $invoice_date, array &$errors): void{
            if(empty($invoice_date)){
                $errors["invoice_date"][] = "Invoice date is required.";
            }
    }

    public static function validateInvoiceItems(array $items, array &$errors): void{
                if(empty($items) || !is_array($items)){
                        $errors["items_general"][] = "At least one item is required.";
                        
                    }else{
                        foreach($items as $index => $item){
                            if(empty($item['description'])){
                                $errors["items"][$index]['description'][] = "Description is required.";
                            }else if(Utils::isLongerThan($item['description'], 255)){
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

    public static function validateCustomerEmail(string $customer_email, array &$errors): void{
            if(empty($customer_email)){
                $errors["customer_email"][] = "Customer email is required.";
            }else{
                if(Utils::isLongerThan($customer_email, 150)){
                    $errors["customer_email"][] = "Customer email must be less than 150 characters.";
                }
                if(!Utils::isEmail($customer_email)){
                    $errors["customer_email"][] = "Invalid email format.";
                }
            }
    }

    public static function validateCustomerName(string $customer_name, array &$errors): void{
            if(empty($customer_name)){
                $errors["customer_name"][] = "Customer name is required.";
            }else{
                if(Utils::isLongerThan($customer_name, 255)){
                    $errors["customer_name"][] = "Customer name must be less than 255 characters.";
                }
            }
    }

    public static function validateBankAccountNumber(string $bank_account_number, array &$errors, string $source): void{
        if(empty($bank_account_number)){
            $errors[$source]["bank_account_number"][] = "Bank account number is required.";
        }else{
            if(!ctype_digit($bank_account_number)){
                $errors[$source]["bank_account_number"][] = "Bank account number must contain only digits.";
            }
            if(strlen($bank_account_number) !== 10){
                $errors[$source]["bank_account_number"][] = "Bank account number must be exactly 10 digits.";
            }
        }
    }
    public static function validateBusinessName(string $business_name, array &$errors, string $source): void{
        if(empty($business_name)){
            $errors[$source]["business_name"][] = "Business name is required.";
        }else{
            if(Utils::isLongerThan($business_name, 255)){
                $errors[$source]["business_name"][] = "Business name must be less than 255 characters.";
            }
        }
    }

    public static function validateBusinessEmail(string $business_email, array &$errors, string $source): void {
        if (empty($business_email)) {
            $errors[$source]["business_email"][] = "Business email is required.";
        } else {
            if (Utils::isLongerThan($business_email, 150)) {
                $errors[$source]["business_email"][] = "Business email must be less than 150 characters.";
            }
            if (!Utils::isEmail($business_email)) {
                $errors[$source]["business_email"][] = "Invalid email format.";
            }
        }
    }

    public static function validateBusinessAddress(string $business_address, array &$errors, string $source): void{ //field is not required
            if(!empty($business_address) && Utils::isLongerThan($business_address, 500)){
                $errors[$source]["business_address"][] = "Business address must be less than 500 characters.";
            }
    }

    public static function validateBusinessPhone(string $business_phone, array &$errors, string $source): void{  // field is not required 
        if(!empty($business_phone) && !preg_match("/^\+?[0-9]{7,15}$/", $business_phone)){
            $errors[$source]["business_phone"][] = "Invalid phone number format.";
        }
    }

    public static function validateBankAccountName(string $bank_account_name, array &$errors, string $source): void {
        if (empty($bank_account_name)) {
            $errors[$source]["bank_account_name"][] = "Bank account name is required.";
        } else {
            // Only letters, spaces, hyphens and apostrophes (for names like O'Brien or Obi-Wan)
            if (!preg_match("/^[a-zA-Z\s\-']+$/", $bank_account_name)) {
                $errors[$source]["bank_account_name"][] = "Bank account name must contain letters only.";
            }
            if (strlen($bank_account_name) < 2) {
                $errors[$source]["bank_account_name"][] = "Bank account name must be at least 2 characters.";
            }
            if (Utils::isLongerThan($bank_account_name, 255)) {
                $errors[$source]["bank_account_name"][] = "Bank account name must be less than 255 characters.";
            }
        }
    }

    public static function validateBankName(string $bank_name, array &$errors, string $source): void {
        if (empty($bank_name)) {
            $errors[$source]["bank_name"][] = "Bank name is required.";
        } else {
            // Only letters, spaces, hyphens and ampersands (for names like Zenith Bank or First & Trust)
            if (!preg_match("/^[a-zA-Z\s\-&]+$/", $bank_name)) {
                $errors[$source]["bank_name"][] = "Bank name must contain letters only.";
            }
            if (strlen($bank_name) < 2) {
                $errors[$source]["bank_name"][] = "Bank name must be at least 2 characters.";
            }
            if (Utils::isLongerThan($bank_name, 100)) {
                $errors[$source]["bank_name"][] = "Bank name must be less than 100 characters.";
            }
        }
    }

    public static function validateProfileFirstName(string $firstname, array &$errors): void{
        if (empty($firstname)) {
                    $errors['profile']["firstname"][] = "First name is required.";
                }else{
                    if (Utils::isLongerThan($firstname, 100)) {
                        $errors['profile']["firstname"][] = "First name must be less than 100 characters.";
                    }
                }
    }

    public static function validateProfileLastName(string $lastname, array &$errors): void{
        if (empty($lastname)) {
                    $errors['profile']["lastname"][] = "Last name is required.";
                }else{
                    if (Utils::isLongerThan($lastname, 100)) {
                        $errors['profile']["lastname"][] = "Last name must be less than 100 characters.";
                    }
                }
    }

    public static function validateProfileEmail(string $email, array &$errors): void {
        if (empty($email)) {
            $errors['profile']["email"][] = "Email is required.";
        } else {
            if (Utils::isLongerThan($email, 150)) {
                $errors['profile']["email"][] = "Email must be less than 150 characters.";
            }
            if (!Utils::isEmail($email)) {
                $errors['profile']["email"][] = "Invalid email format.";
            }
        }
    }
}
