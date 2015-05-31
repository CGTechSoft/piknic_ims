<?php

function get_form_html($form_id, $id) {
    ob_start();
    ?>
    <style>
        .field_name{
            width: 20%;
        }
        .field input{
            width: 100%;
            margin-left: 0px;
        }
        .field textArea{
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
         border-radius: 5px;margin-left: auto;display: none; text-align: center; ">
    </div>
    <div style="margin-top: 10px; background-color:transparent;padding-bottom: 30px;">
        <form action="#" method="post" class="action_form" operation="add" style="width:100%;" >
            <table style="width:100%;">
                <tr>
                    <td class="field_name">                    
                        <font>AMOUNT</font>
                    </td>
                    <td class="field"> 
                        <div  class="parent">
                            <input type="number" step="0.01" id="amount" required />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="field_name">                    
                        <font>BANK ACCOUNT</font>
                    </td>
                    <td class="field"> 
                        <div  class="parent">
                            <select id="bank_account" required >
                                <?php
                                $bank = new bank();
                                $banks = $bank->getBanks();
                                if(is_array($banks) and count($banks)!=0){
                                    foreach ($banks as $bank) {
                                        echo '<option value="'.$bank->id.'">'.$bank->bank_name.' - '.$bank->branch.' - '.$bank->account_number.'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="field_name"> 
                        <font>DESCRIPTION</font>
                    </td>
                    <td class="field"> 
                        <div class="parent">
                            <textarea id="description" required rows="5" ></textarea>
                        </div>
                    </td>
                </tr>
                <tr></tr>
                <tr>
                    <td></td>
                    <td>
                        <div style="padding: 0px 12px;">
                            <div style="width: 100%; margin-left: -12px; padding: 12px; 
                                 background-color: #92BB0D; border-radius: 5px; float: left;">
                                <div style="width: 50%; float: right;  ">
                                     <input style="width: 100%;" type="submit" value="ADD" />
                                </div>
                                <div style="width: 50%;">
                                    <input style="width: 100%;" type="reset" value="CANCEL" />
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <script type="text/javascript">
        function setFormActionListener(){ 
        $('form.action_form').on('submit', function(e) {
            e.preventDefault();
            var id = 4;
            var operation = $(this).attr('operation');
            if (operation == 'add') {
                var data = {
                    form_id: id,
                    amount: $('form input#amount').val(),
                    bank_id: $('form select#bank_account').val(),
                    description: $('form textarea#description').val()
                }
                add_form_data(data, function(message) {
                    get_form(4,function(html) {
                                $('div#form-body').html(html);
                           }, function(message) {
                                $('font#section_heading').empty();
                                $('div#form-body').empty();
                                alert(message);
                           });
                    alert(message);
                }, function(message) {
                    alert(message);
                });
            }else if (operation == 'update') {
                var bank_deposit_id = $('form.action_form').attr('bank_deposit_id');
                var data = {
                    form_id: id,
                    bank_deposit_id: bank_deposit_id,
                    amount: $('form input#amount').val(),
                    bank_id: $('form select#bank_account').val(),
                    description: $('form textarea#description').val()
                }
                update_form_data(data, function(message) {
                    get_form(32,function(html, tools) {
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
            } else {
                alert("Invalid Operation " + id + ' - ' + operation);
            }
        });
        };
        setFormActionListener();
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
            get_form(32,
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