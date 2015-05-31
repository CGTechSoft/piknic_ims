<?php

function get_form_html($form_id, $id) {
    ob_start();
    ?>
    <div id="head_div" style="height: 150px; 
         width: 320px; background-color: #ECECEC; 
         border-radius: 5px;margin-left: auto;display: none; ">

        

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
                    <tr status="not_selected">
                        <td>
                            #
                        </td>
                        <td>
                            ID
                        </td>
                        <td>
                            BANK NAME
                        </td>
                        <td>
                            BRANCH
                        </td>
                        <td style="">
                            IFSC CODE
                        </td>
                        <td style="">
                            ACCOUNT NUMBER
                        </td>
                    </tr>
                </thead>
                <tbody style="padding-left: 3px; text-align: center; ">
                    <?php
                    $bank = new bank();
                    $banks = $bank->getBanks();
                    $i = 0;
                    if($banks==NULL || sizeof($banks)==0){
                        echo '<tr><td colspan="8"> No Bank Found </td></tr>';
                    } else{
                    foreach ($banks as $bank) {
                        ?>
                        <tr id="<?php echo $bank->id; ?>" onclick="select_row(this)">
                            <td style="text-align: center;"><?php echo ++$i; ?></td>
                            <td id="bank_id"><?php echo 'BANK-'.$bank->id; ?></td>
                            <td id="bank_name"><?php echo $bank->bank_name; ?></td>
                            <td id="branch"><?php echo $bank->branch; ?></td>
                            <td id="ifsc_code"><?php echo $bank->ifsc_code; ?></td>
                            <td id="account_number"><?php echo $bank->account_number; ?></td>
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
            var bank_name = selected_row.find('td#bank_name').html();
            var id = selected_row.attr('id');
            var branch = selected_row.find('td#branch').html();
            var ifsc_code = selected_row.find('td#ifsc_code').html();
            var account_number = selected_row.find('td#account_number').html();
            get_form(22,  ///bank create form
                function (html, tools){
                    $('div#form-body').html(html);
                    $('div#content-body-action-tools').html(tools);
                    var form = $('div#form-body').find('form.action_form');
                    form.attr('operation', 'update');
                    form.attr('bank_id', id);
                    form.find('input#bank_name').val(bank_name);
                    form.find('input#branch').val(branch);
                    form.find('input#ifsc_code').val(ifsc_code);
                    form.find('input#account_number').val(account_number);
                    form.find('input[type=submit]').val('UPDATE');
                    $('div#head_div').html('ID : BANK-'+id);
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
            if(confirm('Are you sure you want to delete BANK-'+id+' ?' )){
                var data = {
                    form_id : 23,
                    bank_id : id
                }
                delete_form_data(data, function(message) {
                    get_form(23,
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
        function on_add_clicked(){
            get_form(22,  ///bank create form
                function (html, tools){
                    $('div#form-body').html(html);
                    $('div#content-body-action-tools').html(tools);
                },
                function (message){
                    $('font#section_heading').empty();
                    $('div#form-body').empty();
                    alert(message);
                }
             );
        }
    </script>

    <?php
    $form = ob_get_clean();
    return $form;
}

function get_form_tools_html($id){
    ob_start();
    ?>    
    <img onclick="on_add_clicked()" id="add" src="../ui/images/add.png" height="40" width="40" style="margin: 15px auto 0px 12px; cursor: pointer;">
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