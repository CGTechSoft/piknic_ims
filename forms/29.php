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
    <?php
    $user = new user();
    if($id != 0){
      $user->id = $id;
      $user->getUser();
    }
    ?>
    <div style="margin-top: 10px; background-color:transparent;padding-bottom: 30px;">
        <form action="#" method="post" class="action_form" operation="add" style="width:100%;" >
            <table style="width:100%;">
                <tr>
                    <td class="field_name">                    
                        <font>NAME</font>
                    </td>
                    <td class="field"> 
                        <div  class="parent">
                            <input value="<?php echo $user->name; ?>" type="text" id="name" required />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="field_name"> 
                        <font>SHOP</font>
                    </td>
                    <td class="field"> 
                        <div class="parent">
                            <select id="shop" required  <?php if($id != 0 ){echo 'disabled = "disabled"'; }?> >
                                <option value=""></option>
                                <?php
                                $company = new company();
                                $companies = $company->getCompanies();
                                if (is_array($companies) and count($companies) != 0) {
                                    foreach ($companies as $company){
                                        echo '<option';
                                        if($user->company_id === $company->id){
                                            echo ' selected ';
                                        }
                                        echo ' value="'.$company->id.'"';
                                        echo ' >';
                                        echo $company->company_name .' - '.$company->company_code;
                                        echo '</option>';
                                    }
                                }
                                //print_r($companies);
                                ?>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="field_name"> 
                        <font>TYPE</font>
                    </td>
                    <td class="field"> 
                        <div class="parent">
                            <select id="type" required ="" <?php if($id != 0 ){echo 'disabled = "disabled"'; }?> >
                                <option value=""></option>
                                <?php
                                $user_type = new user_type();
                                $user_types = $user_type->getUserTypes();
                                if (is_array($user_types) and count($user_types) != 0) {
                                    foreach ($user_types as $user_type){
                                        echo '<option';
                                        if($user->user_type_id === $user_type->id){
                                            echo ' selected ';
                                        }
                                        echo ' value="'.$user_type->id.'"';
                                        echo ' >';
                                        echo $user_type->user_type_name;
                                        echo '</option>';
                                    }
                                }
                                //print_r($companies);
                                ?>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="field_name"> 
                        <font>USER NAME</font>
                    </td>
                    <td class="field"> 
                        <div class="parent">
                            <input value="<?php echo $user->user_name; ?>" type="text" id="username" required />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="field_name"> 
                        <font>PASSWORD</font>
                    </td>
                    <td class="field"> 
                        <div class="parent">
                            <input type="password" value="" autocomplete="off" id="paswd" <?php if($id == 0){ echo 'required';} ?> />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="field_name"> 
                        <font>RE-ENTER PASSWORD</font>
                    </td>
                    <td class="field"> 
                        <div class="parent">
                            <input type="password" value="" autocomplete="off" id="re_password" <?php if($id == 0){ echo 'required';} ?> />
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
            var id = 29;
            var operation = $(this).attr('operation');
            if (operation == 'add') {
                var paswd = $('form input#paswd').val();
                var re_password = $('form input#re_password').val();
                if(paswd != re_password){
                    alert('Both passwords are not matching !');
                    $('form input#paswd').val('');
                    $('form input#re_password').val('');
                    $('form input#paswd').focus();
                    return; 
                }
                var data = {
                    form_id: id,
                    name: $('form input#name').val(),
                    username: $('form input#username').val(),
                    shop: $('form select#shop').val(),
                    type: $('form select#type').val(),
                    password: paswd
                }
                add_form_data(data, function(message) {
                    load_users_list();
                    alert(message);
                }, function(message) {
                    alert(message);
                });
            } else if (operation == 'update') {
                var user_id = $('form.action_form').attr('user_id');
                var paswd = $('form input#paswd').val();
                if(paswd !== ''){
                    var re_password = $('form input#re_password').val();
                    if(paswd != re_password){
                        alert('Both passwords are not matching !');
                        $('form input#paswd').val('');
                        $('form input#re_password').val('');
                        $('form input#paswd').focus();
                        return; 
                    }
                }
                var data = {
                    form_id: id,
                    user_id: user_id,
                    name: $('form input#name').val(),
                    username: $('form input#username').val(),
                    password: paswd
                }
                update_form_data(data, function(message) {
                    load_users_list();
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
    <img onclick="load_users_list()" src="../ui/images/list_icon.png" height="40" width="40" style="margin: 15px auto 0px 12px; cursor: pointer;">
    <script>
        function load_users_list(){
            get_form(28,
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