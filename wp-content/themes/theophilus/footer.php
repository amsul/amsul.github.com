<?php
?>
</div>
<div id="footer" class="fix">
	<div id="footer-container">
		<p class="left">Copyright &copy; <a href="<?php echo get_settings('home'); ?>/"><?php bloginfo('name'); ?></a></p>
		<p class="right"><a href="http://www.iamtimothylong.com/theophilus">Theophilus</a> theme by <a href="http://www.iamtimothylong.com">Timothy Long</a><span id="footer-pad">|</span>Powered by <a href="http://wordpress.org">Wordpress</a></p>
	</div>
</div>
<?php wp_footer(); ?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/functions.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/cufon-yui.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory');?>/js/liberation.font.js"></script>
<script type="text/javascript">
	Cufon.replace('h1, h2, h3', {hover: true});
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-11123046-7']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>