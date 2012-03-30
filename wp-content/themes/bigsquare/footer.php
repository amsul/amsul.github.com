	</div>
	
		<!-- start of footer -->
		<?php if(!is_home()): ?>
		<? /* <div class="devider"><a href="#header">Top</a></div> */ ?>
		<?php endif; ?>
		<div id="footer">
			<div class="copyright">
				<p><?php echo wpop_get_option('copyright'); ?></p>
			</div>
			<? /*
			<div class="footlogo alignright">
				<?php include_once(TEMPLATEPATH . "/copyright.php"); ?>
			</div>
			*/ ?>
			<div class="clear"></div>
		</div>
		<!-- end of footer -->
	
	<!-- end of #content -->
	<div class="clear"></div>
	</div> <!-- .main-outer -->
	<?php wp_footer(); ?>
	</body>
</html>