<?php

/**
 * Description of Purchaces
 *
 * @author Sakkeer Hussain
 */
class purchace_items {

    public $id;
    public $purchace_id;
    public $item_id;
    public $quantity;
    public $rate;
    public $created_at;
    public $last_edited;
    
    private $db_handler;
    private $tag = 'PURCHACE ITEM CONTROLLER';

    function __construct() {
        $this->db_handler = new DBConnection();
    }

    public function to_string() {
        return 'id : ' . $this->id . ' - '
                . 'purchace_id : ' . $this->purchace_id . ' - '
                . 'item_id : ' . $this->item_id . ' - '
                . 'quantity : ' . $this->quantity . ' - '
                . 'rate : ' . $this->rate . ' - '
                . 'created_at : ' . $this->created_at . ' - '
                . 'last_edited : ' . $this->last_edited;
    }

    function addPurchaceItem($purchace_item = null) {
        if ($purchace_item == null) {
            $purchace_item = $this;
        }
        $this->db_handler->add_model($purchace_item);
        $description = "Added new Purchace item (" . $purchace_item->to_string() . ")";
        Log::i($this->tag, $description);
    }
    function getPurchace_item(){
        return $this->db_handler->get_model(new Purchace(),  $this->id);
    }
    function getPurchace_items($purchace_id){
        $purchace_items = $this->db_handler->get_model_list($this,  'purchace_id = '.$purchace_id);
        return $purchace_items;
    }    
    function clearPurchaceItems($purchace_id){
        return $this->db_handler->delete_model_list($this,  'purchace_id = '.$purchace_id);
    }
    function getPurchaseRate($company_id,$date,$item_id){
        $query = "SELECT `purchace_items`.`rate` 
                    FROM `purchace_items` 
                    LEFT JOIN `purchaces` on `purchace_items`.`purchace_id` = `purchaces`.`id` 
                    WHERE `purchaces`.`company_id` = '$company_id' 
                        and `purchace_items`.`item_id` = '$item_id' 
                        and DATE(`purchace_items`.`creeated_at`) <= DATE('$date') 
                        and `purchaces`.`stocked` = 1
                    ORDER BY `purchace_items`.`creeated_at` DESC LIMIT  1";
        $purchase = $this->db_handler->get_model_list_from_query($query, "purchace_items");
        return $purchase[0]->rate;
    }
}