<?php

/**
 * Information about the author 0.2.2
 */

if ( !function_exists( 'nkuttler022_links' ) ) {
	function nkuttler0_2_2_links( $plugin ) {
	
		$name			= 'Nicolas Kuttler';
		$gravatar		= '7b75fc655756dd5c58f4df1f4083d2e2.jpg';
		$url_author		= 'http://www.nkuttler.de';
		$url_plugin		= $url_author . '/wordpress/' . $plugin;
		$feedburner		= 'http://feedburner.google.com/fb/a/mailverify?uri=NicolasKuttler&loc=en_US'; // subscribe feed per mail
		$profile		= 'http://wordpress.org/extend/plugins/profile/nkuttler';
	
		/***/
	
		$vote			= 'http://wordpress.org/extend/plugins/' . $plugin;
		$homeFeed		= 'http://www.nkuttler.de/feed/';
		$donate			= 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=11041772';
		$commentsFeed	= $url_plugin . '/feed/'; ?>
	
		<div id="nkbox" >
			<strong><?php _e( 'Do you like this plugin?', $plugin ) ?></strong>
			<div class="gravatar" >
				<a href="<?php echo $url_author ?>"><img src="http://www.gravatar.com/avatar/<?php echo $gravatar ?>?s=50" alt="<?php echo $author ?>" title="<?php echo $author ?>" /></a>
				<br />
				<?php echo $name ?>
			</div>
			<ul >
				<li>
					<?php printf( __( "<a href=\"%s\">Rate</a> it", $plugin ), $vote ) ?> <br />
				</li>
				<li>
					<?php printf( __( "<a href=\"%s\">Visit</a> it's homepage", $plugin ), $url_plugin ) ?> <br />
				</li>
				<li>
					<?php printf( __( "<a href=\"%s\">Subscribe</a> the feed", $plugin ), $commentsFeed ) ?> <br />
				</li>
				<li>
					<?php printf( __( "<a href=\"%s\">Donate</a>!", $plugin ), $donate ) ?> <br />
				</li>
			</ul>
			<strong><?php _e( 'About the author', $plugin ) ?></strong> <br />
			<ul >
				<li>
					<?php printf( __( 'My <a href="%s">blog</a>', $plugin ), $url_author ) ?> <br />
				</li>
				<li>
					<?php printf( __( "Subscribe via <a href=\"%s\">RSS</a> or <a href=\"%s\">email</a>", $plugin ), $homeFeed, $feedburner ) ?> <br />
				</li>
			</ul>
			<div >
				<a href="<?php echo $profile ?>"><?php _e( 'My other plugins', $plugin ) ?></a><br />
				<?php _e( '<a href="http://www.nkuttler.de">Translated by Nicolas</a>', $plugin ) ?><br />
			</div>
		</div> <?php
	}
}

?>
