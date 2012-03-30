<?php
/*
	Section: TwitterBar
	Author: Simon Prosser
	Author URI: http://www.pagelines.com
	Description: Loads twitter feed into the site morefoor area.
	Version: 1.5.2.1
*/

class PageLinesTwitterBar extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Twitter Bar', 'pagelines');
		$id = 'twitterbar';
	
		
		$default_settings = array(
			'type' 			=> 'standard',
			'description' 	=> 'Displays your latest twitter post.',
			'workswith' 	=> array('morefoot'),
			'folder' 		=> '', 
			'init_file' 	=> 'twitterbar.php',
			'icon'			=> PL_ADMIN_ICONS . '/twitter.png',
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
		parent::__construct($name, $id, $settings);    
   }

	function section_template() { 

		if( !pagelines('twittername') ) :
			?><div class="tbubble">
			<?php _e('Set your Twitter account name in your settings to use the TwitterBar Section.</div>', 'pagelines');
			return;
			endif;
			
			if ( false === ( $tweets = get_transient( 'section-twitter' ) ) ) {
				$params = array(
					'screen_name'=>pagelines('twittername'), // Twitter account name
					'trim_user'=>true, // only basic user data (slims the result)
					'include_entities'=>false, // as of Sept 2010 entities were not included in all applicable Tweets. regex still better
					'include_rts' => true
				);

				/**
				 * The exclude_replies parameter filters out replies on the server. If combined with count it only filters that number of tweets (not all tweets up to the requested count)
				 * If we are not filtering out replies then we should specify our requested tweet count
				 */

				$twitter_json_url = esc_url_raw( 'http://api.twitter.com/1/statuses/user_timeline.json?' . http_build_query( $params ), array( 'http', 'https' ) );
				unset( $params );
				$response = wp_remote_get( $twitter_json_url, array( 'User-Agent' => 'WordPress.com Twitter Widget' ) );
				$response_code = wp_remote_retrieve_response_code( $response );
				if ( 200 == $response_code ) {
					$tweets = wp_remote_retrieve_body( $response );
					$tweets = json_decode( $tweets, true );
					$expire = 900;
					if ( !is_array( $tweets ) || isset( $tweets['error'] ) ) {
						$tweets = 'error';
						$expire = 300;
					}
				} else {
					$tweets = 'error';
					$expire = 300;
					set_transient( 'section-twitter-response-code', $response_code, $expire );
				}

				set_transient( 'section-twitter', $tweets, $expire );
		}
	
			echo '<div class="tbubble">';
			echo '<span class="twitter">"';
			pagelines_register_hook( 'pagelines_before_twitterbar_text', $this->id ); // Hook

			if ( 'error' != $tweets ) {
				echo make_clickable( $tweets[0]['text'] );
			} else {

				$error = get_transient( 'section-twitter-response-code' );		
				switch( $error ) {
						
					case 401:
						echo wp_kses( sprintf( __( 'Error: Please make sure the Twitter account is <a href="%s">public</a>.', 'pagelines' ), 'http://support.twitter.com/forums/10711/entries/14016' ), array( 'a' => array( 'href' => true ) ) );
					break;
						
					case 403:
						_e( 'Error 403: Your IP is being rate limited by Twitter.', 'pagelines' );
					break;
						
					case 404:
						_e( 'Error 404: Your username was not found on Twitter.', 'pagelines' );
					break;
						
					case 420:
						_e( 'Error 420: Your IP is being rate limited by Twitter.', 'pagelines' );
					break;
						
					case 502:
						_e( 'Error 502: Twitter is being upgraded.', 'pagelines' );
					break;
						
					default:
						_e( 'Unknown Twitter error.', 'pagelines' );
					break;
				}				
			} 
			echo '" &mdash;&nbsp;<a class="twitteraccount" href="http://twitter.com/#!/' . pagelines('twittername') . '">' . pagelines('twittername') . '</a></span></div>';
	}
}
/*
	End of section class
*/