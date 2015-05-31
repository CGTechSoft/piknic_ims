<?php

function get_form_html($form_id, $id) {
    ob_start();
    ?>
    <div style="height: 150px; 
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
                            VENDOR NAME
                        </td>
                        <td>
                            CONTACT NUMBER
                        </td>
                        <td>
                            TIN NUMBER
                        </td>
                        <td style="">
                            CONTACT ADDRESS
                        </td>
                    </tr>
                </thead>
                <tbody style="padding-left: 3px; text-align: center; ">
                    <?php
                    $vendor = new wendors();
                    $vendors = $vendor->getWendors();
                    $i = 0;
                    if($vendors==NULL || sizeof($vendors)==0){
                        echo '<tr><td colspan="8"> No Vendor Found </td></tr>';
                    } else{
                    foreach ($vendors as $vendor) {
                        ?>
                    <tr id="<?php echo $vendor->id; ?>" onclick="select_row(this)"  status="not_selected">
                            <td style="text-align: center;"><?php echo ++$i; ?></td>
                            <td><?php echo 'VENDOR-'.$vendor->id; ?></td>
                            <td id="vendor_name"><?php echo $vendor->wendor_name; ?></td>
                            <td id="contact_no"><?php echo $vendor->contact_no; ?></td>
                            <td id="wendor_tin_number"><?php echo $vendor->wendor_tin_number; ?></td>
                            <td id="contact_address"><?php echo $vendor->contact_address; ?></td>
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
            var vendor_name = selected_row.find('td#vendor_name').html();
            var id = selected_row.attr('id');
            var contact_number = selected_row.find('td#contact_no').html();
            var tin_number = selected_row.find('td#wendor_tin_number').html();
            var contact_address = selected_row.find('td#contact_address').html();
            get_form(10,  ///vendor create form
                function (html, tools){
                    $('div#form-body').html(html);
                    $('div#content-body-action-tools').html(tools);
                    var form = $('div#form-body').find('form.action_form');
                    form.attr('operation', 'update');
                    form.attr('vendor_id', id);
                    form.find('input#vendor_name').val(vendor_name);
                    form.find('input#contact_number').val(contact_number);
                    form.find('input#contact_address').val(contact_address);
                    form.find('input#tin_number').val(tin_number);
                    form.find('input[type=submit]').val('UPDATE');
                    $('div#head_div').html('ID : VENDOR-'+id);
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
            if(confirm('Are you sure you want to delete VENDOR-'+id+' ?' )){
                var data = {
                    form_id : 18,
                    vendor_id : id
                }
                delete_form_data(data, function(message) {
                    get_form(18,
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
            get_form(10,  ///vendor create form
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