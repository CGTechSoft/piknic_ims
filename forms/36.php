<?php

function get_form_html($form_id, $id) {
    ob_start();
    ?>
    <div id="head_div" style="padding: 5px 0; background-color: #ECECEC;  color: #21ACD7;
         border-radius: 5px;margin-left: auto; text-align: center; display: none; ">
    </div>
    <div style="margin-top: 10px; background-color:transparent;padding-bottom: 30px; 
         color: #fff; text-align: center; ">
        Will be avail soon :)
    </div>
    
    <?php
    $form = ob_get_clean();
    return $form;
}


function get_form_tools_html($id){
    $tools = "";
    return $tools;
}
?>