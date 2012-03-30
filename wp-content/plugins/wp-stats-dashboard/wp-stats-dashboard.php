<?php
/*
Plugin Name: WP-Stats-Dashboard
Plugin URI: http://www.daveligthart.com/wp-stats-dashboard-10/
Description: Displays the WordPress.com stats, your traffic and social metrics graphs on your dashboard.
Version: 2.7.9
Author: Dave Ligthart
Author URI: http://www.daveligthart.com
*/

if(!defined('WPSD_PLUGIN_URL')) {
	define('WPSD_PLUGIN_URL',  trailingslashit(get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__)));
}

if(!defined('WPSD_CACHE_PATH')) {
	define('WPSD_CACHE_PATH', realpath(dirname(__FILE__) . '/../..') . '/cache/');
}

if(!defined('WPSD_CACHE_PATH_ALT')) {
	define('WPSD_CACHE_PATH_ALT', realpath(dirname(__FILE__) . '/../..') . '/uploads/');
}

if(!defined('WPSD_FILE')) {
	define('WPSD_FILE', 'wp-stats-dashboard/wp-stats-dashboard.php');
}

if(!defined('WPSD_VERSION')) { 
	define('WPSD_VERSION', '2.7.9'); 
}

if(!defined('WPSD_RPC_VERSION')) {
	define('WPSD_RPC_VERSION', '1.2');
}

if(!defined('WPSD_DB_VERSION')) {
	define('WPSD_DB_VERSION','1.0');
}

if(!defined('WPSD_DEBUG')) {
	define('WPSD_DEBUG', false);
	define('WPSD_DEBUG_REMOTE_URL', ''); 
}

/**
 * wpsd_get_metrics_types function.
 * 
 * @access public
 * @return array metrics
 */
function wpsd_get_metrics_types() {
	return
	array('engagement'=>array('Engagement', 'eng', ''),
	'pagerank'=>array('Google Pagerank', 'pr', ''), 
	'backlinks'=>array('Google Backlinks', 'bl', ''),
	'socialgraph'=>array('Google Social Graph', 'pr', ''),
	'alexa'=>array('Alexa', 'alexa', ''),
	'technorati'=>array('Technorati', 'tr', ''),
	'delicious'=>array('Delicious', 'ds', ''),
	'compete'=>array('Compete', 'cc', ''),
	'yahoo'=>array('Yahoo', 'yahoo', ''),
	'mozrank'=>array('Mozrank', 'moz', ''),
	'postrank'=>array('Postrank', 'ptr', ''),
	'digg'=>array('Digg', 'digg', 'http://digg.com/{username}'),
	'twitter'=>array('Twitter', 'tw', 'http://twitter.com/{username}'),
	'reddit'=>array('Reddit', 'rd', 'http://www.reddit.com/user/{username}/'), 
	'stumbleupon'=>array('StumbleUpon', 'su', 'http://www.stumbleupon.com/stumbler/{username}'),
	'blogcatalog'=>array('BlogCatalog', 'bc', 'http://www.blogcatalog.com/user/{username}'),
	'bing'=>array('Bing', 'bing', ''),
	'linkedin'=>array('LinkedIn', 'li', 'http://www.linkedin.com/in/{username}'),
	'bitly'=>array('Bit.ly', 'bitly', ''),
	'klout'=>array('Klout', 'kl', 'http://klout.com/{username}'),
	'feedburner'=>array('FeedBurner', 'fb', '{username}'),
	'lastfm'=>array('Last.fm', 'lfm', 'http://www.last.fm/user/{username}'),
	'facebook'=>array('Facebook', 'fab', '{username}'),
	'flickr'=>array('Flickr', 'fr', 'http://www.flickr.com/photos/{username}'),
	'diigo'=>array('Diigo', 'dg', 'http://www.diigo.com/profile/{username}'),
	'brazencareerist'=>array('BrazenCareerist', 'br', 'http://www.brazencareerist.com/profile/{username}'),
	'newsvine'=>array('Newsvine', 'nv', 'http://{username}.newsvine.com'),
	'youtube'=>array('Youtube', 'yt', 'http://www.youtube.com/{username}'),
	'myspace'=>array('Myspace', 'ms', 'http://www.myspace.com/{username}'),
	'wordpress'=>array('WordPress', 'wp', ''),
	'posterous'=>array('Posterous', 'ps', 'http://{username}.posterous.com'),
	'plancast'=>array('Plancast', 'pc', 'http://plancast.com/{username}'),
	'lazyfeed'=>array('Lazyfeed', 'lf', 'http://www.lazyfeed.com/user/{username}'),
	'sphinn'=>array('Sphinn', 'spn', 'http://sphinn.com/user/{username}'),
	'jaiku'=>array('Jaiku', 'jk', 'http://{username}.jaiku.com'),
	'plurk'=>array('Plurk', 'plk', 'http://www.plurk.com/{username}'),
	'hyves'=>array('Hyves', 'hyves', 'http://{username}.hyves.nl'),
	'foursquare'=>array('Foursquare','fsq','http://foursquare.com/user/{username}'),
	'disqus'=>array('Disqus','dqs', 'http://disqus.com/{username}'),
	'blippr'=>array('Blippr', 'blippr', 'http://www.blippr.com/profiles/{username}'),
	'amplify'=>array('Amplify', 'amp', 'http://{username}.amplify.com'),
	'runkeeper'=>array('Runkeeper', 'rk', 'http://runkeeper.com/user/{username}/profile'),
	'blippy'=>array('Blippy', 'blippy', 'http://blippy.com/{username}'),
	'friendfeed'=>array('FriendFeed','ff', 'http://friendfeed.com/{username}'),	
	'battlenet'=>array('Battlenet', 'battlenet', '{username}'),
	'educopark'=>array('Educopark', 'educopark', 'http://www.educopark.com/people/view/{username}'),
	'vimeo'=>array('Vimeo', 'vimeo', 'http://vimeo.com/{username}'),
	'identica'=>array('Identi.ca', 'identica', 'http://identi.ca/{username}'),
	'netlog'=>array('Netlog', 'netlog', 'http://en.netlog.com/{username}/'),
	'plaxo'=>array('Plaxo', 'plaxo', '{username}'),
	'99designs'=>array('99designs', '99designs', 'http://99designs.com/people/{username}'),
	'quora'=>array('Quora', 'quora', 'http://www.quora.com/{username}'),
	'getglue'=>array('Getglue', 'getglue', 'http://getglue.com/{username}'),
	'googleplus'=>array('Google Plus', 'googleplus', 'https://plus.google.com/{username}/posts'),
	'hunch'=>array('Hunch', 'hunch', 'http://hunch.com/{username}/'),
	'peerindex'=>array('PeerIndex', 'peerindex', 'http://peerindex.com/{username}'),
	'weread'=>array('weRead',' weread', ''),
	'socialmention'=>array('SocialMention', 'socialmention', ''),
	'eave'=>array('Empire Avenue', 'eave', ''),
	'archive'=>array('Archive', 'archive',''),
	'googlebot'=>array('GoogleBot','googlebot',''),
	'w3c'=>array('W3 Validator','w3c',''),
	'views'=>array('Views', 'views', ''),
	'age'=>array('Site age', 'age', ''),
	'est'=>array('Estimated', 'est', ''),
	'powered'=>array('Powered by', 'pow', '') );
	//'mylikes'=>array('Mylikes','mylikes', 'http://mylikes.com/{username}'),
	//'xbox'=>array('Xbox', 'xbox', 'http://live.xbox.com/en-US/profile/profile.aspx?pp=0&GamerTag={username}'),
	//'backtype'=>array('Backtype', 'bt', ''),
	//'buzz'=>array('Google Buzz', 'buzz', ''),
	//'sixent'=>array('Sixent','sxt','http://{username}.sixent.com'),
	//'society'=>array('Society','soc','http://www.society.me/{username}'),
	//'koornk'=>array('Koornk', 'knk', 'http://www.koornk.com/user/{username}'),
	//'yahoobuzz'=>array('YahooBuzz', 'yahoo', '{username}'),
	//'blogpulse'=>array('BlogPulse', 'blogpulse', ''),
}

require(dirname(__FILE__) . '/classes/util/WPSDUtils.php');
require(dirname(__FILE__) . '/classes/util/com.daveligthart.util.wordpress.php');
require(dirname(__FILE__) . '/classes/util/WPSDWPPlugin.php');
require(dirname(__FILE__) . '/classes/dao/WPSDTrendsDao.php');
require(dirname(__FILE__) . '/classes/util/WPSDStatsFactory.php');
require(dirname(__FILE__) . '/classes/util/excel/WPSDExcel.php');	
require(dirname(__FILE__) . '/classes/model/WPSDAdminConfigForm.php');
require(dirname(__FILE__) . '/classes/model/WPSDAdminConfigMetricsForm.php');
require(dirname(__FILE__) . '/classes/action/WPSDFrontEndAction.php');
require(dirname(__FILE__) . '/classes/action/WPSDAdminAction.php');
require(dirname(__FILE__) . '/classes/action/WPSDAdminConfigAction.php');
require(dirname(__FILE__) . '/classes/dao/WPSDUpgradeDao.php');
require(dirname(__FILE__) . '/classes/util/WPSDStatsRemoting.php');
require(dirname(__FILE__) . '/classes/util/metrics/user/WPSDUserMetrics.php');

/* Widgets */
require(dirname(__FILE__) . '/classes/widget/WPSDMetricsWidget.php');
require(dirname(__FILE__) . '/classes/widget/WPSDSocialProfilesWidget.php');
require(dirname(__FILE__) . '/classes/widget/WPSDTotalViewsWidget.php');
require(dirname(__FILE__) . '/classes/widget/WPSDMiniGraphWidget.php');
require(dirname(__FILE__) . '/classes/widget/WPSDAuthorWidget.php');

/**
 * wpsd_admin_include function.
 * 
 * @access public
 * @return void
 */
function wpsd_admin_include() {
	
	$user = wp_get_current_user();
	
	if($user->caps["administrator"] || wpsd_has_access()) {
	
		require(dirname(__FILE__) . '/classes/widget/admin/WPSDAdminWidget.php');
		require(dirname(__FILE__) . '/classes/widget/admin/WPSDDashboardWidget.php');
		require(dirname(__FILE__) . '/classes/widget/admin/WPSDOverviewDashboardWidget.php');
		require(dirname(__FILE__) . '/classes/widget/admin/WPSDClicksDashboardWidget.php');
		require(dirname(__FILE__) . '/classes/widget/admin/WPSDReferrersDashboardWidget.php');
		require(dirname(__FILE__) . '/classes/widget/admin/WPSDPostViewsDashboardWidget.php');
		require(dirname(__FILE__) . '/classes/widget/admin/WPSDSearchTermsDashboardWidget.php');
		require(dirname(__FILE__) . '/classes/widget/admin/WPSDCompeteDashboardWidget.php');
		require(dirname(__FILE__) . '/classes/widget/admin/WPSDTrendsDashboardWidget.php');
		//require(dirname(__FILE__) . '/classes/widget/admin/WPSDBlogPulseDashboardWidget.php');
		//require(dirname(__FILE__) . '/classes/widget/admin/WPSDBlogPulseConversationDashboardWidget.php');
		require(dirname(__FILE__) . '/classes/widget/admin/WPSDOptimizeDashboardWidget.php');
		require(dirname(__FILE__) . '/classes/widget/admin/WPSDAuthorDashboardWidget.php');
		require(dirname(__FILE__) . '/classes/util/WPSDSoaHelper.php');
	}
}

add_action('plugins_loaded', 'wpsd_admin_include');

if(is_admin()) {

	wp_enqueue_script('jquery'); // use jquery.
	
	$config = new WPSDAdminConfigForm();
	
	if(!$config->getWpsdDisableWidgets()) { 

		// Widgets only for admin.
		add_action('plugins_loaded', create_function('', '
		$user = wp_get_current_user();		
		if($user->caps["administrator"] || wpsd_has_access()){' .
		'global $wpsdClicksWidget; $wpsdClicksWidget = new WPSDClicksDashboardWidget();' .
		'global $wpStatsDashWidget; $wpStatsDashWidget = new WPSDDashboardWidget();' .
		'global $wpsdOverviewWidget; $wpsdOverviewWidget = new WPSDOverviewDashboardWidget();' .
		'global $wpsdRefWidget; $wpsdRefWidget = new WPSDReferrersDashboardWidget();' .
		'global $wpsdPostViewsWidget; $wpsdPostViewsWidget = new WPSDPostViewsDashboardWidget();' .
		'global $wpsdSearchTermsWidget; $wpsdSearchTermsWidget = new WPSDSearchTermsDashboardWidget();' .
		'global $wpsdCompeteWidget; $wpsdCompeteWidget = new WPSDCompeteDashboardWidget();' . 
		'global $wpsdTrendsWidget; $wpsdTrendsWidget = new WPSDTrendsDashboardWidget();' .
		'global $wpsdAuthorsWidget; $wpsdAuthorsWidget = new WPSDAuthorDashboardWidget();' .
		'global $wpsdOptimizeWidget; $wpsdOptimizeWidget = new WPSDOptimizeDashboardWidget();'
		.'}') );
		
		/* 		
		'global $wpsdBlogPulseWidget; $wpsdBlogPulseWidget = new WPSDBlogPulseDashboardWidget();' . 
		'global $wpsdBlogPulseConvWidget; $wpsdBlogPulseConvWidget = new WPSDBlogPulseConversationDashboardWidget();' . 
		*/
	}
}

/**
 * WPSD main.
 * @author dligthart <info@daveligthart.com>
 * @version 1.4.0
 * @package wp-stats-dashboard
 */
class WPSDMain extends WPSDWPPlugin {

	/**
	 * @var AdminAction admin action handler
	 */
	var $adminAction = null;

	/**
	 * @var FrontEndAction frontend action handler
	 */
	var $frontEndAction = null;

	 /**
	  * __construct()
	  */
	function WPSDMain($path) {
		
		$this->register_plugin('wp-stats-dashboard', $path);
		
		if (is_admin()) {

			$this->adminAction = new WPSDAdminAction($this->plugin_name, $this->plugin_base);
		}
		
		$this->frontEndAction = new WPSDFrontEndAction($this->plugin_name, $this->plugin_base);
	}
}

// Start app.
$wp_stats_dashboard = new WPSDMain(__FILE__);
?>