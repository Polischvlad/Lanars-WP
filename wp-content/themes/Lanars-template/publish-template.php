<?php
/**
 * Template Name: publishing-template
 */
?>
<?php get_header(); ?>
<?php if ( !is_user_logged_in()) {
   echo 'Зарегестрируйтесь и войдите, чтобы создавать задачи исполнителям';
    } else {
?>

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


<?php
                   
?>



<?php } ?> <?php /* is logged in */ ?>