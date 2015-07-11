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
        $tag = "ADD_FORM_DATA";
        if ($form_id == 1) {  /// add sale
            if (isset($_POST['customer_id']) 
                    and isset($_POST['total']) and !empty($_POST['total']) 
                    and isset($_POST['net_amount']) // and !empty($_POST['net_amount']) 
                    and isset($_POST['tax_amount']) // and !empty($_POST['tax_amount'])
                    and isset($_POST['discount']) // and !empty($_POST['discount']) 
                    and isset($_POST['items']) and !empty($_POST['items'])) {
                $sale = new sales();
                $sale->customer_id = $_POST['customer_id'];
                $sale->amount = $_POST['total'];
                $sale->net_amount = $_POST['net_amount'];
                $sale->tax_amount = $_POST['tax_amount'];
                $sale->discount = $_POST['discount'];
                $user = new user();
                $user->id = $_SESSION['user_id'];
                $user->getUser();
                $sale->company_id = $user->company_id;
                $sales_items = array();
                foreach ($_POST['items'] as $sales_array_item) {
                    $sales_item = new sales_items();
                    $sales_item->item_id = $sales_array_item['id'];
                    $sales_item->quantity = $sales_array_item['quantity'];
                    $sales_item->rate = $sales_array_item['rate'];
                    $sales_item->tax = $sales_array_item['tax'];
                    $sales_item->discount = $sales_array_item['discount'];
                    array_push($sales_items, $sales_item);
                }
                $sale->setSalesItems($sales_items);
                $inserted_id = $sale->addSales();
                $message = "Sale completed successfuly";
                $responce = array('status' => 'success', 'error' => ''.$sale->to_string(), 'data' => array("message" => $message, "id"=>$inserted_id));
            } else {
                ob_start();
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }
        } else if ($form_id == 4) {  //bank deposit
            if (isset($_POST['amount']) and !empty($_POST['amount']) 
                    and isset($_POST['bank_id']) and !empty($_POST['bank_id'])
                    and isset($_POST['description']) and !empty($_POST['description'])) {
                $bank_deposit = new bank_deposits();
                $bank_deposit->description = $_POST['description'];
                $bank_deposit->amount = $_POST['amount'];
                $bank_deposit->bank_id = $_POST['bank_id'];
                $user = new user();
                $user->id = $_SESSION['user_id'];
                $user->getUser();                
                $bank_deposit->user_id = $user->id;
                $bank_deposit->company_id = $user->company_id;
                if ($bank_deposit->addBankDeposits()) {
                    $responce = array('status' => 'success', 'error' => '',
                        'data' => array('message' => 'Bank Deposit Added successfully'));
                } else {
                    Log::e($tag, "Bank Deposit adding failed, Bank Deposit : " . $bank_deposit->to_string() . 'Error : '.  mysql_error());
                    if(empty(mysql_error())){
                        $error_message = 'Some server error occured';
                    }else{
                        $error_message = mysql_error();
                    }
                    $responce = array('status' => 'failed', 'error' => $error_message, 'data' => array());
                }
            } else {
                ob_start();
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }
        } else if ($form_id == 5) {
            if (isset($_POST['amount']) and !empty($_POST['amount']) 
                    and isset($_POST['description']) and !empty($_POST['description'])) {
                $expence = new expences();
                $expence->description = $_POST['description'];
                $expence->amount = $_POST['amount'];
                $user = new user();
                $user->id = $_SESSION['user_id'];
                $user->getUser();                
                $expence->user_id = $user->id;
                $expence->company_id = $user->company_id;
                if ($expence->addExpence()) {
                    $responce = array('status' => 'success', 'error' => '',
                        'data' => array('message' => 'Expence Added successfully'));
                } else {
                    Log::e($tag, "Expence adding failed Expence : " . $expence->to_string() . 'Error : '.  mysql_error());
                    if(empty(mysql_error())){
                        $error_message = 'Some server error occured';
                    }else{
                        $error_message = mysql_error();
                    }
                    $responce = array('status' => 'failed', 'error' => $error_message, 'data' => array());
                }
            } else {
                ob_start();
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }
        } else if ($form_id == 6) {
            if (isset($_POST['customer_name']) and !empty($_POST['customer_name']) 
                    and isset($_POST['contact_number']) and !empty($_POST['contact_number']) ) {
                $customer = new customer();
                $customer->customer_name = $_POST['customer_name'];
                $customer->contact_number = $_POST['contact_number'];
                $user = new user();
                $user->id = $_SESSION['user_id'];
                $user->getUser();
                $customer->company_id = $user->company_id;
                if($customer->addCustomer()){
                    $message = "Customer added successfully";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message));
                } else {
                    Log::e($tag, "Customer adding failed Customer : " . $customer->to_string() . 'Error : '.  mysql_error());
                    if(empty(mysql_error())){
                        $error_message = 'Some server error occured';
                    }else{
                        $error_message = mysql_error();
                    }
                    $responce = array('status' => 'failed', 'error' => $error_message, 'data' => array());
                }
            } else {
                ob_start();
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }
        }else if ($form_id == 7) {
            if (isset($_POST['wendor_id']) and !empty($_POST['wendor_id']) 
                    and isset($_POST['bill_number'])// and !empty($_POST['bill_number'])
                    and isset($_POST['total']) and !empty($_POST['total']) 
                    and isset($_POST['items']) and !empty($_POST['items'])) {
                $purchace = new purchaces();
                $purchace->amount = $_POST['total'];
                $purchace->wendor_id = $_POST['wendor_id'];
                $purchace->purchace_manager_id = $_SESSION['user_id'];
                $purchace->bill_number = $_POST['bill_number'];
                $user = new user();
                $user->id = $_SESSION['user_id'];
                $user->getUser();
                $purchace->company_id = $user->company_id;
                $purchace_items = array();
                foreach ($_POST['items'] as $items_array_item) {
                    $purchace_item = new purchace_items();
                    $purchace_item->item_id = $items_array_item['id'];
                    $purchace_item->quantity = $items_array_item['quantity'];
                    $purchace_item->rate = $items_array_item['rate'];
                    array_push($purchace_items, $purchace_item);
                }
                $purchace->setPurchaceItems($purchace_items);
                $purchace->addPurchace();
                $message = "Purchace added successfully";
                $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message));
            } else {
                ob_start();
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }
        } else if ($form_id == 10) { //vendor form
            if (isset($_POST['vendor_name']) and !empty($_POST['vendor_name']) 
                    and isset($_POST['contact_number']) and !empty($_POST['contact_number']) 
                    and isset($_POST['tin_number']) and !empty($_POST['tin_number']) 
                    and isset($_POST['contact_address']) and !empty($_POST['contact_address'])) {

                $vendor = new wendors();
                $vendor->wendor_name = $_POST['vendor_name'];
                $vendor->contact_no = $_POST['contact_number'];
                $vendor->wendor_tin_number = $_POST['tin_number'];
                $vendor->contact_address = $_POST['contact_address'];
                if ($vendor->addWendor()) {
                    $responce = array('status' => 'success', 'error' => '',
                        'data' => array('message' => 'Vendor Added successfully'));
                } else {
                    Log::e($tag, "Vendor adding failed item : " . $vendor->to_string());
                    $responce = array('status' => 'failed', 'error' => 'Some server error occured', 'data' => array());
                }
            } else {
                $responce = array('status' => 'failed', 'error' => 'Data missing', 'data' => array());
            }
        }else if ($form_id == 11) { //item form
            if (isset($_POST['item_code']) and !empty($_POST['item_code']) 
                    and isset($_POST['item_name']) and !empty($_POST['item_name']) 
                    and isset($_POST['mrp']) // and !empty($_POST['mrp']) 
                    and isset($_POST['tax_category_id']) and !empty($_POST['tax_category_id']) 
                    and isset($_POST['purchace_rate']) // and !empty($_POST['purchace_rate'])
                    and isset($_POST['discount_percent']) // and !empty($_POST['discount_percent'])
                    ) {

                $item = new item();
                $item->item_code = $_POST['item_code'];
                $item->item_name = $_POST['item_name'];
                $item->mrp = $_POST['mrp'];
                $item->tax_category_id = $_POST['tax_category_id'];
                $item->purchace_rate = $_POST['purchace_rate'];
                $item->discount_percent = $_POST['discount_percent'];
                if ($item->addItem()) {
                    $responce = array('status' => 'success', 'error' => '',
                        'data' => array('message' => 'Item Added successfully'));
                } else {
                    Log::e($tag, "Item adding failed item : " . $item->to_string() . 'Error : '.  mysql_error());
                    if(empty(mysql_error())){
                        $error_message = 'Some server error occured';
                    }else{
                        $error_message = mysql_error();
                    }
                    $responce = array('status' => 'failed', 'error' => $error_message, 'data' => array());
                }
            } else {
                $responce = array('status' => 'failed', 'error' => 'Data missing', 'data' => array());
            }
        } else if ($form_id == 22) { //bank form
            if (isset($_POST['bank_name']) and !empty($_POST['bank_name']) 
                    and isset($_POST['branch']) and !empty($_POST['branch']) 
                    and isset($_POST['ifsc_code']) and !empty($_POST['ifsc_code']) 
                    and isset($_POST['account_number']) and !empty($_POST['account_number'])) {

                $bank = new bank();
                $bank->bank_name = $_POST['bank_name'];
                $bank->branch = $_POST['branch'];
                $bank->ifsc_code = $_POST['ifsc_code'];
                $bank->account_number = $_POST['account_number'];
                if ($bank->addBank()) {
                    $responce = array('status' => 'success', 'error' => '',
                        'data' => array('message' => 'Bank Added successfully'));
                } else {
                    Log::e($tag, "Bank adding failed item : " . $bank->to_string() . 'Error : '.  mysql_error());
                    if(empty(mysql_error())){
                        $error_message = 'Some server error occured';
                    }else{
                        $error_message = mysql_error();
                    }
                    $responce = array('status' => 'failed', 'error' => $error_message, 'data' => array());
                }
            } else {
                $responce = array('status' => 'failed', 'error' => 'Data missing', 'data' => array());
            }
        } else if ($form_id == 27) { //shop form
            if (isset($_POST['shop_name']) and !empty($_POST['shop_name']) 
                    and isset($_POST['shop_code']) and !empty($_POST['shop_code'])) {

                $company = new company();
                $company->company_name = $_POST['shop_name'];
                $company->company_code = $_POST['shop_code'];
                if ($company->addCompany()) {
                    $responce = array('status' => 'success', 'error' => '',
                        'data' => array('message' => 'Shop Added successfully'));
                } else {
                    Log::e($tag, "Shop adding failed item : " . $company->to_string() . 'Error : '.  mysql_error());
                    $responce = array('status' => 'failed', 'error' => 'Some server error occured', 'data' => array());
                }
            } else {
                $responce = array('status' => 'failed', 'error' => 'Data missing', 'data' => array());
            }
        } else if ($form_id == 29) { //user form
            if (isset($_POST['name']) and !empty($_POST['name']) 
                    and isset($_POST['username']) and !empty($_POST['username'])
                    and isset($_POST['shop']) and !empty($_POST['shop'])
                    and isset($_POST['type']) and !empty($_POST['type'])
                    and isset($_POST['password']) and !empty($_POST['password'])) {

                $user = new user();
                $user->name = $_POST['name'];
                $user->user_name = $_POST['username'];
                $user->company_id = $_POST['shop'];
                $user->user_type_id = $_POST['type'];
                $user->password_hashed = md5($_POST['password']);
                if ($user->addUser()) {
                    $responce = array('status' => 'success', 'error' => '',
                        'data' => array('message' => 'User Added successfully'));
                } else {
                    Log::e($tag, "User adding failed item : " . $user->to_string() . 'Error : '.  mysql_error());
                    $responce = array('status' => 'failed', 'error' => 'Some server error occured', 'data' => array());
                }
            } else {
                $responce = array('status' => 'failed', 'error' => 'Data missing', 'data' => array());
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
