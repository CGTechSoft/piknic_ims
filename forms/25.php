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
    <div id="head_div" style="padding: 5px 0; background-color: #ECECEC;  color: #21ACD7;
         border-radius: 5px;margin-left: auto;display: none; text-align: center; ">
    </div>
    <div style="margin-top: 10px; background-color:transparent;padding-bottom: 30px;">
        <form action="#" method="post" class="action_form" operation="update" style="width:100%;" >
            <table style="width:100%;">
                <tr>
                    <td class="field_name">                    
                        <font>ITEM NAME</font>
                    </td>
                    <td class="field"> 
                        <div  class="parent">
                            <input type="text" id="item_name" required disabled="disabled" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="field_name"> 
                        <font>IN STOCK COUNT</font>
                    </td>
                    <td class="field"> 
                        <div class="parent">
                            <input type="number" id="in_stock_count" required disabled="disabled" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="field_name"> 
                        <font>MRP</font>
                    </td>
                    <td class="field"> 
                        <div style="padding: 0px 0px;">
                            <input type="number" id="mrp" required disabled="disabled"  />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="field_name"> 
                        <font>TAX CATEGORY</font>
                    </td>
                    <td class="field"> 
                        <div class="parent">
                            <select id="tax_category" required disabled="disabled" >
                                <option value=""></option>
                                <?php
                                $tax_category = new tax_category();
                                $tax_categories = $tax_category->getTaxCategories();
                                foreach ($tax_categories as $tax_category) {
                                    echo '<option id="'.$tax_category->id.'" value="'.$tax_category->tax_percentage.'">'.$tax_category->tax_category_name.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr></tr>
                <tr>
                    <td></td>
                    <td>
                        <div style="padding: 0px 12px;">
                            <div style="width: 100%; margin-left: -12px; padding: 12px; 
                                 background-color: #0d92bb; border-radius: 5px; float: left;">
                                <div style="width: 50%; float: right;  ">
                                    <input style="width: 100%;" disabled="disabled" type="submit" value="UPDATE" />
                                </div>
                                <div style="width: 50%;">
                                    <input style="width: 100%;" disabled="disabled" type="reset" value="CANCEL" />
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
            var id = 25;
            var operation = $(this).attr('operation');
            if (operation == 'update') {
                var inventry_id = $('form.action_form').attr('inventry_id');
                var data = {
                    form_id: id,
                    inventry_id: inventry_id,
                    in_stock_count: $('form input#in_stock_count').val(),
                    mrp: $('form input#mrp').val(),
                    tax_category_id: $('form select#tax_category').find('option:selected').attr('id')
                }
                update_form_data(data, function(message) {
                    get_form(20,
                        function(html, tools) {
                            $('div#form-body').html(html);
                            $('div#content-body-action-tools').html(tools);
                        }, function(message) {
                            $('font#section_heading').empty();
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
            get_form(17,
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