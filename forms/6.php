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
                        <font>CUSTOMER NAME</font>
                    </td>
                    <td class="field"> 
                        <div  class="parent">
                            <input type="text" id="customer_name" required />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="field_name"> 
                        <font>CONTACT NUMBER</font>
                    </td>
                    <td class="field"> 
                        <div class="parent">
                            <input type="text" id="contact_number" required />
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
            var id = 6;
            var operation = $(this).attr('operation');
            if (operation == 'add') {
                var data = {
                    form_id: id,
                    customer_name: $('form input#customer_name').val(),
                    contact_number: $('form input#contact_number').val()
                }
                add_form_data(data, function(message) {
                    get_form(6,function(html) {
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
                var customer_id = $('form.action_form').attr('customer_id');
                var data = {
                    form_id: id,
                    customer_id: customer_id,
                    customer_name: $('form input#customer_name').val(),
                    contact_number: $('form input#contact_number').val()
                }
                update_form_data(data, function(message) {
                    get_form(21,function(html, tools) {
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
            get_form(21,
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