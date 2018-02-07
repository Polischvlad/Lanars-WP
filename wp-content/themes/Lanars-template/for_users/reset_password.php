<?php
$nonce = $_POST['nonce'];
if (!wp_verify_nonce($nonce, 'reset_password')) wp_send_json_error(array('message' => 'Данные присланные со сторонней страницы ', 'redirect' => false)); // проверим соль

$redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : ''; // пишим в переменные
$rp_key = isset($_POST['key']) ? $_POST['key'] : '';
$rp_login = isset($_POST['login']) ? $_POST['login'] : '';
$pass1 = isset($_POST['pass1']) ? $_POST['pass1'] : '';
$pass2 = isset($_POST['pass2']) ? $_POST['pass2'] : '';

if (!$rp_key || !$rp_login) { // параметры сброса пароля
    wp_send_json_error(array('message' => 'Параметры изменения пароля отсутствуют.', 'redirect' => false));
}

if (!$pass1 || !$pass2) { // поля с паролями
    wp_send_json_error(array('message' => 'Заполните поля с паролями.', 'redirect' => false));
}

// проверки ключа для сброса, если все ок вернется объект с пользователем, если нет, то объект с ошибкой -вроде как то так :)
$user = check_password_reset_key($rp_key, $rp_login); 

if (!$user || is_wp_error($user)) { 
    if ($user && $user->get_error_code() === 'expired_key' ) wp_send_json_error(array('message' => 'Ключ безопасности устарел, запросите смену пароля повторно.', 'redirect' => false)); 
    else wp_send_json_error(array('message' => 'Ключ безопасности не верный, запросите смену пароля повторно.', 'redirect' => false));
} 

// пароли совпадают ?
if ($pass1 != $pass2) wp_send_json_error(array('message' => 'Пароли не совпадают.', 'redirect' => false)); 

do_action('validate_password_reset', new WP_Error(), $user); // другие хуки

reset_password($user, $pass1);

wp_send_json_success(array('message' => 'Вы удачно изменили пароль.', 'redirect' => $redirect_to ? $redirect_to : '/')); 

?>