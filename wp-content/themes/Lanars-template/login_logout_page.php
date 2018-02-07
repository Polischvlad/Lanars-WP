<?php
/**
 * Template Name: login-logout-template
 */
get_header(); ?>
<div class="container">
<div class="row">  
<section>
    
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
<h1><?php the_title(); ?></h1>

<!-- LOGIN SECTION -->
        <?php if (is_user_logged_in()) { 
            $current_user = wp_get_current_user();  ?>
        <p>Привет, <?php echo $current_user->display_name; ?>. <a href="#" class="logout" data-nonce="<?php echo wp_create_nonce('logout_me_nonce'); ?>">Выйти</a></p> 
        <?php } else {  ?>
        <div id="login">    
            <form name="loginform" id="loginform" method="post" class="userform" action=""> 
                <input type="text" name="log" id="user_login" placeholder="Логин или email"> 
                <input type="password" name="pwd" id="user_pass" placeholder="Пароль"> 
                <input type="submit" value="Войти">
                <input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('login_me_nonce'); ?>"> 
                <input type="hidden" name="action" value="login_me"> 
                <div class="response"></div> 
                <a class="forget-pass" href="<?php echo esc_url( wp_lostpassword_url( get_permalink() ) ); ?>" title="Забыли пароль?">Забыли пароль?</a>
                <a class="forget-pass" href="/registration" title="Не зарегестрированы ?">Зарегестрироваться</a>        
            </form>
        </div>
        <?php } ?>
<!-- LOGIN SECTION END -->

<!--ADD TASKS SECTION START -->    
<?php 
   /* if ( !is_user_logged_in()) { echo 'Зарегестрируйтесь и войдите, чтобы создавать задачи исполнителям'; } 
    else { */
?>
    
<?php if( current_user_can('employer')) {  ?> 
    
    Можете Создать задачу
    <div id="postbox">

        <form id="new_post" name="new_post" method="post" action="">

            <p><label for="title">Title:</label><br />
                <input type="text" id="title" value="" tabindex="1" size="20" name="title" />
            </p>

            <p><label for="description">Description:</label><br />
                <textarea id="description" tabindex="3" name="description" cols="50" rows="6"></textarea>
            </p>

            <p>
                <?php wp_dropdown_categories( 'show_option_none=Category&tab_index=4&taxonomy=category' ); ?>
            </p>

            <p>
                <input type="submit" value="Publish" tabindex="6" id="submit" name="submit" />
            </p>

            <input type="hidden" name="post_type" id="post_type" value="post" />

            <input type="hidden" name="action" value="post" />

            <?php wp_nonce_field( 'new-post' ); ?>

        </form>

    </div>
<?php } ?>    

<?php if( current_user_can('employee')) {  ?> 
    Можете взять задачу на выполнение
<?php } ?> 
    
    
<?php //} ?> <?php /* is logged in */ ?>    
<!--ADD TASKS SECTION END -->  
    
<?php endwhile; ?>
</section>
    </div>
    </div>
<?php get_footer();  ?>