<?php get_header(); ?>
	<!-- #content -->
	<div id="content">
		<!-- start of .post -->
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post<?php if(!has_post_thumbnail()) echo " no-featured"; ?>">
			<?php if(has_post_thumbnail()): ?>
			<div class="featured">
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(array(700,9999)); ?></a>
				<div class="credit"></div>
			</div>
			<?php endif; ?>
			<div class="detail">
				<div class="meta">
					<span><strong><?php echo get_the_date(); ?></strong></span>
				</div>
				<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
				<div class="excerpt">
					<?php the_content(); ?>
					<? /* php the_excerpt(); */ ?>
				</div>
				
				<?php 
					$args = array(
						'post_type' => 'attachment',
						'numberposts' => -1,
						'post_status' => null,
						'post_parent' => $post->ID
						); 
					$attachments = get_posts($args);
					if(count($attachments) > 1): 
				?>
				<div class="pic-excerpt">
					<div class="top"></div>
					<div class="outer">
						<h3><?php _e('Pictures In This Set'); ?></h3>
						<div class="pic">
							<?php the_image_excerpt(wpop_get_option('pic_excerpt')); ?>
						</div>
					</div>
					<div class="bottom"></div>
				</div> <!-- pic-excerpt -->
				<?php endif; ?>
				
			</div>
		</div>
		<? /*
		<div class="devider"><a href="#header"><?php _e('Top'); ?></a></div>
		*/ ?>
		<?php endwhile;?>
		<div class="paging">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries', 0); ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;', 0); ?></div>
			<div class="clear"></div>
		</div>
		<?php endif;?>
		<!-- end of .post -->
<?php get_footer(); ?>