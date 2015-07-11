<?php

/**
 * Description of Customer
 *
 * @author Sakkeer Hussain
 */
class customer {
    
    public $id;
    public $customer_name;
    public $total_purchace_amount;
    public $contact_number;
    public $company_id;
    public $purchace_amount_to_avail_redeem;
    public $created_at;
    public $last_edited;
    
    private $db_handler;
    private $tag = 'CUSTOMER CONTROLLER';

    function __construct() {
        $this->db_handler = new DBConnection();
    }

    public function to_string() {
        return 'id : ' . $this->id . ' - '
                . 'customer_name : ' . $this->customer_name . ' - '
                . 'total_purchace_amount : ' . $this->total_purchace_amount . ' - '
                . 'purchace_amount_to_avail_redeem : ' . $this->purchace_amount_to_avail_redeem . ' - '
                . 'contact_number : ' . $this->contact_number . ' - '
                . 'company_id : ' . $this->company_id . ' - '
                . 'created_at : ' . $this->created_at . ' - '
                . 'last_edited : ' . $this->last_edited;
    }

    function addCustomer($customer = null) {
        if ($customer == null){
            $customer = $this;
        }
        $result = $this->db_handler->add_model($customer);        
        if($result){
            $description = "Added new Customer (" . $customer->to_string() . ")";
            Log::i($this->tag, $description);
        }
        return $result;
    }
    function getCustomer(){
        return $this->db_handler->get_model($this,  $this->id);
    }
    function updateCustomer(){
        return $this->db_handler->update_model($this);
        $description = "Updating Customer (" . $this->to_string() . ")";
        Log::i($this->tag, $description);
    }
    function deleteCustomer(){
        $result = $this->db_handler->delete_model($this);
        $description = "Customer deleted, result : ".$result;
        Log::i($this->tag, $description);
        return $result;
    }
    function getCustomers($company_id){
        $customers = $this->db_handler->get_model_list($this,  'company_id = '.$company_id . " ORDER BY `id` DESC ");
        return $customers;
    }
    function getCustomersPaged($company_id, $start, $limit){
        $customers = $this->db_handler->get_model_list($this,  'company_id = '.$company_id . " ORDER BY `id` DESC LIMIT  $start,$limit");
        return $customers;
    }
    function getCustomersCount($company_id){
        $customers_count = $this->db_handler->get_model_count($this, 'company_id = ' . $company_id );
        return $customers_count;
    }
}
