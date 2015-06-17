<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Sales_items
 *
 * @author Sakkeer Hussain
 */
class sales_items {

    public $id;
    public $sale_id;
    public $item_id;
    public $rate;
    public $quantity;
    public $tax;
    public $last_edited;
    public $company_id;
    public $total;
    public $discount;
    
    private $db_handler;
    private $tag = 'SALES ITEMS CONTROLLER';

    function __construct() {
        $this->db_handler = new DBConnection();
    }

    public function to_string() {
        return 'id : ' . $this->id . ' - '
                . 'sale_id : ' . $this->sale_id . ' - '
                . 'item_id : ' . $this->item_id . ' - '
                . 'rate : ' . $this->rate . ' - '
                . 'quantity : ' . $this->quantity . ' - '
                . 'discount : ' . $this->discount . ' - '
                . 'tax : ' . $this->tax;
    }

    function addSaleItem($sale_item = null) {
        if ($sale_item == null){
            $sale_item = $this;
        }
        $result = $this->db_handler->add_model($sale_item);
        if($result){
            $description = "Added new Sale Item (" . $sale_item->to_string() . ")";
            Log::i($this->tag, $description);
        }else{
            $description = "Adding new Sale Item failed (" . $sale_item->to_string() . ")";
            $description = $description . mysql_error();
            Log::i($this->tag, $description);
        }
    }
    function getSaleItem(){
        return $this->db_handler->get_model($this,  $this->id);
    }
    function getSaleItems($sale_id){
        return $this->db_handler->get_model_list($this,  'sale_id = '.$sale_id);
    }
    function getTodaysSaleItems($company_id){
        $query = "SELECT `item_id` , SUM(`quantity`) as `quantity`, SUM(`rate` * `quantity`) as `total` FROM `sales_items` WHERE DATE(`created_at`) = DATE(NOW()) and `company_id` = $company_id GROUP BY (`item_id`)";
        return $this->db_handler->get_model_list_from_query($query,  'sales_items');
    }
    function getOneDaysSaleItems($company_id,$date){
        $query = "SELECT `item_id` , SUM(`quantity`) as `quantity`, SUM(`rate` * `quantity`) as `total` FROM `sales_items` WHERE DATE(`created_at`) = '".$date."' and `company_id` = $company_id GROUP BY (`item_id`)";
        return $this->db_handler->get_model_list_from_query($query,  'sales_items');
    }
    function clearSaleItems($sale_id){
        return $this->db_handler->delete_model_list($this,  'sale_id = '.$sale_id);
    }
}
