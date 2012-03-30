<?php get_header(); ?>
	<!-- #content -->
	<div id="content">
		<!-- start of .post -->
		<div class="sidelist">
			<h1><?php _e('Archive'); ?> ”<?php single_cat_title(); ?>”</h1>
			<div class="cat-desc"><?php echo category_description(); ?></div>
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div class="post">
				<?php the_post_thumbnail('pic-archive'); ?>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="date"><?php echo get_the_date(); ?></div>
			</div>
			<?php endwhile; endif;?>
		</div>
		<?php get_footer(); ?>