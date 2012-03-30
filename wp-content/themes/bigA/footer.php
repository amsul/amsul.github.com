	</div>
	
		<!-- start of footer -->
		<?php if(!is_home()): ?>
		<? /* <div class="devider"><a href="#header">Top</a></div> */ ?>
		<?php endif; ?>
		<div id="footer">
			<div class="copyright">
				<p><a class="home" href="/"><strong><big>amsul</big></strong></a></p><br />
				<p><small><a href="http://creativecommons.org/licenses/by/3.0/" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/images/cc-14.png" /><span class="text">it's all good to share or remix</span></a></small></p>
				<? /* <p><?php echo wpop_get_option('copyright'); ?></p> */ ?>
			</div>
			<? /*
			<div class="footlogo alignright">
				<?php include_once(TEMPLATEPATH . "/copyright.php"); ?>
			</div>
			*/ ?>
			<div class="clear"></div>
			<div id="i_am">days in.. daze out. i am design. i am thought.</div>
		</div>
		<!-- end of footer -->
	
	<!-- end of #content -->
	<div class="clear"></div>
	</div> <!-- .main-outer -->
	<?php wp_footer(); ?>
	</body>
</html>