<?php

function get_form_html($form_id, $date) {
    ob_start();
    ?>
    <div id="head_div" style="padding: 5px 0; background-color: #ECECEC;  color: #FFD00F;
         border-radius: 5px;margin-left: auto; text-align: center; ">
        DAY END REPORT OF 
        <input id="date_field" value="<?php 
            if($date == 0 ){ 
                $date = date('d/m/Y',time());
            }
            echo $date;
            ?>" />
    </div>
    <div id="sales_items_table" style="margin-top: 10px; background-color:transparent;padding-bottom: 30px;">
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
            <table id="sales_items_table" style="border-collapse: collapse; width: 100%; 
                   background-color: #fff; border-radius: 10px;  color: #FFD00F;">
                <thead style="text-align: center;">
                    <tr>
                        <td style="width: 5%;">
                            #
                        </td>
                        <td>
                            ITEM
                        </td>
                        <td style="width: 15%;">
                            COUNT
                        </td>
                        <td style="width: 15%;">
                            TOTAL
                        </td>
                    </tr>
                </thead>
                <tbody style="padding-left: 3px; text-align: center; ">
                    <?php
                    $grand_total = 0;
                    $total_count = 0;
                    $sale_item = new sales_items();
                    $user = new user();
                    $user->id = $_SESSION['user_id'];
                    $user->getUser();
                    $date = str_replace('/', '-', $date);
                    $date = date('Y-m-d', strtotime($date));
                    $sales_items = $sale_item->getOneDaysSaleItems($user->company_id, $date);
                    $i = 0;
                    if($sales_items==NULL || sizeof($sales_items)==0){
                        echo '<tr><td colspan="8"> No Sales Items Found </td></tr>';
                    } else{
                    foreach ($sales_items as $sales_item) {
                        $grand_total = $grand_total + $sales_item->total;
                        $total_count = $total_count + $sales_item->quantity;
                        ?>
                        <tr>
                            <td style="text-align: center;">
                                <?php echo ++$i; ?>
                            </td>
                            <?php 
                                $item = new item();
                                $item->id = $sales_item->item_id;
                                $item->getItem();
                            ?>
                            <td style="text-align: left;">
                                <?php echo $item->item_name.' ( '. $item->item_code .' )'; ?>
                            </td>
                            <td>
                                <?php echo number_format($sales_item->quantity, 3, '.',''); ?>
                            </td>
                            
                            <td id="tax">
                                <?php echo number_format($sales_item->total, 2, '.','');  ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
                    ?>
                        <tr>
                            <td></td>
                            <td style="text-align: right;"> TOTAL </td>
                            <td><?php echo number_format($total_count, 3, '.',''); ?></td>
                            <td><?php echo number_format($grand_total, 2, '.',''); ?></td>
                        </tr>
                  </tbody>                               
            </table>
            <table id="sales_statistics_table" style="border-collapse: collapse; width: 100%; 
                   background-color: #fff; border-radius: 10px;  color: #FFD00F; margin-top: 20px;">
                <thead style="text-align: center;">
                    <tr>
                            <td style="width: 5%;">
                                #
                            </td>
                            <td>
                                STATISTICS
                            </td>
                            <td style="width: 15%;">
                                COUNT
                            </td>
                            <td style="width: 15%;">
                                TOTAL TAX
                            </td>
                            <td style="width: 15%;">
                                TOTAL NET. AMOUNT
                            </td>
                            <td style="width: 15%;">
                                TOTAL AMOUNT
                            </td>
                    </tr>
                </thead>
                <tbody style="text-align: center;">
                    <tr style="margin-top: 20px;">
                        <?php
                        $sale = new sales();
                        $vals = $sale->getOneDaysSaleStatistics($user->company_id, $date);
                        ?>
                            <td>1</td>
                            <td style="text-align: left;">SALES</td>
                            <td><?php echo $vals['count']; ?></td>
                            <td><?php echo number_format($vals['tax_amount'], 2, '.',''); ?></td>
                            <td><?php echo number_format($vals['net_amount'], 2, '.',''); ?></td>
                            <td><?php echo number_format($vals['amount'], 2, '.',''); ?></td>
                    </tr>
                </tbody>                               
            </table>
        </div>
    </div>
    <script type="text/javascript">
        function load_day_end_report(){
            var date = $('input#date_field').val();
            get_form(24,
                        function(html, tools) {
                             $('div#form-body').html(html);
                             $('div#content-body-action-tools').html(tools);
                        }, function(message) {
                             $('font#section_heading').empty();
                             $('div#form-body').empty();
                             alert(message);
                        },
                        date);
        }
        function on_print_clicked() {
            var date = $('input#date_field').val();
            $('div#print_container_header')
                    .html('<font style="color:#FFD00F; font-size:20px; ">DAY END REPORT OF '+date+'</font>');
            var html = $('div#sales_items_table').html();
            $('div#print_container_body').html(html);  
            print();
            $('div#print_container_header').empty();
            $('div#print_container_body').empty();
            $('div#print_container_footer').empty();
        }
       function setup_datepicker(){
           $('input#date_field').datepick({
               minDate:'26/04/2015', 
               dateFormat:'dd/mm/yyyy',
               maxDate:'0',
               onSelect:function(){
                    load_day_end_report();
                }
           });
       }
       setup_datepicker();
    </script>

    <?php
    $form = ob_get_clean();
    return $form;
}

function get_form_tools_html($id){
    ob_start();
    ?>    
    <img id="print_fade" src="../ui/images/printer_fade.png" height="40" width="40" style="margin: 15px auto 0px 12px; display: none;">
    <img id="print" onclick="on_print_clicked()" src="../ui/images/printer.png" height="40" width="40" style="margin: 15px auto 0px 12px; cursor: pointer;">
    <script>
        
    </script>
    <?php
    $tools = ob_get_clean();
    return $tools;
}
?>