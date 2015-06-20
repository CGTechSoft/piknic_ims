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
        $responce = array('status'=>'failed','error'=>'User does not exists','data'=> array());
    }  else {  
        $responce = array('status'=>'success','error'=>'','data'=> array('user'=>$user));
    }
}else{
    $responce = array('status'=>'failed','error'=>'Session expired','data'=> array());
}
echo json_encode($responce);