//var user = null;
$(document).ready(function(e) {
    is_loged_in(function(user) {
//            $.user = user;
        $('font#user_info_name').html(user.name);
        load_content_page();
    }, function() {
        load_login_page();
    });

    $('a#logout').on('click', function() {
        logout(function() {
            load_login_page();
        }, function(message) {
            alert(message);
        });
    });

    $('form#login_form').on('submit', function(e) {
        e.preventDefault();
        var user_name = $('form input#user_name').val();
        var password = $('form input#password').val();
        login(user_name, password, function(user) {
//            $.user = user;
            $('font#user_info_name').html(user.name);
            load_content_page();
        }, function(message) {
            alert(message);
        });
    });

//    $('a#reset_password').on('click', function(e) {
//        e.preventDefault();
//        //TODO	
//    });

    function load_login_page() {
        console.log('loading log in page');
        $('div#spinner-wraper').css({'display': 'none'});
        $('div#content').css({'display': 'none'});
        $('div#login-wraper').css({'display': 'block'});
        $('body').css({'background-color': '#FFD00F'});
        $('font#section_heading').html('');
        $('div#content-body-menu').empty();
        $('div#form-body').empty();
    }

    function load_content_page() {
        console.log('loading content page');
        $('div#spinner-wraper').css({'display': 'none'});
        $('div#content').css({'display': 'block'});
        $('div#login-wraper').css({'display': 'none'});
        $('body').css({'background-color': '#ECECEC'});
//        if($.user.name != null){
//            $('font#user_info_name').html($.user.name);
//        }
        console.log("Loading menu items")
        load_menu_items();
    }
    function load_menu_items() {
        get_menu_items(function(menu_list) {
            $('div#content-body-menu').empty();
            $.each(menu_list.menu_list, function() {

                html = "<div class=\"menu-item\" id=\"" + this.menu_item_id + "\">"
                        + "<font id=\"menu-item-font\">" + this.menu_item_name + "</font>"
                        + "</div> ";
                $('div#content-body-menu').append(html);
            });
            setListenerForMenuItems();
        }, function(message) {
            alert(message);
        });
    }
    setListeners();
});


function setListeners() {
    setListenerForMenuItems();
}
function setListenerForMenuItems() {
    $("div.menu-item").on('click', function(e) {
        var heading = $(this).find('font#menu-item-font').html();
        $('div#form-body').empty();
        $('div#content-body-action-tools').empty();
        $('font#section_heading').html(heading);
        get_form($(this).attr('id'),
                function(html, tools) {
                    $('div#form-body').html(html);
                    $('div#content-body-action-tools').html(tools);
                }, function(message) {
            $('font#section_heading').empty();
            alert(message);
        });
    });
}
//function setListenerForActionForms() {
//    $('form.action_form').on('submit', function(e) {
//        e.preventDefault();
//        id = $(this).attr('id');
//        operation = $(this).attr('operation');
//        //console.log(id+operation);
//        if (operation == 'add') {
//            if (id == 11) {
//                var data = {
//                    form_id: id,
//                    item_name: $('form input#item_name').val(),
//                    item_code: $('form input#item_code').val(),
//                    mrp: $('form input#mrp').val(),
//                    purchace_rate: $('form input#purchace_rate').val()
//                }
//                add_form_data(data, function(message) {
//                    $('form input#item_name').val('');
//                    $('form input#item_code').val('');
//                    $('form input#mrp').val('');
//                    $('form input#purchace_rate').val('');
//                    alert(message);
//                }, function(message) {
//                    alert(message);
//                });
//
//            } else if (id == 7) {
//                $('tbody#items_table_body').find('#item').each(function() {
//                    consle.log($(this).html());
//                });
//            } else {
//                alert("Invalid form " + id + ' - ' + operation);
//            }
//        } else {
//            alert("Invalid Operation " + id + ' - ' + operation);
//        }
//    });
//}