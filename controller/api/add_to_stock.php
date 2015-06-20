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
    if (isset($_POST['purchace_id']) and !empty($_POST['purchace_id'])) {

        $purchace = new purchaces();
        $purchace->id = $_POST['purchace_id'];
        $purchace->getPurchace();
        if ($purchace->stocked) {
            $responce = array('status' => 'failed', 'error' => 'Purchace already stocked', 'data' => array());
        } else {

            foreach ($purchace->getPurchaceItems() as $p_item) {
                $inv = new inventry();
                $inv->company_id = $purchace->company_id;
                $inv->item_id = $p_item->item_id;
                $invs = $inv->getInventryForSpecificCompanyAndItem();
                if ($invs) {
                    $inv->id = $invs[0]->id;
                    $inv->in_stock_count = $invs[0]->in_stock_count + $p_item->quantity;
                    $item = new item();
                    $item->id = $p_item->item_id;
                    $item->getItem();
                    $inv->selling_prize = $item->mrp;
                    $inv->tax_category_id = $item->tax_category_id;
                    $inv->updateInventry();
                } else {
                    $inv->in_stock_count = $p_item->quantity;
                    $item = new item();
                    $item->id = $p_item->item_id;
                    $item->getItem();
                    $inv->selling_prize = $item->mrp;
                    //need to update
                    $inv->tax_category_id = 1;
                    $inv->addInventry();
                }
            }
            if ($purchace->markAsStocked()) {
                $message = "Purchace added to stock succesfully";
                $responce = array('status' => 'success', 'error' => '', 'data' => array('message' => $message));
            } else {
                $responce = array('status' => 'failed', 'error' => 'Purchace already stocked', 'data' => array());
            }
        }
    }
} else {
    $responce = array('status' => 'failed', 'error' => 'Session expired', 'data' => array());
}
echo json_encode($responce);
