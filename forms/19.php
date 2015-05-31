<?php

function get_form_html($form_id, $id, $page, $limit, $adjacents) {
    ob_start();
    $purchace_obj = new purchaces();
    $user = new user();
    $user->id = $_SESSION['user_id'];
    $user->getUser();
    $count = $purchace_obj->getPurchacesCount($user->company_id);
    if ($page == 1) {
        $start = 0;
        $head_message = "LAST $limit PURCHACES";
    } else {
        $start = ($page - 1) * $limit;
        $head_message = "PURCHACE $start TO ".($start + $limit);
    }
?>
    <div id="head_div" style="padding: 10px 0; background-color: #ECECEC;  color: #FFD00F;
         border-radius: 5px;margin-left: auto; text-align: center;overflow-x: auto; ">
         <?php echo $head_message; ?>
    </div>
    <div style="margin-top: 10px; background-color:transparent;padding-bottom: 30px;">
        <style>
            div#purchace_items td, div#purchace_items th{
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
        <input type="text" id="search" placeholder="Enter Search Key here..." style="width: 100%; margin-left: 0px;" onkeyup="search()" />
        <style>
            img#search{
                position: relative;
                height: 20px;
                width: 20px;
                float: right;
                top: -29px;
                right: 10px;
            }
        </style>
        <img id="search" src="../ui/images/search.png" onclick="search()" />
        
        <div style="padding: 10px 0; background-color: transparent; 
             border-radius: 5px;margin-left: auto; text-align: center;overflow-x: auto; ">
             <?php echo pagination($limit, $adjacents, $count, $page); ?>
        </div>
        
        <div id="purchace_items" style="width: 100%; padding: 10px 0; color: #FFD00F;">           
            <table id="items_table" style="border-collapse: collapse; width: 100%; 
                   background-color: #fff; border-radius: 10px;  color: #FFD00F;">
                <thead style="text-align: center;">
                    <tr>
                        <th>
                            #
                        </th>
                        <th>
                            ID
                        </th>
                        <th>
                            BILL NO.
                        </th>
                        <th>
                            DATE
                        </th>
                        <th style="">
                            PURCHACED FROM
                        </th>
                        <th style="">
                            PURCHACED BY
                        </th>
                        <th>
                            AMOUNT
                        </th>
                        <th style="">
                            STOCKED
                        </th>
                        <th style="">

                        </th>
                    </tr>
                </thead>
                <tbody style="padding-left: 3px; text-align: center; ">
                    <?php
                    $purchaces = $purchace_obj->getPurchacesDESC($user->company_id, $start, $limit);
                    $i = $start;
                    if ($purchaces == NULL || sizeof($purchaces) == 0) {
                        echo '<tr><td colspan="8"> No Purchace Found </td></tr>';
                    } else {
                        foreach ($purchaces as $purchace) {
                            ?>
                            <tr id="<?php echo $purchace->id; ?>" onclick="select_row(this)" status="not_selected">
                                <td style="text-align: center;">
                                    <?php echo ++$i; ?>
                                </td>
                                <td>
                                    <?php echo $purchace->id; ?>
                                </td>
                                <td id="bill_number" >
                                    <?php echo $purchace->bill_number; ?>
                                </td>
                                <?php
                                $date = date('d/m/Y', (strtotime($purchace->created_at) + (5.5 * 60 * 60)));
                                $time = date('h:m A', (strtotime($purchace->created_at) + (5.5 * 60 * 60)));
                                ?>
                                <td id="date" date="<?php echo $date ?>">
                                    <?php echo $date . ' - ' . $time; ?>
                                </td>
                                <?php
                                $vendor = new wendors();
                                $vendor->id = $purchace->wendor_id;
                                $vendor->getWendor();
                                ?>
                                <td id="vendor" vendor_address="<?php echo $vendor->contact_address; ?>"><?php echo $vendor->wendor_name; ?></td>
                                <td>
                                    <?php
                                    $p_manager = new user();
                                    $p_manager->id = $purchace->purchace_manager_id;
                                    $p_manager->getUser();
                                    echo $p_manager->name;
                                    ?>
                                </td>
                                <td id="amount"><?php echo $purchace->amount; ?></td>
                                <td>
                                    <?php
                                    if ($purchace->stocked) {
                                        echo "YES";
                                    } else {
                                        echo 'NO';
                                    }
                                    ?>
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
                                    <table id="purchace_items" style="border-collapse: collapse; background-color: #c0effd; width: 80%; color: #FFD00F; float: right;">
                                        <thead>
                                            <tr>
                                                <th>
                                                    ITEM
                                                </th>
                                                <th>
                                                    QUANTITY
                                                </th>
                                                <th>
                                                    PURCHACE RATE
                                                </th>
                                                <th>
                                                    MRP
                                                </th>
                                                <th>
                                                    TOTAL
                                                </th>
                                            </tr>
                                        </thead> 
                                        <tbody> 
                                            <?php
                                            foreach ($purchace->getPurchaceItems() as $p_item) {
                                                ?>
                                                <tr>
                                                    <td id="item_name"><?php
                                $item = new item();
                                $item->id = $p_item->item_id;
                                $item->getItem();
                                echo $item->item_name . ' ( ' . $item->item_code . ' )';
                                                ?></td>
                                                    <td id="quantity"><?php echo $p_item->quantity; ?></td>
                                                    <?php
                                                    $tax = new tax_category();
                                                    $tax->id = $item->tax_category_id;
                                                    $tax->getTaxCategory();
                                                    ?>
                                                    <td id="rate" tax_rate="<?php echo $tax->tax_percentage; ?>"><?php
                                    echo $p_item->rate;
                                                    ?></td>
                                                    <td><?php echo $item->mrp; ?></td>
                                                    <td>
                                                        <?php echo ($p_item->quantity * $p_item->rate); ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>        
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
        <div style="padding: 10px 0; background-color: transparent; 
             border-radius: 5px;margin-left: auto; text-align: center;overflow-x: auto; ">
             <?php echo pagination($limit, $adjacents, $count, $page); ?>
        </div>
    </div>
    <script type="text/javascript">
        function toggle_items_visibility(down_button) {
            var row = $(down_button).closest('tr');
            row.next('tr').fadeToggle();
            row.find('img#toggle_button').toggle();

        }
        function select_row(row) {
            var j_row = $(row);
            if (j_row.attr('status') == 'selected') {
                $('table#items_table tr').attr('status', 'not_selected');
                $('table#items_table tr').css('background-color', '#FFF');
                $('img#print').css('display', 'none');
                $('img#print_fade').css('display', 'block');
                $('img#delete').css('display', 'none');
                $('img#delete_fade').css('display', 'block');
            } else {
                $('table#items_table tr').attr('status', 'not_selected');
                $('table#items_table tr').css('background-color', '#FFF');
                j_row.attr('status', 'selected');
                j_row.css('background-color', '#C0EFFD');
                $('img#print').css('display', 'block');
                $('img#print_fade').css('display', 'none');
                $('img#delete').css('display', 'block');
                $('img#delete_fade').css('display', 'none');
            }
        }

        function on_print_clicked() {
            var selected_row = $('tr[status="selected"]');
            var items_table_rows = selected_row.next('tr#purchace_item').find('table#purchace_items').find('tbody').children();
            var items = new Array();
            var i = 0;
            var tax_amount = 0;
            items_table_rows.each(function() {
                var item_row = $(this);
                var item_name = item_row.find('td#item_name').html();
                var quantity = item_row.find('td#quantity').html();
                var rate = item_row.find('td#rate').html();
                var tax_rate = item_row.find('td#rate').attr('tax_rate');
                var total = parseFloat(rate) * parseFloat(quantity);
                total = total.toFixed(2);
                var tax = (total * tax_rate) / (100);
                tax_amount = parseFloat(tax_amount) + parseFloat(tax);
                var item = {
                    item_name: item_name,
                    quantity: quantity,
                    rate: rate,
                    tax: tax,
                    tax_rate: tax_rate,
                    total: total
                }
                items[i++] = item;
            });
            tax_amount = tax_amount.toFixed(2);
            var vendor_name = selected_row.find('td#vendor').html();
            var vendor_address = selected_row.find('td#vendor').attr('vendor_address');
            var amount = selected_row.find('td#amount').html();
            amount = parseFloat(amount).toFixed(2);
            var id = selected_row.attr('id');
            var grand_total = (parseFloat(amount) + parseFloat(tax_amount));
            grand_total = grand_total.toFixed(2);
            var rounded_grand_total = Math.round(grand_total);
            rounded_grand_total = rounded_grand_total.toFixed(2);
            var round_off = grand_total - rounded_grand_total;
            round_off = round_off.toFixed(2);
            var bill_number = selected_row.find('td#bill_number').html();
            var date = selected_row.find('td#date').attr('date');
            var data = {
                id: id,
                vendor_name: vendor_name,
                vendor_address: vendor_address,
                amount: amount,
                date: date,
                less_discount: '0.00',
                tax_amount: tax_amount,
                grand_total: grand_total,
                rounded_grand_total: rounded_grand_total,
                round_off: round_off,
                bill_number: bill_number,
                items: items
            }
            print_purchace_invoice(data);
        }

        function print_purchace_invoice(data) {
            var html = '';
            html = '<font style="font-size:30px;">ROYALE PIKNIK TRADERS LLP</font><br/>'
                    + '<font>29/861, PARAYANCHERI, CALICUT - 673006</font><br/>'
                    + '<font>Tin No. 32110844692 </font><br/>'
                    + '<font>PURCHACE BILL</font><br/><br/>';
            $('div#print_container_header').html(html);
            html = '';
            //var d = new Date();
            //var date = d.getDate()+"/"+(parseInt(d.getMonth())+parseInt(1))+"/"+d.getFullYear();
            html = html + "<div<!-- style=\"padding:10px 0;\"><table style=\"float:right;\">"
                    + "<tr><td>Date</td><td>:</td><td>" + data.date + "</td></tr></table>";

            html = html + "<table>"
                    + "<tr><td>Bill No. </td><td>:</td><td>" + data.bill_number/*id*/ + "</td></tr>"
                    + "<tr><td>Name </td><td>:</td><td>" + data.vendor_name + "</td></tr>"
                    + "<tr><td></td><td>:</td><td>" + data.vendor_address + "</td></tr></table></div>";

            html = html + "<div style=\"border-top:1px dashed #000; margin:10px auto 0 auto;padding:0 0 10px 0;\"><table style=\"width:100%;\"><tr style=\"border-bottom: 1px dashed #000; border-top: 1px dashed #000;\">"
                    + "<td style=\"width:5%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px;\">SNo</td>"
                    + "<td style=\"width:30%; border-bottom:1px dashed #000; padding-bottom:5px; margin-bottom:5px;\">COMMODITY</td>"
                    + "<td style=\"width:20%; border-bottom:1px dashed #000; border-left:1px dashed #000; padding-bottom:5px; margin-bottom:5px; text-align:center;\">SCH/HSN NO</td>"
                    + "<td style=\"width:10%; border-bottom:1px dashed #000; border-left:1px dashed #000; padding-bottom:5px; margin-bottom:5px; text-align:center;\">RATE OF TAX</td>"
                    + "<td style=\"width:10%; border-bottom:1px dashed #000; border-left:1px dashed #000; padding-bottom:5px; margin-bottom:5px; text-align:center;\">UNIT PRICE</td>"
                    + "<td style=\"width:10%; border-bottom:1px dashed #000; border-left:1px dashed #000; padding-bottom:5px; margin-bottom:5px; text-align:center;\">QTY</td>"
                    + "<td style=\"width:15%; border-bottom:1px dashed #000; border-left:1px dashed #000; padding-bottom:5px; margin-bottom:5px; text-align:center;\">PURCHACE VALUE</td></tr>";
            var i = 1;
            for (var key in data.items) {
                var item = data.items[key];
                html = html + "<tr><td>" + i++ + "</td>"
                        + "<td colspan=\"2\">" + item.item_name + "</td>"
                        + "<td style=\"text-align:center;\">" + item.tax_rate + "</td>"
                        + "<td style=\"text-align:center;\">" + item.rate + "</td>"
                        + "<td style=\"text-align:center;\">" + item.quantity + "</td>"
                        //+"<td style=\"text-align:center;\">" + (parseFloat(item.total) - parseFloat(item.tax)) + "</td>"
                        //+"<td style=\"text-align:center;\">" + item.tax + "</td>"
                        + "<td style=\"text-align:right;\">" + item.total + "</td>"
                        + "</tr>";
            }
            html = html + "</table></div>";
            html = html + "<div style=\"border-top:1px dashed #000; padding:10px 0;\"><table style=\"margin-left: auto;\">";
            html = html + "<tr><td>Amount</td><td style=\"margin:0 30px;\"></td><td style=\"text-align:right;\">" + data.amount + "</td></tr>";
            html = html + "<tr><td>Less Discount</td><td style=\"margin:0 30px;\"></td><td style=\"text-align:right;\">" + data.less_discount + "</td></tr>";
            html = html + "<tr><td>VAT</td><td style=\"margin:0 30px;\"></td><td style=\"text-align:right;\">" + data.tax_amount + "</td></tr>";
            html = html + "<tr><td>Round off</td><td style=\"margin:0 30px;\"></td><td style=\"text-align:right;\">" + data.round_off + "</td></tr>";
            html = html + "<tr style=\"font-size:18px;\"><td><b>GRAND TOTAL</b></td><td style=\"margin:0 30px;\"></td><td style=\"text-align:right;\"><b>" + data.rounded_grand_total + "</b></td></tr>";
            html = html + "</table></div>";
    //                console.log("Creating bill : " + html);
            $('div#print_container_body').html(html);
            html = '';
            html = '<div style="width:100%;border-bottom:1px dashed #000;text-align:left;"><font>Rupees: ' + toWords(parseInt(data.rounded_grand_total)) + ' Only. </font></div>';
            $('div#print_container_footer').html(html);
            print();
            $('div#print_container_header').empty();
            $('div#print_container_body').empty();
            $('div#print_container_footer').empty();
        }
        // American Numbering System
        var th = ['', 'Thousand', 'Million', 'Billion', 'Trillion'];

        var dg = ['Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];

        var tn = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];

        var tw = ['Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        function toWords(s) {
            s = s.toString();
            s = s.replace(/[\, ]/g, '');
            if (s != parseFloat(s))
                return 'not a number';
            var x = s.indexOf('.');
            if (x == -1)
                x = s.length;
            if (x > 15)
                return 'too big';
            var n = s.split('');
            var str = '';
            var sk = 0;
            for (var i = 0; i < x; i++) {
                if ((x - i) % 3 == 2) {
                    if (n[i] == '1') {
                        str += tn[Number(n[i + 1])] + ' ';
                        i++;
                        sk = 1;
                    } else if (n[i] != 0) {
                        str += tw[n[i] - 2] + ' ';
                        sk = 1;
                    }
                } else if (n[i] != 0) {
                    str += dg[n[i]] + ' ';
                    if ((x - i) % 3 == 0)
                        str += 'hundred ';
                    sk = 1;
                }
                if ((x - i) % 3 == 1) {
                    if (sk)
                        str += th[(x - i - 1) / 3] + ' ';
                    sk = 0;
                }
            }
            if (x != s.length) {
                var y = s.length;
                str += 'point ';
                for (var i = x + 1; i < y; i++)
                    str += dg[n[i]] + ' ';
            }
            return str.replace(/\s+/g, ' ');

        }

        function search() {
            var search_key = $('input#search').val();
            if (search_key !== '') {
                console.log("search key " + search_key);
                searchTable(search_key);
            } else {
                $('#items_table tr').show();
                $('#items_table tr#purchace_item').hide();
            }
        }

        function searchTable(inputVal) {
            var table = $('#items_table');
            table.find('tr').each(function(index, row)
            {
                if ($(row).attr('id') !== 'purchace_item') {
                    var allCells = $(row).find('td');
                    if (allCells.length > 0)
                    {
                        var found = false;
                        allCells.each(function(index, td)
                        {
                            var regExp = new RegExp(inputVal, 'i');
                            if (regExp.test($(td).text()))
                            {
                                found = true;
                                return false;
                            }
                        });
                        if (found == true)
                            $(row).show();
                        else
                            $(row).hide();
                    }

                    var items_row = $(row).next('tr');
                    var allCells = items_row.find('td');
                    if (allCells.length > 0)
                    {
                        var found = false;
                        allCells.each(function(index, td)
                        {
                            var regExp = new RegExp(inputVal, 'i');
                            if (regExp.test($(td).text()))
                            {
                                found = true;
                                return false;
                            }
                        });
                        if (found == true) {
                            $(row).show();
                            items_row.show();
                        } else {
                            items_row.hide();
                        }
                    }

                }
            });
        }

        function on_delete_clicked() {
            var selected_row = $('tr[status="selected"]');
            var id = selected_row.attr('id');
            if (confirm('Are you sure you want to delete PURCHACE-' + id + ' ?')) {
                var data = {
                    form_id: 19,
                    purchace_id: id
                }
                delete_form_data(data, function(message) {
                    get_form(19,
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
                get_form(19,
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

function get_form_tools_html($id) {
    ob_start();
    ?>    
    <img id="print_fade" src="../ui/images/printer_fade.png" height="40" width="40" style="margin: 15px auto 0px 12px;">
    <img onclick="on_print_clicked()" id="print" onclick="" src="../ui/images/printer.png" height="40" width="40" style="margin: 15px auto 0px 12px; cursor: pointer; display: none;">
    <img id="delete_fade" src="../ui/images/delete_fade.png" height="40" width="40" style="margin: 15px auto 0px 12px;">
    <img onclick="on_delete_clicked()" id="delete" src="../ui/images/delete.png" height="40" width="40" style="margin: 15px auto 0px 12px; cursor: pointer; display: none;">
    <script>

    </script>
    <?php
    $tools = ob_get_clean();
    return $tools;
}
?>