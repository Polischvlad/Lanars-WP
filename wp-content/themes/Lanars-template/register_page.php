<?php
/**
 * Template Name: registration by polischvlad
 */
get_header();?>

    <div class="container">
        <div class="row">
            <h1>
                <?php the_title();  ?>
            </h1>

            <?php if (is_user_logged_in()) {  ?>
            <p>Вы уже зарегистрированы.</p>
            <?php } else {  ?>
            
            <form name="registrationform" id="registrationform" method="post" class="userform" action="">
                <input type="text" name="user_login" id="user_login" placeholder="Логин">
                <input type="email" name="user_email" id="user_email" placeholder="Email">

                <input type="password" name="pass1" id="pass1" placeholder="Пароль">
                <input type="password" name="pass2" id="pass2" placeholder="Повторите пароль">

                <input type="text" name="first_name" id="first_name" placeholder="Имя">
                <input type="text" name="phone" id="phone" placeholder="телефон">
                
                <select name="gender" id="gender"> 
                    <option value=" "> Пол </option> 
                    <option value="Male">Муж.</option> 
                    <option value="Female">Жен.</option> 
                </select>   
                
                <input type="text" name="adress" id="adress" placeholder="адрес">
                <!--input type="file" name="avatar" id="avatar"-->
                <input type="submit" value="Зарегистрироваться">
                <input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('register_me_nonce'); ?>">
                <input type="hidden" name="action" value="register_me">
                <div class="response"></div>
                <!-- ответ от сервера -->
            </form>
            
            <?php } ?>
        </div>
    </div>
    <?php get_footer();  ?>
