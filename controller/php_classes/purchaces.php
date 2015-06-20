<?php

//require './DBConnection.php';
//require './Constants.php';
//require './Log.php';
//require './Purchace_items.php';
/**
 * Description of Purchaces
 *
 * @author Sakkeer Hussain
 */
class purchaces {

    public $id;
    public $wendor_id;
    public $purchace_manager_id;
    public $company_id;
    public $amount;
    public $created_at;
    public $last_edited;
    public $stocked;
    public $bill_number;
    private $purchace_items = array();
    private $db_handler;
    private $tag = 'PURCHACE CONTROLLER';

    function __construct() {
        $this->db_handler = new DBConnection();
    }

    public function to_string() {
        $purchace_items = '';
        
        if(is_array($this->purchace_items) and count($this->purchace_items)){
            foreach ($this->purchace_items as $purchace_item) {
                $purchace_items = $purchace_items . '[' . $purchace_item->to_string() . ']';
            }
        }else{
            $purchace_items = "No items";
        }
        return 'id : ' . $this->id . ' - '
                . 'wendor_id : ' . $this->wendor_id . ' - '
                . 'purchace_manager_id : ' . $this->purchace_manager_id . ' - '
                . 'company_id : ' . $this->company_id . ' - '
                . 'amount : ' . $this->amount . ' - '
                . 'purchace_items : ' . $purchace_items . ' - '
                . 'created_at : ' . $this->created_at . ' - '
                . 'last_edited : ' . $this->last_edited;
    }

    public function setPurchaceItems($purchace_items) {
        $this->purchace_items = $purchace_items;
    }

    public function getPurchaceItems() {
        return $this->purchace_items;
    }
    
    function updatePurchace($purchace = null){
        if($purchace==null){
            $purchace = $this;
        }
        $purchace_id = $this->id;
        $result = $this->db_handler->update_model($purchace);
        if($result){
            $purchace_item_obj = new purchace_items();
            $purchace_item_obj->clearPurchaceItems($purchace_id);
            foreach ($this->purchace_items as $purchace_item) {
                $purchace_item->purchace_id = $purchace_id;
                $purchace_item->addPurchaceItem();
            }
            $description = "Updating Purchace (". $purchace->to_string().")";
            Log::i($this->tag, $description);
            return TRUE;        
        }else{
            return FALSE;
        }
    }
    
    function deletePurchace(){
        $result = $this->db_handler->delete_model($this);
        if($result){
            $purchace_item_obj = new purchace_items();
            $purchace_item_obj->clearPurchaceItems($this->id);
            $description = "Deleted Purchace (". $this->to_string().")";
            Log::i($this->tag, $description);
            return TRUE;        
        }else{
            return FALSE;
        }
    }

    function addPurchace($purchace = null) {
        if ($purchace == null) {
            $purchace = $this;
        }
        $purchace_id = $this->db_handler->add_model($purchace);
        foreach ($this->purchace_items as $purchace_item) {
            $purchace_item->purchace_id = $purchace_id;
            $purchace_item->addPurchaceItem();
        }
        $description = "Added new Purchace (" . $purchace->to_string() . ")";
        Log::i($this->tag, $description);
    }

    function markAsStocked() {
        $query = "UPDATE `purchaces` SET `stocked`=1 WHERE `id` =" . $this->id . ";";
        return $this->db_handler->executeQuery($query);
    }

    function getPurchace() {
        $result = $this->db_handler->get_model($this, $this->id);
        if($result){
            $purchace_item = new purchace_items();
            $purchace_items = $purchace_item->getPurchace_items($this->id);
            $this->purchace_items = $purchace_items;
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function getPurchaces($company_id) {
        $purchaces = $this->db_handler->get_model_list($this, 'company_id = ' . $company_id);
        if($purchaces!=NULL && sizeof($purchaces)!=0){
            foreach ($purchaces as $purchace) {
            	$purchace_item = new purchace_items();
            	$purchace->purchace_items = $purchace_item->getPurchace_items($purchace->id);
            }
        }
        return $purchaces;
    }

    function getPurchacesDESC($company_id, $start, $limit) {
        $purchaces = $this->db_handler->get_model_list($this, 'company_id = ' . $company_id . " ORDER BY `id` DESC LIMIT  $start,$limit");
        if($purchaces!=NULL && sizeof($purchaces)!=0){
            foreach ($purchaces as $purchace) {
            	$purchace_item = new purchace_items();
            	$purchace->purchace_items = $purchace_item->getPurchace_items($purchace->id);
            }
        }
        return $purchaces;
    }

    function getNotStockedPurchaces($company_id) {
        $purchaces = $this->db_handler->get_model_list($this, 'company_id = ' . $company_id . " and stocked=0 ORDER BY `id` DESC");
        if($purchaces!=NULL && sizeof($purchaces)!=0){
            foreach ($purchaces as $purchace) {
                $purchace_item = new purchace_items();
                $purchace->purchace_items = $purchace_item->getPurchace_items($purchace->id);
            }
        }
        return $purchaces;
    }

    function getOneMonthsPurchaceSummary($company_id, $month, $year) {
        $query = "SELECT SUM(`amount`) as `amount` FROM `purchaces` WHERE YEAR(`created_at`) = $year AND MONTH(`created_at`) = $month and `company_id` = $company_id and `stocked` = 1 ";
        $result = $this->db_handler->executeQuery($query);
        $vals = array();
        if ($row = mysql_fetch_assoc($result)) {
            foreach ($row as $key => $value) {
                $vals[$key] = $value;
            }
            return $vals;
        } else {
            return FALSE;
        }
    }

    function getPurchacesCount($company_id) {
        $purchaces_count = $this->db_handler->get_model_count($this, 'company_id = ' . $company_id );
        return $purchaces_count;
    }

}

//registering for class auto loading
//spl_autoload_register(function($class_name) {
//    $controller_root = $_SERVER['DOCUMENT_ROOT'] . '/piknik_ims/controller';
//    if (file_exists($controller_root . '/php_classes/' . $class_name . '.php')) {
//        $file_name = $controller_root . '/php_classes/' . $class_name . '.php';
//        require_once $file_name;
//    } else {
//        throw new Exception("Class " . $class_name . " Not found");
//    }
//});
//$p = new Purchaces();
//$p->id = 4;
////$p->amount = 300;
////$p->wendor_id = 1;
////$p->purchace_manager_id = 1;
////$pi = new Purchace_items();
////$pi->item_id=1;
////$pi->quantity=10;
////$pi->rate=10;
////$pia = array($pi,$pi,$pi);
////$p->setPurchaceItems($pia);
////$p->addPurchace();
//$p->getPurchace();
//print_r($p);
//echo $p->getPurchacesCount(1);