<?php

function get_form_html($form_id, $date) {
    ob_start();
    ?>
    <div id="head_div" style="padding: 5px 0; background-color: #ECECEC;  color: #21ACD7;
         border-radius: 5px;margin-left: auto; text-align: center; ">
        BALANCE SHEET OF
        <input id="date_field" value="<?php
        if ($date == 0) {
            $date = date('d/m/Y', time());
        }
        echo $date;
        ?>" />
    </div>
    <div id="sales_items_table" style="margin-top: 10px; background-color:transparent;padding-bottom: 30px;">
        <style>
            div#purchace_items td{
                border: 1px solid #21ACD7;
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
        <div id="purchace_items" style="width: 100%; padding: 10px 0; color: #21ACD7;">           
            <table id="sales_items_table" style="border-collapse: collapse; width: 100%; 
                   background-color: #fff; border-radius: 10px;  color: #21ACD7;">
                <thead style="text-align: center;">
                    <tr>
                        <td style="width: 5%;">
                            #
                        </td>
                        <td>
                            SHOP
                        </td>
                        <td style="width: 18%;">
                            INCOME (SALES)
                        </td>
                        <td style="width: 18%;">
                            EXPENSE (PURCHASE) 
                        </td>
                        <td style="width: 15%;">
                            PROFIT 
                        </td>
                    </tr>
                </thead>
                <tbody style="padding-left: 3px; text-align: center; ">
                    <?php
                    $total_income = 0;
                    $total_expense = 0;
//                    $sale_item = new sales_items();
//                    $user = new user();
//                    $user->id = $_SESSION['user_id'];
//                    $user->getUser();
                    $date = str_replace('/', '-', $date);
                    $date = date('Y-m-d', strtotime($date));
                    $shop = new company();
                    $shops = $shop->getCompanies();
                    $i = 0;
                    if (is_array($shops) and count($shops)) {
                        foreach ($shops as $shop) {
                            $sale = new sales();
                            $sale_income_for_this_shop = $sale->getOneDaySaleIncome($shop->id, $date);
                            $total_income += $sale_income_for_this_shop['amount'];                            
                            
                            $purchace_expence_for_this_shop = $sale->getOneDaysSaleExpence($shop->id, $date);
                            $total_expense += $purchace_expence_for_this_shop;
                            ?>
                            <tr>
                                <td style="text-align: center;">
                                    <?php echo ++$i; ?>
                                </td>
                                <td style="text-align: left;">
                                    <?php echo $shop->company_name. ' - ' .$shop->company_code; ?>
                                </td>
                                <td>
                                    <?php echo number_format($sale_income_for_this_shop['amount'], 2, '.', ''); ?>
                                </td>

                                <td id="tax">
                                    <?php echo number_format($purchace_expence_for_this_shop, 2, '.', ''); ?>
                                </td>

                                <td id="tax">
                                    <?php echo number_format(($sale_income_for_this_shop['amount'] - $purchace_expence_for_this_shop), 2, '.', ''); ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo '<tr><td colspan="8"> No Shop Found </td></tr>';
                    }
                    ?>
                    <tr>
                        <td></td>
                        <td style="text-align: right;"> TOTAL </td>
                        <td><?php echo number_format($total_income, 2, '.', ''); ?></td>
                        <td><?php echo number_format($total_expense, 2, '.', ''); ?></td>
                        <td><?php echo number_format(($total_income - $total_expense), 2, '.', ''); ?></td>
                    </tr>
                </tbody>                               
            </table>
        </div>
    </div>
    <script type="text/javascript">
        function load_day_end_report() {
            var date = $('input#date_field').val();
            get_form(40,
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
            var header = '<font style="color:#21ACD7; font-size:20px; ">Nutiez</font>'
                            +'<br/><br/>'
                            +'<font style="color:#21ACD7; font-size:20px; ">BALANCE SHEET OF ' + date + '</font>';
            $('div#print_container_header')
                    .html(header);
            var html = $('div#sales_items_table').html();
            $('div#print_container_body').html(html);
            print();
            $('div#print_container_header').empty();
            $('div#print_container_body').empty();
            $('div#print_container_footer').empty();
        }
        function setup_datepicker() {
            $('input#date_field').datepick({
                minDate: '26/04/2015',
                dateFormat: 'dd/mm/yyyy',
                maxDate: '0',
                onSelect: function() {
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

function get_form_tools_html($id) {
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