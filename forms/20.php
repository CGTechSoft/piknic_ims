<?php

function get_form_html($form_id, $id) {
    ob_start();
    ?>
    <div id="head_div" style="padding: 5px 0; background-color: #ECECEC;  color: #FFD00F;
         border-radius: 5px;margin-left: auto; text-align: center; ">
        <?php
        $user = new user();
        $user->id = $_SESSION['user_id'];
        $user->getUser();
        $company = new company();
        $company->id = $user->company_id;
        $company->getCompany();
        echo "STOCK REPORT OF $company->company_name - $company->company_code";  
        ?>
    </div>
    <div style="margin-top: 10px; background-color:transparent;padding-bottom: 30px;">
        <style>
            div#purchace_items td,div#purchace_items th{
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
        <input type="text" id="search" placeholder="Enter Search Key here..." style="width: 100%; margin-left: 0px;" onkeyup="search()" />
        <style>
            img#search{
                position: relative;
                height: 20px;
                width: 20px;
                float: right;
                top: -29px;
                right: 10px;
            }
        </style>
        <img id="search" src="../ui/images/search.png" onclick="search()" />
        <div id="purchace_items" style="width: 100%; padding: 10px 0; color: #FFD00F;">           
            <table id="items_table" style="border-collapse: collapse; width: 100%; 
                   background-color: #fff; border-radius: 10px;  color: #FFD00F;">
                <thead style="text-align: center;">
                    <tr  status="not_selected">
                        <th>
                            #
                        </th>
                        <th>
                            ITEM
                        </th>
                        <th>
                            STOCK COUNT
                        </th>
                        <th style="">
                            SELLING PRIZE
                        </th>
                        <th style="">
                            TAX
                        </th>
                    </tr>
                </thead>
                <tbody style="padding-left: 3px; text-align: center; ">
                    <?php
                    $inventry = new inventry();
                    $inventries = $inventry->getInventryForSpecificCompany($user->company_id);
                    $i = 0;
                    if($inventries==NULL || sizeof($inventries)==0){
                        echo '<tr><td colspan="8"> No Stock Found </td></tr>';
                    } else{
                    foreach ($inventries as $inventry) {
                        ?>
                        <tr id="<?php echo $inventry->id; ?>"  onclick="select_row(this)" status="not_selected">
                            <td style="text-align: center;">
                                <?php echo ++$i; ?>
                            </td>
                            <td id="item_name"><?php
                                $item = new item();
                                $item->id = $inventry->item_id;
                                $item->getItem();
                                echo $item->item_name. ' ('.$item->item_code.')';
                            ?></td>
                            <td id="in_stock_count"><?php echo number_format($inventry->in_stock_count, 3, '.',''); ?></td>
                            <td id="mrp"><?php echo number_format($inventry->selling_prize, 2, '.',''); ?></td>
                            <td id="tax_category" tax_category_id="<?php echo $inventry->tax_category_id; ?>"><?php
                                $tax = new tax_category();
                                $tax->id = $inventry->tax_category_id;
                                $tax->getTaxCategory();
                                echo $tax->tax_category_name;
                            ?></td>
                        </tr>
                    <?php
                    }
                }
                    ?>
                </tbody>                               
            </table>
        </div>
    </div>
    <script type="text/javascript">
        function select_row(row) {
            var j_row = $(row);
            if(j_row.attr('status') == 'selected'){
                $('table#items_table tr').attr('status', 'not_selected');
                $('table#items_table tr').css('background-color', '#FFF');
                $('img#edit').css('display', 'none');
                $('img#edit_fade').css('display', 'block');
                $('img#delete').css('display', 'none');
                $('img#delete_fade').css('display', 'block');
            }else{            
                $('table#items_table tr').attr('status', 'not_selected');
                $('table#items_table tr').css('background-color', '#FFF');
                j_row.attr('status', 'selected');
                j_row.css('background-color', '#C0EFFD');
                $('img#edit').css('display', 'block');
                $('img#edit_fade').css('display', 'none');
                $('img#delete').css('display', 'block');
                $('img#delete_fade').css('display', 'none');
            }          
        }
        function on_edit_clicked(){
            var selected_row = $('tr[status="selected"]');
            var item_name = selected_row.find('td#item_name').html();
            var id = selected_row.attr('id');
            var instock_count = selected_row.find('td#in_stock_count').html();
            var mrp = selected_row.find('td#mrp').html();
            var tax_category_id = selected_row.find('td#tax_category').attr('tax_category_id');
            get_form(25,  ///inventry edit form
                function (html, tools){
                    $('div#form-body').html(html);
                    $('div#content-body-action-tools').html(tools);
                    var form = $('div#form-body').find('form.action_form');
                    form.attr('operation', 'update');
                    form.attr('inventry_id', id);
                    form.find('input#item_name').val(item_name);
                    form.find('input#in_stock_count').val(instock_count);
                    form.find('input#in_stock_count').prop('disabled', null);
                    form.find('input#mrp').val(mrp);
                    form.find('input#mrp').prop('disabled', null);
                    form.find('select#tax_category').find('option#'+tax_category_id).prop('selected', true);
                    form.find('select#tax_category').prop('disabled', null);
                    form.find('input[type=submit]').val('UPDATE');
                    form.find('input[type=submit]').prop('disabled', null);
                    form.find('input[type=reset]').prop('disabled', null);
                    $('div#head_div').html('ID : INVENTRY-'+id);
                    $('div#head_div').css('display', 'block');
                },
                function (message){
                    $('font#section_heading').empty();
                    $('div#form-body').empty();
                    alert(message);
                }
             );
        }
        function on_delete_clicked(){            
            var selected_row = $('tr[status="selected"]');
            var id = selected_row.attr('id');
            if(confirm('Are you sure you want to delete INVENTRY-'+id+' ?' )){
                var data = {
                    form_id : 20,
                    inventry_id : id
                }
                delete_form_data(data, function(message) {
                    get_form(20,
                        function(html, tools) {
                             $('div#form-body').html(html);
                             $('div#content-body-action-tools').html(tools);
                        }, function(message) {
                             $('font#section_heading').empty();
                             $('div#form-body').empty();
                             alert(message);
                        });
                    alert(message);
                }, function(message) {
                    alert(message);
                });
            }
        }
        function search(){
            var search_key = $('input#search').val();
            if(search_key !== ''){                
                console.log("search key "+ search_key);
                searchTable(search_key);
            }else{
                $('#items_table tr').show();
            }
        }
        function searchTable(inputVal)
        {
                var table = $('#items_table');
                table.find('tr').each(function(index, row)
                {
                        var allCells = $(row).find('td');
                        if(allCells.length > 0)
                        {
                                var found = false;
                                allCells.each(function(index, td)
                                {
                                        var regExp = new RegExp(inputVal, 'i');
                                        if(regExp.test($(td).text()))
                                        {
                                                found = true;
                                                return false;
                                        }
                                });
                                if(found == true)$(row).show();else $(row).hide();
                        }
                });
        }
    </script>

    <?php
    $form = ob_get_clean();
    return $form;
}

function get_form_tools_html($id){
    ob_start();
    ?>    
    <img id="edit_fade" src="../ui/images/edit_fade.png" height="40" width="40" style="margin: 15px auto 0px 12px;">
    <img onclick="on_edit_clicked()" id="edit" onclick="" src="../ui/images/edit.png" height="40" width="40" style="margin: 15px auto 0px 12px; cursor: pointer; display: none;">
    <img id="delete_fade" src="../ui/images/delete_fade.png" height="40" width="40" style="margin: 15px auto 0px 12px;">
    <img onclick="on_delete_clicked()" id="delete" onclick="" src="../ui/images/delete.png" height="40" width="40" style="margin: 15px auto 0px 12px; cursor: pointer; display: none;">
    <script>
        
    </script>
    <?php
    $tools = ob_get_clean();
    return $tools;
}
?>