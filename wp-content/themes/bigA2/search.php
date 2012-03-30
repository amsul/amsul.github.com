<?php get_header(); ?>
	<!-- #content -->
	<div id="content">
		<!-- start of .post -->
		<div class="sidelist">
			<h1><?php _e('Search Result For'); ?> ”<?php echo get_search_query(); ?>”</h1>
			<div class="cat-desc"><?php echo category_description(); ?></div>
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div class="post">
				<?php the_post_thumbnail('pic-archive'); ?>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="date"><?php echo get_the_date(); ?></div>
			</div>
			<?php endwhile; endif;?>
			<div class="paging">
				<div class="alignleft"><?php next_posts_link('&laquo; Older Entries', 0); ?></div>
				<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;', 0); ?></div>
				<div class="clear"></div>
			</div>
		</div>
		<?php get_footer(); ?>