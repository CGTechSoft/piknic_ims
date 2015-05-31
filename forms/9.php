<?php

function get_form_html($form_id, $id) {
    ob_start();
    ?>
    <div id="head_div" style="padding: 5px 0; background-color: #ECECEC;  color: #FFD00F;
         border-radius: 5px;margin-left: auto; text-align: center; ">
        <?php
        $user = new user();
        $user->id = $_SESSION['user_id'];
        $user->getUser();
        $company = new company();
        $company->id = $user->company_id;
        $company->getCompany();
        echo "STOCK REPORT OF $company->company_name - $company->company_code";  
        ?>
    </div>
    <div style="margin-top: 10px; background-color:transparent;padding-bottom: 30px;">
        <style>
            div#purchace_items td{
                border: 1px solid #FFD00F;
            }
            div#purchace_items tbody td{
                padding: 5px 0 5px 5px;
            }
            div#purchace_items th{
                border: 1px solid #FFD00F;
            }
            div#purchace_items tbody th{
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
        <div id="purchace_items" style="width: 100%; padding: 10px 0; color: #FFD00F;">           
            <table id="items_table" style="border-collapse: collapse; width: 100%; 
                   background-color: #fff; border-radius: 10px;  color: #FFD00F;">
                <thead style="text-align: center;">
                    <tr>
                        <th>
                            #
                        </th>
                        <th>
                            ITEM
                        </th>
                        <th>
                            STOCK COUNT
                        </th>
                        <th style="">
                            SELLING PRIZE
                        </th>
                        <th style="">
                            TAX
                        </th>
                    </tr>
                </thead>
                <tbody style="padding-left: 3px; text-align: center; ">
                    <?php
                    $inventry = new inventry();
                    $inventries = $inventry->getInventryForSpecificCompany($user->company_id);
                    $i = 0;
                    if($inventries==NULL || sizeof($inventries)==0){
                        echo '<tr><td colspan="8"> No Purchace Found </td></tr>';
                    } else{
                    foreach ($inventries as $inventry) {
                        ?>
                        <tr id="<?php echo $inventry->id; ?>" >
                            <td style="text-align: center;">
                                <?php echo ++$i; ?>
                            </td>
                            <td>
                                <?php
                                $item = new item();
                                $item->id = $inventry->item_id;
                                $item->getItem();
                                echo $item->item_name.' - '.$item->item_code ;
                                ?>
                            </td>
                            <td>
                                <?php echo number_format($inventry->in_stock_count, 3, '.',''); ?>
                            </td>
                            <td>
                                <?php echo number_format($inventry->selling_prize, 2, '.',''); ?>
                            </td>
                            <td>
                                <?php
                                $tax = new tax_category();
                                $tax->id = $inventry->tax_category_id;
                                $tax->getTaxCategory();
                                echo $tax->tax_category_name;
                                ?>
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
        function search(){
            var search_key = $('input#search').val();
            if(search_key !== ''){                
                console.log("search key "+ search_key);
                searchTable(search_key);
            }else{
                $('#items_table tr').show();
            }
        }
        function searchTable(inputVal)
        {
                var table = $('#items_table');
                table.find('tr').each(function(index, row)
                {
                        var allCells = $(row).find('td');
                        if(allCells.length > 0)
                        {
                                var found = false;
                                allCells.each(function(index, td)
                                {
                                        var regExp = new RegExp(inputVal, 'i');
                                        if(regExp.test($(td).text()))
                                        {
                                                found = true;
                                                return false;
                                        }
                                });
                                if(found == true)$(row).show();else $(row).hide();
                        }
                });
        }
    </script>

    <?php
    $form = ob_get_clean();
    return $form;
}

function get_form_tools_html($id){
    ob_start();
    $tools = ob_get_clean();
    return $tools;
}
?>