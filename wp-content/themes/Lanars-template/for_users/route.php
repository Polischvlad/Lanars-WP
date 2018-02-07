<?php

add_action('wp_print_scripts','include_scripts'); // повесим функцию на событие вывода скриптов
function include_scripts(){
	wp_enqueue_script('jquery'); //  скрипты jQuery
	wp_enqueue_script('jquery-form'); //  плагин jQuery forms
	wp_enqueue_script('for_users', get_template_directory_uri() . '/js/for_users.js', array('jquery-form'));
    wp_localize_script('jquery', 'ajax_var', // добавим объект с глобальными JS переменными
		array( 
			'url' => admin_url('admin-ajax.php'), // и сунем в него путь до AJAX обработчика
		)
	);
}

add_action('wp_ajax_nopriv_login_me', 'login_me'); // функцию на аякс запрос с параметром action=login_user
function login_me(){
	require_once dirname(__FILE__) . '/login.php'; 
}


add_action('wp_ajax_logout_me', 'logout_me'); 
function logout_me() { // logout
   require_once dirname(__FILE__) . '/logout.php';  // подключим нужный обработчик
}

add_action('wp_ajax_nopriv_register_me', 'register_me'); 
function register_me() { 
    require_once dirname(__FILE__) . '/register.php';  
}

add_action('wp_ajax_edit_profile', 'edit_profile'); 
function edit_profile(){ 
    require_once dirname(__FILE__) . '/edit_profile.php';
}

function set_html_content_type() { 
	return 'text/html';
}

add_action('wp_ajax_nopriv_lost_password', 'lost_password');  
function lost_password(){
    require_once dirname(__FILE__) . '/lost_password.php';  
}

add_action('wp_ajax_nopriv_reset_password_front', 'reset_password_front');
function reset_password_front(){ 
    require_once dirname(__FILE__) . '/reset_password.php'; //  файл с обработкой формы восстановления пароля 
}