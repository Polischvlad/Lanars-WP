<?php
/**
 * Шаблон рубрики (category.php)
 */
get_header(); ?> 
<section>
	<div class="container">
		<div class="row">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					<?php get_template_part('loop');  ?>
				<?php endwhile;
				else: echo '<p>Нет записей.</p>'; endif; ?>	 
		</div>
	</div>
</section>
<?php get_footer(); ?>