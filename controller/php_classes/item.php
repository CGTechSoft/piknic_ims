<?php

/**
 * Description of Item
 *
 * @author Sakkeer Hussain
 */
class item {
    public $id ;
    public $item_name;
    public $item_code;
    public $mrp;
    public $purchace_rate;
    public $tax_category_id;
    
    private $db_handler;
    private $tag = 'ITEM CONTROLLER';
    
    function __construct() {
        $this->db_handler = new DBConnection();
    }
    public function to_string() {
        return 'id : '.$this->id.' - '
                .'item_code : '.$this->item_code.' - '
                .'item_name : '.$this->item_name.' - '
                .'purchace_rate : '.$this->purchace_rate.' - '
                .'item_code : '.$this->item_code.' - '
                .'tax_category_id : '.$this->tax_category_id.' - '
                .'mrp : '.$this->mrp;
    }
    function addItem($item=null){
        if($item==null){
            $item = $this;
        }
        if($this->db_handler->add_model($item)){
            $description = "Added new item (".  $item->to_string().")";
            Log::i($this->tag, $description);
            return True;
        }else{
            return FALSE;
        }
    }
    function getItem(){
        return $this->db_handler->get_model($this,  $this->id);
    }
    function getItems(){
        return $this->db_handler->get_model_list($this);
    }
}
