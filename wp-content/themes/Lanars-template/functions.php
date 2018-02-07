<?php
/**
 * Функции шаблона (function.php)
 */

require_once dirname(__FILE__) . '/for_users/route.php';

/* FORM for ADDING TASKS */
    if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] )) {
        if (isset ($_POST['title'])) {
            $title =  $_POST['title'];
        } else {
            echo 'Please enter a title';
        }
        if (isset ($_POST['description'])) {
            $description = $_POST['description'];
        } else {
            echo 'Please enter the content';
        }
        $tags = $_POST['post_tags'];
        $post = array(
            'post_title'	=> $title,
            'post_content'	=> $description,
            'post_category'	=> $_POST['cat'], 
            'tags_input'	=> $tags,
            'post_status'	=> 'publish',
            'post_type'	=> $_POST['post_type']
        );
        wp_insert_post($post);  // http://codex.wordpress.org/Function_Reference/wp_insert_post
        wp_redirect( '/task-list/');
    }

    do_action('wp_insert_post', 'wp_insert_post'); 

//роли заказчика и исполнитея

        $result = add_role( 'employee', __('Employee' ),array(
            'read' => true, // true allows this capability
            'edit_posts' => true, // Allows user to edit their own posts
            'edit_pages' => false, // Allows user to edit pages
            'edit_others_posts' => true, // Allows user to edit others posts not just their own
            'create_posts' => true, // Allows user to create new posts
            'manage_categories' => true, // Allows user to manage post categories
            'publish_posts' => true, // Allows the user to publish, otherwise posts stays in draft mode
            'edit_themes' => false, // false denies this capability. User can’t edit your theme
            'install_plugins' => false, // User cant add new plugins
            'update_plugin' => false, // User can’t update any plugins
            'update_core' => false // user cant perform core updates
        ));

        add_role( 'employer', __('Employer' ),array(
            'read' => true, // true allows this capability
            'edit_posts' => true, // Allows user to edit their own posts
            'edit_pages' => false, // Allows user to edit pages
            'edit_others_posts' => true, // Allows user to edit others posts not just their own
            'create_posts' => false, // Allows user to create new posts
            'manage_categories' => false, // Allows user to manage post categories
            'publish_posts' => false, // Allows the user to publish, otherwise posts stays in draft mode
            'edit_themes' => false, // false denies this capability. User can’t edit your theme
            'install_plugins' => false, // User cant add new plugins
            'update_plugin' => false, // User can’t update any plugins
            'update_core' => false // user cant perform core updates
        ));




/*      // страницa для сброса пароля через хук  (получим http://site.ru/getpassword?redirect=URL_редиректа)

            add_filter( 'lostpassword_url', 'change_lostpassword_url', 10, 2 );

            function change_lostpassword_url( $url, $redirect ){
                $new_url = home_url( '/getpassword' );
                return add_query_arg( array('redirect'=>$redirect), $new_url );
            }
*/




/* custom-login-page-css*/

    /*
    function my_custom_login() {
    echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/login/custom-login-styles.css" />';
    }
    add_action('login_head', 'my_custom_login');
    */

/* custom-login-page-css END*/


register_nav_menus(array( 
	'top' => 'Верхнее',
	'bottom' => 'Внизу' 
));

add_theme_support('post-thumbnails'); // включаем поддержку миниатюр
set_post_thumbnail_size(250, 150); // задаем размер миниатюрам 250x150
add_image_size('big-thumb', 400, 400, true); // добавляем еще один размер картинкам 400x400 с обрезкой

register_sidebar(array( // регистрируем левую колонку, этот кусок можно повторять для добавления новых областей для виджитов
	'name' => 'Сайдбар', // Название в админке
	'id' => "sidebar", // идентификатор для вызова в шаблонах
	'description' => 'Обычная колонка в сайдбаре', // Описалово в админке
	'before_widget' => '<div id="%1$s" class="widget %2$s">', // разметка до вывода каждого виджета
	'after_widget' => "</div>\n", // разметка после вывода каждого виджета
	'before_title' => '<span class="widgettitle">', //  разметка до вывода заголовка виджета
	'after_title' => "</span>\n", //  разметка после вывода заголовка виджета
));





add_action('wp_footer', 'add_scripts'); 
if (!function_exists('add_scripts')) { 
	function add_scripts() {
	    if(is_admin()) return false; 
	    wp_deregister_script('jquery'); 
	    wp_enqueue_script('jquery','//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js','','',true);
	    wp_enqueue_script('bootstrap', get_template_directory_uri().'/js/bootstrap.min.js','','',true);
	    wp_enqueue_script('main', get_template_directory_uri().'/js/main.js','','',true);
	}
}

add_action('wp_print_styles', 'add_styles'); 
if (!function_exists('add_styles')) { 
	function add_styles() { 
	    if(is_admin()) return false;
	    wp_enqueue_style( 'bs', get_template_directory_uri().'/css/bootstrap.min.css' );
		wp_enqueue_style( 'main', get_template_directory_uri().'/style.css' ); 
	}
}

if (!class_exists('bootstrap_menu')) {
	class bootstrap_menu extends Walker_Nav_Menu { 
		private $open_submenu_on_hover;

		function __construct($open_submenu_on_hover = true) {
	        $this->open_submenu_on_hover = $open_submenu_on_hover;
	    }

		function start_lvl(&$output, $depth = 0, $args = array()) { 
			$output .= "\n<ul class=\"dropdown-menu\">\n";
		}
		function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
			$item_html = ''; 
			parent::start_el($item_html, $item, $depth, $args); 
			if ( $item->is_dropdown && $depth === 0 ) { 
			   if (!$this->open_submenu_on_hover) $item_html = str_replace('<a', '<a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"', $item_html); 
			   $item_html = str_replace('</a>', ' <b class="caret"></b></a>', $item_html); 
			}
			$output .= $item_html; 
		}
		function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output) { 
			if ( $element->current ) $element->classes[] = 'active'; 
			$element->is_dropdown = !empty( $children_elements[$element->ID] ); 
			if ( $element->is_dropdown ) {
			    if ( $depth === 0 ) { 
			        $element->classes[] = 'dropdown';
			        if ($this->open_submenu_on_hover) $element->classes[] = 'show-on-hover';
			    } elseif ( $depth === 1 ) { // если 2 уровня
			        $element->classes[] = 'dropdown-submenu'; 
			    }
			}
			parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output); 
		}
	}
}
?>