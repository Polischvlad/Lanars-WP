<?php
$nonce = $_POST['nonce'];
if (!wp_verify_nonce($nonce, 'lost_password')) wp_send_json_error(array('message' => 'Данные присланные со сторонней страницы ', 'redirect' => false)); //  проверим  форма отправлена откуда надо

$user_login = $_POST['user_login']; 
$redirect_to = $_POST['redirect_to'];

if (!$user_login) wp_send_json_error(array('message' => 'Вы не заполнили поле', 'redirect' => false)); // если не заполнили

global $wpdb, $current_site; //  надо заглобалить

if (strpos($user_login,'@')) {
    $user = get_user_by('email',trim($user_login));
} else { 
    $user = get_user_by('login', trim($user_login)); 
}

if (!$user) wp_send_json_error(array('message' => 'Пользователя с таким email не существует.', 'redirect' => false));
if (get_user_meta( $user->ID, 'has_to_be_activated', true ) != false) wp_send_json_error(array('message' => 'Пользователь еще не активирован.', 'redirect' => false)); 


do_action('lostpassword_post'); // чтобы работали другие хуки

$user_login = $user->user_login; 
$user_email = $user->user_email;

do_action('retrieve_password', $user_login);

$allow = apply_filters('allow_password_reset', true, $user->ID);

if (!$allow) wp_send_json_error(array('message' => 'Сброс пароля запрещено. Пожалуйста свяжитесь с администратором сайта.', 'redirect' => false));
else if (is_wp_error($allow)) wp_send_json_error(array('message' => $allow->get_error_message(), 'redirect' => false)); 

$key = wp_generate_password(20, false); //  уникальный строку-ключ

do_action('retrieve_password_key', $user_login, $key); 

if ( empty( $wp_hasher ) ) { 
    require_once ABSPATH . WPINC . '/class-phpass.php'; // либу для создания хэшей для сброса
    $wp_hasher = new PasswordHash( 8, true ); //  экзепляр класса
}


$hashed = time() . ':' . $wp_hasher->HashPassword( $key ); // создание хэша для версий выше 4.3

$wpdb->update( $wpdb->users, array('user_activation_key' => $hashed), array('user_login' => $user_login)); // пишим в базу 

//отправляем письмо с сылкой на сброс пароля
$reset_link = home_url().'/reset-password/?key='.$key.'&login='.rawurlencode($user_login).'&redirect_to='.esc_attr($redirect_to); 
$txt = '<h3>Доброго времени.</h3><p>Кто-то запросил сброс пароля на сайте: '.home_url().', чтобы сбросить пароль перейдите по ссылке: <a href="'.$reset_link.'">'.$reset_link.'</a>, иначе проигнорируйте это письмо.</p>'; 
add_filter( 'wp_mail_content_type', 'set_html_content_type' ); 
wp_mail( $user_email, 'Сброс пароля пользователя '.$user_login, $txt );
remove_filter( 'wp_mail_content_type', 'set_html_content_type' ); 

wp_send_json_success(array('message' => 'Письмо со ссылкой на страницу изменения пароля отправлено на адрес, указанный при регистрации. Если вы не получили письмо, проверьте папку "Спам" или попробуйте еще раз.', 'redirect' => false));
?>