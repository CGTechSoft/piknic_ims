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


$user = new user();
if(isset($_SESSION['user_id']) and !empty($_SESSION['user_id'])){    
    $form_id = $_SESSION['user_id'];
    $user->id = $form_id;
    $user->getUser();
    if($user==NULL){        
        $responce = array('status'=>'failed','error'=>'Server error occured, Please inform your admin','data'=> array());
    }  else if($user->user_type_id==1){  
        
        $menu_list = array(array('menu_item_name'=>'Sales Invoice','menu_item_id'=>'1'),
            array('menu_item_name'=>'Sales Return','menu_item_id'=>'2'),
            array('menu_item_name'=>'Sales Report','menu_item_id'=>'3'),
            array('menu_item_name'=>'Day End Report','menu_item_id'=>'24'),
            array('menu_item_name'=>'Expence','menu_item_id'=>'5'),
            array('menu_item_name'=>'Customer','menu_item_id'=>'6'),
            array('menu_item_name'=>'Add to stock','menu_item_id'=>'16'),
            array('menu_item_name'=>'Stock Report','menu_item_id'=>'9'),
            array('menu_item_name'=>'Help','menu_item_id'=>'35'));
        
        $responce = array('status'=>'success','error'=>'','data'=> array('menu_list'=>$menu_list));
    }else if($user->user_type_id==2){  
        
        $menu_list = array(array('menu_item_name'=>'Purchace Invoice','menu_item_id'=>'7'),
            array('menu_item_name'=>'Edit Purchace','menu_item_id'=>'8'),
            array('menu_item_name'=>'Purchace Return','menu_item_id'=>'30'),
            array('menu_item_name'=>'Purchace Report','menu_item_id'=>'19'),
            array('menu_item_name'=>'Stock Report','menu_item_id'=>'9'),
            array('menu_item_name'=>'Help','menu_item_id'=>'36'));
        
        $responce = array('status'=>'success','error'=>'','data'=> array('menu_list'=>$menu_list));
    }else if($user->user_type_id==3){
        
        $menu_list = array(//array('menu_item_name'=>'User Management','menu_item_id'=>'13'),
            //array('menu_item_name'=>'Redeem','menu_item_id'=>'14'),
            array('menu_item_name'=>'Balance Sheet','menu_item_id'=>'33'),
            array('menu_item_name'=>'Day End Report','menu_item_id'=>'24'),
            array('menu_item_name'=>'Sales Report','menu_item_id'=>'15'),
            array('menu_item_name'=>'Bank Deposit','menu_item_id'=>'4'),
            array('menu_item_name'=>'Stock Report','menu_item_id'=>'20'),
            array('menu_item_name'=>'Help','menu_item_id'=>'37'));
        
        $responce = array('status'=>'success','error'=>'','data'=> array('menu_list'=>$menu_list));
    }else if($user->user_type_id==4){   
        
        $menu_list = array(array('menu_item_name'=>'Items','menu_item_id'=>'17'),
            //array('menu_item_name'=>'Company','menu_item_id'=>'12'),
            array('menu_item_name'=>'Vendors','menu_item_id'=>'18'),
            array('menu_item_name'=>'Banks','menu_item_id'=>'23'),
            array('menu_item_name'=>'Shops','menu_item_id'=>'26'),
            array('menu_item_name'=>'Users','menu_item_id'=>'28'),
            array('menu_item_name'=>'Help','menu_item_id'=>'34'));
         
        $responce = array('status'=>'success','error'=>'','data'=> array('menu_list'=>$menu_list));
    }else {  
        $responce = array('status'=>'failed','error'=>'Invalid user type','data'=> array());
    }
}else{
    $responce = array('status'=>'failed','error'=>'Session expired','data'=> array());
}
echo json_encode($responce);