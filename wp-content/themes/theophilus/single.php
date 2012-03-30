<?php
get_header();
?>
<div id="filler" class="fix">
	<div id="main-column">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post-meta">
			<ul>
				<li><?php the_time('F j, Y') ?></li>
				<li>Section: <?php the_category(', '); ?></li>
				<li><?php comments_popup_link('Comments (0)', 'Comments (1)', 'Comments (%)'); ?></li>
				<li class="perma"><a href="<?php the_permalink() ?>">Permalink</a></li>
			</ul>
		</div>
		<div id="post-<?php the_ID(); ?>" class="post">
			<h2><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title() ?></a></h2>
			<div class="entry">
				<?php the_content('<p>Read the rest of this entry &raquo;</p>'); ?>
			</div>
			<div id="comments-container">
				<?php comments_template(); ?>
			</div>
		</div>
		<?php endwhile; else: ?>
		<div class="post">
			<h2>No matching results.</h2>
			<div class="entry">
				<p>You seem to have found a mis-linked page or search query with no associated results. Please trying your search again. If you feel that you should be staring at something a little more concrete, feel free to email the author of this site or browse the archives.</p>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php include (TEMPLATEPATH . '/side-column-post.php'); ?>
</div>
<?php get_footer(); ?>