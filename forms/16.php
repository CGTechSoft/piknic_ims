<?php

function get_form_html($form_id, $id) {
    ob_start();
    ?>
    <div id="head_div" style="padding: 5px 0; background-color: #ECECEC;  color: #FFD00F;
         border-radius: 5px;margin-left: auto; text-align: center; ">
        UN-STOCKED PURCHACES
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
                    <tr>
                        <td>
                            #
                        </td>
                        <td>
                            ID
                        </td>
                        <td>
                            DATE
                        </td>
                        <td style="">
                            PURCHACED FROM
                        </td>
                        <td style="">
                            PURCHACED BY
                        </td>
                        <td style="">
                            AMOUNT
                        </td>
                        <td style="">

                        </td>
                        <td style="">

                        </td>
                    </tr>
                </thead>
                <tbody style="padding-left: 3px; text-align: center; ">
                    <?php
                    $purchace_obj = new purchaces();
                    $user = new user();
                    $user->id = $_SESSION['user_id'];
                    $user->getUser();
                    $purchaces = $purchace_obj->getNotStockedPurchaces($user->company_id);
                    $i = 0;
                    if($purchaces==NULL || sizeof($purchaces)==0){
                        echo '<tr><td colspan="8"> No Purchace Found </td></tr>';
                    } else{
                    foreach ($purchaces as $purchace) {
                        ?>
                        <tr id="<?php echo $purchace->id; ?>">
                            <td style="text-align: center;">
                                <?php echo ++$i; ?>
                            </td>
                            <td>
                                <?php echo $purchace->id; ?>
                            </td>
                            <td>
                                <?php echo $purchace->created_at; ?>
                            </td>
                            <td>
                                <?php
                                $vendor = new wendors();
                                $vendor->id = $purchace->wendor_id;
                                $vendor->getWendor();
                                echo $vendor->wendor_name;
                                ?>
                            </td>
                            <td>
                                <?php
                                $p_manager = new user();
                                $p_manager->id = $purchace->purchace_manager_id;
                                $p_manager->getUser();
                                echo $p_manager->name;
                                ?>
                            </td>
                            <td>
                                <?php echo $purchace->amount; ?>
                            </td>
                            <td id="ok_button" style="width: 20px;text-align: center; padding: 10px;">
                                <img id="add_to_stock_button" style="width: 20px; height: 20px; cursor: pointer;"
                                     onclick="add_to_stock(this)" src="../ui/images/tick_button.png"/>
                            </td>
                            <td id="down_button" style="width: 20px;text-align: center; padding: 10px;">
                                <img id="toggle_button" style="width: 20px; height: 20px; cursor: pointer;"
                                     onclick="toggle_items_visibility(this)" src="../ui/images/down_arrow.png"/>
                                <img id="toggle_button" style="width: 20px; height: 20px; cursor: pointer; display: none;"
                                     onclick="toggle_items_visibility(this)" src="../ui/images/up_arrow.png"/>
                            </td>
                        </tr>
                        <tr id="purchace_item" style="display: none;">
                            <td colspan="8" style="padding:0 0 20px 0;">
                                <table style="border-collapse: collapse; background-color: #c0effd; width: 80%; color: #FFD00F; float: right;">
                                    <tr>
                                        <td>
                                            ITEM
                                        </td>
                                        <td>
                                            QUANTITY
                                        </td>
                                        <td>
                                            PURCHACE RATE
                                        </td>
                                        <td>
                                            MRP
                                        </td>
                                        <td>
                                            TOTAL
                                        </td>
                                    </tr>
                                    <?php
                                    foreach ($purchace->getPurchaceItems() as $p_item) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                $item = new item();
                                                $item->id = $p_item->item_id;
                                                $item->getItem();
                                                echo $item->item_name . ' - ' . $item->item_code .' (ID : '.$item->id.')';
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo $p_item->quantity; ?>
                                            </td>
                                            <td>
                                                <?php echo $p_item->rate; ?>
                                            </td>
                                            <td>
                                                <?php echo $item->mrp; ?>
                                            </td>
                                            <td>
                                        <?php echo ($p_item->quantity * $p_item->rate); ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                            </td>
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
        function add_to_stock(ok_button) {
            var row = $(ok_button).closest('tr');
            var purchace_id = row.attr('id');
            var data = {
                purchace_id: purchace_id
            }
            add_purchace_to_stock(data, function(message) {
                row.hide();
                row.next().hide();
                if(row.parent('tbody').children('tr:visible').length==0){
                    row.parent('tbody').html('<tr><td colspan="8"> No Purchace left more </td></tr>');
                }
                alert(message);
            }, function(message) {
                alert(message);
            });
        }
        function toggle_items_visibility(down_button) {
            var row = $(down_button).closest('tr');
            row.next('tr').fadeToggle();
            row.find('img#toggle_button').toggle();
            
        }
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
            get_form(20,
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