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
        ID : PURCHACE - <input style="padding: 0 0 0 5px;" onchange="load_purchace()" type="number" id="purchace_id" />
    </div>
    <div style="margin-top: 10px; background-color:transparent;padding-bottom: 100px;">
        <form action="#" method="post" onsubmit="return false" class="action_form" operation="update" >
            <table style="width:100%;">
                <tr>
                    <td class="field_name">                    
                        <font>VENDOR</font>
                    </td>
                    <td class="field"> 
                        <div class="parent">
                            <input id="wendor_id" required disabled="true" autocomplete="off" style="border: 0;" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="field_name"> 
                        <font>BILL NUMBER</font>
                    </td>
                    <td class="field"> 
                        <div class="parent">
                            <input type="text" id="bill_number" />
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
                                        <td style="width: 17%;">
                                            QUANTITY
                                        </td>
                                        <td style="width: 17%;">
                                            RATE
                                        </td>
                                        <td style="width: 17%;">
                                            TOTAL
                                        </td>
                                        <td style="width: 10%;">
                                            DELETE
                                        </td>
                                    </tr>
                                </thead>
                                <tbody style="padding-left: 3px;">
                                <datalist id="items">
                                    <?php
                                    $item = new item();
                                    $items = array();
                                    $items = $item->getItems();
                                    foreach ($items as $item) {
                                        echo '<option id="' . $item->id .'"'
                                        . 'purchace_rate="' . $item->purchace_rate .'"'        
                                        . ' value="' . $item->item_name . ' - ' . $item->item_code . ' ( ID : ' . $item->id . ' )" >'
                                        . $item->item_name . ' - ' . $item->item_code . ' ( ID : ' . $item->id . ' )'
                                        . '</option>';
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
                                            <input type="text" oninput="update_item_details(this)" onchange="update_item_details(this)" onfocus="$(this).css('border', '0px')" autocomplete="off" list="items" id="item" required />
                                        </td>
                                        <td>
                                            <input type="number" min="0" step="0.001" required onchange="calculate_total(this)" onkeyup="calculate_total(this)"  id="quantity"/>
                                        </td>
                                        <td>
                                            <input type="number" min="0" step="0.01" required onchange="calculate_total(this)" onkeyup="calculate_total(this)"  id="rate"/>
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
                        <div style="background-color: #FFD00F; color: #fff; text-align: center; padding-right: 20px;">
                            <span style="margin-right: 20px;">TOTAL </span>
                            <span id="total">0</span>
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
                                    <input style="width: 100%;" disabled="true" type="submit" value="UPDATE" />
                                </div>
                                <div style="width: 33.33%;  float: right;  ">
                                    <input style="width: 100%;" disabled="true" type="reset" value="CANCEL" /> 
                                </div>
                                <div style="width: 33.33%;">
                                    <input style="width: 100%;" disabled="true" onclick="add_purchace_item()" type="button" value="ADD ITEM" /> 
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
                var purchace_rate = item_option_obj.attr('purchace_rate');
                var row = item_input.parent('td').parent('tr');
                row.find('input#rate').val(purchace_rate);
                calculate_total(row.find('input#rate').get(0));
            }
        }
        function calculate_total(field) {
            var $parent = $(field).closest('tr');
            var $quantity = parseInt($parent.find('input#quantity').val());
            var $rate = parseInt($parent.find('input#rate').val());
            var $total = $quantity * $rate;
            if ($.isNumeric($total)) {
                $parent.find('input#total').val($total);
            } else {
                $parent.find('input#total').val(0);
            }
            calculate_purchace_total();
        }
        function calculate_purchace_total() {
            var items_table = $('#items_table').find('tbody').children(); 
            var total = 0;
            items_table.each(function() {
                var item_total_input = $(this).find('input#total');
                var row_status = $(this).attr('status');
                var item_total = item_total_input.val();
                if ($.isNumeric(item_total) && row_status=='active') {
                    total = parseInt(total) + parseInt(item_total);
                }
            });
            $('span#total').html(total);
        }
        function delete_this_row(delete_btn) {
            var row = $(delete_btn).closest('tr');
            row.attr('status', 'deativated');
            row.css('background-color', '#c0effd');
            row.find('input').prop('required',null);
            row.find('input').css('border',"0px");
            $(delete_btn).css('display','none');
            $(delete_btn).siblings().first().css('display','block');
            $(delete_btn).parent('td').css('text-align', 'centre');
            calculate_purchace_total();
        }
        function enable_this_row(enable_btn) {
            var row = $(enable_btn).closest('tr');
            row.attr('status', 'active');
            row.css('background-color', '#fff');
            row.find('input').prop('required','required');
            $(enable_btn).siblings().first().css('display','block');
            $(enable_btn).css('display','none');
            $(enable_btn).parent('td').css('text-align', 'centre');
            calculate_purchace_total();
        }
        function add_purchace_item() {
            var row = '<tr  status="active" slno=""><td style="text-align: center;"></td><td>'
                    +'<input type="text" oninput="update_item_details(this)" onchange="update_item_details(this)" onfocus="$(this).css(\'border\', \'0px\')" autocomplete="off" list="items" id="item" required />'
                    +'</td><td><input type="number" min="0" required onchange="calculate_total(this)" onkeyup="calculate_total(this)"  step="0.001" id="quantity"/>'
                    +'</td><td><input type="number" min="0" required onchange="calculate_total(this)" onkeyup="calculate_total(this)"  step="0.01" id="rate"/>'
                    +'</td><td><input type="text" min="0" required  id="total" disabled/></td><td style="width: 20px; text-align: center; padding-right: 5px;">'
                    +'<img id="delete_button" onclick="delete_this_row(this)" style="color: #f00; cursor: pointer; height: 20px; width: 20px; margin-right: auto;  margin-left: auto;" src="../ui/images/cross_button.png"/>'
                    +'<img id="activate_button" onclick="enable_this_row(this)" style="color: #f00; cursor: pointer; height: 20px; width: 20px; margin-right: auto; margin-left: auto; display: none;" src="../ui/images/tick_button.png" />'
                    +'</td></tr>';
            var lastcount = $('table#items_table tbody tr:last-child').attr('slno');
            if(!($.isNumeric(lastcount))){
                lastcount = 0;
            }
            $('table#items_table tbody').append(row);
            lastcount = parseInt(lastcount) + 1;
            $('table#items_table tbody tr:last-child').attr('slno', lastcount);
            $('table#items_table tbody tr:last-child td:first-child').html(lastcount);
        }
        function load_purchace(){
            var purchace_id = $('input#purchace_id').val();
            var data = {
                        form_id: 8,
                        purchace_id: purchace_id
            }
            get_form_data(data,
                   function(message, purchace) {
                       //alert(message);
                       var form = $('form.action_form');
                       form.find('input#wendor_id').val(purchace.wendor);
                       form.find('input#bill_number').val(purchace.bill_number);
                       form.attr('purchace_id', purchace.id);
                       form.find('span#total').html(purchace.amount);
                       var items = purchace.items;
                       $('table#items_table tbody').empty();
                       for(var i = 0; i<items.length; i++){
                            add_purchace_item();
                            var row = $('table#items_table tbody tr:last-child');
                            var item = items[i];
                            row.find('input#item').attr('disabled', 'disabled');
                            row.find('input#item').val(item.item_name);
                            row.find('input#quantity').val(item.quantity);
                            //row.find('input#quantity').attr('max', item.quantity);
                            row.find('input#rate').val(item.rate);
                            row.find('input#total').val(item.rate * item.quantity);
                       } 
                       //alert(purchace.stocked);
                       if(purchace.stocked==1){
                           //form.find('input').prop('disabled', 'true');
                           form.find('input#quantity').prop('disabled', 'disabled');
                           form.find('input#rate').prop('disabled', null);
                           form.find('img#delete_button').prop('onclick', false);
                           form.find('img#activate_button').prop('onclick', false);
                           form.find('input[type="button"]').prop('disabled', 'disabled');
                           form.find('input[type="submit"]').prop('disabled', null);
                           form.find('input[type="reset"]').prop('disabled', 'disabled');
                           form.find('input#quantity').prop('step', "0.001");
                           form.find('input#rate').prop('step', "0.01");
                           alert("Purchace already stocked. Can't edit quantity now");
                       }else{                           
                           form.find('input[type="button"]').prop('disabled', null);
                           form.find('input[type="submit"]').prop('disabled', null);
                           form.find('input[type="reset"]').prop('disabled', null);
                           form.find('input[type="number"]').prop('disabled', null);
                           form.find('input#quantity').prop('step', "0.001");
                           form.find('input#rate').prop('step', "0.01");
                       }
                   }, function(message) {
                       var form = $('form.action_form');
                       form.find('input').prop('disabled', 'true');
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
                    if($(this).attr('status')=='active'){
                        var item_input = $(this).find('input#item');
                        var item_name = item_input.val();
                        var item_option_obj = $('datalist#items').find("option[value='" + item_name + "']");
                        if (item_option_obj.length == "0") {
                            item_input.css('border', '1px solid #f00');
                            invalid_item_precent = true;
                            return;
                        }else{
                            var id = item_option_obj.attr('id');
                            var quantity = $(this).find('input#quantity').val();
                            var rate = $(this).find('input#rate').val();
                            var total = $(this).find('input#total').val();
                            var item = {
                                id:id,
                                quantity:quantity,
                                rate:rate,
                                total:total
                            }
                            items[i++] = item;
                        }
                    }
                });

                if (invalid_item_precent) {
                    //alert("Invalid Item Precent");
                    return;
                }
                
                var id = 8;
                var operation = $(this).attr('operation');
                var purchace_id = $(this).attr('purchace_id');
                var bill_number = $('form input#bill_number').val();
                var total = $('span#total').html();
                if(items.length == 0){
                    items = 'no_items';
                }
                if (operation == 'update') {
                    var data = {
                        form_id: id,
                        purchace_id: purchace_id,
                        total: total,
                        bill_number: bill_number,
                        items: items
                    }
                    update_form_data(data, 
                        function(message) {
                            get_form(8,function(html, tools) {
                                    $('div#form-body').html(html);
                                    $('div#content-body-action-tools').html(tools);
                                }, function(message) {
                                    $('font#section_heading').empty();
                                    $('div#content-body-action-tools').empty();
                                    $('div#form-body').empty();
                                    alert(message);
                                });
                        alert(message);
                    }, function(message) {
                        alert(message);
                    });
                } else {
                    alert("Invalid Operation " + id + ' - ' + operation);
                }
            });
        });
    </script>

    <?php
    $form = ob_get_clean();
    return $form;
}

function get_form_tools_html($id){
    ob_start();
    
    $tools = ob_get_clean();
    return $tools;
}
?>