<?php

function get_form_html($form_id, $id, $page, $limit, $adjacents) {
    ob_start();
    $customer = new customer();
    $user = new user();
    $user->id = $_SESSION['user_id'];
    $user->getUser();
    $count = $customer->getCustomersCount($user->company_id);
    if ($page == 1) {
        $start = 0;
        $head_message = "LAST $limit CUSTOMERS";
    } else {
        $start = ($page - 1) * $limit;
        $head_message = "CUSTOMERS $start TO ".($start + $limit);
    }
    ?>
    <div id="head_div" style="padding: 5px 0; background-color: #ECECEC;  color: #FFD00F;
         border-radius: 5px;margin-left: auto; text-align: center; ">
        <?php echo $head_message; ?>
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
        
        <div style="padding: 10px 0; background-color: transparent; 
             border-radius: 5px;margin-left: auto; text-align: center;overflow-x: auto; ">
             <?php echo pagination($limit, $adjacents, $count, $page); ?>
        </div>
        
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
                            CUSTOMER NAME
                        </td>
                        <td style="">
                            CONTACT NUMBER
                        </td>
                        <td style="">
                            TOTAL PURCHACE
                        </td>
                    </tr>
                </thead>
                <tbody style="padding-left: 3px; text-align: center; ">
                    <?php
                    $customers = $customer->getCustomersPaged($user->company_id, $start, $limit);
                    $i = $start;
                    if($customers==NULL || sizeof($customers)==0){
                        echo '<tr><td colspan="8"> No Customer Found </td></tr>';
                    } else{
                    foreach ($customers as $customer) {
                        ?>
                        <tr id="<?php echo $customer->id; ?>"  onclick="select_row(this)" status="not_selected">
                            <td style="text-align: center;">
                                <?php echo ++$i; ?>
                            </td>
                            <td>
                                <?php echo $customer->id; ?>
                            </td>
                            <td id="customer_name"><?php echo $customer->customer_name; ?></td>
                            <td id="contact_number"><?php echo $customer->contact_number; ?></td>
                            <td>
                                <?php echo $customer->total_purchace_amount; ?>
                            </td>
                        </tr>
                    <?php
                    }
                }
                    ?>
                </tbody>                               
            </table>
        </div>
        
        <div style="padding: 10px 0; background-color: transparent; 
             border-radius: 5px;margin-left: auto; text-align: center;overflow-x: auto; ">
             <?php echo pagination($limit, $adjacents, $count, $page); ?>
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
            var customer_name = selected_row.find('td#customer_name').html();
            var id = selected_row.attr('id');
            var contact_number = selected_row.find('td#contact_number').html();
            get_form(6,  ///customer create form
                function (html, tools){
                    $('div#form-body').html(html);
                    $('div#content-body-action-tools').html(tools);
                    var form = $('div#form-body').find('form.action_form');
                    form.attr('operation', 'update');
                    form.attr('customer_id', id);
                    form.find('input#customer_name').val(customer_name);
                    form.find('input#contact_number').val(contact_number);
                    form.find('input[type=submit]').val('UPDATE');
                    $('div#head_div').html('ID : CUSTOMER-'+id);
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
            if(confirm('Are you sure you want to delete CUSTOMER-'+id+' ?' )){
                var data = {
                    form_id : 21,
                    customer_id : id
                }
                delete_form_data(data, function(message) {
                    get_form(21,
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
        
        function set_pagination_listener(){          
            $('.pagination').on('click','.page-numbers',function(e){
                e.preventDefault();
                var page = $(this).attr('page');
                var id = 0;
                get_form(21,
                    function(html, tools) {
                        $('div#form-body').html(html);
                        $('div#content-body-action-tools').html(tools);
                    }, function(message) {
                        $('font#section_heading').empty();
                        alert(message);
                    },id
                    ,page
                );
                return false;
             }); 
        }
        set_pagination_listener();
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
    <img onclick="on_delete_clicked()" id="delete" onclick="" src="../ui/images/delete.png" height="40" width="40" style="margin: 15px auto 0px 12px; cursor: pointer; display: none;">
    <script>
        
    </script>
    <?php
    $tools = ob_get_clean();
    return $tools;
}
?>