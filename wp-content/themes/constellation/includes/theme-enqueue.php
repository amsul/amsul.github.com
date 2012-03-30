<?php

################################################################################
// Enqueue Scripts
################################################################################

function init_scripts() {
    wp_deregister_script( 'jquery' );
    wp_deregister_script( 'comment-reply' );
    // Register Scripts
    wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js');
    wp_register_script( 'comment-reply', get_bloginfo('url') . '/wp-includes/js/comment-reply.js?ver=20090102');
    // Queue Scripts
    wp_enqueue_script('modernizr', get_bloginfo('template_url') . '/js/modernizr-1.5.min.js', '', 1.5, false);
    wp_enqueue_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js', '', '1.4.2', true);
    
    if ( get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply',  get_bloginfo('url') . '/wp-includes/js/comment-reply.js?ver=20090102', 'jquery', '', true );
    
    wp_enqueue_script('jquery-plugins', get_bloginfo('template_url') . 'js/plugins.js', 'jquery', '', true);
    wp_enqueue_script('jquery-scripts', get_bloginfo('template_url') . 'js/script.js', 'jquery', '', true);
}    
 
function footer_scripts() {
	
	?>
	<script>!window.jQuery && document.write('<script src="<?php bloginfo('template_url'); ?>/js/jquery-1.4.2.min.js"><\/script>')</script>
	<!--[if lt IE 7 ]><script src="<?php bloginfo('template_url'); ?>js/dd_belatedpng.js?v=1"></script><![endif]-->
	<?php if ($analytics = get_option('constellation_google_analytics')) : ?><script>
		var _gaq = [['_setAccount', '<?php echo $analytics; ?>'], ['_trackPageview']];
		(function(d, t) {
		var g = d.createElement(t),
		s = d.getElementsByTagName(t)[0];
		g.async = true;
		g.src = '//www.google-analytics.com/ga.js';
		s.parentNode.insertBefore(g, s);
		})(document, 'script');
	</script><?php endif; ?><?php
}

if (!is_admin()) add_action('init', 'init_scripts', 0);
add_action('wp_footer', 'footer_scripts', 10);