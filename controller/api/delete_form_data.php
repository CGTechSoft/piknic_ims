<?php

//registering for class auto loading
spl_autoload_register(function($class_name) {
    $controller_root = $_SERVER['DOCUMENT_ROOT'] . '/piknik_ims/controller';
    if (file_exists($controller_root . '/php_classes/' . $class_name . '.php')) {
        $file_name = $controller_root . '/php_classes/' . $class_name . '.php';
        require_once $file_name;
    } else {
        throw new Exception("Class " . $class_name . " Not found");
    }
});

session_start();
if (isset($_SESSION['user_id']) and !empty($_SESSION['user_id'])) {
    if (isset($_POST['form_id']) and !empty($_POST['form_id'])) {
        $form_id = $_POST['form_id'];
        $tag = "DELETE_FORM_DATA";
        if ($form_id == 17) {   ///item delete
            if (isset($_POST['item_id']) and !empty($_POST['item_id'])) {
                $item = new item();
                $item->id = $_POST['item_id'];
                if($item->deleteItem()){
                    $message = "Item deleted Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$item->id)); 
                }else{
                    $description = "Item delete failed, item : ".$item->to_string();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'failed', 'error' => $message, 'data' => array());
                }                    
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }            
        }else if ($form_id == 18) {   ///vendor delete
            if (isset($_POST['vendor_id']) and !empty($_POST['vendor_id'])) {
                $vendor = new wendors();
                $vendor->id = $_POST['vendor_id'];
                if($vendor->deleteWendor()){
                    $message = "Vendor deleted Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$vendor->id)); 
                }else{
                    $description = "Vendor delete failed, item : ".$vendor->to_string();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'failed', 'error' => $message, 'data' => array());
                }                    
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }            
        }else if ($form_id == 19) {   ///purchace delete
            if (isset($_POST['purchace_id']) and !empty($_POST['purchace_id'])) {
                $purchace = new purchaces();
                $purchace->id = $_POST['purchace_id'];
                $purchace->getPurchace();
                if($purchace->stocked){
                    $responce = array('status' => 'failed', 'error' => 'Purchace already stocked, Can\'t delete !', 'data' => array());
                }else{
                    if($purchace->deletePurchace()){
                        $message = "Purchace deleted Successfuly";
                        $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$purchace->id)); 
                    }else{
                        $description = "Purchace delete failed, Purchace : ".$purchace->to_string();
                        Log::e($tag, $description);
                        $message = "Some server error occured";
                        $responce = array('status' => 'failed', 'error' => $message, 'data' => array());
                    }   
                }
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }            
        }else if ($form_id == 20) {   ///stock: (inventry) delete
            if (isset($_POST['inventry_id']) and !empty($_POST['inventry_id'])) {
                $inventry = new inventry();
                $inventry->id = $_POST['inventry_id'];
                if($inventry->deleteInventry()){
                    $message = "Inventry deleted Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$inventry->id)); 
                }else{
                    $description = "Inventry delete failed, item : ".$inventry->to_string();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'failed', 'error' => $message, 'data' => array());
                }                    
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }            
        }else if ($form_id == 21) {   ///customer delete
            if (isset($_POST['customer_id']) and !empty($_POST['customer_id'])) {
                $customer = new customer();
                $customer->id = $_POST['customer_id'];
                if($customer->deleteCustomer()){
                    $message = "Customer deleted Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$customer->id)); 
                }else{
                    $description = "Customer delete failed, item : ".$customer->to_string();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'failed', 'error' => $message, 'data' => array());
                }                    
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }            
        }else if ($form_id == 23) {   ///bank delete
            if (isset($_POST['bank_id']) and !empty($_POST['bank_id'])) {
                $bank = new bank();
                $bank->id = $_POST['bank_id'];
                if($bank->deleteBank()){
                    $message = "Bank deleted Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$bank->id)); 
                }else{
                    $description = "Bank delete failed, item : ".$bank->to_string()." Error : ".  mysql_error();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'failed', 'error' => $message, 'data' => array());
                }                    
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }            
        } else if ($form_id == 26) {   ///shop delete
            if (isset($_POST['company_id']) and !empty($_POST['company_id'])) {
                $company = new company();
                $company->id = $_POST['company_id'];
                if($company->deleteCompany()){
                    $message = "Shop deleted Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$company->id)); 
                }else{
                    $description = "Shop delete failed, item : ".$company->to_string()." Error : ".  mysql_error();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'failed', 'error' => $message, 'data' => array());
                }                    
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }            
        } else if ($form_id == 28) {   ///user delete
            if (isset($_POST['user_id']) and !empty($_POST['user_id'])) {
                $user = new user();
                $user->id = $_POST['user_id'];
                if($user->deleteUser()){
                    $message = "User deleted Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$user->id)); 
                }else{
                    $description = "User delete failed, User : ".$user->to_string()." Error : ".  mysql_error();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'failed', 'error' => $message, 'data' => array());
                }                    
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }            
        } else if ($form_id == 31) {   ///expence delete
            if (isset($_POST['expence_id']) and !empty($_POST['expence_id'])) {
                $expence = new expences();
                $expence->id = $_POST['expence_id'];
                if($expence->deleteExpence()){
                    $message = "Expence deleted Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$expence->id)); 
                }else{
                    $description = "Expence delete failed, Expence : ".$expence->to_string()." Error : ".  mysql_error();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'failed', 'error' => $message, 'data' => array());
                }                    
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }            
        } else if ($form_id == 32) {   ///bank deposit delete
            if (isset($_POST['bank_deposit_id']) and !empty($_POST['bank_deposit_id'])) {
                $bank_deposit = new bank_deposits();
                $bank_deposit->id = $_POST['bank_deposit_id'];
                if($bank_deposit->deleteBankDeposit()){
                    $message = "Bank Deposit deleted Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$bank_deposit->id)); 
                }else{
                    $description = "Expence delete failed, Expence : ".$bank_deposit->to_string()." Error : ".  mysql_error();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'failed', 'error' => $message, 'data' => array());
                }                    
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }            
        } else {
            $responce = array('status' => 'failed', 'error' => 'Invalid Form', 'data' => array());
        }
    } else {
        $responce = array('status' => 'failed', 'error' => 'Data missing', 'data' => array());
    }
} else {
    $responce = array('status' => 'failed', 'error' => 'Session expired', 'data' => array());
}
echo json_encode($responce);
