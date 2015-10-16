<?php

/**
 * Description of Sales
 *
 * @author Sakkeer Hussain
 */
class sales {

    public $id;
    public $bill_number;
    public $customer_id;
    public $amount;
    public $sale_at;
    public $net_amount;
    public $tax_amount;
    public $company_id;
    public $last_edited;
    public $discount;


    private $sales_items = array();
    private $db_handler;
    private $tag = 'SALES CONTROLLER';

    function __construct() {
        $this->db_handler = new DBConnection();
    }

    public function to_string() {
        $sales_items = '';
        foreach ($this->sales_items as $sales_item) {
            $sales_items = $sales_items . '[' . $sales_item->to_string() . ']';
        }

        return 'id : ' . $this->id . ' - '
                . 'customer_id : ' . $this->customer_id . ' - '
                . 'amount : ' . $this->amount . ' - '
                . 'sale_at : ' . $this->sale_at . ' - '
                . 'sale_items : ' . $sales_items . ' - '
                . 'net_amount : ' . $this->net_amount . ' - '
                . 'discount : ' . $this->discount . ' - '
                . 'company_id : ' . $this->company_id;
    }

    public function setSalesItems($sales_items) {
        $this->sales_items = $sales_items;
    }

    public function getSalesItems() {
        return $this->sales_items;
    }

    function addSales($sale = null) {
        if ($sale == null) {
            $sale = $this;
        }
        $bill_number = $this->db_handler->get_model_max_value($this, "bill_number", "company_id = ".$this->company_id);
        $this->bill_number = $bill_number + 1;
        $sale_id = $this->db_handler->add_model($sale);
        if (is_array($this->sales_items) and count($this->sales_items) != 0) {
            foreach ($this->sales_items as $sales_item) {
                $sales_item->sale_id = $sale_id;
                $sales_item->company_id = $sale->company_id;
                $sales_item->addSaleItem();
                $inv = new inventry();
                $inv->company_id = $sale->company_id;
                $inv->item_id = $sales_item->item_id;
                $invs = $inv->getInventryForSpecificCompanyAndItem();
                $inv = $invs[0];
                $inv->in_stock_count = $inv->in_stock_count - $sales_item->quantity;
                $inv->updateInventry();
            }
        }
        $description = "Added new Sale (" . $sale->to_string() . ")";

        $customer = new customer();
        $customer->id = $sale->customer_id;
        $customer->getCustomer();
        $customer->total_purchace_amount = $customer->total_purchace_amount + $sale->amount;
        $customer->updateCustomer();

        Log::i($this->tag, $description);
        $sale_array = array("id"=>$sale_id, "bill_number"=>  $this->bill_number);
        return $sale_array;
    }

    function updateSale($sale = null) {
        if ($sale == null) {
            $sale = $this;
        }
        $sale_id = $this->id;
        $this->db_handler->update_model($sale);
        $sale_item_obj = new sales_items();
        $sale_item_obj->clearSaleItems($sale_id);
        if (is_array($this->sales_items) and count($this->sales_items) != 0) {
            foreach ($this->sales_items as $sales_item) {
                $sales_item->sale_id = $sale_id;
                $sales_item->company_id = $sale->company_id;
                $sales_item->addSaleItem();
            }
        }
        $description = "Updating Sale (" . $sale->to_string() . ")";
        Log::i($this->tag, $description);
    }

    function getSale() {
        $result = $this->db_handler->get_model($this, $this->id);
        if ($result) {
            $sale_item = new sales_items();
            $this->sales_items = $sale_item->getSaleItems($this->id);
            return $this;
        } else {
            return FALSE;
        }
    }

    function getSales($company_id) {
        $sales = $this->db_handler->get_model_list($this, 'company_id = ' . $company_id);
        if (is_array($sales) and count($sales) != 0) {
            foreach ($sales as $sale) {
                $sale_item = new sales_items();
                $sale->sales_items = $sale_item->getSaleItems($sale->id);
            }
        }
        return $sales;
    }

    function getTodaysSales($company_id) {
        $sales = $this->db_handler->get_model_list($this, 'company_id = ' . $company_id . ' and DATE(`sale_at`) = DATE(NOW()) ORDER BY `id` DESC');
        if (is_array($sales) and count($sales) != 0) {
            foreach ($sales as $sale) {
                $sale_item = new sales_items();
                $sale->sales_items = $sale_item->getSaleItems($sale->id);
            }
        }
        return $sales;
    }

    function getLastThreeDaysSales($company_id, $start, $limit) {
        $sales = $this->db_handler->get_model_list($this, 'company_id = ' . $company_id . " and `sale_at` >= DATE_SUB(CURDATE(), INTERVAL 2 DAY) ORDER BY `id` DESC LIMIT  $start,$limit");
        if (is_array($sales) and count($sales) != 0) {
            foreach ($sales as $sale) {
                $sale_item = new sales_items();
                $sale->sales_items = $sale_item->getSaleItems($sale->id);
            }
        }
        return $sales;
    }

    function getLastThreeDaysSalesCount($company_id) {
        $last_three_days_sales_count = $this->db_handler->get_model_count($this, 'company_id = ' . $company_id . ' and `sale_at` >= DATE_SUB(CURDATE(), INTERVAL 2 DAY) ORDER BY `id` DESC');
        return $last_three_days_sales_count;
    }

    function getSalesOfADay($company_id, $date) {
        $sales = $this->db_handler->get_model_list($this, '`company_id` = ' . $company_id . ' and DATE(`sale_at`) = \''.$date.'\' ORDER BY `id` DESC');
        if (is_array($sales) and count($sales) != 0) {
            foreach ($sales as $sale) {
                $sale_item = new sales_items();
                $sale->sales_items = $sale_item->getSaleItems($sale->id);
            }
        }
        return $sales;
    }

    function getThisMonthsSales($company_id) {
        $sales = $this->db_handler->get_model_list($this, 'company_id = ' . $company_id . ' and YEAR(`sale_at`) = YEAR(NOW()) and MONTH(`sale_at`) = MONTH(NOW()) ORDER BY `id` DESC');
        if (is_array($sales) and count($sales) != 0) {
            foreach ($sales as $sale) {
                $sale_item = new sales_items();
                $sale->sales_items = $sale_item->getSaleItems($sale->id);
            }
        }
        return $sales;
    }

    function getOneMonthsSaleSummary($company_id, $month, $year) {
        $query = "SELECT SUM(`net_amount`) as `net_amount`,SUM(`tax_amount`) as `tax_amount`,SUM(`amount`) as `amount` FROM `sales` WHERE YEAR(`sale_at`) = $year AND MONTH(`sale_at`) = $month and `company_id` = $company_id ";
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

    function getOneDaySaleIncome($company_id, $date) {
        $query = "SELECT SUM(`amount`) as `amount` FROM `sales` WHERE DATE(`sale_at`) = '$date' and `company_id` = $company_id ";
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

    function getOneDaysSaleStatistics($company_id, $date) {
        $query = "SELECT count(*) as `count`, SUM(`amount`) as `amount` , SUM(`net_amount`) as `net_amount` ,"
                . " MIN(`bill_number`) as `min_bill_number`, MAX(`bill_number`) as `max_bill_number`, "
                . " SUM(`tax_amount`) as `tax_amount`, SUM(`discount`) as `discount` "
                    ."FROM `sales` WHERE DATE(`sale_at`) = '" . $date . "' and `company_id` = $company_id ";
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

    function getOneDayTaxDetails($company_id, $date, $tax_category_id) {
        $query = "SELECT count(*) as `count`, SUM(`quantity` * `rate`) as `amount`, SUM(`tax`) as `tax` 
                    FROM `sales_items`
                    LEFT JOIN `item` on `sales_items`.`item_id` = `item`.`id`   
                    WHERE DATE(`sales_items`.`created_at`) = '$date' 
                                and `sales_items`.`company_id` = '$company_id' 
                                and `item`.`tax_category_id` = '$tax_category_id'";
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
    function getOneDaysSaleExpence($company_id,$date){
        $sale_item = new sales_items();
        $sales_items = $sale_item->getOneDaysSaleItems($company_id, $date);
        $total_purchase_cost = 0;
        $purchase_item = new purchace_items();
        if(is_array($sales_items) and count($sales_items)){
            foreach ($sales_items as $sale_item_array) {
                $item_id = $sale_item_array->item_id;
                $quantity = $sale_item_array->quantity;
                $purchace_rate = $purchase_item->getPurchaseRate($company_id, $date, $item_id);
                /* @var $purchace_rate : float */
                $total_purchase_cost += $purchace_rate * $quantity;
            }
        }
        return $total_purchase_cost;
    }

}

//$s = new sales();
//$s->amount  = 100;
//$s->company_id =1;
//$s->customer_id=1;
//$s->net_amount =66;
//$s->tax_amount = 34;
//
//$si = new sales_items();
//$si->item_id = 1;
//$si->quantity=10;
//$si->sale_id = 1;
//$si->rate = 1;
//
//$s->setSalesItems(array($si, $si, $si));

//$s->id = 3;
//$s->getSale();
//
//print_r($s);
//$s->addSales();
