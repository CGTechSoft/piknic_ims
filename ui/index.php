<?php
//registering for class auto loading
spl_autoload_register(function($class_name) {
    $controller_root = $_SERVER['DOCUMENT_ROOT'] . '/piknik_ims/controller';
    if (file_exists($controller_root . '/php_classes/' . $class_name . '.php')) {
        $file_name = $controller_root . '/php_classes/' . $class_name . '.php';
        require_once $file_name;
    } else {
        throw new Exception("Class " . $class_name . " Not found");
    }
});
?>

<html>
    <head>
        <meta charset="utf-8">
        <title>Pik Nik</title>
        <link rel="stylesheet" type="text/css" media="screen" href="css/style_screen.css" />
        <link rel="stylesheet" type="text/css" media="print" href="css/style_print.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/jquery.datepick.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/style_pagination.css" />
        <script type="application/javascript" src="js/jquery-1.11.1.min.js"></script>
        <script type="application/javascript" src="js/jquery.datepick.js"></script>
        <script type="application/javascript" src="js/ajax.js" ></script>
        <script type="application/javascript" src="js/event_handler.js" ></script>
        <link rel="icon" type="image/png" href="../favicon.png" />
    </head>

    <body>
        <div id="spinner-wraper" class="centre">
            <div id="spinner" class="centre">
                <img src="images/spinner.gif"/>
            </div>
        </div>
        <div id="login-wraper" >
            <div id="login" class="centre">
                <div id="login-brand-name">
                    <font class="centre-text" id="login-brand-name">Pik-Nik Royalee</font>
                </div>

                <form id="login_form" method="post">
                    <input type="text" required id="user_name" placeholder="Username" class="login-input">
                    <br/>
                    <input type="password" required id="password" placeholder="Password" class="login-input">
                    <br/>
                    <input type="submit" value="Login" class="login-button button" />

                </form>
                <div id="forgot-passeword">
                    <font class="centre-text" style="color: #7D7D7D;">
                    Forgot your password? <a href="password_reset.html" id="reset_password">Click&nbsp;here</a>&nbsp;to&nbsp;reset&nbsp;it.
                    </font>
                </div>
            </div>
        </div>
        <div id="content">
            <div id="content-header">
                <div id="user_info" style="float:right;">
                    <font id="user_info_name" style="color: #FFD00F;"></font>
                    <a id="logout" style="cursor: pointer;"> Logout </a>
                </div>                
                <font class="centre-text" id="brand-name">Pik-Nik Royalee</font>
            </div>
            <div id="content-body">
                <div id="content-body-menu">
                    <!-- Loading menu item here dynamically -->
                </div>
                <div id="content-body-action-dummy-parent">
                    <div id="content-body-action">
                        <div id="content-body-action-tools">

                        </div>
                        <div style="margin-right: 80px; height: 100%;">
                            <div id="content-body-action-form">
                                <div id="form-header">
                                    <font id="section_heading"></font>
                                </div>
                                <div id="form-body">
                                    <!-- form loads here -->
                                    <?php 
//                                    include '../forms/1.php';
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer>
                Copyright &COPY; Royal Piknik Traders LLP <?php echo date('Y'); ?>
            </footer>
        </div>
        
        <div id="print_container">
            
            <div id="print_container_header"> 
                
            </div>
            
            <div id="print_container_body"> 
                
            </div>   
            
            <div id="print_container_footer"> 
                
            </div> 
        </div>
    </body>    
</html>
