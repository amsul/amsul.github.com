<?php
include_once('metrics/WPSDStats.php');
//include_once('metrics/WPSDBlogPulse.php');

/**
 * Stats Factory.
 * @author dligthart <info@daveligthart.com>
 * @package wp-stats-dashboard
 * @subpackage util
 * @version 1.5
 */
class WPSDStatsFactory {
	
	var $pagerank = 1;
	var $alexa = 2;
	var $technorati = 3;
	var $delicious = 4;	/* not used */
	var $compete = 5;
	var $yahoo = 6; /* deprecated */
	var $mozrank = 7;
	var $postrank = 8;
	var $twitter_followers = 9;
	var $engagement = 10;
	var $linkedin_connections = 11;
	var $google_backlinks = 12;
	var $google_socialgraph_in = 13;
	var $bitly = 14;
	var $bing = 15;
	var $klout = 16;
	var $feedburner = 17;
	var $lastfm = 18;
	var $facebook = 19;
	var $backtype = 20; /* deprecated */
	var $stumbleupon = 21;
	var $hits = 22;
	var $youtube = 23;
	var $myspace = 24;
	var $wordpress = 25;
	var $runkeeper = 26;
	var $googleplus = 27;
	var $peerindex = 28;
	var $twitter_ratio = 29;
	var $last = 29;
	
	/**
	 * statsTypes
	 * 
	 * @var mixed
	 * @access public
	 */
	var $statsTypes = array(
	'pagerank', 
	'alexa rank', 
	'technorati rank', 
	'delicious', 
	'compete rank', 
	'yahoo incoming', 
	'mozrank', 
	'postrank', 
	'twitter followers', 
	'engagement comments', 
	'linkedin connections', 
	'google backlinks', 
	'google socialgraph incoming',
	'bitly clicks', 
	'bing rank', 
	'klout rank', 
	'feedburner circulation', 
	'lastfm friends', 
	'facebook fans', 
	'backtype tweets', 
	'stumbleupon views', 
	'views', 
	'youtube views', 
	'myspace friends', 
	'wordpress likes',
	'runkeeper total distance',
	'google plus',
	'peerindex', 
	'twitter ff ratio');
	
	/**
	 * WPSDStatsFactory function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDStatsFactory() {
		
	}
	
	/**
	 * getStatsType function.
	 * 
	 * @access public
	 * @param mixed $index
	 * @return void
	 */
	function getStatsType($index) {
		
		return $this->statsTypes[($index-1)];	
	}
	
	/**
	 * Prepare stats.
	 * @param $stats
	 * @return string new stats
	 */
	function prepare($stats) {
		
		if(null == $stats) return 0;
		
		if(!is_string($stats)) return 0;
		
		return str_replace(',','',$stats);	
	}
	
	/**
	 * Get stats.
	 * @param $type
	 * @return integer stats
	 */
	function getStats($type) {
		
		$o = $this->create($type);
		
		$stats = 0;
		
		switch($type) {
			case 1: // pagerank.
				$stats = $o->getPageRank();
			break;
			case 2: // alexa.
				$stats = $o->getRank();
			break;
			case 3: // technorati.
				$stats = $o->getRank();
			break;
			case 4: // delicious.
				$stats = $o->getLinksIn();
			break;
			case 5: // compete.
				$stats = $o->getRank();
			break;
			case 6: // yahoo.
				$stats = $o->getLinksIn();
			break;
			case 7: // mozrank.
				$stats = $o->getRank();
			break;
			case 8: // postrank.
				$stats = $o->getRank();
			break;
			case 9: // twitter followers.
				$stats = $o->getFollowers();
			break;
			case 10: // engagement.
				$stats = $o->getEngagement();
			break;
			case 11: // linkedin.
				$stats = $o->getConnectionCount();
			break;
			case 12: // google.
				$stats = $o->getRank();
			break;
			case 13: // google.
				$stats = $o->getNodeInCount();
			break;
			case 14: // bitly.
				$stats = $o->getClicks();
			break;
			case 15: // bing.
				$stats = $o->getRank();
			break;
			case 16: // klout.
				$stats = $o->getRank();
			break;
			case 17: // feedburner.
				$stats = $o->getCirculation();
			break;
			case 18: // last fm.
				$stats = $o->getFriends();
			break;
			case 19: // facebook.
				$stats = $o->getFans();
			break;
			case 20: // backtype.
				//$stats = $o->getTweets();
				$stats = 0;
			break;
			case 21: // stumbleupon.
				$stats = $o->getViews();
			break;
			case 22: // site hits.
				//$stats = $o->getViews();
				$stats = 0;
			break;
			case 23: // youtube video views.
				$stats = $o->getViews();
			break;
			case 24: // myspace friends.
				$stats = $o->getFriends();
			break;
			case 25: // wordpress likes.
				//$stats = $o->getLikePosts();
				$stats = 0;
			break;
			case 26: // runkeeper total distance.
				$stats = $o->getDistance();
			break;
			case 27: // google plus.
				$stats = $o->getFollowers();
			break;
			case 28: // peerindex.
				$stats = $o->getScore();
			break;
			case 29: // twitter ff ratio.
				$stats = round($o->getRatio(), 1);
			break;
		}
		
		$stats = $this->prepare($stats);
		
		return $stats;
	}
	
	/**
	 * Create type.
	 * @param integer $type
	 * @return object
	 */	
	function create($type) {
			
		switch($type) {
			case 1: include_once('metrics/WPSDPageRank.php');
				return new WPSDPageRank();
			break;
			case 2: include_once('metrics/WPSDAlexaRank.php');
				return new WPSDAlexaRank(get_bloginfo('url'), true); 
			break;
			case 3: include_once('metrics/WPSDTechnoratiRank.php');
				return new WPSDTechnoratiRank(get_bloginfo('url'), true);
			break;
			case 4: include_once('metrics/WPSDDeliciousRank.php');
				return new WPSDDeliciousRank(get_bloginfo('url'), true);
			break;
			case 5: include_once('metrics/WPSDCompeteRank.php');
				return new WPSDCompeteRank(get_bloginfo('url'), true);
			break;
			case 6: include_once('metrics/WPSDYahooRank.php');
				return  new WPSDYahooRank(get_bloginfo('url'), true);
			break;
			case 7: include_once('metrics/WPSDMozRank.php');
				return new WPSDMozRank();
			break; 
			case 8: include_once('metrics/WPSDPostRank.php');
				return new WPSDPostRank();
			break;
			
			case 9:
			case 29: 
				include_once('metrics/WPSDTweetMeme.php');
				include_once('metrics/WPSDTwitter.php');			
				return new WPSDTwitter();
			break;
			
			case 10: include_once('metrics/WPSDEngagement.php');
				return new WPSDEngagement();
			break;
			case 11: include_once('metrics/WPSDLinkedIn.php');
				return new WPSDLinkedIn(true);
			break;
			case 12: include_once('metrics/WPSDGoogleBackLinks.php');
				return new WPSDGoogleBackLinks(get_bloginfo('url'), true);
			break;
			case 13: include_once('metrics/WPSDGoogleSocialGraph.php');
				return new WPSDGoogleSocialGraph(get_bloginfo('url'));
			break;
			case 14: include_once('metrics/WPSDBitly.php');
				return new WPSDBitly(get_bloginfo('url'), true);
			break;
			case 15: include_once('metrics/WPSDBing.php');
				return new WPSDBing(get_bloginfo('url'), true);
			break;
			case 16: include_once('metrics/WPSDKlout.php');
				return new WPSDKlout();
			break;
			case 17: include_once('metrics/WPSDFeedBurner.php');
				return new WPSDFeedBurner();
			break;
			case 18: include_once('metrics/WPSDLastFm.php');
				return new WPSDLastFm();
			break;
			case 19: include_once('metrics/WPSDFaceBook.php');
				return new WPSDFaceBook();
			break;
			//case 20: include_once('metrics/WPSDBacktype.php');
			//	return new WPSDBacktype(get_bloginfo('url'), true);
			//break;	
			case 21: include_once('metrics/WPSDStumbleUpon.php');
				return new WPSDStumbleUpon(get_bloginfo('url'), true);
			break;	
			/*case 22: 
				include_once('metrics/WPSDSiteAge.php');
				include_once('metrics/WPSDSiteValue.php');
				include_once('metrics/WPSDHits.php');
				return new WPSDHits();
			break;*/
			case 23: include_once('metrics/WPSDYoutube.php');
				return new WPSDYoutube();
			break;
			case 24: include_once('metrics/WPSDMyspace.php');
				return new WPSDMyspace();
			break; 
			/*case 25: include_once('metrics/WPSDWordPress.php');
				return new WPSDWordPress();
			break;*/
			case 26:include_once('metrics/WPSDRunkeeper.php');
				return new WPSDRunkeeper();
			break;
			case 27: include_once('metrics/WPSDGooglePlus.php');
				return new WPSDGooglePlus();
			break;
			case 28: include_once('metrics/WPSDPeerIndex.php');
				return new WPSDPeerIndex();
			break;
			// 29.
		}
		
		return null;
	}
}
?>