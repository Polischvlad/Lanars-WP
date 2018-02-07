<?php
$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : ''; // строку безопасности
if (!wp_verify_nonce($nonce, 'login_me_nonce')) wp_send_json_error(array('message' => 'Данные присланные со сторонней страницы ', 'redirect' => false)); 

if (is_user_logged_in()) wp_send_json_error(array('message' => 'Вы уже авторизованы.', 'redirect' => false)); 

$log = isset($_POST['log']) ? $_POST['log'] : false; // получаем данные с формы
$pwd = isset($_POST['pwd']) ? $_POST['pwd'] : false;
$redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : false;
$rememberme = isset($_POST['rememberme']) ? $_POST['rememberme'] : false;

if (!$log) wp_send_json_error(array('message' => 'Поле логин или email не заполнено', 'redirect' => false)); 
if (!$pwd) wp_send_json_error(array('message' => 'Поле пароль не заполнено', 'redirect' => false));

$user = get_user_by( 'login', $log ); 
if (!$user) $user = get_user_by( 'email', $log ); 

if (!$user) wp_send_json_error(array('message' => 'Ошибочное логин/email или пароль.', 'redirect' => false)); 
if (get_user_meta( $user->ID, 'has_to_be_activated', true ) != false) wp_send_json_error(array('message' => 'Пользователь еще не активирован.', 'redirect' => false));

$log = $user->user_login; 

$creds = array( // массив с данными для логина
	'user_login' => $log,
	'user_password' => $pwd,
	'remember' => $rememberme
);
$user = wp_signon( $creds, false ); // пробуем
if (is_wp_error($user)) wp_send_json_error(array('message' => 'Ошибочное логин/email или пароль.', 'redirect' => false)); 
else wp_send_json_success(array('message' => 'Приветик '.$user->display_name.'. Загрузка ...', 'redirect' => $redirect_to));

?>