<?php

function get_form_html($form_id, $date) {
    ob_start();
    ?>
    <div id="head_div" style="padding: 5px 0; background-color: #ECECEC;  color: #FFD00F;
         border-radius: 5px;margin-left: auto; text-align: center; ">
        SALES REPORT OF 
        <input id="date_field" value="<?php 
            if($date == 0 ){ 
                $date = date('d/m/Y',time());
            }
            echo $date;
            ?>" />
    </div>
    <div style="margin-top: 10px; background-color:transparent;padding-bottom: 30px;">
        <style>
            div#purchace_items td{
                border: 1px solid #FFD00F;
            }
            div#purchace_items tbody td{
                padding: 5px 0 5px 5px;
            }
            div#purchace_items tbody td input,div#purchace_items tbody td select{
                padding: 0;
                border: 0;
                margin: 0;
                height: 100%;
                width: 100%;
                background-color: transparent;
            }
        </style>
        <div id="purchace_items" style="width: 100%; padding: 10px 0; color: #FFD00F;">           
            <table id="items_table" style="border-collapse: collapse; width: 100%; 
                   background-color: #fff; border-radius: 10px;  color: #FFD00F;">
                <thead style="text-align: center;">
                    <tr>
                        <td>
                            #
                        </td>
                        <td>
                            ID
                        </td>
                        <td>
                            DATE &amp; TIME
                        </td>
                        <td>
                            CUSTOMER
                        </td>
                        <td style="">
                            TAX
                        </td>
                        <td style="">
                            NET. AMOUNT
                        </td>
                        <td style="">
                            TOTAL
                        </td>
                        <td style="">

                        </td>
                    </tr>
                </thead>
                <tbody style="padding-left: 3px; text-align: center; ">
                    <?php
                    $sale_obj = new sales();
                    $user = new user();
                    $user->id = $_SESSION['user_id'];
                    $user->getUser();
                    
                    $date = str_replace('/', '-', $date);
                    $date = date('Y-m-d', strtotime($date));
                    
                    $sales = $sale_obj->getSalesOfADay($user->company_id, $date);
                    $i = 0;
                    if(is_array($sales) and count($sales)!=0){
                      foreach ($sales as $sale) {
                        ?>
                        <tr id="<?php echo $sale->id; ?>" onclick="select_row(this)" status="not_selected"  >
                            <td style="text-align: center;">
                                <?php echo ++$i; ?>
                            </td>
                            <td>
                                <?php echo $sale->id; ?>
                            </td>
                            <?php 
                                $date = date('d/m/Y',(strtotime($sale->sale_at)+(5.5*60*60) ));
                                $time = date('h:m A',(strtotime($sale->sale_at)+(5.5*60*60) ));
                            ?>
                            <td id="date" date="<?php  echo $date ?>" time="<?php  echo $time ?>">
                                <?php  echo $date . ' - ' . $time; ?>
                            </td>
                            <?php 
                                $customer = new customer();
                                $customer->id = $sale->customer_id;
                                $customer->getCustomer();
                            ?>
                            <td id="customer" c_name="<?php echo $customer->customer_name?>" c_id="<?php echo $customer->id ?>" >
                                <?php 
                                echo $customer->customer_name. ' ( ID : '.$customer->id.' )';
                                ?>
                            </td>
                            <td id="tax">
                                <?php echo $sale->tax_amount; ?>
                            </td>
                            <td id="net_amount">
                                <?php echo $sale->net_amount; ?>
                            </td>
                            <td id="total">
                                <?php echo $sale->amount; ?>
                            </td>
                            <td id="down_button" style="width: 20px;text-align: center; padding: 10px;">
                                <img id="toggle_button" style="width: 20px; height: 20px; cursor: pointer;"
                                     onclick="toggle_items_visibility(this)" src="../ui/images/down_arrow.png"/>
                                <img id="toggle_button" style="width: 20px; height: 20px; cursor: pointer; display: none;"
                                     onclick="toggle_items_visibility(this)" src="../ui/images/up_arrow.png"/>
                            </td>
                        </tr>
                        <tr style="display: none;">
                            <td colspan="8" style="padding:0 0 20px 0;">
                                <table  id="sale_items" style="border-collapse: collapse; background-color: #c0effd; width: 80%; color: #FFD00F; float: right;">
                                    <thead>
                                    <tr>
                                        <td>
                                            ITEM
                                        </td>
                                        <td>
                                            QUANTITY
                                        </td>
                                        <td>
                                            RATE
                                        </td>
                                        <td>
                                            NET. AMOUNT
                                        </td>
                                        <td>
                                            TAX
                                        </td>
                                        <td>
                                            TOTAL
                                        </td>
                                    </tr>
                                </thead>
                                    <tbody>
                                    <?php
                                    if (is_array($sale->getSalesItems()) and count($sale->getSalesItems()) ) {
                                    foreach ($sale->getSalesItems() as $s_item) {
                                        ?>
                                    <tr id="<?php echo $s_item->id; ?>">
                                        <?php
                                        $item = new item();
                                        $item->id = $s_item->item_id;
                                        $item->getItem();
                                        ?>
                                            <td id="item" item_name="<?php echo $item->item_name; ?>"><?php
                                                echo $item->item_name . ' - ' . $item->item_code;
                                            ?></td>
                                            <td id="quantity" val="<?php echo $s_item->quantity; ?>">
                                                <?php echo number_format($s_item->quantity, 3, '.',''); ?>
                                            </td>
                                            <td id="rate" val="<?php echo $s_item->rate; ?>">
                                                <?php echo number_format($s_item->rate, 2, '.',''); ?>
                                            </td>                                            
                                            <td>
                                                <?php echo number_format((($s_item->quantity * $s_item->rate) - $s_item->tax), 2, '.',''); ?>
                                            </td>    
                                            <?php 
                                                $tax_category = new tax_category();
                                                $tax_category->id = $item->tax_category_id;
                                                $tax_category->getTaxCategory();
                                            ?>
                                            <td id="tax" val="<?php echo $s_item->tax; ?>" tax_rate="<?php echo $tax_category->tax_percentage; ?>">
                                                <?php echo number_format($s_item->tax, 2, '.',''); ?>
                                            </td>
                                            <td id="total" val="<?php echo ($s_item->quantity * $s_item->rate); ?>">
                                                <?php echo number_format(($s_item->quantity * $s_item->rate), 2, '.',''); ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    }else{
                                        echo '<tr><td colspan="6">No item found</td></tr>';
                                    }
                                    ?>
                                        </tbody>
                                </table>
                            </td>
                        </tr>
                        <?php
                    } 
                }else{
                    echo '<tr><td colspan="8"> No Sales Found </td></tr>';
                }
                    ?>
                </tbody>                               
            </table>
        </div>
    </div>
    <script type="text/javascript">
        function load_sales_report(){
            var date = $('input#date_field').val();
            get_form(15,
                        function(html, tools) {
                             $('div#form-body').html(html);
                             $('div#content-body-action-tools').html(tools);
                        }, function(message) {
                             $('font#section_heading').empty();
                             $('div#form-body').empty();
                             alert(message);
                        },
                        date);
        }
        function toggle_items_visibility(down_button) {
            var row = $(down_button).closest('tr');
            row.next('tr').fadeToggle();
            row.find('img#toggle_button').toggle();
            
        }
        function on_print_clicked() {
            var selected_row = $('tr[status="selected"]');
            var sale_items_table = selected_row.next('tr').find('table#sale_items');
            var sale_items = sale_items_table.find('tbody').children();
            var items = new Array();
            var i = 0;
            sale_items.each(function() {
                var item_name = $(this).find('td#item').attr('item_name');
                var id = $(this).attr('id');
                var quantity = $(this).find('td#quantity').attr('val');
                var rate = $(this).find('td#rate').attr('val');
                var tax = $(this).find('td#tax').attr('val');
                var total = $(this).find('td#total').attr('val');
                total = parseFloat(total);
                total = total.toFixed(2);
                var item = {
                     id: id,
                     quantity: quantity,
                     rate: rate,
                     item_name: item_name,
                     total: total,
                     tax: tax
                }
                items[i++] = item;
             });
             var c_name = selected_row.find('td#customer').attr('c_name');
             var c_id = selected_row.find('td#customer').attr('c_id');
             if(c_id == 0){
                 c_name = 'Not Regd.';
             }
             var sale_id = selected_row.attr('id');
             var total = selected_row.find('td#total').html();
             total = parseFloat(total);
             total = total.toFixed(2);
             var total_tax = selected_row.find('td#tax').html();
             total_tax = parseFloat(total_tax);
             total_tax = total_tax.toFixed(2);
             var net_total = selected_row.find('td#net_amount').html();
             net_total = parseFloat(net_total);
             net_total = net_total.toFixed(2);             
             var date = selected_row.find('td#date').attr('date');
             var time = selected_row.find('td#date').attr('time');
             var data = {
                  customer_id: c_id,
                  total: total,
                  net_amount: net_total,
                  tax_amount: total_tax,
                  items: items,
                  date: date,
                  time: time
             }
             print_bill(data, c_name, sale_id);
        }
        function print_bill(data, customer_name, sale_id) {
                var html = '';
                html ='<img id="logo_bill" src="images/nutiez.png"/><br/><font id="print_container_header_company_adderss">Royal Piknik Traders LLP, 29/861</font>'
                        +'<br/><font id="print_container_header_company_adderss">Parayancheri, Calicut</font><br/><font id="print_container_header_company_adderss">'
                        +'Phone : 0495 2741095,+91 9388627725</font><br/><font id="print_container_header_company_adderss">The kerala value added tax rules 2005/ form no. 8</font>'
                        +'<br/><font id="print_container_header_company_adderss">Tin : 32110844692</font>'
                        +'<br/><font id="print_container_header_company_adderss">True Copy</font>';
                $('div#print_container_header').html(html);
                html = '';

                html = html + "<div<!-- style=\"padding:10px 0;\"><table style=\"float:right;font-size: 12px;\">"
                        +"<tr><td>Date</td><td>:</td><td style=\"text-align:right;\">" + data.date + "</td></tr>"
                        +"<tr><td>Time</td><td>:</td><td style=\"text-align:right;\">" + data.time + "</td></tr></table>";
                
                html = html + "<table style=\"font-size: 12px;\">"
                        +"<tr><td>Bill No.</td><td>:</td><td>" + sale_id + "</td></tr>"
                        +"<tr><td>Cust. ID</td><td>:</td><td>" + data.customer_id + "</td></tr>"
                        +"<tr><td>Cust. Name</td><td>:</td><td>" + customer_name + "</td></tr></table></div>";
                
                html = html + "<div style=\"border-top:1px dashed #000; margin:10px auto 0 auto;padding:0 0 10px 0;\">"
                        + "<table style=\"width:100%;font-size: 12px;\"><tr style=\"border-bottom: 1px solid #000; border-top: 1px solid #000;\">"
                        + "<td style=\"width:45%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px;\">Description</td>"
                        + "<td style=\"width:17%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; text-align:right;\">Qty</td>"
                        + "<td style=\"width:17%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; text-align:right;\">Rate</td>"
                        // + "<td style=\"width:15%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; text-align:right;\">Amount</td>"
                        // + "<td style=\"width:10%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; text-align:right;\">Tax</td>"
                        + "<td style=\"width:21%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; text-align:right;\">Total</td></tr>";
                var i = 0;
                for (var key in data.items) {
                    var item = data.items[key];
                    html = html + "<tr><td>" + item.item_name + "</td>"
                            +"<td style=\"text-align:right;\">" + parseFloat(item.quantity).toFixed(3) + "</td>"
                            +"<td style=\"text-align:right;\">" + parseFloat(item.rate).toFixed(2) + "</td>"
                            //+"<td style=\"text-align:right;\">" + (parseFloat(item.total) - parseFloat(item.tax)) + "</td>"
//                            +"<td style=\"text-align:right;\">" + item.tax + "</td>"
                            +"<td style=\"text-align:right;\">" + parseFloat(item.total).toFixed(2) + "</td>"
                            +"</tr>";
                }
                html = html + "</table></div>";
                html = html + "<div style=\"border-top:1px dashed #000; padding:10px 0;\"><table style=\"margin-left: auto;font-size: 12px;\">";
                html = html + "<tr><td>Net. Amount</td><td style=\"margin:0 15;\">:</td><td style=\"text-align:right;\">" + data.net_amount + "</td></tr>";
                html = html + "<tr><td>Tax</td><td style=\"margin:0 15;\">:</td><td style=\"text-align:right;\">" + data.tax_amount + "</td></tr>";
                html = html + "<tr style=\"font-size:18px;\"><td><b>Total</b></td><td style=\"margin:0 15;\">:</td><td style=\"text-align:right;\"><b>" + data.total + "</b></td></tr>";
                html = html + "</table></div>";
//                console.log("Creating bill : " + html);
                $('div#print_container_body').html(html);
                html = '';
                html = '<font>Thank you for shoping</font><br/><font>Visit again...</font> ';
                $('div#print_container_footer').html(html);
                print();
                $('div#print_container_header').empty();
                $('div#print_container_body').empty();
                $('div#print_container_footer').empty();
            }
        function select_row(row) {
            var j_row = $(row);
            if(j_row.attr('status') == 'selected'){
                $('table#items_table tr').attr('status', 'not_selected');
                $('table#items_table tr').css('background-color', '#FFF');
                $('img#edit').css('display', 'none');
                $('img#edit_fade').css('display', 'block');
                $('img#print').css('display', 'none');
                $('img#print_fade').css('display', 'block');
            }else{            
                $('table#items_table tr').attr('status', 'not_selected');
                $('table#items_table tr').css('background-color', '#FFF');
                j_row.attr('status', 'selected');
                j_row.css('background-color', '#C0EFFD');
                $('img#edit').css('display', 'block');
                $('img#edit_fade').css('display', 'none');
                $('img#print').css('display', 'block');
                $('img#print_fade').css('display', 'none');
            }          
        }
        function setup_datepicker(){
           $('input#date_field').datepick({
               minDate:'26/04/2015', 
               dateFormat:'dd/mm/yyyy',
               maxDate:'0',
               onSelect:function(){
                    load_sales_report();
                }
           });
       }
       setup_datepicker();
    </script>

    <?php
    $form = ob_get_clean();
    return $form;
}

function get_form_tools_html($id){
    ob_start();
    ?>    
    <img id="print_fade" src="../ui/images/printer_fade.png" height="40" width="40" style="margin: 15px auto 0px 12px;">
    <img id="print" onclick="on_print_clicked()" src="../ui/images/printer.png" height="40" width="40" style="margin: 15px auto 0px 12px; cursor: pointer; display: none;">
    <script>
        
    </script>
    <?php
    $tools = ob_get_clean();
    return $tools;
}
?>