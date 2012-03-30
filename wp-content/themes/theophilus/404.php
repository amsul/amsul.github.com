<?php
get_header();
?>
<div id="filler" class="fix">
	<div id="main-column" class="fix"><a name="main"></a>
		<div class="post-page">
			<h2>404</h2>
			<div class="entry">
				<p>Hmm, that's peculiar. It appears the URL you entered has either been removed, possibly mistyped (not by your competent hands, of course), or has been moved to another location. Visit the <a href="<?php echo get_settings('home'); ?>/">homepage</a> for help.</p>
			</div>
		</div>
	</div>
	<?php include (TEMPLATEPATH . '/side-column-index.php'); ?>
</div>
<?php get_footer(); ?>