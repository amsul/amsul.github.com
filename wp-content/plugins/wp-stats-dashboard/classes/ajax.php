<?php 
/**
 * Load social media metrics asynchronously.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 2.7
 * @package wp-stats-dashboard
 */

if(!isset($_POST['_ajax_nonce'])) {
	
	require( dirname(__FILE__) . '/../../../../wp-load.php'); // wordpress context.
	
	global $current_user;
	
	if(!$current_user->caps['administrator'] && $_REQUEST['wpsd_update_cache']) {
		die(__('You need to be administrator to view this content', 'wpsd'));	
	}
}

if(!class_exists('Services_JSON')) {
	include_once('util/Services_JSON.php');
}

require_once('util/metrics/WPSDStats.php');
require_once('util/metrics/WPSDAlexaRank.php');
require_once('util/metrics/WPSDCompeteRank.php');
require_once('util/metrics/WPSDPageRank.php');
require_once('util/metrics/WPSDTechnoratiRank.php');
require_once('util/metrics/WPSDDeliciousRank.php');
require_once('util/metrics/WPSDGoogleSocialGraph.php');
//require_once('util/metrics/WPSDYahooRank.php');
require_once('util/metrics/WPSDGoogleBackLinks.php');
require_once('util/metrics/WPSDMozRank.php');
require_once('util/metrics/WPSDPostRank.php');
require_once('util/metrics/WPSDDigg.php');
require_once('util/metrics/WPSDSiteAge.php');
require_once('util/metrics/WPSDSiteValue.php');
require_once('util/metrics/WPSDEngagement.php');
require_once('util/metrics/WPSDTweetMeme.php');
require_once('util/metrics/WPSDReddit.php');
require_once('util/metrics/WPSDBacktype.php');
require_once('util/metrics/WPSDStumbleUpon.php');
require_once('util/metrics/WPSDBlogCatalog.php');
require_once('util/metrics/WPSDBing.php');
require_once('util/metrics/WPSDTwitter.php');
require_once('util/metrics/WPSDLinkedIn.php');
require_once('util/metrics/WPSDBitly.php');
require_once('util/metrics/WPSDKlout.php');
require_once('util/metrics/WPSDFeedBurner.php');
require_once('util/metrics/WPSDLastFm.php');
require_once('util/metrics/WPSDFaceBook.php');
require_once('util/metrics/WPSDFlickr.php');
require_once('util/metrics/WPSDHits.php');
require_once('util/metrics/WPSDDiigo.php');
require_once('util/metrics/WPSDBrazenCareerist.php');
require_once('util/metrics/WPSDNewsVine.php');
require_once('util/metrics/WPSDYoutube.php');
require_once('util/metrics/WPSDMyspace.php');
require_once('util/metrics/WPSDWordPress.php');
require_once('util/metrics/WPSDPosterous.php');
require_once('util/metrics/WPSDPlancast.php');
require_once('util/metrics/WPSDLazyfeed.php');
require_once('util/metrics/WPSDSphinn.php');
require_once('util/metrics/WPSDJaiku.php');
require_once('util/metrics/WPSDKoornk.php');
require_once('util/metrics/WPSDPlurk.php');
require_once('util/metrics/WPSDHyves.php'); 
require_once('util/metrics/WPSDXbox.php');
require_once('util/metrics/WPSDFoursquare.php');
require_once('util/metrics/WPSDDisqus.php');
require_once('util/metrics/WPSDBlippr.php');
require_once('util/metrics/WPSDAmplify.php');
require_once('util/metrics/WPSDRunkeeper.php');
require_once('util/metrics/WPSDBlippy.php');
require_once('util/metrics/WPSDWeRead.php');
require_once('util/metrics/WPSDGoogleBuzz.php');
require_once('util/metrics/WPSDEmpireAvenue.php');
require_once('util/metrics/WPSDFriendFeed.php');
//require_once('util/metrics/WPSDSociety.php'); // website closed.
require_once('util/metrics/WPSDMylikes.php');
require_once('util/metrics/WPSDPluginStats.php');
require_once('util/metrics/WPSDBattlenet.php');
require_once('util/metrics/WPSDEducoPark.php');
//require_once('util/metrics/WPSDYahooBuzz.php');
require_once('util/metrics/WPSDVimeo.php');
require_once('util/metrics/WPSDIdentica.php');
require_once('util/metrics/WPSDPlaxo.php');
//require_once('util/metrics/WPSDBlogPulse.php');
require_once('util/metrics/WPSDNetlog.php');
require_once('util/metrics/WPSD99Designs.php');
require_once('util/metrics/WPSDGravatar.php');
require_once('util/metrics/WPSDQuora.php');
require_once('util/metrics/WPSDGetglue.php');
require_once('util/metrics/WPSDArchive.php');
require_once('util/metrics/WPSDGoogleBot.php');
require_once('util/metrics/WPSDW3Validator.php');
require_once('util/metrics/WPSDSocialMention.php');
require_once('util/metrics/WPSDGooglePlus.php');
require_once('util/metrics/WPSDHunch.php');
require_once('util/metrics/WPSDPeerIndex.php');
//require_once('util/metrics/WPSDSixent.php');
require_once('model/WPSDAdminConfigMetricsForm.php');

/**
 * wpsd_load_stats function.
 * 
 * @access public
 * @return void
 */
function wpsd_load_stats() {
		
	// Sixent.(no public metrics yet...)
	//	$sxt = new WPSDSixent();
	
	if(isset($_REQUEST['_ajax_nonce'])) {
	
		$opts = null;
	
		$widget_opts = get_option('widget_wpsdmetricswidget');
		
		if(null != $widget_opts) {
		
			foreach($widget_opts as $opt) {
				
				if(null != $opt && count($opt) > 0) {
					
					if(is_array($opt)) {
						foreach($opt as $key=>$value) {
							
							if($value == 'on') {
								
								$opts[$key] = $value;
							}
						}
					}
				}	
			}
		}					
?><style type="text/css">
#wpsd-overview-table, #wpsd-overview-table td, #wpsd-overview-table tr, #wpsd-overview-table td span {
border:0 solid black;
font-family: inherit;
font-size: 12px;
font-weight:inherit;
margin:0;
padding:0;
}
</style>
<?php 	
	
		echo '<table id="wpsd-overview-table" border="0" style="width:100%;">';
		
		
	} else {
		
		$config_form = new WPSDAdminConfigForm();
	
		$config = new WPSDAdminConfigMetricsForm();
		
		$opts = $config->getOpts();
				
			//http://gravatar.com/avatar/e8e299893ddd1df022e8f8bf1dc5af3f
		
		$current_user = wp_get_current_user();
			
		if(null != $current_user) {
			
			$opts_count = $config_form->getActiveCount();
						
			$total_opts_count = $config_form->getTotalCount();
			
			$perc = round(($opts_count / $total_opts_count) * 100);
			
			$gravatar = new WPSDGravatar();
			
			$location = $gravatar->getLocation();
			
			$fullname = $gravatar->getFullName();

echo '<style type="text/css">.percent{
display: block;
float: left;
background:url('.WPSD_PLUGIN_URL.'/resources/images/percent.png) top left no-repeat;
height: 17px;
width:75px;
}
.percent span{
background:url('.WPSD_PLUGIN_URL.'/resources/images/percent.png) bottom right no-repeat;
display:block;
float:left;
height:17px;
}</style>';

			echo '<div style="margin-left:0px; margin-bottom:10px; height:52px; min-height:52px;-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px;  padding:5px 5px; width:97%; font-weight: bold; color:#000 !important; " class="highlight"><a href="'.$gravatar->getAddress().'" target="_blank" title="Gravatar profile" style="float:left;padding-right:5px;"><img width="50" src="'.$gravatar->getAvatar().'" title="Identity online avatar" style="-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; " /></a><span style="margin-left:10px;">&nbsp;</span><span class="percent"><span style="width:'.$perc.'%">'.$perc.'%</span></span>'.__('connected','wpsd');
			
			echo '<span style="padding:5px 5px;"><a href="http://www.daveligthart.com/products/smo/?utm_source=wpsd&utm_medium=optimize_link&utm_campaign=wpstatsdashboard&utm_content='.urlencode($fullname).'&wpsd_user='.urlencode($fullname).'" target="_blank" title="Social Optimize?">optimize?</a></span>';
			
			if('' != $fullname) { 
				
				echo "<div style=\"margin:10px 0px;\">{$fullname}&nbsp;@&nbsp;{$location}</div>";
				
					
				echo '<input style="float:right;margin:-15px 0;" type="button" value="' . __('Reload', 'wpsd') . '" class="button" id="btn_wpsd_reload_1" onclick="javascript:wpsd_load_stats_refresh();" />';
			}
			else {
				
				echo '<input style="float:right;" type="button" value="' . __('Reload', 'wpsd') . '" class="button" id="btn_wpsd_reload_1" onclick="javascript:wpsd_load_stats_refresh();" />';
		
			}
			echo '</div>';			
	
		}
			
		echo '<style type="text/css">#wpsd-overview-table, #wpsd-overview-table td, #wpsd-overview-table tr {border:0px solid black; margin:0;padding:0;}#wpsd-overview-table img {width:23px;}</style>';

		echo '<table id="wpsd-overview-table" border="0" style="border-top: 0px solid #CCC; width:100%;">';
	}
	
	if($opts == null || $opts['engagement']) {
		
		// Get engagement.
		$eng = new WPSDEngagement();
		
		wpsd_helper_add_row('feed', '<span>Engagement:</span>', 
		'<a href="#" title="Comment count" rel="nofollow">' . $eng->getEngagement() . 
		'</a> | <a href="http://en.wikipedia.org/wiki/Pingback" title="Pingback" target="_blank" rel="nofollow">' . $eng->getPingback() . 
		'</a> | <a href="http://en.wikipedia.org/wiki/Trackback" title="Trackback" target="_blank" rel="nofollow">' . $eng->getTrackback() . 
		'</a>', 1);				
		
		unset($eng);
	}
	
	if($opts == null || $opts['views'])	{
		
		// Daily views.
		$views = new WPSDHits();
	
		wpsd_helper_add_row('views', '<span>'.__('Views', 'wpsd').':</span>', 
			sprintf('<a href="%s" target="_blank" title="Views | Count Daily" rel="nofollow">%s</a>', '#', $views->getViews()) . ' | ' .
			sprintf('<a href="%s" target="_blank" title="Views | All time" rel="nofollow">%s</a>', '#', $views->getViewsAllTime()), 1);
	}
	
	if($opts == null || $opts['age']){
		
		// Age.
		$siteage = new WPSDSiteAge(get_bloginfo('url'));
		
		$age = $siteage->getAge(get_bloginfo('url'));
	
		wpsd_helper_add_row('age', 	'<span>Site age:</span>', 
		'<a href="'.$siteage->getAddress().'" title="whois" target="_blank" rel="nofollow">' . 
		number_format($age, 4) . '</a>', 2);
		
		unset($siteage);
	}
	
	if($opts == null || $opts['pagerank']){
		
		// Get google pagerank.
		$pr = new WPSDPageRank();
		
		wpsd_helper_add_row('pr', 	'<span>PageRank:</span>', 
			sprintf('<a href="http://www.google.com/technology/pigeonrank.html" title="google pagerank | pagerank" target="_blank" rel="nofollow">%d</a> / %d', 
			$pr->getPagerank(), 10), 2 );
			
		unset($pr);
	}
	
	if($opts == null || $opts['backlinks']){
		
		// Backlinks.
		$bl = new WPSDGoogleBackLinks(get_bloginfo('url'), true);
		
		$inbound = $bl->getRank();
	
		wpsd_helper_add_row('pr', 	'<span>Backlinks:</span>', 
			sprintf('<a href="%s" title="Google Search | backlinks" target="_blank" rel="nofollow">%s</a>' ,
			$bl->getAddress(), $inbound) . ' | ' . 
			sprintf('<a href="%s" title="Google Blog Search | backlinks" target="_blank" rel="nofollow">%s</a>' ,
			$bl->getBlogSearchAddress(), $bl->getBsBacklinks()) . ' | ' . 
			sprintf('<a href="%s" title="Google Ego Search | results based on your name" target="_blank" rel="nofollow">%s</a>' ,
			$bl->getEgoSearchAddress(), $bl->getEgoSearchResults()),1 );
			
		unset($bl);
	}
	
	if($opts == null || $opts['socialgraph']){
		
		// Get google social graph in out xfn relations.
		$gsg = new WPSDGoogleSocialGraph(get_bloginfo('url'));
	
		wpsd_helper_add_row('pr', 	'<span>Social Graph:</span>', 
			sprintf('<a href="%s" title="google social graph | relations incoming and outgoing" target="_blank" rel="nofollow">%d | %d</a>', 
			$gsg->getHomeAddress(), $gsg->getNodeInCount(), $gsg->getNodeOutCount() ), 2 );
			
		unset($gsg);
	}
	
	if($opts == null || $opts['googlebot']){
		
		// Googlebot.
		$bot = new WPSDGoogleBot();
	
		wpsd_helper_add_row('pr', 	'<span>GoogleBot:</span>', 
			sprintf('<a href="%s" title="google bot | last visit date" target="_blank" rel="nofollow">%s</a>', 
			$bot->getAddress(), $bot->getVisit()), 2 );
			
		unset($bot);
	}
	
	if($opts == null || $opts['googleplus']) {
	
		// Google Plus.
		$plus = new WPSDGooglePlus();
		
		wpsd_helper_add_row('googleplus', '<span>Google Plus:</span>', 
			sprintf('<a href="%s" target="_blank" title="Google Plus Followers" rel="nofollow">%s</a> | <a href="%s" target="_blank" title="Google Plus Following" rel="nofollow">%s</a>', 
			$plus->getAddress(), $plus->getFollowers(), $plus->getAddress(), $plus->getFollowing()), 2);
			
		unset($plus);		
	}
	
	if($opts == null || $opts['buzz']){	
		
		// Google Buzz.
		$buzz = new WPSDGoogleBuzz();
	
		wpsd_helper_add_row('buzz', '<span>Google Buzz:</span>', 
			sprintf('<a href="%s" target="_blank" title="Google Buzz" rel="nofollow">%s</a>', 
			$buzz->getAddress(), $buzz->getBuzz()), 2);
			
		unset($buzz);
	}	
	
	
	if($opts == null || $opts['alexa']){
	
		// Get alexa rank.
		$alexa = new WPSDAlexaRank(get_bloginfo('url'), true); //get alexa traffic rank.

		wpsd_helper_add_row('alexa','<span>Alexa:</span>', 
			sprintf('<a href="http://www.alexa.com/data/details/traffic_details/%s" title="alexa | rank and links incoming" target="_blank" rel="nofollow">%s | %d</a>', 
			get_bloginfo('url'), $alexa->getRank(), $alexa->getLinksIn() ), 1 );

		unset($alexa);
	}
	
	if($opts == null || $opts['technorati']){	
		
		// Get technorati rank.
		$tr = new WPSDTechnoratiRank(get_bloginfo('url'), true); // get technorati rank.
	
		wpsd_helper_add_row('tr', 	'<span>Technorati:</span>', 
			sprintf('<a href="%s" title="technorati | rank" target="_blank" rel="nofollow">%s</a>', 
			$tr->getAddress(), $tr->getRank() ), 2 );

		unset($tr);
	}
	
	if($opts == null || $opts['delicious']){
		
		// Get delicious linksin.
		$ds = new WPSDDeliciousRank(get_bloginfo('url'), true); // get number of delicious bookmarks.

		wpsd_helper_add_row('ds',
			'<span>Del.icio.us:</span>', 
			sprintf('<a href="%s" title="Delicious | Search Result Count" target="_blank" rel="nofollow">%s</a>', $ds->getAddress(), $ds->getResultCount() ) . ' | ' .
			sprintf('<a href="%s" title="Delicious | Url Result Count" target="_blank" rel="nofollow">%s</a>', $ds->getAddress(), $ds->getUrlResultCount() ) . ' | ' .
			sprintf('<a href="%s" title="Delicious | Link Count" target="_blank" rel="nofollow">%s</a>', $ds->getAddress(), $ds->getLinksIn() ), 1 );

		unset($ds);
	}
	
	if($opts == null || $opts['compete']){

		// Get compete.com rank.
		$cc = new WPSDCompeteRank(get_bloginfo('url'), true); // get compete.com rank.

		wpsd_helper_add_row('cc','<span>Compete:</span>', 
			sprintf('<a href="%s" title="compete.com | rank" target="_blank" rel="nofollow">%s</a>', 
			$cc->getHomeAddress(), $cc->getRank()),2 );

		unset($cc);
	}
	
	/*if($opts == null || $opts['yahoo']){
		
		// Get yahoo inbound.
		$yi = new WPSDYahooRank(get_bloginfo('url'), true);

		wpsd_helper_add_row('yahoo','<span>Yahoo:</span>', sprintf('<a href="%s" title="yahoo | links incoming" target="_blank" rel="nofollow">%s</a>', $yi->getHomeAddress(), $yi->getLinksIn() ), 1 );

		unset($yi);
	}*/
	
	if($opts == null || $opts['mozrank']){
		
		// Get Moz Rank.
		$mr = new WPSDMozRank();
		
		wpsd_helper_add_row('moz', 	'<span>MozRank:</span>', sprintf('<a href="%s" title="mozrank | rank" target="_blank" rel="nofollow">%s</a> / %d', $mr->getHomeAddress(), $mr->mozRank(get_bloginfo('url') ), 10 ), 2 );
		
		unset($mr);
	}
	
	if($opts == null || $opts['postrank']){
		
		// Get Post Rank.
		$postrank = new WPSDPostRank();
		
		wpsd_helper_add_row('ptr', 	'<span>PostRank:</span>', sprintf('<a href="%s" title="postrank | rank" target="_blank" rel="nofollow">%s</a> / %d',$postrank->getHomeAddress(), $postrank->getPostRank(get_bloginfo('url')), 10 ),1 );
		
		unset($postrank);
	}
	
	if($opts == null || $opts['archive'])	{
		
		// Archive.
		$archive = new WPSDArchive();
	
		wpsd_helper_add_row('archive', '<span>Archive:</span>', 
			sprintf('<a href="%s" target="_blank" title="Archive | Count Daily" rel="nofollow">%s</a>', $archive->getAddress(), $archive->getResults()), 2);
		
		unset($archive);
	}
	
	if($opts == null || $opts['w3c'])	{
		
		// W3 Validator.
		$w3 = new WPSDW3Validator();
	
		wpsd_helper_add_row('w3c', '<span>W3 Validator:</span>', 
			sprintf('<a href="%s" target="_blank" title="Validator Errors | Count" rel="nofollow">%s</a>', $w3->getAddress(), $w3->getErrors()), 2);
		
		unset($w3);
	}
	
	if($opts == null || $opts['bing']){
		// Bing.
		$bing = new WPSDBing(get_bloginfo('url'), true);
	
		wpsd_helper_add_row('bing', '<span>Bing:</span>', 
			sprintf('<a href="%s" title="bing | results" target="_blank" rel="nofollow">%s</a>', $bing->getAddress(), $bing->getRank()), 2 );
		
		unset($bing);
	}
	
	if($opts == null || $opts['socialmention']){	
		
		$sm = new WPSDSocialMention();
	
		wpsd_helper_add_row('socialmention', '<span>SocialMention:</span>', 
			sprintf('<a href="%s" target="_blank" title="SocialMention | Mentions" rel="nofollow">%s</a>', 
			$sm->getAddress(), $sm->getMentions()) , 1);
			
		unset($sm);
	}	
	
	if($opts == null || $opts['digg']){
		
		// Digg.
		$digg = new WPSDDigg();
	
		wpsd_helper_add_row('digg', '<span>Digg:</span>', 
			sprintf('<a href="%s" title="Digg | Followers" target="_blank" rel="nofollow">%s</a>', 
				$digg->getAddress(), $digg->getFollowers()) . ' | ' . 
			sprintf('<a href="%s" title="Digg | Following" target="_blank" rel="nofollow">%s</a>', 
				$digg->getAddress(), $digg->getFollowing()) . ' | ' .
			sprintf('<a href="%s" title="Digg | Diggs" target="_blank" rel="nofollow">%s</a>', 
				$digg->getAddress(), $digg->getDiggs()) . ' | ' . 
			sprintf('<a href="%s" title="Digg | Comments" target="_blank" rel="nofollow">%s</a>', 
				$digg->getAddress(), $digg->getComments()), 2 );
		
		unset($digg);
	}
	
	if($opts == null || $opts['twitter']){
		
		// Tweets.
		$tm = new WPSDTweetMeme(get_bloginfo('url'), true);
		
		// Twitter.
		$twit = new WPSDTwitter();
	
		wpsd_helper_add_row('tw', 	
			'<span>Twitter</span>', 
			sprintf('<a href="%s" title="tweetmeme | tweets" target="_blank" rel="nofollow">%s</a>', $tm->getAddress(), $tm->getCount()) . ' | ' . 
			sprintf('<a href="%s" title="twitter | followers" target="_blank" rel="nofollow">%s</a>', $twit->getAddress(), $twit->getFollowers()) . ' | ' .  
			sprintf('<a href="%s" title="twitter | following" target="_blank" rel="nofollow">%s</a>', $twit->getAddress(), $twit->getFollowing()) . ' | '.  
			sprintf('<a href="%s" title="twitter | list count" target="_blank" rel="nofollow">%s</a>', $twit->getAddress(), $twit->getLists()) . ' | '.  
			sprintf('<a href="%s" title="twitter | tweet count" target="_blank" rel="nofollow">%s</a>', $twit->getAddress(), $twit->getTweets()),1);
		
		unset($tm);
		
		unset($twit);
	}
	
	if($opts == null || $opts['reddit']){
		
		// Reddit score, comments.
		$reddit = new WPSDReddit(get_bloginfo('url'), true);
	
		wpsd_helper_add_row('rd',
			'<span>Reddit:</span>', 
			sprintf('<a href="%s" title="reddit | rank and comments" target="_blank" rel="nofollow">%d | %d</a>', $reddit->getHomeAddress(), $reddit->getRank() , $reddit->getComments()) . ' | ' .
			sprintf('<a href="%s" title="reddit | karma" target="_blank" rel="nofollow">%d</a>', $reddit->getProfileAddress(), $reddit->getKarma()) , 2);
		
		unset($reddit);
	}
	
	if($opts == null || $opts['backtype']){
	
		// Backtype.
		$bt = new WPSDBacktype(get_bloginfo('url'), true);
	
		wpsd_helper_add_row('bt',   '<span>Backtype:</span>', 
		sprintf('<a href="%s" title="Backtype | Comments and tweets" target="_blank" rel="nofollow">%d | %d</a> | <a href="%s" title="BackTweets | Score" target="_blank" rel="nofollow">%d</a>', $bt->getHomeAddress(), $bt->getComments(), $bt->getTweets(), $bt->getBackTweetsAddress(), $bt->getScore()) , 1);
		
		unset($bt);
	}
	
	if($opts == null || $opts['stumbleupon']){
		
		// StumbleUpon.
		$su = new WPSDStumbleUpon(get_bloginfo('url'), true);
		
		wpsd_helper_add_row('su', 
			'<span>StumbleUpon:</span>', 
			sprintf('<a href="%s" title="StumbleUpon | Views" target="_blank" rel="nofollow">%s</a>', $su->getAddress(), $su->getViews()) . ' | ' . 
			sprintf('<a href="%s" title="StumbleUpon | Followers" target="_blank" rel="nofollow">%s</a>', $su->getProfileAddress(), $su->getFollowers()) . ' | ' . 
			sprintf('<a href="%s" title="StumbleUpon | Following" target="_blank" rel="nofollow">%s</a>', $su->getProfileAddress(), $su->getFollowing()), 2 );
		
		unset($su);
	}
	
	if($opts == null || $opts['blogcatalog']){
		
		// BlogCatalog.
		$bc = new WPSDBlogCatalog(get_bloginfo('url'), true); 
	
		/*wpsd_helper_add_row('bc', '<span>BlogCatalog:</span>', 
			sprintf('<a href="%s" title="blogcatalog.com | rank and views" target="_blank" rel="nofollow">%s | %s</a>', 
			$bc->getHomeAddress(), $bc->getRank(), $bc->getViews() ), 1 );*/
		
		wpsd_helper_add_row('bc', '<span>BlogCatalog:</span>', 
			sprintf('<a href="%s" title="BlogCatalog | Blog" target="_blank" rel="nofollow">%s</a>', $bc->getHomeAddress(), $bc->getBlog()) . ' | ' . 
			sprintf('<a href="%s" title="BlogCatalog | Followers" target="_blank" rel="nofollow">%s</a>', $bc->getHomeAddress(), $bc->getFollowers()) . ' | ' . 
			sprintf('<a href="%s" title="BlogCatalog | Following" target="_blank" rel="nofollow">%s</a>', $bc->getHomeAddress(), $bc->getFollowing()), 2 );
		
		unset($bc);
	}
		
	if($opts == null || $opts['linkedin']){
		
		// LinkedIn.
		$li = new WPSDLinkedIn(true);
	
		wpsd_helper_add_row('li', '<span>LinkedIn:</span>', sprintf('<a href="%s" title="LinkedIn | connections" target="_blank" rel="nofollow">%s</a>', $li->getAddress(), $li->getConnectionCount()), 1 );
		
		wpsd_helper_add_row('li', '<span>LinkedIn Company:</span>', sprintf('<a href="%s" title="LinkedIn Company | new hires" target="_blank" rel="nofollow">%s</a> | <a href="%s" title="LinkedIn Company | stats" target="_blank" rel="nofollow">view stats</a>', $li->getCompanyAddress(), $li->getCompanyNewHires(), $li->getCompanyStatsAddress()), 2 );
		
		unset($li);
	}
	
	if($opts == null || $opts['bitly']){
		
		// Bitly.
		$bitly = new WPSDBitly(get_bloginfo('url'), true);
	
		wpsd_helper_add_row('bitly','<span>Bit.ly:</span>', sprintf('<a href="http://bit.ly" target="_blank" title="bit.ly url shortening | clicks" rel="nofollow">%d</a> | <a href="%s" title="bit.ly your short url" target="_blank" rel="nofollow">%s</a>', $bitly->getClicks(), $bitly->getShortUrl(), $bitly->getShortUrl()), 2 );
		
		unset($bitly);
	}
	
	if($opts == null || $opts['klout']){
		
		// Klout.
		$kl = new WPSDKlout();
			
		wpsd_helper_add_row('kl',   '<span>Klout:</span>', 
			sprintf('<a href="%s" target="_blank" title="klout | score" rel="nofollow">%s</a> | 
			<a href="%s" target="_blank" title="klout | type | %s" rel="nofollow">%s</a>', 
			$kl->getAddress(), 
			$kl->getRank(), 
			$kl->getAddress(), 
			$kl->getDesc(), 
			$kl->getType()), 1);
		
		unset($kl);
	}
	
	if($opts == null || $opts['peerindex']){
		
		// PeerIndex.
		$pi = new WPSDPeerIndex();
			
		wpsd_helper_add_row('peerindex',   '<span>PeerIndex:</span>', 
			sprintf('<a href="%s" target="_blank" title="PeerIndex | Score" rel="nofollow">%s</a>', 				
				$pi->getAddress(), 
				$pi->getScore()), 1);
		
		unset($pi);
	}
	
	if($opts == null || $opts['feedburner']) {
		
		// Feedburner.
		$fb = new WPSDFeedBurner();
	
		wpsd_helper_add_row('fb',   '<span>FeedBurner:</span>', sprintf('<a href="%s" target="_blank" title="FeedBurner | Circulation" rel="nofollow">%s</a>', $fb->getAddress(), $fb->getCirculation()) . ' | ' . sprintf('<a href="%s" target="_blank" title="FeedBurner | Hits" rel="nofollow">%s</a>', $fb->getAddress(), $fb->getHits()), 2 );
		
		unset($fb);
	}
	
	if($opts == null || $opts['lastfm']) {	
		
		// Last.fm.
		$lfm = new WPSDLastFm();
	
		wpsd_helper_add_row('lfm',   '<span>Last.fm:</span>', 
		sprintf('<a href="%s" target="_blank" title="Last.fm | Friends" rel="nofollow">%s</a>', 
		$lfm->getAddress(), $lfm->getFriends()) . ' | ' . 
		sprintf('<a href="%s" target="_blank" title="Last.fm | Tracks Played" rel="nofollow">%s</a>', 
		$lfm->getAddress(), $lfm->getPlays()). ' | ' . 
		sprintf('<a href="%s" target="_blank" title="Last.fm | Playlists" rel="nofollow">%s</a>', 
		$lfm->getAddress(), $lfm->getPlaylists()). ' | ' . 
		sprintf('<a href="%s" target="_blank" title="Last.fm | Loved Tracks" rel="nofollow">%s</a>', 
		$lfm->getAddress(), $lfm->getLovedTracks()) . ' | ' . 
		sprintf('<a href="%s" target="_blank" title="Last.fm | Posts" rel="nofollow">%s</a>', 
		$lfm->getAddress(), $lfm->getPosts()). ' | ' . 
		sprintf('<a href="%s" target="_blank" title="Last.fm | Shouts" rel="nofollow">%s</a>', 
		$lfm->getAddress(), $lfm->getShout()), 1 );
		
		unset($lfm);
	}
	
	if($opts == null || $opts['facebook']){
		
		// Facebook.
		$fab = new WPSDFaceBook();
	
		wpsd_helper_add_row('fab',   '<span>Facebook Fans:</span>', 
			sprintf('<a href="%s" target="_blank" title="Facebook | Fans" rel="nofollow">%s</a>', $fab->getAddress(), $fab->getFans()), 2 );
		
		$metrics = $fab->getLikeMetrics();
		
		if(null != $metrics) {	
			
			wpsd_helper_add_row('fab',   '<span>Facebook Like:</span>', 
				sprintf('<a href="%s" target="_blank" title="Facebook | Homepage Likes" rel="nofollow">%s</a> | <a href="%s" target="_blank" title="Facebook | Homepage Total" rel="nofollow">%s</a> | <a href="%s" target="_blank" title="Facebook | Homepage Share" rel="nofollow">%s</a> | <a href="%s" target="_blank" title="Facebook | Homepage Click" rel="nofollow">%s</a>', 
				$fab->getAddress(), $metrics['like'],
				$fab->getAddress(), $metrics['total'],
				$fab->getAddress(), $metrics['share'],
				$fab->getAddress(), $metrics['click']
				), 1 );
		}
		
		unset($fab);
	}
	
	if($opts == null || $opts['flickr']){	
		
		// Flickr.
		$fr = new WPSDFlickr();
	
		wpsd_helper_add_row('fr',   '<span>Flickr:</span>', sprintf('<a href="%s" target="_blank" title="Flickr | Count" rel="nofollow">%s</a>', $fr->getAddress(), $fr->getCount()), 1 );
		
		unset($fr);
	}
	
	if($opts == null || $opts['diigo'])	{
		
		// Diigo.
		$dg = new WPSDDiigo(true);
	
		wpsd_helper_add_row('dg', '<span>Diigo:</span>', sprintf('<a href="%s" target="_blank" title="Diigo | Bookmarks" rel="nofollow">%s</a> | <a href="%s" target="_blank" title="Diigo | Followers" rel="nofollow">%s</a> | <a href="%s" target="_blank" title="Diigo | Following" rel="nofollow">%s</a>', $dg->getAddress(), $dg->getBookmarks(), $dg->getAddress(), $dg->getFollowers(), $dg->getAddress(), $dg->getFriends()), 2 );
		
		unset($dg);
	}
	
	if($opts == null || $opts['brazencareerist']){	
		
		// BrazenCareerist.
		$brazen = new WPSDBrazenCareerist();
	
		wpsd_helper_add_row('br', '<span>Brazen Careerist:</span>', sprintf('<a href="%s" target="_blank" title="Brazen Careerist | Fans" rel="nofollow">%s</a> | <a href="%s" target="_blank" title="Brazen Careerist | Following" rel="nofollow">%s</a> | <a href="%s" target="_blank" title="Brazen Careerist | Networks" rel="nofollow">%s</a>', $brazen->getAddress(), $brazen->getFans(), $brazen->getAddress(), $brazen->getFollowing(), $brazen->getAddress(), $brazen->getNetworks()), 1 );
		
		unset($brazen);
	}
	
	if($opts == null || $opts['newsvine']){
		
		// Newsvine.
		$nv = new WPSDNewsVine();
	
		wpsd_helper_add_row('nv', '<span>Newsvine:</span>', sprintf('<a href="%s" target="_blank" title="NewsVine | Articles" rel="nofollow">%s</a> | <a href="%s" target="_blank" title="NewsVine | Links" rel="nofollow">%s</a>', $nv->getAddress(), $nv->getArticles(), $nv->getAddress(), $nv->getLinks()), 2 );
		
		unset($nv);
	}
	
	if($opts == null || $opts['youtube']) {	
		
		// Youtube.
		$yt = new WPSDYoutube();
	
		wpsd_helper_add_row('yt', '<span>Youtube:</span>', 
			sprintf('<a href="%s" target="_blank" title="Youtube | Subscribers" rel="nofollow">%s</a> 
			| <a href="%s" target="_blank" title="Youtube | Views" rel="nofollow">%s</a>', 
			$yt->getAddress(), 
			$yt->getSubscribers(), 
			$yt->getAddress(), 
			$yt->getViews()), 1 );
		
		unset($yt);
	}
	
	if($opts == null || $opts['myspace']){	
		
		// Myspace.
		$ms = new WPSDMyspace();
	
		wpsd_helper_add_row('ms', '<span>Myspace:</span>', sprintf('<a href="%s" target="_blank" title="Myspace | Friends" rel="nofollow">%s</a>', $ms->getAddress(), $ms->getFriends()), 2);
		
		unset($ms);
	}
	
	if($opts == null || $opts['wordpress'])	{
		
		// WordPress.
		$wp = new WPSDWordPress();
	
		wpsd_helper_add_row('wp', '<span>WordPress:</span>', sprintf('<a href="%s" target="_blank" title="WordPress | Likes" rel="nofollow">%s</a>', $wp->getAddress(), $wp->getLikePosts()), 1);
		
		unset($wp);
	}
	
	if($opts == null || $opts['posterous'])	{
		
		// Posterous.
		$ps = new WPSDPosterous();
	
		wpsd_helper_add_row('ps', '<span>Posterous:</span>', sprintf('<a href="%s" target="_blank" title="Posterous | Subscribers" rel="nofollow">%s</a>', $ps->getAddress(), $ps->getSubscribers()), 2);
		
		unset($ps);
	}
	
	if($opts == null || $opts['plancast']){
		
		// Plancast.
		$pc = new WPSDPlancast();
	
		wpsd_helper_add_row('pc', '<span>Plancast:</span>', sprintf('<a href="%s" target="_blank" title="Plancast | Subscribers" rel="nofollow">%s</a>', $pc->getAddress(), $pc->getSubscribers()) . ' | ' . sprintf('<a href="%s" target="_blank" title="Plancast | Subscriptions" rel="nofollow">%s</a>', $pc->getAddress(), $pc->getSubscriptions()), 1);
		
		unset($pc);
	}
	
	if($opts == null || $opts['lazyfeed']){
		
		// Lazyfeed.
		$lf = new WPSDLazyfeed();
	
		wpsd_helper_add_row('lf', '<span>Lazyfeed:</span>', sprintf('<a href="%s" target="_blank" title="Lazyfeed | Followers" rel="nofollow">%s</a>', $lf->getAddress(), $lf->getFollowers()) . ' | ' . sprintf('<a href="%s" target="_blank" title="Lazyfeed | Following" rel="nofollow">%s</a>', $lf->getAddress(), $lf->getFollowing()), 2);
		
		unset($lf);
	}
	
	if($opts == null || $opts['sphinn']){	
		
		// Sphinn.
		$spn = new WPSDSphinn();
	
		wpsd_helper_add_row('spn', '<span>Sphinn:</span>', sprintf('<a href="%s" target="_blank" title="Sphinn | Topics" rel="nofollow">%s</a>', $spn->getAddress(), $spn->getTopics()) . ' | ' . sprintf('<a href="%s" target="_blank" title="Sphinn | Comments" rel="nofollow">%s</a>', $spn->getAddress(), $spn->getComments()) . ' | ' . sprintf('<a href="%s" target="_blank" title="Sphinn | Hot Topics" rel="nofollow">%s</a>', $spn->getAddress(), $spn->getHot()) . ' | ' . sprintf('<a href="%s" target="_blank" title="Sphinn | Cast" rel="nofollow">%s</a>', $spn->getAddress(), $spn->getCast()), 1);
		
		unset($spn);
	}
	
	if($opts == null || $opts['jaiku']){	
	
		// Jaiku.
		$jk = new WPSDJaiku();
	
		wpsd_helper_add_row('jk', '<span>Jaiku:</span>', sprintf('<a href="%s" target="_blank" title="Jaiku | Contacts" rel="nofollow">%s</a>', $jk->getAddress(), $jk->getContacts()), 2);
	
		unset($jk);
	}
	
	if($opts == null || $opts['koornk']){	

		// Koornk.
		$knk = new WPSDKoornk();

		wpsd_helper_add_row('knk', '<span>Koornk:</span>', sprintf('<a href="%s" target="_blank" title="Koornk | Followers" rel="nofollow">%s</a>', $knk->getAddress(), $knk->getFollowers()) . ' | ' . sprintf('<a href="%s" target="_blank" title="Koornk | Friends" rel="nofollow">%s</a>', $knk->getAddress(), $knk->getFriends()) , 1);

		unset($knk);
	}
	
	if($opts == null || $opts['plurk']){	
		
		// Plurk.
		$plk = new WPSDPlurk();
	
		wpsd_helper_add_row('plk', '<span>Plurk:</span>', sprintf('<a href="%s" target="_blank" title="Plurk | Plurks" rel="nofollow">%s</a>', $plk->getAddress(), $plk->getPlurks()) . ' | ' . sprintf('<a href="%s" target="_blank" title="Plurk | Karma" rel="nofollow">%s</a>', $plk->getAddress(), $plk->getKarma()) . ' | ' . sprintf('<a href="%s" target="_blank" title="Plurk | Friends" rel="nofollow">%s</a>', $plk->getAddress(), $plk->getFriends()) . ' | ' . sprintf('<a href="%s" target="_blank" title="Plurk | Fans" rel="nofollow">%s</a>', $plk->getAddress(), $plk->getFans()) . ' | ' . sprintf('<a href="%s" target="_blank" title="Plurk | Responses" rel="nofollow">%s</a>', $plk->getAddress(), $plk->getResponses()), 2);
		
		unset($plk);
	}
	
	if($opts == null || $opts['hyves']){	
		
		// Hyves.
		$hyves = new WPSDHyves();

		wpsd_helper_add_row('hyves', '<span>Hyves:</span>', 
		sprintf('<a href="%s" target="_blank" title="Hyves | Friends" rel="nofollow">%s</a>', 
		
		$hyves->getAddress(), $hyves->getFriends()), 1);
		
		unset($hyves);
	}
	
	if($opts == null || $opts['xbox']){	
		
		// Xbox live
		$xbox = new WPSDXbox();
	
		wpsd_helper_add_row('xbox', '<span>Xbox:</span>', 
		sprintf('<a href="%s" target="_blank" title="Xbox Live | Score" rel="nofollow">%s</a>', 
		$xbox->getAddress(), $xbox->getScore()) . ' | ' . 
		sprintf('<a href="%s" target="_blank" title="Xbox Live | Reputation" rel="nofollow">%s</a>', 
		$xbox->getAddress(), $xbox->getReputation()), 2);
		
		unset($xbox);
	}
	
	if($opts == null || $opts['foursquare']){	
		
		// Foursquare.
		$fsq = new WPSDFoursquare();
	
		wpsd_helper_add_row('fsq', '<span>Foursquare:</span>', 
		sprintf('<a href="%s" target="_blank" title="Foursquare | Total Days Out" rel="nofollow">%s</a>', 
		$fsq->getAddress(), $fsq->getTotalDaysOut()) . ' | ' . 
		sprintf('<a href="%s" target="_blank" title="Foursquare | Total Checkins" rel="nofollow">%s</a>', 
		$fsq->getAddress(), $fsq->getTotalCheckins()). ' | ' . 
		sprintf('<a href="%s" target="_blank" title="Foursquare | Things done" rel="nofollow">%s</a>', 
		$fsq->getAddress(), $fsq->getThingsDone()), 1);
		
		unset($fsq);
	}
	
	if($opts == null || $opts['disqus']){	
		
		// Disqus.
		$dqs = new WPSDDisqus();
	
		wpsd_helper_add_row('dqs', '<span>Disqus:</span>', 
			sprintf('<a href="%s" target="_blank" title="Disqus | Comments Posted" rel="nofollow">%s</a>', 
			$dqs->getAddress(), $dqs->getComments()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Disqus | Points Received" rel="nofollow">%s</a>', 
			$dqs->getAddress(), $dqs->getPoints()), 2);
			
		unset($dqs);
	}
	
	if($opts == null || $opts['blippr']){	
		
		// Blippr.
		$blippr = new WPSDBlippr();
		
		wpsd_helper_add_row('blippr', '<span>Blippr:</span>', 
			sprintf('<a href="%s" target="_blank" title="Blippr | Blips" rel="nofollow">%s</a>', 
			$blippr->getAddress(), $blippr->getBlips()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Blippr | Lists" rel="nofollow">%s</a>', 
			$blippr->getAddress(), $blippr->getLists()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Blippr | Followers" rel="nofollow">%s</a>', 
			$blippr->getAddress(), $blippr->getFollowers()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Blippr | Following" rel="nofollow">%s</a>', 
			$blippr->getAddress(), $blippr->getFollowing()), 1);
			
		unset($blippr);
	}
	
	if($opts == null || $opts['amplify']){	
		
		// Amplify.
		$amp = new WPSDAmplify();
	
		wpsd_helper_add_row('amp', '<span>Amplify:</span>', 
			sprintf('<a href="%s" target="_blank" title="Amplify | Followers" rel="nofollow">%s</a>', 
			$amp->getAddress(), $amp->getFollowers()). ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Amplify | Sources" rel="nofollow">%s</a>', 
			$amp->getAddress(), $amp->getSources()). ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Amplify | Posts" rel="nofollow">%s</a>', 
			$amp->getAddress(), $amp->getPosts()) , 2);
			
		unset($amp);
	}
	
	if($opts == null || $opts['runkeeper']){	
		
		// Runkeeper.
		$rk = new WPSDRunkeeper();
		
		wpsd_helper_add_row('rk', '<span>Runkeeper:</span>', 
			sprintf('<a href="%s" target="_blank" title="Runkeeper | Total Activity" rel="nofollow">%s</a>', 
			$rk->getAddress(), $rk->getTotal()). ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Runkeeper | Total Distance" rel="nofollow">%s</a>', 
			$rk->getAddress(), $rk->getDistance()). ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Runkeeper | Total Calories" rel="nofollow">%s</a>', 
			$rk->getAddress(), $rk->getCalories()) , 1);
			
		unset($rk);
	}
	
	if($opts == null || $opts['blippy']){	
		
		// Blippy.
		$blippy = new WPSDBlippy();
	
		/*wpsd_helper_add_row('blippy', '<span>Blippy:</span>', 
			sprintf('<a href="%s" target="_blank" title="Blippy | Purchases" rel="nofollow">%s</a>', 
			$blippy->getAddress(), $blippy->getPurchases()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Blippy | Reviewed Purchases" rel="nofollow">%s</a>', 
			$blippy->getAddress(), $blippy->getReviewedPurchases()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Blippy | Following" rel="nofollow">%s</a>', 
			$blippy->getAddress(), $blippy->getFollowing())	. ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Blippy | Awesomes" rel="nofollow">%s</a>', 
			$blippy->getAddress(), $blippy->getAwesomes()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Blippy | Funnys" rel="nofollow">%s</a>', 
			$blippy->getAddress(), $blippy->getFunnys()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Blippy | Informatives" rel="nofollow">%s</a>', 
			$blippy->getAddress(), $blippy->getInformatives()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Blippy | Omgwtfs" rel="nofollow">%s</a>', 
			$blippy->getAddress(), $blippy->getOmgwtfs()), 2); */
		
		wpsd_helper_add_row('blippy', '<span>Blippy:</span>', 
			sprintf('<a href="%s" target="_blank" title="Blippy | Reviews" rel="nofollow">%s</a>', 
			$blippy->getAddress(), $blippy->getReviews()) , 2);

			
		unset($blippy);
	}
	
	if($opts == null || $opts['weread']){	
		
		// weRead
		$weread = new WPSDWeRead();
	
		wpsd_helper_add_row('weread', '<span>weRead:</span>', 
			sprintf('<a href="%s" target="_blank" title="weRead | Books" rel="nofollow">%s</a>', 
			$weread->getAddress(), $weread->getBooks()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="weRead | Reviews" rel="nofollow">%s</a>', 
			$weread->getAddress(), $weread->getReviews()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="weRead| Ratings" rel="nofollow">%s</a>', 
			$weread->getAddress(), $weread->getRatings()), 1);
			
		unset($weread);
	}	
	
	if($opts == null || $opts['eave']){	
		
		// Empire Avenue.
		$eave = new WPSDEmpireAvenue();
	
		wpsd_helper_add_row('eave', '<span>Empire Avenue:</span>', 
			sprintf('<a href="%s" target="_blank" title="Empire Avenue | Last trade" rel="nofollow">%s</a>', 
			$eave->getAddress(), $eave->getLastTrade()) . ' | ' .
			sprintf('<a href="%s" target="_blank" title="Empire Avenue | Shareholders" rel="nofollow">%s</a>', 
			$eave->getAddress(), $eave->getShareHolders()), 1);
			
		unset($eave);
	}	
	
	if($opts == null || $opts['friendfeed']){	
		
		// FriendFeed.
		$ff = new WPSDFriendFeed();
	
		wpsd_helper_add_row('ff', '<span>FriendFeed:</span>', 
			sprintf('<a href="%s" target="_blank" title="FriendFeed | subscribers" rel="nofollow">%s</a>', 
			$ff->getAddress(), $ff->getSubscribers()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="FriendFeed | subscriptions" rel="nofollow">%s</a>', 
			$ff->getAddress(), $ff->getSubscriptions()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="FriendFeed | comments" rel="nofollow">%s</a>', 
			$ff->getAddress(), $ff->getComments()), 2);
			
		unset($ff);
	}	
	
	/*if($opts == null || $opts['society']){	
		
		// Society.
		$soc = new WPSDSociety();
	
		wpsd_helper_add_row('soc', '<span>Society:</span>', 
			sprintf('<a href="%s" target="_blank" title="Society | followers" rel="nofollow">%s</a>', 
			$soc->getAddress(), $soc->getFollowers()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Society | following" rel="nofollow">%s</a>', 
			$soc->getAddress(), $soc->getFollowing()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Society | likes" rel="nofollow">%s</a>', 
			$soc->getAddress(), $soc->getLikes()). ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Society | answered" rel="nofollow">%s</a>', 
			$soc->getAddress(), $soc->getAnswered()), 1);
			
		unset($soc);
	}*/	
	
	if($opts == null || $opts['mylikes']){	
		
		// Mylikes.
		$mylikes = new WPSDMylikes();
	
		wpsd_helper_add_row('mylikes', '<span>Mylikes:</span>', 
			sprintf('<a href="%s" target="_blank" title="Mylikes | likes" rel="nofollow">%s</a>', 
			$mylikes->getAddress(), $mylikes->getLikes()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Mylikes | comments" rel="nofollow">%s</a>', 
			$mylikes->getAddress(), $mylikes->getComments()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Mylikes | first likes" rel="nofollow">%s</a>', 
			$mylikes->getAddress(), $mylikes->getFLikes()). ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Mylikes | followers" rel="nofollow">%s</a>', 
			$mylikes->getAddress(), $mylikes->getFollowers()). ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Mylikes | influence" rel="nofollow">%s</a>', 
			$mylikes->getAddress(), $mylikes->getInfluence()), 1);
			
		unset($mylikes);
	}	
	
	if($opts == null || $opts['battlenet']){	
		
		// Battlenet.
		$bn = new WPSDBattlenet();
	
		wpsd_helper_add_row('battlenet', '<span>Battlenet:</span>', 
			sprintf('<a href="%s" target="_blank" title="Battlenet | Rank" rel="nofollow">%s</a>', 
			$bn->getAddress(), $bn->getRank()). ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Battlenet | League games" rel="nofollow">%s</a>', 
			$bn->getAddress(), $bn->getGames()). ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Battlenet | Custom games" rel="nofollow">%s</a>', 
			$bn->getAddress(), $bn->getCustomGames()). ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Battlenet | Co-Op vs AI" rel="nofollow">%s</a>', 
			$bn->getAddress(), $bn->getCoop()). ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Battlenet | Free for all" rel="nofollow">%s</a>', 
			$bn->getAddress(), $bn->getFFA()), 2);
			
		unset($bn);
	}	
	
	if($opts == null || $opts['educopark']){	
		
		// Educopark.
		$ep = new WPSDEducoPark();
	
		wpsd_helper_add_row('educopark', '<span>Educopark:</span>', 
			sprintf('<a href="%s" target="_blank" title="Educopark | Life Lessons" rel="nofollow">%s</a>', 
			$ep->getAddress(), $ep->getLessons()). ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Educopark | Life Talks" rel="nofollow">%s</a>', 
			$ep->getAddress(), $ep->getTalks()). ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Educopark | Books" rel="nofollow">%s</a>', 
			$ep->getAddress(), $ep->getBooks()), 1);
			
		unset($ep);
	}	
	
	/*if($opts == null || $opts['yahoobuzz']){
		
		// Yahoo buzz.
		$yb = new WPSDYahooBuzz();

		wpsd_helper_add_row('yahoo','<span>YahooBuzz:</span>', 
			sprintf('<a href="%s" target="_blank" title="YahooBuzz | First Buzzer" rel="nofollow">%s</a>', 
			$yb->getAddress(), $yb->getFirstBuzzer() . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="YahooBuzz | Buzzed Up" rel="nofollow">%s</a>', 
			$yb->getAddress(), $yb->getBuzzedUp()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="YahooBuzz | Buzzed Down" rel="nofollow">%s</a>', 
			$yb->getAddress(), $yb->getBuzzedDown()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="YahooBuzz | Comments" rel="nofollow">%s</a>', 
			$yb->getAddress(), $yb->getComments()) ), 2);

		unset($yb);
	}*/
	
	if($opts == null || $opts['vimeo']){
		
		// Vimeo
		$vim = new WPSDVimeo();

		wpsd_helper_add_row('vimeo','<span>Vimeo:</span>', 
			sprintf('<a href="%s" target="_blank" title="Vimeo | Videos" rel="nofollow">%s</a>', 
			$vim->getAddress(), $vim->getVideos() . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Vimeo | Likes" rel="nofollow">%s</a>', 
			$vim->getAddress(), $vim->getLikes()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Vimeo | Contacts" rel="nofollow">%s</a>', 
			$vim->getAddress(), $vim->getContacts())  ), 1);

		unset($vim);
	}
	
	if($opts == null || $opts['identica']){
		
		// Vimeo
		$idca = new WPSDIdentica();

		wpsd_helper_add_row('identica','<span>Identi.ca:</span>', 
			sprintf('<a href="%s" target="_blank" title="identi.ca | Subscriptions" rel="nofollow">%s</a>', 
			$idca->getAddress(), $idca->getSubscriptions() ) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="identi.ca | Subscribers" rel="nofollow">%s</a>', 
			$idca->getAddress(), $idca->getSubscribers() ), 2);

		unset($idca);
	}
	
	if($opts == null || $opts['plaxo']){
		
		// Plaxo.
		$pl = new WPSDPlaxo();

		wpsd_helper_add_row('plaxo','<span>Plaxo:</span>', 
			sprintf('<a href="%s" target="_blank" title="Plaxo | Connections" rel="nofollow">%s</a>', 
			$pl->getAddress(), $pl->getConnectionCount() ), 1);

		unset($pl);
	}
	
	/*if($opts == null || $opts['blogpulse']){
		
		// BlogPulse.
		$bp = new WPSDBlogPulse(get_bloginfo('url'));

		wpsd_helper_add_row('blogpulse','<span>BlogPulse:</span>', 
			sprintf('<a href="%s" target="_blank" title="BlogPulse | Results" rel="nofollow">%s</a>', 
			$bp->getAddress(), $bp->getResults() ) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="BlogPulse | Conversation count" rel="nofollow">%s</a>', 
			$bp->getAddress(), $bp->getConversationCount() ), 2);

		unset($bp);
	}*/
	
	if($opts == null || $opts['netlog']){
		
		// Netlog.
		$nl = new WPSDNetlog();

		wpsd_helper_add_row('netlog','<span>Netlog:</span>', 
			sprintf('<a href="%s" target="_blank" title="Netlog | Friends" rel="nofollow">%s</a>', 
			$nl->getAddress(), $nl->getFriends() ) . ' | ' .
			//sprintf('<a href="%s" target="_blank" title="Netlog | Groups" rel="nofollow">%s</a>', 
			//$nl->getAddress(), $nl->getGroups() ) . ' | ' .
			sprintf('<a href="%s" target="_blank" title="Netlog | Guestbook" rel="nofollow">%s</a>', 
			$nl->getAddress(), $nl->getGuestbook() ) . ' | ' .
			sprintf('<a href="%s" target="_blank" title="Netlog | Pictures" rel="nofollow">%s</a>', 
			$nl->getAddress(), $nl->getPictures() ) . ' | ' .
			sprintf('<a href="%s" target="_blank" title="Netlog | Blog" rel="nofollow">%s</a>', 
			$nl->getAddress(), $nl->getBlog() ) . ' | ' .
			//sprintf('<a href="%s" target="_blank" title="Netlog | Links" rel="nofollow">%s</a>', 
			//$nl->getAddress(), $nl->getLinks() ). ' | ' .
			sprintf('<a href="%s" target="_blank" title="Netlog | Visitors" rel="nofollow">%s</a>', 
			$nl->getAddress(), $nl->getVisitors() ), 1);

		unset($nl);
	}

	if($opts == null || $opts['99designs']){	
		
		// 99Designs.
		$nn = new WPSD99Designs();
	
		wpsd_helper_add_row('99designs', '<span>99Designs:</span>', 
			sprintf('<a href="%s" target="_blank" title="99 Designs | Contests held" rel="nofollow">%s</a>', 
			$nn->getAddress(), $nn->getContests()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="99 Designs | Contests active" rel="nofollow">%s</a>', 
			$nn->getAddress(), $nn->getActive()). ' | ' . 
			sprintf('<a href="%s" target="_blank" title="99 Designs | Contests awarded" rel="nofollow">%s</a>', 
			$nn->getAddress(), $nn->getAwarded()), 2);
			
		unset($nn);
	}	
	
	if($opts == null || $opts['quora']){	
		
		$q = new WPSDQuora();
	
		wpsd_helper_add_row('quora', '<span>Quora:</span>', 
			sprintf('<a href="%s" target="_blank" title="Quora | Followers" rel="nofollow">%s</a>', 
			$q->getAddress(), $q->getFollowers()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Quora | Following" rel="nofollow">%s</a>', 
			$q->getAddress(), $q->getFollowing()), 1);
			
		unset($q);
	}	
	
	if($opts == null || $opts['getglue']){	
		
		$gg = new WPSDGetGlue();
	
		wpsd_helper_add_row('getglue', '<span>Getglue:</span>', 
			sprintf('<a href="%s" target="_blank" title="Getglue | Checkins" rel="nofollow">%s</a>', 
			$gg->getAddress(), $gg->getCheckins()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Getglue | Likes" rel="nofollow">%s</a>', 
			$gg->getAddress(), $gg->getLikes()). ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Getglue | Reviews" rel="nofollow">%s</a>', 
			$gg->getAddress(), $gg->getReviews()), 2);
			
		unset($gg);
	}
	
	if($opts == null || $opts['hunch']){	
		
		$h = new WPSDHunch();
	
		wpsd_helper_add_row('hunch', '<span>Hunch:</span>', 
			sprintf('<a href="%s" target="_blank" title="Hunch | Recommendation" rel="nofollow">%s</a>', 
			$h->getAddress(), $h->getRecommendation()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Hunch | Saved for later" rel="nofollow">%s</a>', 
			$h->getAddress(), $h->getSaved()) . ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Hunch | Followers" rel="nofollow">%s</a>', 
			$h->getAddress(), $h->getFollowers()). ' | ' . 
			sprintf('<a href="%s" target="_blank" title="Hunch | Following" rel="nofollow">%s</a>', 
			$h->getAddress(), $h->getFollowing()), 1);
			
		unset($h);
	}		
	
	//if($opts == null || $opts['sixent']){	
	//	wpsd_helper_add_row('sxt', '<span>Sixent:</span>', sprintf('<a href="%s" target="_blank" title="Sixent | Contacts" rel="nofollow">%s</a>', $sxt->getAddress(), $sxt->getContacts()), 2);
	//	unset($sxt);
	//}
	
	if($opts == null || $opts['est']){
		
		// Value.
		$sitevalue = new WPSDSiteValue($age, 1.2, $inbound);
	
		wpsd_helper_add_row('est', 	'<span>Estimated:</span> ', $sitevalue->getValue(), 1);
		
		unset($sitevalue);
	}
	
	if($opts == null || $opts['powered']){
	
		// Plugin Stats.
		$plugin_stats = new WPSDPluginStats();
		
		$downloads = $plugin_stats->getDownloads();
		
		wpsd_helper_add_row('pow', 	'<span>Powered by:</span> ',
		sprintf('<a href="%s" target="_blank" title="WP-Stats-Dashboard - %s downloads " rel="external">%s</a> | %s', 
		'http://www.daveligthart.com/wp-stats-dashboard-10/', $downloads, 'WPSD', $downloads), 2);
	}

	echo '</table>';
		
	if(date('Y-m-d') != get_option('wpsd_cache_date')) {
		
		update_option('wpsd_cache_date', date('Y-m-d'));
	}
}

/**
 * wpsd_helper_add_row function.
 * 
 * @access public
 * @param mixed $icon
 * @param mixed $html1
 * @param mixed $html2
 * @param mixed $i
 * @return void
 */

function wpsd_helper_add_row($icon, $html1, $html2, $i) { 
global $alt_count;
	?><tr>
	<td><?php echo '<img src="'. WPSD_PLUGIN_URL . '/resources/images/icons/'.$icon.'.png" alt="'.$icon.' icon" />'; ?></td>
	<td<?php echo ($alt_count % 2 == 0)?' class="alternate"':'';?>><?php echo $html1; ?></td>
	<td<?php echo ($alt_count % 2 == 0)?' class="alternate"':'';?>><?php echo $html2; ?></td>
</tr>
<?php $alt_count++;
}
// Start loading.
wpsd_load_stats();

$dao = new WPSDTrendsDao();

$factory = new WPSDStatsFactory();

// Update stats.
for($i=1; $i<=$factory->last; $i++) {
	$dao->update($i, $factory->getStats($i));
}

die();
?>