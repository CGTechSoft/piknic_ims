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
                            NAME
                        </td>
                        <td style="">
                            USER NAME
                        </td>
                        <td style="">
                            SHOP
                        </td>
                        <td style="">
                            TYPE   
                        </td>
                    </tr>
                </thead>
                <tbody style="padding-left: 3px; text-align: center; ">
                    <?php
                    $user = new user();
                    $users = $user->getUsers();
                    $i = 0;
                    if(is_array($users) and count($users)!=0){
                       foreach ($users as $user) {
                           ?>
                           <tr id="<?php echo $user->id; ?>" <?php if($user->id!=1){echo ' onclick="select_row(this)"';} ?>>
                               <td style="text-align: center;"><?php echo ++$i; ?></td>
                               <td id="user_id"><?php echo 'USER-'.$user->id; ?></td>
                               <td id="name"><?php echo $user->name; ?></td>
                               <td id="user_name"><?php echo $user->user_name; ?></td>
                               <?php if($user->user_type_id == 4){
                                   echo '<td>NA</td>';
                               }else{ ?>
                               <td id="company_id" value="<?php echo $user->company_id; ?>"><?php 
                                    $company = new company();
                                    $company->id = $user->company_id; 
                                    $company->getCompany();
                                    echo $company->company_name . ' - ' . $company->company_code;
                               ?></td>
                               <?php 
                               } 
                               ?>
                               <td id="user_type_id" value="<?php echo $user->user_type_id; ?>"><?php 
                                    $user_type = new user_type();
                                    $user_type->id = $user->user_type_id; 
                                    $user_type->getUserType();
                                    echo $user_type->user_type_name;
                               ?></td>
                           </tr>
                           <?php
                       }
                    }else{
                        echo '<tr><td colspan="8"> No User Found </td></tr>';
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
            var id = selected_row.attr('id');
            get_form(29,  ///user create form
                function (html, tools){
                    $('div#form-body').html(html);
                    $('div#content-body-action-tools').html(tools);
                    var form = $('div#form-body').find('form.action_form');
                    form.attr('operation', 'update');
                    form.attr('user_id', id);
                    form.find('input[type=submit]').val('UPDATE');
                    $('div#head_div').html('ID : SHOP - '+id);
                    $('div#head_div').css('display', 'block');
                },
                function (message){
                    $('font#section_heading').empty();
                    $('div#form-body').empty();
                    alert(message);
                },
                id
             );
        }
        function on_add_clicked(){
            get_form(29,  ///user create form
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
        function on_delete_clicked(){            
            var selected_row = $('tr[status="selected"]');
            var id = selected_row.attr('id');
            if(confirm('Are you sure you want to delete USER-'+id+' ?' )){
                var data = {
                    form_id : 28,
                    user_id : id
                }
                delete_form_data(data, function(message) {
                    get_form(28,
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
    <img onclick="on_add_clicked()" id="add" src="../ui/images/add.png" height="40" width="40" style="margin: 15px auto 0px 12px; cursor: pointer;">
    <img id="edit_fade" src="../ui/images/edit_fade.png" height="40" width="40" style="margin: 15px auto 0px 12px;">
    <img onclick="on_edit_clicked()" id="edit" onclick="" src="../ui/images/edit.png" height="40" width="40" style="margin: 15px auto 0px 12px; cursor: pointer; display: none;">
    <img id="delete_fade" src="../ui/images/delete_fade.png" height="40" width="40" style="margin: 15px auto 0px 12px;">
    <img onclick="on_delete_clicked()" id="delete" src="../ui/images/delete.png" height="40" width="40" style="margin: 15px auto 0px 12px; cursor: pointer; display: none;">
    <script>
        
    </script>
    <?php
    $tools = ob_get_clean();
    return $tools;
}
?>