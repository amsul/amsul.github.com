<?php get_header(); ?>
	<!-- #content -->
	<div id="content">
		<!-- start of .post -->
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post">
			<h1><?php _e('404 Not Found'); ?></h1>
			<div class="meta">
				&nbsp;
			</div>
			<?php the_content(); ?>
		</div>
		<!-- end of .post -->
		<?php endwhile; endif;?>
		
		<?php get_footer(); ?>