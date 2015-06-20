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

session_start();
if (isset($_SESSION['user_id']) and !empty($_SESSION['user_id'])) {
    if (isset($_POST['menu_item_id']) and !empty($_POST['menu_item_id'])) {
        $form_id = $_POST['menu_item_id'];
        $id = $_POST['id'];
        if(isset($_POST['page']) and !empty($_POST['page'])){
            $page = $_POST['page'];
        }else{
            $page = 1;
        }
        //pagination constants
        $limit = 15;
        $adjacents = 5;
        //form file name
        $file_name = "../../forms/" . $form_id . ".php";
        if (file_exists($file_name)) {
            include $file_name;
            $data = get_form_html($form_id, $id, $page, $limit, $adjacents);
            $tools = get_form_tools_html($form_id);
            $responce = array('status' => 'success', 'error' => '', 'data' => array('form' => $data, 'tools' => $tools));
        } else {
            $responce = array('status' => 'failed',
                'error' => 'Some server error occured, File not exists file name ' . $file_name, 'data' => array());
        }
    } else {
        $responce = array('status' => 'failed', 'error' => 'Data missing', 'data' => array());
    }
} else {
    $responce = array('status' => 'failed', 'error' => 'Session expired', 'data' => array());
}
echo json_encode($responce);

function pagination($limit, $adjacents, $rows, $page) {
    $pagination = '';
    if ($page == 0)
        $page = 1;     //if no page var is given, default to 1.
    $prev = $page - 1;       //previous page is page - 1
    $next = $page + 1;       //next page is page + 1
    $prev_ = '';
    $first = '';
    $lastpage = ceil($rows / $limit);
    $next_ = '';
    $last = '';
    if ($lastpage > 1) {

        //previous button
        if ($page > 1)
            $prev_.= "<span class='page-numbers' page=\"$prev\">previous</span>";
        else {
            //$pagination.= "<span class=\"disabled\">previous</span>";	
        }

        //pages	
        if ($lastpage < 5 + ($adjacents * 2)) { //not enough pages to bother breaking it up
            $first = '';
            for ($counter = 1; $counter <= $lastpage; $counter++) {
                if ($counter == $page)
                    $pagination.= "<span class=\"current\">$counter</span>";
                else
                    $pagination.= "<span class='page-numbers' page=\"$counter\">$counter</span>";
            }
            $last = '';
        }
        elseif ($lastpage > 3 + ($adjacents * 2)) { //enough pages to hide some
            //close to beginning; only hide later pages
            $first = '';
            if ($page < 1 + ($adjacents * 2)) {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                    if ($counter == $page)
                        $pagination.= "<span class=\"current\">$counter</span>";
                    else
                        $pagination.= "<span class='page-numbers' page=\"$counter\">$counter</span>";
                }
                $last.= "<span class='page-numbers' page=\"$lastpage\">Last</span>";
            }

            //in middle; hide some front and some back
            elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                $first.= "<span class='page-numbers' page=\"1\">First</span>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<span class=\"current\">$counter</span>";
                    else
                        $pagination.= "<span class='page-numbers' page=\"$counter\">$counter</span>";
                }
                $last.= "<span class='page-numbers' page=\"$lastpage\">Last</span>";
            }
            //close to end; only hide early pages
            else {
                $first.= "<span class='page-numbers' page=\"1\">First</span>";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<span class=\"current\">$counter</span>";
                    else
                        $pagination.= "<span class='page-numbers' page=\"$counter\">$counter</span>";
                }
                $last = '';
            }
        }
        if ($page < $counter - 1)
            $next_.= "<span class='page-numbers' page=\"$next\">next</span>";
        else {
            //$pagination.= "<span class=\"disabled\">next</span>";
        }
        $pagination = "<div class=\"pagination\">" . $first . $prev_ . $pagination . $next_ . $last;
        //next button

        $pagination.= "</div>\n";
    }

    return $pagination;
}
