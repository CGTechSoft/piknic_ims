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
        $tag = "GET_FORM_DATA";
        if ($form_id == 2) { // sales
            if (isset($_POST['sale_id']) and !empty($_POST['sale_id'])) {
                $sale = new sales();
                $sale->id = $_POST['sale_id'];
                $result = $sale->getSale();
                if($result){
                    $user = new user();
                    $user->id = $_SESSION['user_id'];
                    $user->getUser();
                    if($sale->company_id == $user->company_id){
                        $message = "Sale Fetched successfuly";
                        $customer = new customer();
                        $customer->id = $sale->customer_id;
                        $customer->getCustomer();
                        $customer_name = $customer->customer_name.' ( '.$customer->id.' ) ';
                        $items = array();
                        if(is_array($sale->getSalesItems()) and count($sale->getSalesItems())){
                            foreach ($sale->getSalesItems() as $s_item) {
                                $item = new item();
                                $item->id = $s_item->item_id;
                                $item->getItem();
                                
                                $tax_category = new tax_category();
                                $tax_category->id = $item->tax_category_id;
                                $tax_category->getTaxCategory();
                                
                                $total = ($s_item->quantity * $s_item->rate);
                                $discount_percent = (($s_item->discount*100)/$total);
                                $total = $total - $s_item->discount;                                
                                
                                $s_item_array=array("item_id"=>$item->id ,"item_name"=>$item->item_name.' - '.$item->item_code , "quantity"=>  number_format($s_item->quantity, 3, '.',''), "rate"=>  number_format($s_item->rate, 2, '.',''), "tax"=>$s_item->tax, "tax_rate"=>$tax_category->tax_percentage, "discount"=>$s_item->discount, "discount_percent"=>$discount_percent, "total"=>  number_format($total, 2, '.',''));
                                array_push($items, $s_item_array);
                            }
                        }
                        $date = date('d/m/Y',(strtotime($sale->sale_at)+(5.5*60*60) ));
                        $time = date('h:m A',(strtotime($sale->sale_at)+(5.5*60*60) ));
                        $sales_array = array("id"=>$sale->id,"date"=>$date,"time"=>$time,"customer"=>$customer_name,"c_name"=>$customer->customer_name,"c_id"=>$customer->id, "amount"=>  number_format($sale->amount, 2, '.',''), "tax"=>$sale->tax_amount, "discount"=>$sale->discount, "items"=>$items);
                        $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "data"=>$sales_array));
                    } else {
                        $responce = array('status' => 'failed', 'error' => 'The Sale is of another shop', 'data' => array());
                    }
                } else {
                    $responce = array('status' => 'failed', 'error' => 'Invalid Sale ID', 'data' => array());
                }                    
           } else {
                ob_start();
                $a = ob_get_clean();
                $responce = array('status' => 'failed', 'error' => 'Data missing' . $a, 'data' => array());
            }
        } else if ($form_id == 8) { //purchace
            if (isset($_POST['purchace_id']) and !empty($_POST['purchace_id'])) {
                $purchace = new purchaces();
                $purchace->id = $_POST['purchace_id'];
                $result = $purchace->getPurchace();
                if($result){
                    $user = new user();
                    $user->id = $_SESSION['user_id'];
                    $user->getUser();
                    if($purchace->company_id == $user->company_id){
                        $message = "Purchace Fetched successfuly";
                        $vendor = new wendors();
                        $vendor->id = $purchace->wendor_id;
                        $vendor->getWendor();
                        $vendor_name = $vendor->wendor_name.' ( '.$vendor->id.' ) ';
                        $items = array();
                        if(is_array($purchace->getPurchaceItems()) and count($purchace->getPurchaceItems())){
                            foreach ($purchace->getPurchaceItems() as $p_item) {
                                $item = new item();
                                $item->id = $p_item->item_id;
                                $item->getItem();
                                $p_item_array=array("item_name"=>$item->item_name.' - '.$item->item_code.' ( ID : '.$item->id.' )' , "quantity"=> number_format($p_item->quantity, 3, '.', ''), "rate"=>number_format($p_item->rate, 2, '.', ''));
                                array_push($items, $p_item_array);
                            }
                        }
                        $purchace_array = array("id"=>$purchace->id,"wendor"=>$vendor_name, "stocked"=>$purchace->stocked, "amount"=>  number_format($purchace->amount, 2, '.', ''), "bill_number"=>$purchace->bill_number, "items"=>$items);
                        $responce = array('status' => 'success', 'error' => '', 'data' => array("message" => $message, "data"=>$purchace_array));
                    } else {
                        $responce = array('status' => 'failed', 'error' => 'The Purchace is of another shop', 'data' => array());
                    }
                } else {
                    $responce = array('status' => 'failed', 'error' => 'Invalid Purchace ID', 'data' => array());
                }                    
           } else {
                ob_start();
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
