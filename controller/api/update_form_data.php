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
        $tag = "UPDATE_FORM_DATA";
        if ($form_id == 2) {   ///sales return
            if (isset($_POST['total']) //and !empty($_POST['total']) 
                    and isset($_POST['sale_id']) and !empty($_POST['sale_id']) 
                    and isset($_POST['net_amount']) //and !empty($_POST['net_amount']) 
                    and isset($_POST['tax_amount']) //and !empty($_POST['tax_amount']) 
                    and isset($_POST['discount'])// and !empty($_POST['discount']) 
                    and isset($_POST['items']) and !empty($_POST['items'])) {
                $sale = new sales();
                $sale->id = $_POST['sale_id'];
                $sale->getSale();
                $balance = $_POST['total'] - $sale->amount;
                $sale->amount = $_POST['total'];
                $sale->net_amount = $_POST['net_amount'];
                $sale->tax_amount = $_POST['tax_amount'];
                $sale->discount = $_POST['discount'];
                $sales_items_prev = $sale->getSalesItems();
                $sales_items_new = array();
                if(!($_POST['items'] == 'no_items')){
                    foreach ($_POST['items'] as $sales_array_item) {
                        $sales_item = new sales_items();
                        $sales_item->item_id = $sales_array_item['id'];
                        $sales_item->quantity = $sales_array_item['quantity'];
                        $sales_item->rate = $sales_array_item['rate'];
                        $sales_item->tax = $sales_array_item['tax'];
                        $sales_item->discount = $sales_array_item['discount'];
                        array_push($sales_items_new, $sales_item);
                    }
                }
                
                
                $sale->setSalesItems($sales_items_new);
                $sale->updateSale();
                
                if(isset($sale->customer_id) and !empty($sale->customer_id)){
                    $customer = new customer();
                    $customer->id = $sale->customer_id;
                    $customer->getCustomer();
                    $customer->total_purchace_amount = $customer->total_purchace_amount + $balance;
                    $customer->updateCustomer();
                }
                
                
                ///updating stock
                
                //fixing multiple occurences of same item in new array
                for ($i=0; $i<sizeof($sales_items_new); $i++){
                    $item_id = $sales_items_new[$i]->item_id;                    
                    for ($j=$i+1; $j<sizeof($sales_items_new); $j++){
                        if($item_id == $sales_items_new[$j]->item_id){
                            $sales_items_new[$i]->quantity = 
                                    $sales_items_new[$i]->quantity + $sales_items_new[$j]->quantity;
                            unset($sales_items_new[$j]);
                            $j--;
                            $sales_items_new = array_values($sales_items_new);
                        }
                    }
                }

                //fixing multiple occurences of same item in prev array
                for ($i=0; $i<sizeof($sales_items_prev); $i++){
                    $item_id = $sales_items_prev[$i]->item_id;                    
                    for ($j=$i+1; $j<sizeof($sales_items_prev); $j++){
                        if($item_id == $sales_items_prev[$j]->item_id){
                            $sales_items_prev[$i]->quantity = 
                                    $sales_items_prev[$i]->quantity + $sales_items_prev[$j]->quantity;
                            unset($sales_items_prev[$j]);
                            $j--;
                            $sales_items_prev = array_values($sales_items_prev);
                        }
                    }
                }
                
                
                
                ///statrted updating
                foreach ($sales_items_new as $sale_item_new) {
                    $item_id = $sale_item_new->item_id;
                    $new_item = true;
                    foreach ($sales_items_prev as $sale_item_prev) {
                        if($item_id == $sale_item_prev->item_id){
                            $new_item = false;
                            if($sale_item_new->quantity != $sale_item_prev->quantity ){
                                //updating quantity changed items
                                $diff = $sale_item_prev->quantity - $sale_item_new->quantity;
                                $inv = new inventry();
                                $inv->company_id = $sale->company_id;
                                $inv->item_id = $item_id;
                                $invs = $inv->getInventryForSpecificCompanyAndItem();
                                $inv = $invs[0];  
                                $inv->in_stock_count = $inv->in_stock_count + $diff; 
                                $inv->updateInventry();
                                $description = "Updating inventry (sales return) diff : ".$diff.", inventry : ".$inv->to_string();
                                Log::i($tag, $description);
                            }
                        }
                    }
                    if($new_item){   ///updating stock with new items
                        $qty = $sale_item_new->quantity;
                        $inv = new inventry();
                        $inv->company_id = $sale->company_id;
                        $inv->item_id = $item_id;
                        $invs = $inv->getInventryForSpecificCompanyAndItem();
                        $inv = $invs[0];  
                        $inv->in_stock_count = $inv->in_stock_count - $qty; 
                        $inv->updateInventry();
                        $description = "Updating inventry (sales return - new item) qty : ".$qty.", inventry : ".$inv->to_string();
                        Log::i($tag, $description);
                    }
                }
                //checking for removed items
                foreach ($sales_items_prev as $sale_item_prev) {
                    $item_id = $sale_item_prev->item_id;
                    $removed_item = true;
                    foreach ($sales_items_new as $sale_item_new) {
                        if($item_id == $sale_item_new->item_id){
                            $removed_item = false;
                        }
                    }
                    if($removed_item){
                        $qty = $sale_item_prev->quantity;
                        $inv = new inventry();
                        $inv->company_id = $sale->company_id;
                        $inv->item_id = $item_id;
                        $invs = $inv->getInventryForSpecificCompanyAndItem();
                        $inv = $invs[0];  
                        $inv->in_stock_count = $inv->in_stock_count + $qty; 
                        $inv->updateInventry();
                        $description = "Updating inventry (sales return - remoed item) diff : ".$qty.", inventry : ".$inv->to_string();
                        Log::i($tag, $description);
                    }
                }
                
                $message = "Sale Updated Successfuly";
                $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$sale->id));
            } else {
                ob_start();
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }
        } else if ($form_id == 4) { // bank deposit edit
            if (isset($_POST['amount']) and !empty($_POST['amount']) 
                    and isset($_POST['bank_deposit_id']) and !empty($_POST['bank_deposit_id'])
                    and isset($_POST['description']) and !empty($_POST['description'])) {
                $bank_deposit = new bank_deposits();
                $bank_deposit->id = $_POST['bank_deposit_id'];
                $bank_deposit->getBankDeposit();
                $bank_deposit->description = $_POST['description'];
                $bank_deposit->amount = $_POST['amount'];
                $bank_deposit->bank_id = $_POST['bank_id'];
                if ($bank_deposit->updateBankDeposit()) {
                    $responce = array('status' => 'success', 'error' => '',
                        'data' => array('message' => 'Bank Deposit Updated successfully'));
                } else {
                    Log::e($tag, "Bank Deposit updation failed Expence : " . $bank_deposit->to_string() . 'Error : '.  mysql_error());
                    $responce = array('status' => 'failed', 'error' => 'Some server error occured', 'data' => array());
                }
            } else {
                ob_start();
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }
        } else if ($form_id == 5) {
            if (isset($_POST['amount']) and !empty($_POST['amount']) 
                    and isset($_POST['expence_id']) and !empty($_POST['expence_id'])
                    and isset($_POST['description']) and !empty($_POST['description'])) {
                $expence = new expences();
                $expence->id = $_POST['expence_id'];
                $expence->getExpence();
                $expence->description = $_POST['description'];
                $expence->amount = $_POST['amount'];
                if ($expence->updateExpence()) {
                    $responce = array('status' => 'success', 'error' => '',
                        'data' => array('message' => 'Expence Updated successfully'));
                } else {
                    Log::e($tag, "Expence updation failed Expence : " . $expence->to_string() . 'Error : '.  mysql_error());
                    $responce = array('status' => 'failed', 'error' => 'Some server error occured', 'data' => array());
                }
            } else {
                ob_start();
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }
        } else if ($form_id == 6) {   ///edit customer
            if (isset($_POST['customer_id']) and !empty($_POST['customer_id']) 
                    and isset($_POST['customer_name']) and !empty($_POST['customer_name']) 
                    and isset($_POST['contact_number']) and !empty($_POST['contact_number'])) {
                $customer = new customer();
                $customer->id = $_POST['customer_id'];
                $customer->getCustomer();
                $customer->customer_name = $_POST['customer_name'];
                $customer->contact_number = $_POST['contact_number'];
                if($customer->updateCustomer()){
                    $message = "Customer Updated Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$customer->id)); 
                }else{
                    $description = "Customer update failed, Customer : ".$customer->to_string();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'success', 'error' => $message, 'data' => array());
                }               
                
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }            
        } else if ($form_id == 8) {   ///edit : purchace edit
            if (isset($_POST['purchace_id']) and !empty($_POST['purchace_id']) 
                    and isset($_POST['bill_number']) //and !empty($_POST['bill_number']) 
                    and isset($_POST['total']) //and !empty($_POST['total']) 
                    and isset($_POST['items']) and !empty($_POST['items'])) {
                $purchace = new purchaces();
                $purchace->id = $_POST['purchace_id'];
                $purchace->getPurchace();
                $purchace->amount = $_POST['total'];
                $purchace->bill_number = $_POST['bill_number'];
                $purchace_items_prev = $purchace->getPurchaceItems();
                $purchace_items_new = array();
                if(!($_POST['items'] == 'no_items')){
                    foreach ($_POST['items'] as $purchace_array_item) {
                        $purchace_item = new purchace_items();
                        $purchace_item->item_id = $purchace_array_item['id'];
                        $purchace_item->quantity = $purchace_array_item['quantity'];
                        $purchace_item->rate = $purchace_array_item['rate'];
                        array_push($purchace_items_new, $purchace_item);
                    }
                }
                $purchace->setPurchaceItems($purchace_items_new);
                
                if($purchace->updatePurchace()){
                    $message = "Purchace Updated Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$purchace->id)); 
                }else{
                    $description = "Purchace update failed, Purchace : ".$purchace->to_string();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'success', 'error' => $message, 'data' => array());
                }               
                
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }            
        }else if ($form_id == 10) {   ///edit vendor
            if (isset($_POST['vendor_id']) and !empty($_POST['vendor_id']) 
                    and isset($_POST['vendor_name']) and !empty($_POST['vendor_name']) 
                    and isset($_POST['contact_number']) and !empty($_POST['contact_number']) 
                    and isset($_POST['tin_number']) and !empty($_POST['tin_number']) 
                    and isset($_POST['contact_address']) and !empty($_POST['contact_address'])) {
                $vendor = new wendors();
                $vendor->id = $_POST['vendor_id'];
                $vendor->getWendor();
                $vendor->contact_address = $_POST['contact_address'];
                $vendor->contact_no = $_POST['contact_number'];
                $vendor->wendor_name = $_POST['vendor_name'];
                $vendor->wendor_tin_number = $_POST['tin_number'];
                if($vendor->updateWendor()){
                    $message = "Vendor Updated Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$vendor->id)); 
                }else{
                    $description = "Vendor update failed, vendor : ".$vendor->to_string();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'success', 'error' => $message, 'data' => array());
                }               
                
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }            
        }else if ($form_id == 11) {   ///edit item
            if (isset($_POST['item_id']) and !empty($_POST['item_id']) 
                    and isset($_POST['item_name']) and !empty($_POST['item_name']) 
                    and isset($_POST['item_code']) and !empty($_POST['item_code']) 
                    and isset($_POST['mrp']) // and !empty($_POST['mrp']) 
                    and isset($_POST['tax_category_id']) and !empty($_POST['tax_category_id']) 
                    and isset($_POST['purchace_rate']) // and !empty($_POST['purchace_rate'])
                    and isset($_POST['discount_percent']) // and !empty($_POST['discount_percent'])
                    ) {
                $item = new item();
                $item->id = $_POST['item_id'];
                $item->getItem();
                $item->item_code = $_POST['item_code'];
                $item->item_name = $_POST['item_name'];
                $item->mrp = $_POST['mrp'];
                $item->purchace_rate = $_POST['purchace_rate'];
                $item->tax_category_id = $_POST['tax_category_id'];
                $item->discount_percent = $_POST['discount_percent'];
                if($item->updateItem()){
                    $message = "Item Updated Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$item->id)); 
                }else{
                    $description = "Item update failed, item : ".$item->to_string();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'success', 'error' => $message, 'data' => array());
                }               
                
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }            
        } else if ($form_id == 22) {   ///edit bank
            if (isset($_POST['bank_id']) and !empty($_POST['bank_id']) 
                    and isset($_POST['bank_name']) and !empty($_POST['bank_name']) 
                    and isset($_POST['branch']) and !empty($_POST['branch']) 
                    and isset($_POST['ifsc_code']) and !empty($_POST['ifsc_code']) 
                    and isset($_POST['account_number']) and !empty($_POST['account_number'])) {
                $bank = new bank();
                $bank->id = $_POST['bank_id'];
                $bank->getBank();
                $bank->bank_name = $_POST['bank_name'];
                $bank->branch = $_POST['branch'];
                $bank->ifsc_code = $_POST['ifsc_code'];
                $bank->account_number = $_POST['account_number'];
                if($bank->updateBank()){
                    $message = "Bank Updated Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$bank->id)); 
                }else{
                    $description = "Bank update failed, Bank : ".$bank->to_string();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'success', 'error' => $message, 'data' => array());
                }               
                
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }            
        } else if ($form_id == 25) {   ///edit inventry
            if (isset($_POST['inventry_id']) and !empty($_POST['inventry_id']) 
                    and isset($_POST['in_stock_count']) // and !empty($_POST['in_stock_count']) 
                    and isset($_POST['mrp']) // and !empty($_POST['mrp']) 
                    and isset($_POST['tax_category_id']) and !empty($_POST['tax_category_id'])) {
                
                $inv = new inventry();
                $inv->id = $_POST['inventry_id'];
                $inv->getInventry();
                $inv->in_stock_count = $_POST['in_stock_count'];
                $inv->selling_prize = $_POST['mrp'];
                $inv->tax_category_id = $_POST['tax_category_id'];
                if($inv->updateInventry()){
                    $message = "Stock Updated Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$inv->id)); 
                }else{
                    $description = "Stock update failed, Stock : ".$inv->to_string();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'success', 'error' => $message, 'data' => array());
                }               
                
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }
        }else if ($form_id == 27) {   ///edit company
            if (isset($_POST['company_id']) and !empty($_POST['company_id']) 
                    and isset($_POST['shop_name']) and !empty($_POST['shop_name']) 
                    and isset($_POST['shop_code']) and !empty($_POST['shop_code'])) {

                $company = new company();
                $company->id = $_POST['company_id'];
                $company->company_name = $_POST['shop_name'];
                $company->company_code = $_POST['shop_code'];
                if($company->updateCompany()){
                    $message = "Shop Updated Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$company->id)); 
                }else{
                    $description = "Shop update failed, Shop : ".$company->to_string();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'success', 'error' => $message, 'data' => array());
                } 
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }
        } else if ($form_id == 29) {   ///edit user
            if (isset($_POST['user_id']) and !empty($_POST['user_id']) 
                    and isset($_POST['name']) and !empty($_POST['name']) 
                    and isset($_POST['username']) and !empty($_POST['username'])) {

                $user = new user();
                $user->id = $_POST['user_id'];
                $user->getUser();
                $user->name = $_POST['name'];
                $user->user_name = $_POST['username'];
                if(isset($_POST['password']) and !empty($_POST['password'])){
                    $user->password_hashed = md5($_POST['password']);                    
                }
                if($user->updateUser()){
                    $message = "User Updated Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$user->id)); 
                }else{
                    $description = "User update failed, user : ".$user->to_string();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'success', 'error' => $message, 'data' => array());
                } 
            }else {
                ob_start();
                print_r($_POST);
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }
        }else if ($form_id == 30) {   ///edit : purchace return
            if (isset($_POST['purchace_id']) and !empty($_POST['purchace_id']) 
                    and isset($_POST['bill_number']) //and !empty($_POST['bill_number']) 
                    and isset($_POST['total']) //and !empty($_POST['total']) 
                    and isset($_POST['items']) and !empty($_POST['items'])) {
                $purchace = new purchaces();
                $purchace->id = $_POST['purchace_id'];
                $purchace->getPurchace();
                $purchace->amount = $_POST['total'];
                $purchace->bill_number = $_POST['bill_number'];
                $purchace_items_prev = $purchace->getPurchaceItems();
                $purchace_items_new = array();
                if(!($_POST['items'] == 'no_items')){
                    foreach ($_POST['items'] as $purchace_array_item) {
                        $purchace_item = new purchace_items();
                        $purchace_item->item_id = $purchace_array_item['id'];
                        $purchace_item->quantity = $purchace_array_item['quantity'];
                        $purchace_item->rate = $purchace_array_item['rate'];
                        array_push($purchace_items_new, $purchace_item);
                    }
                }
                
                //getting user info 
                $user = new user();
                $user->id = $purchace->purchace_manager_id;
                $user->getUser();
                $shop_id = $user->company_id;
                //updating stock
                if(is_array($purchace_items_prev) and (count($purchace_items_prev) > 0)){
                    foreach ($purchace_items_prev as $purchace_item_prev) {
                        foreach ($purchace_items_new as $purchace_item_new) {
                            if($purchace_item_prev->item_id === $purchace_item_new->item_id){
                                $diff =  $purchace_item_prev->quantity - $purchace_item_new->quantity;
                                
                                $inventry = new inventry();
                                $inventry->company_id = $shop_id;
                                $inventry->item_id = $purchace_item_prev->item_id;
                                $inventry = $inventry->getInventryForSpecificCompanyAndItem()[0];
                                $inventry->in_stock_count = $inventry->in_stock_count - $diff;
                                $inventry->updateInventry();
                            }
                        }
                    } 
                }
                
                $purchace->setPurchaceItems($purchace_items_new);
                
                if($purchace->updatePurchace()){
                    $message = "Purchace Updated Successfuly";
                    $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "id"=>$purchace->id)); 
                }else{
                    $description = "Purchace update failed, Purchace : ".$purchace->to_string();
                    Log::e($tag, $description);
                    $message = "Some server error occured";
                    $responce = array('status' => 'success', 'error' => $message, 'data' => array());
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
