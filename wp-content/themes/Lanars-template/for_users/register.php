<?php
$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : ''; 
if (!wp_verify_nonce($nonce, 'register_me_nonce')) wp_send_json_error(array('message' => 'Данные присланные со сторонней страницы ', 'redirect' => false)); 

if (is_user_logged_in()) wp_send_json_error(array('message' => 'Вы уже авторизованы!', 'redirect' => false)); 

if (!get_option('users_can_register')) wp_send_json_error(array('message' => 'Регистрация пользователей временно недоступна.', 'redirect' => false)); // если регистрацию выключат в админке - то же не будем ничего делать


$user_login = isset($_POST['user_login']) ? $_POST['user_login'] : '';
$user_email = isset($_POST['user_email']) ? $_POST['user_email'] : '';
$pass1 = isset($_POST['pass1']) ? $_POST['pass1'] : '';
$pass2 = isset($_POST['pass2']) ? $_POST['pass2'] : '';
$first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
$last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
$phone = isset($_POST['phone']) ? $_POST['phone'] : '';
$phone = isset($_POST['adress']) ? $_POST['adress'] : '';

$redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : false;

if (!$user_email) wp_send_json_error(array('message' => 'Email - обязательное поле.', 'redirect' => false));
if (!$gender) wp_send_json_error(array('message' => 'пол - обязательное поле.', 'redirect' => false));
if (!$phone) wp_send_json_error(array('message' => 'телефон - обязательное поле.', 'redirect' => false));
if (!preg_match("|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i", $user_email)) wp_send_json_error(array('message' => 'Ошибочный формат email', 'redirect' => false));
if (!$user_login) wp_send_json_error(array('message' => 'Логин - обязательное поле.', 'redirect' => false));
if (!$pass1) wp_send_json_error(array('message' => 'Пароль - обязательное поле.', 'redirect' => false));
if (!$pass2) wp_send_json_error(array('message' => 'Повторите пароль', 'redirect' => false));

// все ли ок с паролями
if ($pass1 != $pass2) wp_send_json_error(array('message' => 'Пароли не совпадают', 'redirect' => false));
if (strlen($pass1) < 4) wp_send_json_error(array('message' => 'Слишком короткий пароль', 'redirect' => false));
if (false !== strpos(wp_unslash($pass1), "\\" ) ) wp_send_json_error(array('message' => 'Пароль не может содержать обратные слеши "\\"', 'redirect' => false));


if (isset($_FILES['avatar'])) { 
    if ($_FILES['avatar']['error']) wp_send_json_error(array('message' => "Ошибка загрузки: " . $_FILES['avatar']['error'].". (".$_FILES['avatar']['name'].") ", 'redirect' => false)); // если произошла серверная ошибка при загрузке файла
    $type = $_FILES['avatar']['type']; // теперь возьмем расширение файла
    if (($type != "image/jpg") && ($type != "image/jpeg") && ($type != "image/png")) wp_send_json_error(array('message' => "Формат файла может быть только jpg или png. (".$_FILES['avatar']['name'].")", 'redirect' => false)); // если формат плохой
}

$user_id = wp_create_user($user_login,$pass1,$user_email); // пробуем создать пользователя с переданными данными

// если есть ошибки
if (is_wp_error($user_id) && $user_id->get_error_code() == 'existing_user_email') wp_send_json_error(array('message' => 'Пользователь с таким email уже существует.', 'redirect' => false));
elseif (is_wp_error($user_id) && $user_id->get_error_code() == 'existing_user_login') wp_send_json_error(array('message' => 'Пользователь с таким логином уже существует.', 'redirect' => false));
elseif (is_wp_error($user_id) && $user_id->get_error_code() == 'empty_user_login') wp_send_json_error(array('message' => 'Логин только латиницей.', 'redirect' => false));
elseif (is_wp_error($user_id)) wp_send_json_error(array('message' => $user_id->get_error_code(), 'redirect' => false));

update_user_meta($user_id,'first_name',$first_name);
update_user_meta($user_id,'last_name',$last_name); 

if (isset($_FILES['avatar'])) { 
    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    require_once(ABSPATH . "wp-admin" . '/includes/media.php');

    $attach_id_img = media_handle_upload('avatar', 0); 
    
    if (is_wp_

    $avatar = array(); 
    $avatar['media_id'] = $attach_id_img;
    $avatar['full'] = wp_get_attachment_url($attach_id_img);
    
    update_user_meta($user_id, 'simple_local_avatar', $avatar ); 


$code = sha1($user_id . time()); 
$activation_link = home_url().'/activate/?key='.$code.'&user='.$user_id; 
add_user_meta( $user_id, 'has_to_be_activated', $code, true ); 
$txt = '<h3>Доброго времени.</h3><p>Для активации вашего пользователя на сайте '.home_url().' перейдите по ссылке: <a href="'.$activation_link.'">'.$activation_link.'</a></p>'; 
add_filter( 'wp_mail_content_type', 'set_html_content_type' ); 
wp_mail( $user_email, 'Активация пользователя.', $txt ); 
remove_filter( 'wp_mail_content_type', 'set_html_content_type' ); 

wp_send_json_success(array('message' => 'Все прошло отлично. Вы зарегистрировались. На вашу почту отправлено письмо с ссылкой на активацию.', 'redirect' => false));
?>