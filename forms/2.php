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
    <div id="head_div" style="padding: 5px 0; background-color: #ECECEC;  color: #FFD00F;
         border-radius: 5px;margin-left: auto; text-align: center; ">
        ID : SALE - <input style="padding: 0 0 0 5px;" onchange="load_sale()" type="number" id="sale_id" />
    </div>
    <div style="margin-top: 10px; background-color:transparent;padding-bottom: 30px;">
        <form action="#" method="post" onsubmit="return false" class="action_form" operation="update" style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td class="field_name">                    
                        <font>CUSTOMER</font>
                    </td>
                    <td class="field"> 
                        <div class="parent">
                            <input id="customer_id" onfocus="$(this).css('border', '0px')" list="customers" autocomplete="off" style="border: 0;" disabled/>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="field_name">                    
                        <font>DATE &amp; TIME</font>
                    </td>
                    <td class="field"> 
                        <div class="parent">
                            <input id="date_and_time" disabled autocomplete="off" style="border: 0;"/>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
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
                                    $user = new user();
                                    $user->id = $_SESSION['user_id'];
                                    $user->getUser();
                                    $inv = new inventry();
                                    $invs = array();
                                    $invs = $inv->getInventryForSpecificCompany($user->company_id);
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
                                            . ' value="' . $item->item_name . ' - ' . $item->item_code . '" >'
                                            . $item->item_name . ' - ' . $item->item_code 
                                            . '</option>';
                                        }
                                    }
                                    ?>  
                                </datalist>
                                <tr  status="active" slno="1">
                                    <td style="text-align: center;">1</td><td>
                                        <input type="text" onchange="update_item_details(this)"  oninput="update_item_details(this)" onfocus="$(this).css('border', '0px')" autocomplete="off" list="items" id="item" required />
                                    </td>
                                    <td>
                                        <input type="number" min="0" step="any" required onchange="calculate_total(this)" oninput="calculate_total(this)"  id="quantity"/>
                                    </td>
                                    <td>
                                        <input type="text" value="0" min="0" required disabled onchange="calculate_total(this)" oninput="calculate_total(this)"  id="rate"/>
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
                                <tr  status="active" slno="2">
                                    <td style="text-align: center;">2</td><td>
                                        <input type="text" onchange="update_item_details(this)"  oninput="update_item_details(this)" onfocus="$(this).css('border', '0px')" autocomplete="off" list="items" id="item" required />
                                    </td>
                                    <td>
                                        <input type="number" min="0" step="any" required onchange="calculate_total(this)" onkeyup="calculate_total(this)"  id="quantity"/>
                                    </td>
                                    <td>
                                        <input type="text"  value="0" min="0" required disabled onchange="calculate_total(this)" onkeyup="calculate_total(this)"  id="rate"/>
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
                                
                                
                              </tbody>                               
                            </table>
                        </div>
                    </td>
                </tr>
                <tr style="height: 5px;"></tr>
                <tr>
                    <td></td>
                    <td>
                        <div style="background-color: #FFD00F; color: #fff; text-align: right; padding-right: 20px;">
                            <span style="margin-right: 20px;">TOTAL </span>
                            <span id="total">0.00</span>
                        </div> 
                    </td>
                </tr>
                <tr style="display: none;">
                    <td></td>
                    <td>
                        <div style="background-color: #FFD00F; color: #fff; text-align: right; padding-right: 20px;">
                            <span style="margin-right: 20px;">DISCOUNT </span>
                            <span id="discount">0.00</span>
                        </div> 
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <div style="background-color: #FFD00F; color: #fff; text-align: right; padding-right: 20px;">
                            <span style="margin-right: 20px;">PAID </span>
                            <span id="total_paid">0.00</span>
                        </div> 
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <div style="background-color: #FFD00F; color: #fff; text-align: right; padding-right: 20px;">
                            <span style="margin-right: 20px;">BALANCE</span>
                            <span id="balance">0.00</span>
                        </div> 
                    </td>
                </tr>
                <tr style="height: 5px;"></tr>
                <tr>
                    <td></td>
                    <td>
                        <div style="padding: 0px 12px;">
                            <div style="width: 100%; margin-left: -12px; padding: 12px; 
                                 background-color: #92BB0D; border-radius: 5px; float: left;">

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
                $parent.find('input#total').attr('discount', 0);
                //$parent.find('input#discount').val(0);
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
            $('span#total').html(total);
            $('span#total').attr('tax', total_tax);
            var paid = $('span#total_paid').html();
            paid = parseFloat(paid);
            var balance = total - paid;
            balance = balance.toFixed(2);
            $('span#balance').html(balance);
            total_discount = total_discount.toFixed(2);
            $('span#total').attr('discount', total_discount);   
            $('span#discount').val(total_discount);         
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
            var row = '<tr  status="active" slno="" previous="false"><td style="text-align: center;"></td><td>'
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
        
        function load_sale(){
            var sale_id = $('input#sale_id').val();
            var data = {
                   form_id: 2,
                   sale_id: sale_id
            }
            get_form_data(data,
                   function(message, sale) {
                       //alert(message);
                       var form = $('form.action_form');
                       form.find('input#customer_id').val(sale.customer);
                       form.find('input#date_and_time').val(sale.date + ' - ' + sale.time);
                       form.find('input#date_and_time').attr('date', sale.date);
                       form.find('input#date_and_time').attr('time', sale.time);
                       form.find('input#customer_id').attr('disabled', 'true');
                       $('table#items_table tbody').empty();
                       var items = sale.items;
                       for(var i = 0; i<items.length; i++){
                            add_sale_item();
                            var row = $('table#items_table tbody tr:last-child');
                            var item = items[i];
                            row.find('input#item').attr('disabled', 'disabled');
                            row.find('input#item').val(item.item_name);
                            row.find('input#item').attr('item_id', item.item_id);
                            row.find('input#quantity').val(item.quantity);
                            row.find('input#quantity').attr('max', item.quantity);
                            row.find('input#rate').val(item.rate);
                            row.find('input#rate').attr('tax', item.tax_rate);
                            row.find('input#discount_percent').val(item.discount_percent);
                            row.find('input#total').val(item.total);
                            row.find('input#total').attr('tax', item.tax);
                            row.find('input#total').attr('discount', item.discount);
                            row.find('input#discount').val(item.discount);
                            row.attr('previous', true);
                    } 
                    form.find('span#total').html(sale.amount);
                    form.find('span#total').attr('tax', sale.tax);
                    form.find('span#total').attr('discount', sale.discount);
                    form.find('span#discount').html(sale.discount);
                    form.find('span#total_paid').html(sale.amount);
                    form.find('span#balance').html('0.00');
                    form.attr('sale_id', sale.id);
                    form.attr('customer_name', sale.c_name);
                    form.attr('customer_id', sale.c_id);   
                    form.find('input[type="button"]').prop('disabled', null);
                    form.find('input[type="submit"]').prop('disabled', null);
                    form.find('input[type="reset"]').prop('disabled', null);
                    form.find('input[type="number"]').prop('disabled', null);
                   
                }, function(message) {
                       var form = $('form.action_form');
                       form.find('input').prop('disabled', 'disabled');
                       alert(message);
                   });
            }
 
        $(document).ready(function(e) {
            $('form.action_form').on('submit', function(e) {
                e.preventDefault();
                var items = new Array();
                var i = 0;
                var items_table = $('#items_table').find('tbody').children();
                var invalid_item_precent = false;
                items_table.each(function() {
                    if ($(this).attr('status') == 'active') {
                        var item_input = $(this).find('input#item');
                        var item_name = item_input.val();
                        var item_option_obj = $('datalist#items').find("option[value='" + item_name + "']");
                        if (item_option_obj.length == "0" && !$(this).attr('previous')) {
                            item_input.css('border', '1px solid #f00');
                            invalid_item_precent = true;
                            return;
                        } else {
                            var id;
                            if($(this).attr('previous') == 'true'){
                                id = item_input.attr('item_id');
                            }else{
                                id = item_option_obj.attr('id');
                                //var item_name = item_option_obj.attr('item_name');
                            }
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

                var form_id = 2;
                var operation = $(this).attr('operation');
                var total = $('span#total').html();
                var total_tax = $('span#total').attr('tax');
                var total_discount = $('span#total').attr('discount');
                var net_total = parseFloat(total) - parseFloat(total_tax);
                net_total = net_total.toFixed(2);
                var total_paid = $('span#total_paid').html();
                total_paid = parseFloat(total_paid);
                total_paid = total_paid.toFixed(2);
                var balance = $('span#balance').html();
                balance = parseFloat(balance);
                balance = balance.toFixed(2);
                var sale_id = $(this).attr('sale_id');
                var customer_name = $(this).attr('customer_name');
                var customer_id = $(this).attr('customer_id');
                var date = $(this).find('input#date_and_time').attr('date');
                var time = $(this).find('input#date_and_time').attr('time');

                if(items.length == 0){
                    items = 'no_items';
                }
                if (operation == 'update') {
                    var data = {
                        form_id: form_id,
                        sale_id: sale_id,
                        total: total,
                        net_amount: net_total,
                        tax_amount: total_tax,
                        discount:total_discount,
                        items: items
                    }
                        update_form_data(data, function(message, sale_id) {
                            //$('form.action_form').get(0).reset();
                            //alert(message);
                            print_bill(data, customer_name, customer_id, sale_id, total_paid, balance, date, time);
                            get_form(2,
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
            function print_bill(data, customer_name, customer_id, sale_id, total_paid, balance, date, time) {
                var html = '';
                html ='<img id="logo_bill" src="images/nutiez.png"/><br/><font id="print_container_header_company_adderss">Royal Piknik Traders LLP, 29/861</font>'
                        +'<br/><font id="print_container_header_company_adderss">Parayancheri, Calicut</font><br/><font id="print_container_header_company_adderss">'
                        +'Phone : 0495 2741095,+91 9388627725</font><br/><font id="print_container_header_company_adderss">The kerala value added tax rules 2005/ form no. 8</font>'
                        +'<br/><font id="print_container_header_company_adderss">Tin : 32110844692</font>'
                        +'<br/><font id="print_container_header_company_adderss">True Copy</font>';
                $('div#print_container_header').html(html);
                
                html = "<div<!-- style=\"padding:10px 0;\"><table style=\"float:right; font-size: 12px;\">"
                        +"<tr><td>Date</td><td>:</td><td style=\"text-align:right;\">" + date + "</td></tr>"
                        +"<tr><td>Time</td><td>:</td><td style=\"text-align:right;\">" + time + "</td></tr></table>";
                
                html = html + "<table style=\"font-size: 12px;\">"
                        +"<tr><td>Bill No.</td><td>:</td><td>" + sale_id + "</td></tr>"
                        +"<tr><td>Cust. ID</td><td>:</td><td>" + customer_id + "</td></tr>"
                        +"<tr><td>Cust. Name</td><td>:</td><td>" + customer_name + "</td></tr></table></div>";
                
                html = html + "<div style=\"border-top:1px dashed #000; margin:10px auto 0 auto;padding:0 0 10px 0;\">"
                        + "<table style=\"width:100%;font-size: 12px;\"><tr style=\"border-bottom: 1px solid #000; border-top: 1px solid #000;\">"
                        + "<td style=\"width:44%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px;\">Description</td>"
                        + "<td style=\"width:14%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; text-align:right;\">Qty</td>"
                        + "<td style=\"width:14%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; text-align:right;\">Rate</td>"
                        // + "<td style=\"width:15%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; text-align:right;\">Amount</td>"
                        // + "<td style=\"width:10%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; text-align:right;\">Tax</td>"
                        + "<td style=\"width:14%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; text-align:right;\">Tax(%)</td>"
                        + "<td style=\"width:14%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px; text-align:right;\">Total</td></tr>";
                var i = 0;
                if(data.items == 'no_items'){
                    html = html + "<tr><td> No item</td></tr>";
                }else{
                    for (var key in data.items) {
                        var item = data.items[key];
                        html = html + "<tr><td>" + item.item_name + "</td>"
                                +"<td style=\"text-align:right;\">" + parseFloat(item.quantity).toFixed(3) + "</td>"
                                +"<td style=\"text-align:right;\">" + parseFloat(item.rate).toFixed(2) + "</td>"
                                //+"<td style=\"text-align:right;\">" + (parseFloat(item.total) - parseFloat(item.tax)) + "</td>"
    //                          +"<td style=\"text-align:right;\">" + item.tax + "</td>"
                                +"<td style=\"text-align:right;\">" + item.tax_rate + "</td>"
                                +"<td style=\"text-align:right;\">" + parseFloat(item.total).toFixed(2) + "</td>"
                                +"</tr>";
                    }
                }
                html = html + "</table></div>";
                html = html + "<div style=\"border-top:1px dashed #000; padding:10px 0;\"><table style=\"margin-left: auto;font-size: 12px;\">";
                html = html + "<tr><td>Net. Amount</td><td style=\"margin:0 15;\">:</td><td style=\"text-align:right;\">" + data.net_amount + "</td></tr>";
                if(parseFloat(data.discount) > 0 ){
                    html = html + "<tr><td>Discount</td><td style=\"margin:0 15;\">:</td><td style=\"text-align:right;\">" + data.discount + "</td></tr>";
                }
                html = html + "<tr><td>Tax</td><td style=\"margin:0 15;\">:</td><td style=\"text-align:right;\">" + data.tax_amount + "</td></tr>";
                html = html + "<tr><td>Total</td><td style=\"margin:0 15;\">:</td><td style=\"text-align:right;\">" + data.total + "</td></tr>";
                html = html + "<tr><td>Paid</td><td style=\"margin:0 15;\">:</td><td style=\"text-align:right;\">" + total_paid + "</td></tr>";
                html = html + "<tr style=\"font-size:18px;\"><td><b>Balance</b></td><td style=\"margin:0 15;\">:</td><td style=\"text-align:right;\"><b>" + balance + "</b></td></tr>";
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