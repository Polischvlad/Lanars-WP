<?php
$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : ''; 
if (!wp_verify_nonce($nonce, 'logout_me_nonce')) wp_send_json_error(array('message' => 'Данные присланные со сторонней страницы ', 'redirect' => false)); // проверяем
if (!is_user_logged_in()) wp_send_json_error(array('message' => 'Вы не авторизованы.', 'redirect' => false)); // не авторизован

wp_logout();

wp_send_json_success(array('message' => 'Вы вышли.', 'redirect' => false)); 
?>