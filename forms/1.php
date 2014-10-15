<?php

function get_form_html($form_id, $id) {
    ob_start();
    ?>
    <style>
        .field_name{
            width: 20%;
        }
        .field{
            width: 50%;
        }
        .field input{
            width: 100%;
            margin-left: 0px;
        }
        .field .parent{
            padding: 0px 0px;
        }
        .field select{
            width: 100%;
        }
    </style>
    <div style="height: 150px; 
         width: 320px; background-color: #ECECEC; 
         border-radius: 5px;margin-left: auto;display: none; ">



    </div>
    <div style="margin-top: 10px; background-color:transparent;padding-bottom: 30px;">
        <form action="#" method="post" onsubmit="return false" class="action_form" operation="add" style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td class="field_name">                    
                        <font>CUSTOMER</font>
                    </td>
                    <td class="field"> 
                        <div class="parent">
                            <input id="customer_id" onfocus="$(this).css('border', '0px')" list="customers" autocomplete="off" style="border: 0;"/>
                            <datalist id="customers">
                                <?php
                                $user = new user();
                                $user->id = $_SESSION['user_id'];
                                $user->getUser();                                      
                                $customer_obj = new customer();
                                $customers = array();
                                $customers = $customer_obj->getCustomers($user->company_id);
                                if(is_array($customers) and count($customers)){
                                    foreach ($customers as $customer) {
                                        echo '<option id="' . $customer->id
                                        . '" customer_name="' . $customer->customer_name
                                        . '" value="' . $customer->customer_name . ' ( ID : ' . $customer->id . ')" >'
                                        . $customer->customer_name . ' ( ID : ' . $customer->id . ')'
                                        . '</option>';
                                    }
                                }
                                ?>    
                            </datalist>
                        </div>
                    </td>
                </tr>
    <!--            <tr>
                    <td class="field_name"> 
                        <font>ITEM MRP </font>
                    </td>
                    <td class="field"> 
                        <div style="padding: 0px 12px;">
                        <input type="number" id="mrp" required />
                        </div>
                    </td>
                </tr>-->
                <tr>
                    <td colspan="2">
                        <style>
                            div#purchace_items td{
                                border: 1px solid #21ACD7;
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
                        <div id="purchace_items" style="width: 100%; padding: 10px 0; color: #21ACD7;">           
                            <table id="items_table" style="border-collapse: collapse; width: 100%; 
                                   background-color: #fff; border-radius: 10px;  color: #21ACD7;">
                                <thead style="text-align: center;">
                                    <tr>
                                        <td>
                                            #
                                        </td>
                                        <td>
                                            ITEM
                                        </td>
                                        <td style="width: 10%;">
                                            QUANTITY
                                        </td>
                                        <td style="width: 10%;">
                                            RATE
                                        </td>
                                        <td style="width: 10%;">
                                            DISCOUNT(%)
                                        </td>
                                        <td style="width: 10%;">
                                            DISCOUNT
                                        </td>
                                        <td style="width: 10%;">
                                            TOTAL
                                        </td>
                                        <td style="width: 8%;">
                                            DELETE
                                        </td>
                                    </tr>
                                </thead>
                                <tbody style="padding-left: 3px;">
                                <datalist id="items">
                                    <?php
                                    $inv = new inventry();
                                    $invs = array();
                                    $invs = $inv->getInventryForSpecificCompany($user->company_id);
                                    if($invs!=NULL and sizeof($invs)!=0){
                                        foreach ($invs as $inv) {
                                            if ($inv->in_stock_count > 0) {
                                                $item = new item();
                                                $item->id = $inv->item_id;
                                                $item->getItem();
                                                $tax_category = new tax_category();
                                                $tax_category->id = $inv->tax_category_id;
                                                $tax_category->getTaxCategory(); echo $tax_category->to_string();
                                                echo '<option id="' . $item->id . '"'
                                                . ' stock_count="' . $inv->in_stock_count . '"'
                                                . ' selling_pize="' . $inv->selling_prize . '"'
                                                . ' tax="' . $tax_category->tax_percentage . '"'
                                                . ' discount_percent="' . $item->discount_percent . '"'
                                                . ' item_name="' . $item->item_name . '"'
                                                . ' value="' . $item->item_name . ' - ' . $item->item_code .'" >'
                                                . $item->item_name . ' - ' . $item->item_code
                                                . '</option>';
                                            }
                                        }
                                    }
                                    ?>  
                                </datalist>
                                <?php
                                for ($i = 0; $i < 2; $i++) {
                                    ?>
                                    <tr  status="active" slno="<?php echo $i + 1; ?>">
                                        <td style="text-align: center;">
                                            <?php echo $i + 1; ?>
                                        </td>
                                        <td>
                                            <input type="text" onchange="update_item_details(this)"  oninput="update_item_details(this)" onfocus="$(this).css('border', '0px')" autocomplete="off" list="items" id="item" required />
                                        </td>
                                        <td>
                                            <input type="number" min="0" step="any" required onchange="calculate_total(this)" oninput="calculate_total(this)"  id="quantity"/>
                                        </td>
                                        <td>
                                            <input type="text" value="0" min="0" required disabled id="rate"/>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" value="0" min="0" max="100" onchange="calculate_total(this)" oninput="calculate_total(this)" id="discount_percent"/>
                                        </td>
                                        <td>
                                            <input type="text" value="0" min="0" required disabled id="discount"/>
                                        </td>
                                        <td>
                                            <input type="text" min="0" required  id="total" disabled/>
                                        </td>
                                        <td style="width: 20px; text-align: center; padding-right: 5px;">
                                            <img id="delete_button" onclick="delete_this_row(this)" style="color: #f00; cursor: pointer; height: 20px; width: 20px; margin-right: auto;  margin-left: auto;" src="../ui/images/cross_button.png"/>
                                            <img id="activate_button" onclick="enable_this_row(this)" style="color: #f00; cursor: pointer; height: 20px; width: 20px; margin-right: auto; margin-left: auto; display: none;" src="../ui/images/tick_button.png" />
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>                               
                            </table>
                        </div>
                    </td>
                </tr>
                <tr style="height: 5px;"></tr>
                <tr>
                    <td></td>
                    <td>
                        <div style="background-color: #21ACD7; color: #fff; text-align: right; padding-right: 20px;">
                            <span style="margin-right: 20px;">TOTAL </span>
                            <span id="total">0.00</span>
                        </div> 
                    </td>
                </tr>
                <tr style="display: none;">
                    <td></td>
                    <td>
                        <div style="background-color: #21ACD7; color: #fff; text-align: right; padding-right: 20px;">
                            <span style="margin-right: 20px;">DISCOUNT </span>
                            <span id="discount">0.00</span>
                        </div> 
                    </td>
                </tr>
                <tr style="height: 5px;"></tr>
                <tr>
                    <td></td>
                    <td>
                        <div style="padding: 0px 12px;">
                            <div style="width: 100%; margin-left: -12px; padding: 12px; 
                                 background-color: #0d92bb; border-radius: 5px; float: left;">

                                <div style="width: 33.33%; float: right;  ">
                                    <input style="width: 100%;" type="submit" value="ADD" />
                                </div>
                                <div style="width: 33.33%;  float: right;  ">
                                    <input style="width: 100%;" type="reset" value="CANCEL" /> 
                                </div>
                                <div style="width: 33.33%;">
                                    <input style="width: 100%;" onclick="add_sale_item()" type="button" value="ADD ITEM" /> 
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <script type="text/javascript">
        function update_item_details(item) {
            var item_input = $(item);
            var item_name = item_input.val();
            var item_option_obj = $('datalist#items').find("option[value='" + item_name + "']");
            if (item_option_obj.length == "0") {
                return;
            } else {
                var selling_prize = item_option_obj.attr('selling_pize');
                var stock_count = item_option_obj.attr('stock_count');
                var tax = item_option_obj.attr('tax');
                var discount_percent = item_option_obj.attr('discount_percent');
                if($.isNumeric(discount_percent)){
                    discount_percent = parseFloat(discount_percent).toFixed(2);
                }else{
                    discount_percent = 0;
                }
                var row = item_input.parent('td').parent('tr');
                row.find('input#rate').val(selling_prize);
                row.find('input#rate').attr('tax', tax);
                row.find('input#discount_percent').val(discount_percent);
                row.find('input#quantity').prop('max', stock_count);
                calculate_total(row.find('input#rate').get(0));
            }
        }
        function calculate_total(field) {
            var $parent = $(field).closest('tr');
            var $quantity = parseFloat($parent.find('input#quantity').val());
            var $rate = parseFloat($parent.find('input#rate').val());
            var $total = $quantity * $rate;
            $total = $total.toFixed(2);
            if ($.isNumeric($total)) {
                var discount_percent = parseFloat($parent.find('input#discount_percent').val());
                var discount = ($total * discount_percent) / 100;
                if($.isNumeric(discount)){
                    //$parent.find('input#total').attr('discount', discount);
                    $parent.find('input#discount').val(discount);
                    $total = $total - discount;
                }else{                    
                    //$parent.find('input#total').attr('discount', 0);
                    $parent.find('input#discount').val(0);
                }
                $parent.find('input#total').val($total);
                var $tax_percentage = parseFloat($parent.find('input#rate').attr('tax'));
                var $tax = ($total * $tax_percentage) / (100 + $tax_percentage);
                //$tax = $tax.toFixed(2);
                if($.isNumeric($tax)){
                    $parent.find('input#total').attr('tax', $tax);
                }else{                    
                    $parent.find('input#total').attr('tax', 0);
                }
            } else {
                $parent.find('input#total').val(0);
                $parent.find('input#total').attr('tax', 0);
                //$parent.find('input#total').attr('discount', 0);
                $parent.find('input#discount').val(0);
            }
            calculate_purchace_total();
        }
        function calculate_purchace_total() {
            var items_table = $('#items_table').find('tbody').children();
            var total = 0;
            var total_tax = 0;
            var total_discount = 0;
            items_table.each(function() {
                var item_total_input = $(this).find('input#total');
                var row_status = $(this).attr('status');
                var item_total = item_total_input.val();
                var item_tax = item_total_input.attr('tax');
                var item_discount = $(this).find('input#discount').val();
                if ($.isNumeric(item_total) && row_status == 'active') {
                    total = parseFloat(total) + parseFloat(item_total);
                    if($.isNumeric(item_tax)){
                        total_tax = parseFloat(total_tax) + parseFloat(item_tax);
                    }
                    if($.isNumeric(item_discount)){
                        total_discount = parseFloat(total_discount) + parseFloat(item_discount);
                    }
                }
            });
            total = total.toFixed(2);
            total_tax = total_tax.toFixed(2);
            total_discount = total_discount.toFixed(2);
            $('span#total').html(total);
            $('span#total').attr('tax', total_tax);
            $('span#total').attr('discount', total_discount);
            $('span#discount').html(total_discount);
        }
        function delete_this_row(delete_btn) {
            var row = $(delete_btn).closest('tr');
            row.attr('status', 'deativated');
            row.css('background-color', '#c0effd');
            row.find('input').prop('required', null);
            row.find('input').css('border', "0px");
            $(delete_btn).css('display', 'none');
            $(delete_btn).siblings().first().css('display', 'block');
            $(delete_btn).parent('td').css('text-align', 'centre');
            calculate_purchace_total();
        }
        function enable_this_row(enable_btn) {
            var row = $(enable_btn).closest('tr');
            row.attr('status', 'active');
            row.css('background-color', '#fff');
            row.find('input').prop('required', 'required');
            $(enable_btn).siblings().first().css('display', 'block');
            $(enable_btn).css('display', 'none');
            $(enable_btn).parent('td').css('text-align', 'centre');
            calculate_purchace_total();
        }
        function add_sale_item() {
            var row = '<tr  status="active" slno=""><td style="text-align: center;"></td><td>'
                    + '<input type="text" onchange="update_item_details(this)"  oninput="update_item_details(this)" onfocus="$(this).css(\'border\', \'0px\')" autocomplete="off" list="items" id="item" required />'
                    + '</td><td><input type="number" min="0" step="any" required onchange="calculate_total(this)" onkeyup="calculate_total(this)"  id="quantity"/>'
                    + '</td><td><input type="text"  value="0" min="0" required disabled onchange="calculate_total(this)" onkeyup="calculate_total(this)"  id="rate"/>'
                    + '</td><td><input type="number" step="0.01" value="0" min="0" max="100" onchange="calculate_total(this)" oninput="calculate_total(this)" id="discount_percent"/>'
                    + '</td><td><input type="text" value="0" min="0" required disabled id="discount"/>'
                    + '</td><td><input type="text" min="0" required  id="total" disabled/></td><td style="width: 20px; text-align: center; padding-right: 5px;">'
                    + '<img id="delete_button" onclick="delete_this_row(this)" style="color: #f00; cursor: pointer; height: 20px; width: 20px; margin-right: auto;  margin-left: auto;" src="../ui/images/cross_button.png"/>'
                    + '<img id="activate_button" onclick="enable_this_row(this)" style="color: #f00; cursor: pointer; height: 20px; width: 20px; margin-right: auto; margin-left: auto; display: none;" src="../ui/images/tick_button.png" />'
                    + '</td></tr>';
            var lastcount = $('table#items_table tbody tr:last-child').attr('slno');
            if(!($.isNumeric(lastcount))){
                lastcount = 0;
            }
            $('table#items_table tbody').append(row);
            lastcount = parseInt(lastcount) + 1;
            $('table#items_table tbody tr:last-child').attr('slno', lastcount);
            $('table#items_table tbody tr:last-child td:first-child').html(lastcount);
        }

        $(document).ready(function(e) {
            $('form.action_form').on('submit', function(e) {
                e.preventDefault();
                var customer_input = $('form input#customer_id');
                var customer = customer_input.val();
                var customer_option_obj = $('datalist#customers').find("option[value='" + customer + "']");
                var customer_id;
                var customer_name;
                if (customer_option_obj.length == "0") {
                    customer_id = 0;
                    customer_name = 'Not Regd.';
                } else {
                    customer_id = customer_option_obj.attr('id');
                    customer_name = customer_option_obj.attr('customer_name');
                }


                var items = new Array();
                var i = 0;
                var items_table = $('#items_table').find('tbody').children();
                var invalid_item_precent = false;
                items_table.each(function() {
                    if ($(this).attr('status') == 'active') {
                        var item_input = $(this).find('input#item');
                        var item_name = item_input.val();
                        var item_option_obj = $('datalist#items').find("option[value='" + item_name + "']");
                        if (item_option_obj.length == "0") {
                            item_input.css('border', '1px solid #f00');
                            invalid_item_precent = true;
                            return;
                        } else {
                            var id = item_option_obj.attr('id');
                            var item_name = item_option_obj.attr('item_name');
                            var quantity = $(this).find('input#quantity').val();
                            quantity = parseFloat(quantity).toFixed(3);
                            var rate = $(this).find('input#rate').val();
                            var tax_rate = $(this).find('input#rate').attr('tax');
                            rate = parseFloat(rate).toFixed(2);
                            var tax = $(this).find('input#total').attr('tax');
                            var discount = $(this).find('input#discount').val();
                            var total = $(this).find('input#total').val();
                            var item = {
                                id: id,
                                quantity: quantity,
                                rate: rate,
                                item_name: item_name,
                                total: total,
                                tax: tax,
                                discount: discount,
                                tax_rate:tax_rate
                            }
                            items[i++] = item;
                        }
                    }
                });

                if (invalid_item_precent) {
                    //alert("Invalid Item Precent");
                    return;
                }

                var form_id = 1;
                var operation = $(this).attr('operation');
                var total = $('span#total').html();
                var total_tax = $('span#total').attr('tax');
                var total_discount = $('span#total').attr('discount');
                var net_total = parseFloat(total) - parseFloat(total_tax);
                net_total = net_total.toFixed(2);

                if (operation == 'add') {
                    var data = {
                        form_id: form_id,
                        customer_id: customer_id,
                        total: total,
                        net_amount: net_total,
                        tax_amount: total_tax,
                        discount:total_discount,
                        items: items
                    }
                        add_form_data(data, function(message, sale_id) {
                            //$('form.action_form').get(0).reset();
                            //alert(message);
                            print_bill(data, customer_name, sale_id);
                            get_form(1,
                                function(html) {
                                    $('div#form-body').html(html);
                                }, function(message) {
                                    $('font#section_heading').empty();
                                    $('div#form-body').empty();
                                    alert(message);
                                });
                        }, function(message) {
                            alert(message);
                        });
                } else {
                    alert("Invalid Operation " + form_id + ' - ' + operation);
                }
            });
            function print_bill(data, customer_name, sale_id) {
                <?php
                $shop = new company();
                $shop->id = $user->company_id;
                $shop->getCompany();
                ?>                
                var html = '';
                html ='<img id="logo_bill" src="images/nutiez.png"/><br/><font id="print_container_header_company_adderss">Royal Piknik Traders LLP, 29/861</font>'
                        +'<br/><font id="print_container_header_company_adderss"><?php echo $shop->company_code; ?>, Calicut</font><br/><font id="print_container_header_company_adderss">'
                        +'Phone : 0495 2741095,+91 9388627725</font><br/><font id="print_container_header_company_adderss">The kerala value added tax rules 2005/ form no. 8</font>'
                        +'<br/><font id="print_container_header_company_adderss">Tin : 32110844692</font>';
                $('div#print_container_header').html(html);
                html = '';
                var d = new Date();
                var date = d.getDate()+"/"+(parseInt(d.getMonth())+parseInt(1))+"/"+d.getFullYear();
                var hour = d.getHours();
                var am_or_pm;
                if(hour<12){
                    am_or_pm = "AM";
                }else{                    
                    am_or_pm = "PM";
                }
                if(hour==0){
                    hour = 12;
                }else if(hour>12){                    
                    hour = parseInt(hour)-parseInt(12);
                }                
                var minut = d.getMinutes();
                if(minut<10){
                    minut = "0"+minut;
                }
                var time = hour+":"+minut+" "+am_or_pm;
                html = html + "<div<!-- style=\"padding:10px 0;\"><table style=\"float:right; font-size: 12px;\">"
                        +"<tr><td>Date</td><td>:</td><td style=\"text-align:right;\">" + date + "</td></tr>"
                        +"<tr><td>Time</td><td>:</td><td style=\"text-align:right;\">" + time + "</td></tr></table>";
                
                html = html + "<table style=\"font-size: 12px;\">"
                        +"<tr><td>Bill No.</td><td>:</td><td>" + sale_id + "</td></tr>"
                        +"<tr><td>Cust. ID</td><td>:</td><td>" + data.customer_id + "</td></tr>"
                        +"<tr><td>Cust. Name</td><td>:</td><td>" + customer_name + "</td></tr></table></div>";
                
                html = html + "<div style=\"border-top:1px dashed #000; margin:10px auto 0 auto;padding:0 0 10px 0;\">"
                        + "<table style=\"width:100%;font-size: 12px;\"><tr style=\"border-bottom: 1px solid #000; border-top: 1px solid #000;\">"
                        + "<td style=\"width:40%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; margin-right:5px;\">Description</td>"
                        + "<td style=\"width:10%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; margin-right:5px; text-align:right;\">Qty</td>"
                        + "<td style=\"width:10%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; margin-right:5px; text-align:right;\">Rate</td>"
                        + "<td style=\"width:10%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; margin-right:5px; text-align:right;\">Amount</td>"
                        + "<td style=\"width:10%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; margin-right:5px; text-align:right;\">Tax</td>"
                        + "<td style=\"width:10%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; margin-right:5px; text-align:right;\">Tax(%)</td>"
                        + "<td style=\"width:10%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; margin-right:5px; text-align:right;\">Total</td></tr>";
                for (var key in data.items) {
                    var item = data.items[key];
                    html = html + "<tr><td>" + item.item_name + "</td>"
                            +"<td style=\"text-align:right;\">" + parseFloat(item.quantity).toFixed(3) + "</td>"
                            +"<td style=\"text-align:right;\">" + parseFloat(item.rate).toFixed(2) + "</td>"
                            +"<td style=\"text-align:right;\">" + (parseFloat(item.total) - parseFloat(item.tax)).toFixed(2) + "</td>"
                            +"<td style=\"text-align:right;\">" + parseFloat(item.tax).toFixed(2) + "</td>"
                            +"<td style=\"text-align:right;\">" + item.tax_rate + "</td>"
                            +"<td style=\"text-align:right;\">" + parseFloat(item.total).toFixed(2) + "</td>"
                            +"</tr>";
                }
                html = html + "</table></div>";
                html = html + "<div style=\"border-top:1px dashed #000; padding:10px 0;\"><table style=\"margin-left: auto;font-size: 12px;\">";
                html = html + "<tr><td>Net. Amount</td><td style=\"margin:0 15;\">:</td><td style=\"text-align:right;\">" + data.net_amount + "</td></tr>";
                if(parseFloat(data.discount) > 0 ){
                    html = html + "<tr><td>Discount</td><td style=\"margin:0 15;\">:</td><td style=\"text-align:right;\">" + data.discount + "</td></tr>";
                }
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
        });
    </script>

    <?php
    $form = ob_get_clean();
    return $form;
}


function get_form_tools_html($id){
    ob_start();
    ?>    
    <img onclick="load_items_list()" src="../ui/images/list_icon.png" height="40" width="40" style="margin: 15px auto 0px 12px; cursor: pointer;">
    <script>
        function load_items_list(){
            get_form(3,
                function(html, tools) {
                    $('div#form-body').html(html);
                    $('div#content-body-action-tools').html(tools);
                }, function(message) {
                    $('font#section_heading').empty();
                    alert(message);
            });
        }
    </script>
    <?php
    $tools = ob_get_clean();
    return $tools;
}
?>