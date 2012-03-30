<?php
/**
 * WPSDAdminAction.
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 1.5
 * @package wp-stats-dashboard
 */
class WPSDAdminAction extends WPSDWPPlugin{
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $plugin_name
	 * @param mixed $plugin_base
	 * @return void
	 */
	function __construct($plugin_name, $plugin_base) {
		
		global $wp_version;

		$this->plugin_name = $plugin_name;

		$this->plugin_base = $plugin_base;

		$this->add_action('activate_' . trim(@$_GET['plugin']) ,'activate');

		$this->add_action('deactivate_' . trim(@$_GET['plugin']), 'deactivate');

		$this->add_action('admin_head'); // header rendering.

		$this->add_action('admin_menu'); // menu rendering.
		
		$this->add_action('admin_init');
		
		$this->add_action('publish_post'); // for autoposting.

		if($wp_version < 2.7) {
			$this->add_action('activity_box_end','admin_dashboard'); // add chart to dashboard.
		}

		$this->add_action('wp_ajax_wpsd_metrics');

		$this->add_action('wp_ajax_nopriv_wpsd_metrics');

		$this->add_action('wp_ajax_wpsd_load_trend');

		$this->add_action('wp_ajax_wpsd_load_clicks');

		$this->add_action('wp_ajax_wpsd_load_postviews');

		$this->add_action('wp_ajax_wpsd_load_referers');

		$this->add_action('wp_ajax_wpsd_load_searchterms');

		$this->add_action('wp_ajax_wpsd_load_compete');
				
		$this->add_action('wp_ajax_wpsd_load_optimize');

		$this->add_action('wp_ajax_wpsd_load_rss');
		
		$this->add_action('wp_ajax_wpsd_load_authors');

		$this->add_action('wp_ajax_wpsd_find_profile');
		
		$this->add_action('wp_ajax_wpsd_jtip');
		
		$this->add_action('wp_ajax_wpsd_rpc_get_key');
		
		$this->add_action('wp_ajax_wpsd_rpc_get_stats');
		
		$this->add_action('wp_ajax_wpsd_rpc_get_stats_by_date');
		
		$this->add_action('wp_ajax_wpsd_rpc_get_metrics');
		
		$this->add_action('wp_ajax_wpsd_rpc_get_stats_by_date_range');
		
		$this->add_action('wp_ajax_wpsd_rpc_get_stats_by_year_and_month');
		
		$this->add_action('wp_ajax_wpsd_rpc_clear_cache');
		
		$this->add_action('wp_ajax_wpsd_rpc_get_version');
				
		$this->add_action( 'show_user_profile');
	
		$this->add_action( 'edit_user_profile');
		
		$this->add_action( 'personal_options_update');
	
		$this->add_action( 'edit_user_profile_update');
		
		$this->doExport();
				
		/*if($_REQUEST['test'] == 1) {
			
			$this->publish_post(1070);
		}*/
	}
	
	/**
	 * WPSDAdminAction function.
	 * 
	 * @access public
	 * @param mixed $plugin_name
	 * @param mixed $plugin_base
	 * @return void
	 */
	function WPSDAdminAction($plugin_name, $plugin_base){
		
		$this->__construct($plugin_name, $plugin_base);
	}
	
	/**
	 * admin_init function.
	 * 
	 * @access public
	 * @return void
	 */
	function admin_init() {
		
		wp_deregister_script('swfobject');

		wp_register_script('swfobject', WPSD_PLUGIN_URL . '/resources/js/swfobject.js', array(), '2.2'); 

		wp_enqueue_script('swfobject');
		
		wp_register_script('jtip', WPSD_PLUGIN_URL . '/resources/js/jtip.js', array('jquery'), '1.0'); 

		wp_enqueue_script('jtip');
		
		wp_register_script('wpsd-admin-scripts', WPSD_PLUGIN_URL . '/resources/js/admin-scripts.js', array('jquery'), '1.0'); 
		
		wp_enqueue_script('wpsd-admin-scripts');
				
		wp_register_style('wpsd', WPSD_PLUGIN_URL . '/resources/css/style.css');
       
        wp_enqueue_style( 'wpsd');       
	}
	
	/**
	 * renderView function.
	 * 
	 * @access public
	 * @return void
	 */
	function renderView() {
		
		$sub = $this->getAction();
		
		$url = $this->getActionUrl();

		// Display submenu
		$this->render_admin('admin_submenu', array ('url' => $url, 'sub' => $sub));

		/**
		 * Show view.
		 */
		switch($sub){
			default:
			case 'main':
				$this->admin_start();
				break;
			case 'login':
				$this->admin_autologin();
				break;
		}
	}

	/**
	 * activate function.
	 * 
	 * @access public
	 * @return void
	 */
	function activate() {
		
		if(!function_exists('curl_init')) {
		
			die(__('Couldn\'t activate WP-Stats-Dashboard: CURL extension not found. CURL is required to be installed on your server. Please ask your hosting provider to install PHP CURL for you.', 'wpsd'));
		}
		
		$upgradeDao = new WPSDUpgradeDao();

		$upgradeDao->install();
	}

	/**
	 * deactivate function.
	 * 
	 * @access public
	 * @return void
	 */
	function deactivate() {
			
		wp_clear_scheduled_hook( 'wpsd_cron_hook' );
	}

	/**
	 * admin_head function.
	 * 
	 * @access private
	 * @return void
	 */
	function admin_head(){

		$this->render_admin('admin_head', array('plugin_name'=>$this->plugin_name));
	}

	/**
	 * Create menu entry for admin.
	 *
	 * @return	void
	 * @access private
	 */
	function admin_menu(){
		
		if (function_exists('add_menu_page')) {

			add_menu_page(__('WPSD', 'wpsd'), __('WPSD', 'wpsd'), 10, $this->plugin_name, array (&$this, 'admin_start'), WPSD_PLUGIN_URL . '/resources/images/stats-icon-16.png' );
			
			if(defined('WPSD_DEBUG') && WPSD_DEBUG) { 	
				
				add_submenu_page($this->plugin_name, __('Rpc'), __('Rpc'), 10, 'wpsd_rpc',  array(&$this, 'admin_rpc') );
			}
		}
		
		if(function_exists('add_submenu_page')) {	
			
			add_submenu_page(
				$this->plugin_name, 
				__('Settings', 'wpsd'), 
				__('Settings', 'wpsd'), 
				10, 
				$this->plugin_name,  
				array(&$this, 'admin_start') );
				
			add_submenu_page(
				$this->plugin_name, 
				__('Profile finder', 'wpsd'), 
				__('Profile finder', 'wpsd'), 
				10, 
				'wpsd_profile_finder',  
				array(&$this, 'admin_profile_finder') );
				
			add_submenu_page(
				$this->plugin_name, 
				__('Export', 'wpsd'), 
				__('Export', 'wpsd'), 
				10, 
				'wpsd_export',  
				array(&$this, 'admin_export') );
							
			$role = 'administrator';
			
			if(wpsd_get_user_role() == 'author' && wpsd_has_access())
				$role = 'author';
			else if(wpsd_get_user_role() == 'editor' && wpsd_has_access())
				$role = 'editor';
			else if(wpsd_get_user_role() == 'contributor' && wpsd_has_access())
				$role = 'contributor';
			else if(wpsd_get_user_role() == 'subscriber' && wpsd_has_access())
				$role = 'subscriber';
				
			$hook = add_submenu_page('index.php', __('Dashboard Stats', 'wpsd'), __('Dashboard Stats', 'wpsd'), $role, 'wpsd', array(&$this, 'wp_stats_dashboard_page'));

		}
	}

	/**
	 * admin_dashboard function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_stats_dashboard_page() {
	
		$this->render_admin('admin_dashboard_page', array('plugin_name'=>$this->plugin_name));
	}

	/**
	 * Display the configuration settings.
	 * @access protected
	 */
	function admin_start(){
		$adminConfigAction = new WPSDAdminConfigAction($this->plugin_name, $this->plugin_base);
		$adminConfigAction->render();
	}

	/**
	 * Display the help page.
	 * @return void
	 * @access private
	 */
	function admin_help(){
		$this->render_admin('admin_help', array("plugin_name"=>$this->plugin_name));
	}

	/**
	 * Display wp-stats chart in dashboard.
	 * @return void
	 * @access private
	 */
	function admin_dashboard() {

		$this->render_admin('admin_dashboard', array("plugin_name"=>$this->plugin_name));
	}

	/**
	 * admin_autologin function.
	 * 
	 * @access public
	 * @return void
	 */
	function admin_autologin() {

		$configForm = new WPSDAdminConfigForm();

		$this->render_admin('admin_autologin', array("plugin_name"=>$this->plugin_name,'form'=>$configForm));
	}

	/**
	 * admin_profile_finder function.
	 * 
	 * @access public
	 * @return void
	 */
	function admin_profile_finder() {

		if(isset($_POST)) {
				
			$types = wpsd_get_metrics_types();
				
			if(null != $types && is_array($types)) {

				foreach($types as $type => $opts) {
						
					if('' != $type) {

						$key_c = 'wpsd-profile-checkbox-' . $type;

						$key_i = 'wpsd-profile-input-' . $type;

						if(isset($_POST[$key_c]) && $_POST[$key_c] == 'on') {
								
							if(isset($_POST[$key_i])) {

								switch($type) {
										
									case 'flickr':
										update_option('wpsd_flickr_uri', 'http://www.flickr.com/photos/' . $_POST[$key_i]);
										break;
											
									case 'feedburner':
										update_option('wpsd_feedburner_uri', 'http://feeds.feedburner.com/' .  $_POST[$key_i]);
										break;

									default:
										update_option('wpsd_' . $type . '_un', $_POST[$key_i]);
										break;
								}
							}
						}
					}
				}
			}
		}

		$this->render_admin('admin_profile_finder');
	}

	/**
	 * admin_export function.
	 * 
	 * @access public
	 * @return void
	 */
	function admin_export() {
			
		$this->render_admin('admin_export');
	}
	
	/**
	 * admin_rpc function.
	 * 
	 * @access public
	 * @return void
	 */
	function admin_rpc() {
	
		$this->render_admin('admin_rpc');
	}
	
	/**
	 * publish_post function.
	 * 
	 * @access public
	 * @return void
	 */
	function publish_post($postId = 0) {
		
		include_once($this->plugin_base . '/classes/util/WPSDAutoPost.php');
		
		if($postId > 0) {
		
			$form = new WPSDAdminConfigForm();
		
			$ap = new WPSDAutoPost();
		
			$ap->setAmplifyEmail($form->getWpsdAmplifyAutoPostEmail());
			
			$ap->setPosterousEmail($form->getWpsdPosterousAutoPostEmail());
			
			$post = get_post($postId);
						
			if(null != $post) {
			
				$autoposted = get_post_meta($post->ID, 'wpsd_autopost', true);
				
				if(!$autoposted && $ap->post($post->post_title, 
					$post->post_excerpt . ' <br/><br/> <a href="' . get_permalink($postId)  . '" target="_blank" rel="follow">' . __('Read more', 'wpsd') . '</a>', 
					get_bloginfo('admin_email'))) {
					
				}
				
				update_post_meta($post->ID, 'wpsd_autopost', 1);
			}
		}
	}
	
	/**
	 * doExport function.
	 * 
	 * @access public
	 * @return void
	 */
	function doExport() {

		if(isset($_POST['wpsd_export_excel_btn'])) {

			$dao = new WPSDTrendsDao();

			$factory = new WPSDStatsFactory();

			$export = new WPSDExcel();
							
			// Update stats.
			for($i=1; $i<=$factory->last; $i++) {

				$rows = $dao->getStats($i);

				if(null != $rows) {
						
					foreach($rows as $r) {

						if(null != $r) {
								
							$export->addRow(array($r->wpsd_trends_date, $factory->getStatsType($i), $r->wpsd_trends_stats));
						}
					}
				}
			}
							
			$export->downloadFile();

			die();
		}
		else if(isset($_POST['wpsd_export_google_btn'])) {
							
			/* if(version_compare(phpversion(), '5', '>=')) { // check for php 5.
				
				set_time_limit ( 600 ); // 10 min time limit.
				
				$dao = new WPSDTrendsDao();

				$factory = new WPSDStatsFactory();
								
				// Zend library include path
				set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/..') . '/util/google');
					
				include_once(realpath(dirname(__FILE__) . '/..') . '/util/google/spreadsheet/Google_Spreadsheet.php');
					
				include_once(realpath(dirname(__FILE__) . '/..') . '/util/WPSDGoogleSpreadsheet.php');
			} */
		}
	}

	/**
	 * getDataRemote function.
	 * 
	 * @access public
	 * @param mixed $url
	 * @return void
	 */
	function getDataRemote($url) {

		@set_time_limit( 60 );

		$options = array();
		$options['redirection'] = 5;

		if ( false == $file_path )
		$options['method'] = 'HEAD';
		else
		$options['method'] = 'GET';

		$response = wp_remote_request($url, $options);

		if ( is_wp_error( $response ) )
		return false;

		return $response;
	}

	/**
	 * getResponseCode function.
	 * 
	 * @access public
	 * @param mixed $response
	 * @return void
	 */
	function getResponseCode($response) {

		return $response['response']['code'];
	}

	/**
	 * wpsd_nonce_tick function.
	 * 
	 * @access public
	 * @return void
	 */
	function wpsd_nonce_tick() {

		$nonce_life = 86400;

		return ceil(time() / ( $nonce_life / 2 ));
	}

	/**
	 * wpsd_verify_nonce function.
	 * 
	 * @access public
	 * @param mixed $nonce
	 * @param int $action. (default: -1)
	 * @return void
	 */
	function wpsd_verify_nonce($nonce, $action = -1) {

		$i = $this->wpsd_nonce_tick();

		// Nonce generated 0-12 hours ago
		if ( substr(wp_hash($i . $action, 'nonce'), -12, 10) == $nonce )
		return 1;
		// Nonce generated 12-24 hours ago
		if ( substr(wp_hash(($i - 1) . $action, 'nonce'), -12, 10) == $nonce )
		return 2;
		// Invalid nonce
		return false;
	}

	/**
	 * wp_ajax_wpsd_metrics function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_metrics() {

		$this->wp_ajax_nopriv_wpsd_metrics();
	}

	/**
	 * wp_ajax_nopriv_wpsd_metrics function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_nopriv_wpsd_metrics() {

		$nonce = $_POST['_ajax_nonce'];

		$action = 'wpsd-metrics-nonce';

		if(!$this->wpsd_verify_nonce($nonce, $action)) { 
			die('Security error');
		}

		if (file_exists ("{$this->plugin_base}/classes/ajax.php")) { 
			include ("{$this->plugin_base}/classes/ajax.php");
		}
		else { 
			echo '<p>' . __('Rendering of stats failed.', 'wpsd') . '</p>';
		}
		exit;
	}

	/**
	 * wp_ajax_wpsd_find_profile function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_find_profile() {

		$username = wpsd_sanitize($_POST['name']);

		if('' != $username) {
				
			$types = wpsd_get_metrics_types();
				
			if(null != $types && is_array($types)) {

				foreach($types as $k => $opts) {
						
					$url = $opts[2];
						
					if('' != trim($url)) {

						$url = str_replace('{username}' , $username, $url);

						$code = $this->getResponseCode( $this->getDataRemote($url) );

						$this->render_admin('admin_profile_type',
							array('username' => $username, 'code' => $code, 'type' => $k, 'url' => $url, 'icon' => $opts[1], 'name' => $opts[0]));
					}
				}
			}
		}
	}

	/**
	 * wp_ajax_wpsd_load_trend function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_load_trend() {

		$type = $_REQUEST['type'];

		$dao = new WPSDTrendsDao();

		$rows = $dao->getStats($type);

		$data = array();

		if(is_array($rows)) {

			foreach($rows as $row) {

				$data[$row->wpsd_trends_date] = $row->wpsd_trends_stats;
			}
		}

		// Default is pagerank.
		$this->render_admin('admin_ajax_trend_visualize', array('set'=>$data, 'label'=>'days', 'title'=>''));
		
		exit;
	}

	/**
	 * wp_ajax_wpsd_load_clicks function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_load_clicks() {

		$form = new WPSDAdminConfigForm();

		if($form->getWpsdWidgetClicks()) {
				
			$result =  wpsd_read_cache();

			echo '<!-- WP-Stats-Dashboard - START Clicks -->';
				
			// Parse Clicks.
			$pattern = '<div id="clicks".*?>(.*?)<\/div>';
				
			preg_match_all('/'.$pattern.'/s', $result, $matches);

			$clicks = preg_replace('/<h4>(.*?)<\/h4>/s', '<h5>$1</h5>', $matches[1][0]);
			$clicks = preg_replace('/<h3>(.*?)<\/h3>/s', '<h4>Clicks</h4>', $clicks);
			$clicks = str_replace('<table', '<table style="width:100%;"', $clicks);
			if(defined('WPSD_PLUGIN_URL')){

				$clicks = str_replace('/i/stats-icon.gif', WPSD_PLUGIN_URL . '/resources/images/stats-icon.gif', $clicks);
			}
		
			echo '<div style="overflow:hidden;">';	
			echo str_replace('class="label"', 'class="label" width="350" style="word-break:break-all; width:350px;"', $clicks);
			echo '</div>';
				
			echo '<!-- WP-Stats-Dashboard - STOP Clicks -->';

		}
		else {
			_e('Widget disabled check', 'wpsd'); echo ' <a href="' . wpsd_get_settings_url() .'" title="wp-stats-dashboard settings" target="_self">'.__('settings', 'wpsd') . '</a>';
		}

		exit;
	}

	/**
	 * wp_ajax_wpsd_load_postviews function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_load_postviews() {

		$form = new WPSDAdminConfigForm();

		if($form->getWpsdWidgetPostViews()) {
				
			$result =  wpsd_read_cache();

			echo '<!-- WP-Stats-Dashboard - START Post views -->';

			// Parse posts views.
			$pattern = '<div id="postviews".*?>(.*?)<\/div>';
				
			preg_match_all('/'.$pattern.'/s', $result, $matches);

			$pv = preg_replace('/<h4>(.*?)<\/h4>/s', '<h5>$1</h5>', $matches[1][0]);				
			$pv = preg_replace('/<h3>(.*?)<\/h3>/s', '<h4>Post views</h4>', $pv);
			$pv = str_replace('<table', '<table style="width:100%;"', $pv);
			
			if(defined('WPSD_PLUGIN_URL')){

				$pv = str_replace('http://dashboard.wordpress.com/i/stats-icon.gif', WPSD_PLUGIN_URL . '/resources/images/stats-icon.gif', $pv);
			}
				
			$pv = str_replace('index.php?page=estats', 'index.php?page=stats', $pv);
			echo '<div style="overflow:hidden;">';	
			echo $pv;
			echo '</div>';
			echo '<!-- WP-Stats-Dashboard - STOP Post views-->';
				
		} else {
				
			_e('Widget disabled check', 'wpsd'); echo ' <a href="' . wpsd_get_settings_url() .'" title="wp-stats-dashboard settings" target="_self">'.__('settings', 'wpsd') . '</a>';
		}

		exit;
	}

	/**
	 * wp_ajax_wpsd_load_referers function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_load_referers() {

		$form = new WPSDAdminConfigForm();

		if($form->getWpsdWidgetReferrers()) {
				
			$result =  wpsd_read_cache();

			echo '<!-- WP-Stats-Dashboard - START Referrers -->';
				
			// Parse Referrers
			$pattern = '<div id="referrers".*?>(.*?)<\/div>';
				
			preg_match_all('/'.$pattern.'/s', $result, $matches);

			$ref = preg_replace('/<h4>(.*?)<\/h4>/s', '<h5>$1</h5>', $matches[1][0]);
				
			$ref = preg_replace('/<h3>(.*?)<\/h3>/s', '<h4>Referrers</h4>', $ref);
			
			$ref = str_replace('<table', '<table style="width:100%;"', $ref);
			
			if(defined('WPSD_PLUGIN_URL')){

				$ref = str_replace('http://dashboard.wordpress.com/i/stats-icon.gif', WPSD_PLUGIN_URL . '/resources/images/stats-icon.gif', $ref);
				
				$ref = str_replace('/i/stats/', 'http://wordpress.com/i/stats/', $ref);
				
			}
			echo '<div style="overflow:hidden;">';	
			echo str_replace('class="label"', 'class="label" width="350" style="word-break:break-all; width:350px;"', $ref);
			echo '</div>';
			
			echo '<style type="text/css">.referrer img.avatar { padding:5px 5px 0px 0px; }</style>';
				
			echo '<!-- WP-Stats-Dashboard - STOP Referrers-->';

				
		} else {
				
			_e('Widget disabled check', 'wpsd'); echo ' <a href="' . wpsd_get_settings_url() .'" title="wp-stats-dashboard settings" target="_self">'.__('settings', 'wpsd') . '</a>';
		}

		exit;
	}

	/**
	 * wp_ajax_wpsd_load_searchterms function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_load_searchterms() {

		$form = new WPSDAdminConfigForm();

		if($form->getWpsdWidgetSearchTerms()) {
				
			$result =  wpsd_read_cache();

			'<!-- WP-Stats-Dashboard - START Search terms-->';
				
			// Parse Search Terms.
			$pattern = '<div id="searchterms".*?>(.*?)<\/div>';
				
			preg_match_all('/'.$pattern.'/s', $result, $matches);

			$st = preg_replace('/<h4>(.*?)<\/h4>/s', '<h5>$1</h5>', $matches[1][0]);
				
			$st = preg_replace('/<h3>(.*?)<\/h3>/s', '<h4>Search Terms</h4>', $st);
			
			$st = str_replace('<table', '<table style="width:100%;"', $st);

			if(defined('WPSD_PLUGIN_URL')){

				$st = str_replace('http://dashboard.wordpress.com/i/stats-icon.gif', WPSD_PLUGIN_URL . '/resources/images/stats-icon.gif', $st);
			}
			
			echo '<div style="overflow:hidden;">';
			echo $st;
			echo '</div>';
			
			echo '<!-- WP-Stats-Dashboard - STOP Search terms -->';
				
		} else {
				
			_e('Widget disabled check', 'wpsd'); echo ' <a href="' . wpsd_get_settings_url() .'" title="wp-stats-dashboard settings" target="_self">'.__('settings', 'wpsd') . '</a>';
		}

		exit;
	}

	/**
	 * wp_ajax_wpsd_load_compete function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_load_compete() {

		$form = new WPSDAdminConfigForm();

		if($form->getWpsdWidgetCompete()) {

			$domain = str_replace('http://', '', get_bloginfo('url'));
			$domain = str_replace('https://', '', $domain);
			$domain = str_replace('www.', '', $domain);

			echo '<!-- WP-Stats-Dashboard - START Compete.com -->';

			echo '<img src="http://home.compete.com.edgesuite.net/'.$domain.'++++_uv.png" alt="compete.com graph" width="100%" />';

			echo '<!-- WP-Stats-Dashboard - STOP Compete.com-->';
		}
		else {

			_e('Widget disabled check', 'wpsd'); echo ' <a href="' . wpsd_get_settings_url() .'" title="wp-stats-dashboard settings" target="_self">'.__('settings', 'wpsd') . '</a>';
		}

		exit;
	}
		
	/**
	 * wp_ajax_wpsd_load_optimize function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_load_optimize() {
		
		include_once($this->plugin_base . '/classes/util/metrics/WPSDTwitter.php');
		include_once($this->plugin_base . '/classes/util/metrics/WPSDEngagement.php');
		include_once($this->plugin_base . '/classes/util/metrics/WPSDPageRank.php');
		include_once($this->plugin_base . '/classes/util/WPSDTwitterSearch.php');
		include_once($this->plugin_base . '/classes/util/metrics/WPSDFaceBook.php');
		
		$enabled = array('twitter', 'engagement', 'pagerank', 'blacklisted', 'twitter-search', 'facebook');
				
		$this->render_admin('admin_optimize_widget_header');		
					
		echo '<ul>';
		
		$this->render_admin('admin_optimize_widget_twitter', array('enabled' => $enabled));
			
		$this->render_admin('admin_optimize_widget_twitter_mentions', array('enabled' => $enabled));
		
		$this->render_admin('admin_optimize_widget_facebook', array('enabled' => $enabled));

		$this->render_admin('admin_optimize_widget_engagement', array('enabled' => $enabled));
			
		$this->render_admin('admin_optimize_widget_pagerank', array('enabled' => $enabled));
	
		$this->render_admin('admin_optimize_widget_blacklisted', array('enabled' => $enabled));
				
		echo '</ul>';
				
		die();
	}
	
	/**
	 * wp_ajax_wpsd_load_authors function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_load_authors() {
		
		global $wpdb;
		
		
		$form = new WPSDAdminConfigForm();
		
		if(!$form->getWpsdWidgetAuthors()) {			
			_e('Widget disabled check', 'wpsd'); 
			echo ' <a href="' . wpsd_get_settings_url() .'" title="wp-stats-dashboard settings" target="_self">' . __('settings', 'wpsd') . '</a>';
			die();
		}	
		
		$results = $wpdb->get_results("
			SELECT COUNT(*) as c, post_author as user_id
			FROM {$wpdb->posts} WHERE post_status = 'publish'
			GROUP BY post_author
			ORDER BY c DESC
			LIMIT 5");

		if(null != $results && is_array($results)) {
			
			print('<style>table.wpsd_top5_authors { width:100%; } </style>');
		
			echo '<table class="wpsd_top5_authors">';
			
			printf('<thead><tr><th>%s</th><th>%s</th><th>%s</th><th>%s</th><th>%s</th><th>%s</th><th>%s</th><th>%s</th></tr></thead>', 
				__('Rank', 'wpsd'),
				__('WPSD Score', 'wpsd'),
				__('Author', 'wpsd'),
				__('Post Count', 'wpsd'),
				__('Comments Received', 'wpsd'),
				__('Comments Placed', 'wpsd'),
				__('Klout Score', 'wpsd'),
				__('Twitter Ratio', 'wpsd') );
			
			echo '<tbody>';
						
			$list = array();
				
			foreach($results as $r) {
				
				if(null == $r->user_id) continue;
				
				$metrics = new WPSDUserMetrics($r->user_id);
					
				$avatar = get_avatar($r->user_id, 32);
				
				$user_data =  get_userdata($r->user_id);
				
				$author = sprintf('<a href="%s">%s <br/> <span>%s</span></a>', 'user-edit.php?user_id=' . $r->user_id, $avatar, $user_data->user_login);					
										
				$wpsd_score = $metrics->getWpsdScore();
						
				$list[$wpsd_score] = array('metrics' => $metrics, 'avatar' => $avatar, 'user_data' => $user_data, 'author' => $author);
			}			
				
			ksort($list, SORT_NUMERIC);
				
			$list = array_reverse($list);
			
			$i = 0;
			
			foreach($list as $item) {
				
				$metrics = $item['metrics'];
					
				$avatar = $item['avatar'];
				
				$user_data =  $item['user_data'];
				
				$author = $item['author'];					
	
				if($i % 2 == 0) { 
					
					echo '<tr>';
				} 
				else {
				
					echo '<tr class="highlight">';
				}
				
				$this->render_admin('admin_user_metrics', array('metrics' => $metrics, 'author' => $author, 'rank' => $i + 1));
				
				echo '</tr>';
				
				$i++;
			}
			
			echo '</tbody>';
			
			echo '</table>';
		}
		else {
		
			_e('No authors found with comments placed', 'wpsd');
		}
		die();
	}

	/**
	 * wp_ajax_wpsd_jtip function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_jtip() {
		
		if(!empty($_REQUEST['jtip'])) {
		
			die(urldecode($_REQUEST['jtip']));
		}
	}
		
	/**
	 * wp_ajax_wpsd_rpc_get_key function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_rpc_get_key() {
	
		$soa = new WPSDSoaHelper(WPSD_DEBUG_REMOTE_URL);
		
		die($soa->getKey($_POST['username'], $_POST['password']));
	}
	
	/**
	 * wp_ajax_wpsd_rpc_get_stats function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_rpc_get_stats() {
	
		$soa = new WPSDSoaHelper(WPSD_DEBUG_REMOTE_URL);
		
		//var_dump($soa->getStats($_POST['key'], $_POST['type']) );
		
		print_r($soa->getStats($_POST['key'], $_POST['type'])); 
		
		die();	
	}
	
	/**
	 * wp_ajax_wpsd_rpc_get_stats_by_date function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_rpc_get_stats_by_date() {
		
		$soa = new WPSDSoaHelper(WPSD_DEBUG_REMOTE_URL);
				
		print_r($soa->getStatsByDate($_POST['key'], $_POST['type'], $_POST['date']));
		
		die();
	}
	
	/**
	 * wp_ajax_wpsd_rpc_get_metrics function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_rpc_get_metrics() {
		
		$soa = new WPSDSoaHelper(WPSD_DEBUG_REMOTE_URL);
				
		print_r($soa->getMetrics($_POST['key']));
		
		die();
	}
	
	/**
	 * wp_ajax_wpsd_rpc_get_stats_by_date_range function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_rpc_get_stats_by_date_range() {
		
		$soa = new WPSDSoaHelper(WPSD_DEBUG_REMOTE_URL);
				
		print_r($soa->getStatsByDateRange($_POST['key'], $_POST['type'], $_POST['from_date'], $_POST['to_date']));
		
		die();
	}
	
	/**
	 * wp_ajax_wpsd_rpc_get_stats_by_year_and_month function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_rpc_get_stats_by_year_and_month() {
		
		$soa = new WPSDSoaHelper(WPSD_DEBUG_REMOTE_URL);
				
		print_r($soa->getStatsByYearAndMonth($_POST['key'])); 
		
		die();
	}
	
	/**
	 * wp_ajax_wpsd_rpc_clear_cache function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_rpc_clear_cache() {
		
		$soa = new WPSDSoaHelper(WPSD_DEBUG_REMOTE_URL);
		
		$soa->clearCache($_POST['key']);
		
		die();				
	}
	
	/**
	 * wp_ajax_wpsd_rpc_get_version function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_ajax_wpsd_rpc_get_version() {
		
		$soa = new WPSDSoaHelper(WPSD_DEBUG_REMOTE_URL);
		 
		die('WPSD Version: ' .  $soa->getVersion());
	}

	/**
	 * show_user_profile function. Render user profile fields.
	 * 
	 * @access public
	 * @param mixed $user
	 * @return void
	 */
	function show_user_profile($user) {
		
		$user_metrics = new WPSDUserMetrics($user->ID);
		
		$this->render_admin('admin_user_profile', 
			array('user' => $user, 'metrics' => $user_metrics));	
	}
	
	/**
	 * edit_user_profile function.
	 * 
	 * @access public
	 * @param mixed $user
	 * @return void
	 */
	function edit_user_profile($user) {
		
		$this->show_user_profile($user);
	}
	
	/**
	 * personal_options_update function.
	 * 
	 * @access public
	 * @param mixed $user_id
	 * @return void
	 */	
	function personal_options_update($user_id) {
		
		if ( !current_user_can( 'edit_user', $user_id ) )
		return false;
		
		if(isset($_POST['wpsd_user_klout'])) { 
			
			update_usermeta( $user_id, 'wpsd_user_klout', $_POST['wpsd_user_klout'] );
		}
		
		if(isset($_POST['wpsd_user_twitter'])) {
			
			update_usermeta( $user_id, 'wpsd_user_twitter', $_POST['wpsd_user_twitter'] );
		}
	}
	
	/**
	 * edit_user_profile_update function.
	 * 
	 * @access public
	 * @param mixed $user_id
	 * @return void
	 */
	function edit_user_profile_update($user_id) {
		
		$this->personal_options_update($user_id);
	}
}