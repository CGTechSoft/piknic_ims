<?php

function get_form_html($form_id, $date) {
    ob_start();
    ?>
    <div id="head_div" style="padding: 5px 0; background-color: #ECECEC;  color: #FFD00F;
         border-radius: 5px;margin-left: auto; text-align: center; ">
        MONTHLY REPORT OF 
        <input id="year_field" value="<?php 
            if($date == 0 ){ 
                $year = date('Y',time());
            }else {
                $year = split('/', $date)[0];
            }
            echo $year;
            ?>"  onchange="load_monthly_report()" style="width: 55px;" min="2015" max="2065" required />
        <select  id="month_field" onchange="load_monthly_report()" >
            <?php
            if($date == 0 ){ 
                $month = date('m',time());
            }else {
                $month = split('/', $date)[1];
            }
            ?>
            <option <?php if($month==1){echo ' selected ';} ?> value="1">JANUARY</option>
            <option <?php if($month==2){echo ' selected ';} ?> value="2">FEBRUARY</option>
            <option <?php if($month==3){echo ' selected ';} ?> value="3">MARCH</option>
            <option <?php if($month==4){echo ' selected ';} ?> value="4">APRIL</option>
            <option <?php if($month==5){echo ' selected ';} ?> value="5">MAY</option>
            <option <?php if($month==6){echo ' selected ';} ?> value="6">JUNE</option>
            <option <?php if($month==7){echo ' selected ';} ?> value="7">JULY</option>
            <option <?php if($month==8){echo ' selected ';} ?> value="8">AUGUST</option>
            <option <?php if($month==9){echo ' selected ';} ?> value="9">SEPTEMBER</option>
            <option <?php if($month==10){echo ' selected ';} ?> value="10">OCTOBER</option>
            <option <?php if($month==11){echo ' selected ';} ?> value="11">NOVEMBER</option>
            <option <?php if($month==12){echo ' selected ';} ?> value="121">DECEMBER</option>
        </select>
    </div>
    <div id="content_table" style="margin-top: 10px; background-color:transparent;padding-bottom: 30px;">
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
            <table id="table" style="border-collapse: collapse; width: 100%; 
                   background-color: #fff; border-radius: 10px;  color: #FFD00F;">
                <thead style="text-align: center;">
                    <tr>
                        <td style="width: 5%;">
                            #
                        </td>
                        <td>
                            DESCRIPTION
                        </td>
                        <td style="width: 15%;">
                            INCOME
                        </td>
                        <td style="width: 15%;">
                            EXPENCE
                        </td>
                        <td style="width: 15%;">
                            BALANCE
                        </td>
                    </tr>
                </thead>
                <tbody style="padding-left: 3px; text-align: center; ">
                    <?php
                    $balance = 0;
                    $income_total = 0;
                    $expence_total = 0;
                    $user = new user();
                    $user->id = $_SESSION['user_id'];
                    $user->getUser();
                    $i = 0;
                    if($year<2015 || $year>2065 || $month<1 || $month>12){
                        echo '<tr><td colspan="8"> In Valid Month Found </td></tr>';
                    } else{
                        //$grand_total = $grand_total + $sales_item->total;
                        ?>
                        <tr>
                            <td style="text-align: center;">
                                <?php echo ++$i; ?>
                            </td>
                            <?php 
                                $sale = new sales();
                                $vals = $sale->getOneMonthsSaleSummary($user->company_id, $month, $year);
                            ?>
                            <td style="text-align: left;">
                                Sales
                            </td>
                            <td>
                                <?php 
                                echo number_format($vals['amount'], 2, '.',''); 
                                $income_total = $income_total + $vals['amount'];
                                ?>
                            </td>
                            <td>
                            </td>                            
                            <td id="tax">
                                <?php 
                                $balance = $income_total - $expence_total; 
                                echo number_format($balance, 2, '.','');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">
                                <?php echo ++$i; ?>
                            </td>
                            <td style="text-align: left;">
                                Tax
                            </td>
                            <td>
                                
                            </td>
                            <td>
                                <?php 
                                echo number_format($vals['tax_amount'], 2, '.',''); 
                                $expence_total = $expence_total + $vals['tax_amount'];
                                ?>
                            </td>                            
                            <td id="tax">
                                <?php 
                                $balance = $income_total - $expence_total; 
                                echo number_format($balance, 2, '.','');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">
                                <?php echo ++$i; ?>
                            </td>
                            <?php 
                                $purchace = new purchaces();
                                $vals = $purchace->getOneMonthsPurchaceSummary($user->company_id, $month, $year);
                            ?>
                            <td style="text-align: left;">
                                Purchaces
                            </td>
                            <td>
                            </td>
                            <td>
                                <?php 
                                echo number_format($vals['amount'], 2, '.',''); 
                                $expence_total = $expence_total + $vals['amount'];
                                ?>
                            </td>                            
                            <td id="tax">
                                <?php 
                                $balance = $income_total - $expence_total; 
                                echo number_format($balance, 2, '.','');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">
                                <?php echo ++$i; ?>
                            </td>
                            <?php 
                                $expence = new expences();
                                $vals = $expence->getOneMonthsExpenceSummary($user->company_id, $month, $year);
                            ?>
                            <td style="text-align: left;">
                                Expences
                            </td>
                            <td>
                            </td>
                            <td>
                                <?php 
                                echo number_format($vals['amount'], 2, '.',''); 
                                $expence_total = $expence_total + $vals['amount'];
                                ?>
                            </td>                            
                            <td id="tax">
                                <?php 
                                $balance = $income_total - $expence_total; 
                                echo number_format($balance, 2, '.','');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">
                                <?php echo ++$i; ?>
                            </td>
                            <?php 
                                $bank_deposit = new bank_deposits();
                                $vals = $bank_deposit->getOneMonthsBankDepositsSummary($user->company_id, $month, $year);
                            ?>
                            <td style="text-align: left;">
                                Bank Deposit
                            </td>
                            <td>
                            </td>
                            <td>
                                <?php 
                                echo number_format($vals['amount'], 2, '.',''); 
                                $expence_total = $expence_total + $vals['amount'];
                                ?>
                            </td>                            
                            <td id="tax">
                                <?php 
                                $balance = $income_total - $expence_total; 
                                echo number_format($balance, 2, '.','');
                                ?>
                            </td>
                        </tr>
                        <?php
                }
                    ?>
                        <tr>
                            <td></td>
                            <td style="text-align: right;"> TOTAL </td>
                            <td><?php echo number_format($income_total, 2, '.',''); ?></td>
                            <td><?php echo number_format($expence_total, 2, '.',''); ?></td>
                            <td><?php echo number_format($balance, 2, '.',''); ?></td>
                        </tr>
                  </tbody>                               
            </table>
            <?php /* ?>
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
            <?php */ ?>
        </div>
    </div>
    <script type="text/javascript">
        function load_monthly_report(){
            var year = $('input#year_field').val();
            if(year<2015 || year>2065){
                alert('Enter a valid year !');
                return; 
            }
            var month = $('select#month_field').val();
            get_form(33,
                        function(html, tools) {
                             $('div#form-body').html(html);
                             $('div#content-body-action-tools').html(tools);
                        }, function(message) {
                             $('font#section_heading').empty();
                             $('div#form-body').empty();
                             alert(message);
                        },
                        year+'/'+month);
        }
        function on_print_clicked() {
            var year = $('input#year_field').val();
            var month = $('select#month_field option:selected').html();
            $('div#print_container_header')
                    .html('<font style="color:#FFD00F; font-size:20px; ">MONTHLY REPORT OF '+year+' '+month+'</font>');
            var html = $('div#content_table').html();
            $('div#print_container_body').html(html);  
            print();
            $('div#print_container_header').empty();
            $('div#print_container_body').empty();
            $('div#print_container_footer').empty();
        }
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