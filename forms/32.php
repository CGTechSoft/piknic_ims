<?php

function get_form_html($form_id, $id) {
    ob_start();
    ?>
    <div id="head_div" style="padding: 5px 0; background-color: #ECECEC;  color: #FFD00F;
         border-radius: 5px;margin-left: auto; text-align: center; ">
        LAST 50 BANK DEPOSITS
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
                    <tr  status="not_selected">
                        <td>
                            #
                        </td>
                        <td>
                            ID
                        </td>
                        <td>
                            DATE AND TIME
                        </td>
                        <td>
                            AMOUNT
                        </td>
                        <td>
                            BANK
                        </td>
                        <td style="">
                            DESCRIPTION
                        </td>
                    </tr>
                </thead>
                <tbody style="padding-left: 3px; text-align: center; ">
                    <?php
                    $bank_deposit = new bank_deposits();
                    $user = new user();
                    $user->id = $_SESSION['user_id'];
                    $user->getUser();
                    $bank_deposits = $bank_deposit->getBankDeposits($user->company_id);
                    $i = 0;
                    if($bank_deposits==NULL || sizeof($bank_deposits)==0){
                        echo '<tr><td colspan="8"> No Bank deposits Found </td></tr>';
                    } else{
                    foreach ($bank_deposits as $bank_deposit) {
                        ?>
                        <tr id="<?php echo $bank_deposit->id; ?>"  onclick="select_row(this)" status="not_selected">
                            <td style="text-align: center;">
                                <?php echo ++$i; ?>
                            </td>
                            <td>
                                <?php echo $bank_deposit->id; ?>
                            </td>
                            <?php 
                                $date = date('d/m/Y',(strtotime($bank_deposit->deposited_at)+(5.5*60*60) ));
                                $time = date('h:m a',(strtotime($bank_deposit->deposited_at)+(5.5*60*60) ));
                            ?>
                            <td>
                                <?php  echo $date . ' - ' . $time; ?>
                            </td>
                            <td id="amount"><?php echo number_format($bank_deposit->amount, 2, '.', ''); ?></td>
                            <?php
                            $bank = new bank();
                            $bank->id = $bank_deposit->bank_id;
                            $bank->getBank();
                            ?>
                            <td id="bank" bank_id="<?php echo $bank->id; ?>">
                                <?php 
                                    echo $bank->bank_name.' - '.$bank->branch.' - '.$bank->account_number; 
                                ?>
                            </td>
                            <td id="description"><?php echo $bank_deposit->description; ?></td>
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
            var amount = selected_row.find('td#amount').html();
            var id = selected_row.attr('id');
            var description = selected_row.find('td#description').html();
            var bank_id = selected_row.find('td#bank').attr('bank_id');
            get_form(4,  ///bank deposit create form
                function (html, tools){
                    $('div#form-body').html(html);
                    $('div#content-body-action-tools').html(tools);
                    var form = $('div#form-body').find('form.action_form');
                    form.attr('operation', 'update');
                    form.attr('bank_deposit_id', id);
                    form.find('input#amount').val(amount);
                    form.find('textarea#description').val(description);
                    form.find('select#bank_account').val(bank_id);
                    form.find('input[type=submit]').val('UPDATE');
                    $('div#head_div').html('ID : BANK DEPOSIT-'+id);
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
            if(confirm('Are you sure you want to delete EXPENCE-'+id+' ?' )){
                var data = {
                    form_id : 32,
                    bank_deposit_id : id
                }
                delete_form_data(data, function(message) {
                    get_form(32,
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
    </script>

    <?php
    $form = ob_get_clean();
    return $form;
}

function get_form_tools_html($id){
    ob_start();
    ?>    
    <img id="edit_fade" src="../ui/images/edit_fade.png" height="40" width="40" style="margin: 15px auto 0px 12px;">
    <img id="edit" onclick="on_edit_clicked()" src="../ui/images/edit.png" height="40" width="40" style="margin: 15px auto 0px 12px; cursor: pointer; display: none;">
    <img id="delete_fade" src="../ui/images/delete_fade.png" height="40" width="40" style="margin: 15px auto 0px 12px;">
    <img onclick="on_delete_clicked()" id="delete" src="../ui/images/delete.png" height="40" width="40" style="margin: 15px auto 0px 12px; cursor: pointer; display: none;">
    <script>
        
    </script>
    <?php
    $tools = ob_get_clean();
    return $tools;
}
?>