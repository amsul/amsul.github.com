<?php get_header(); ?>

<section id="main-content">
	
	<?php if (is_search()) : ?>		
		<h1 class="pagetitle">Search Results: &ldquo;<?php the_search_query(); ?>&rdquo; <?php if (get_query_var('paged')) echo ' &mdash; Page '.get_query_var('paged'); ?></h1>
	<?php endif; ?>
	
	<?php get_template_part('loop'); ?>

</section>

<?php get_footer(); ?>