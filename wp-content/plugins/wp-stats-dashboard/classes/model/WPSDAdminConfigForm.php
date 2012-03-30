<?php
include_once('WPSDBaseForm.php');

/**
 * WPSDAdminConfigForm model object.
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 3.0
 * @package wp-stats-dashboard
 */
class WPSDAdminConfigForm extends WPSDBaseForm {
	
	/**
	 * wpsd_blog_id
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_blog_id;
	
	/**
	 * wpsd_un
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_un;
	
	/**
	 * wpsd_pw
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_pw;
	
	/**
	 * wpsd_type. Chart type.
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_type; 
	
	// Social media usernames and uris.
	var $wpsd_twitter_un;
	var $wpsd_linkedin_un;
	var $wpsd_bitly_un;
	var $wpsd_bitly_key;
	var $wpsd_feedburner_uri;
	var $wpsd_lastfm_un;
	var $wpsd_facebook_un;
	var $wpsd_flickr_uri;
	var $wpsd_flickr_un;
	var $wpsd_diigo_un;
	var $wpsd_brazen_careerist_un;
	var $wpsd_brazencareerist_un;
	var $wpsd_newsvine_un;
	var $wpsd_youtube_un;
	var $wpsd_myspace_un;
	var $wpsd_posterous_un;
	var $wpsd_plancast_un;
	var $wpsd_lazyfeed_un;
	var $wpsd_sphinn_un;
	var $wpsd_jaiku_un;
	var $wpsd_koornk_un;
	var $wpsd_plurk_un;
	var $wpsd_hyves_un;
	var $wpsd_sixent_un; // TODO: sixent has no public profile can't implement.
	var $wpsd_bebo_un; // TODO: bebo has no public profile can't implement.
	var $wpsd_xbox_un;
	var $wpsd_foursquare_un;
	var $wpsd_disqus_un;
	var $wpsd_blippr_un;
	var $wpsd_google_wishlist_uri; // TODO: figure out google ajax.
	var $wpsd_amplify_un;
	var $wpsd_runkeeper_un;
	var $wpsd_blippy_un;
	var $wpsd_weread_id;
	var $wpsd_eave_un; // empire avenue username.
	var $wpsd_eave_pw; // empire avenue password.
	var $wpsd_friendfeed_un;
	var $wpsd_digg_un;
	//var $wpsd_society_un;
	var $wpsd_mylikes_un;
	var $wpsd_battlenet_uri;
	var $wpsd_educopark_un;
	//var $wpsd_yahoobuzz_uri;
	var $wpsd_vimeo_un;			
	var $wpsd_identica_un;
	var $wpsd_blogcatalog_un;
	var $wpsd_plaxo_uri;
	var $wpsd_reddit_un;
	var $wpsd_stumbleupon_un;
	var $wpsd_netlog_un;
	var $wpsd_99designs_un;
	var $wpsd_quora_un;
	var $wpsd_blogger_un;
	var $wpsd_getglue_un;
	var $wpsd_linkedincompany_un;
	var $wpsd_googleplus_un;
	var $wpsd_hunch_un;
	
	/**
	 * wpsd_ego_search
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_ego_search;
	
	/**
	 * wpsd_delicious_search. Delicious search phrase to monitor.
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_delicious_search;
	
	/**
	 * wpsd_delicious_uri. 
	 *
	 * Delicious url to monitor e.g: http://www.delicious.com/url/591d3f6192c88f638547d183355587a6
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_delicious_uri;
	
	/**
	 * wpsd_facebook_login
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_facebook_login;
	
	/**
	 * wpsd_facebook_password
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_facebook_password;
	
	/**
	 * wpsd_pingfm_key
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_pingfm_key; 
	
	// Widgets
	var $wpsd_widget_overview;
	var $wpsd_widget_postviews;
	var $wpsd_widget_referrers;
	var $wpsd_widget_clicks;
	var $wpsd_widget_searchterms;
	var $wpsd_widget_dailyviews;
	var $wpsd_widget_compete;
	var $wpsd_widget_trends;
	var $wpsd_widget_blogpulse;
	var $wpsd_widget_authors;
	
	/**
	 * wpsd_disable_widgets. Widgets not on main dashboard.
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_disable_widgets;
	
	/**
	 * wpsd_trends_type. Default trend graph id.
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_trends_type; 
		
	/**
	 * wpsd_amplify_autopost_email. Amplify email used for autoposting content.
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_amplify_autopost_email;
	
	/**
	 * wpsd_posterous_autopost_email. Posterous email used for autoposting content.
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_posterous_autopost_email;
	
	/**
	 * wpsd_role_author
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_role_author;
	
	/**
	 * wpsd_role_editor
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_role_editor;
	
	/**
	 * wpsd_role_contributor
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_role_contributor;
	
	/**
	 * wpsd_role_subscriber
	 * 
	 * @var mixed
	 * @access public
	 */
	var $wpsd_role_subscriber;
	
	/**
	 * WPSDAdminConfigForm function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDAdminConfigForm(){

		parent::WPSDBaseForm();

		if($this->setFormValues()){

			$this->saveOptions();
		}

		$this->loadOptions();
				
		if($this->wpsd_blog_id == '') {
				
			$options = get_option('stats_options'); // from wp-stats.
			
			$temp = false;
			
			if(isset($options['blog_id'])) {
				
				$temp = $options['blog_id'];
			}
			
			if(!$temp) {
				
				$temp = get_option( 'jetpack_id' );
			}
				
			$this->wpsd_blog_id = $temp;
		}
		
		if('' == $this->wpsd_brazencareerist_un) {
			
			$this->wpsd_brazencareerist_un = $this->wpsd_brazen_careerist_un;
		}
	}

	/**
	 * getWpsdBlogId function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getWpsdBlogId(){
		return trim($this->wpsd_blog_id);
	}

	/**
	 * getWpsdUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdUn(){
		return trim($this->wpsd_un);
	}

	/**
	 * getWpsdPw function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdPw(){
		return trim($this->wpsd_pw);
	}

	/**
	 * getWpsdType function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdType() {
		return trim($this->wpsd_type);
	}

	/**
	 * getWpsdTwitterUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdTwitterUn() {
		return trim($this->wpsd_twitter_un);
	}

	/**
	 * getWpsdLinkedInUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdLinkedInUn(){
		return trim($this->wpsd_linkedin_un);
	}
	
	/**
	 * getWpsdLinkedInCompanyUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdLinkedInCompanyUn() {
		return trim($this->wpsd_linkedincompany_un);
	}
	
	/**
	 * getWpsdBitlyUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdBitlyUn() {
		return trim($this->wpsd_bitly_un);
	}
	
	/**
	 * getWpsdBitlyKey function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdBitlyKey() {
		return trim($this->wpsd_bitly_key);
	}

	/**
	 * getWpsdWidgetOverview function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdWidgetOverview() {
		return trim($this->wpsd_widget_overview);
	}

	/**
	 * getWpsdWidgetPostViews function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdWidgetPostViews() {
		return trim($this->wpsd_widget_postviews);
	}

	/**
	 * getWpsdWidgetReferrers function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdWidgetReferrers() {
		return trim($this->wpsd_widget_referrers);
	}

	/**
	 * getWpsdWidgetClicks function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdWidgetClicks() {
		return trim($this->wpsd_widget_clicks);
	}
	
	/**
	 * getWpsdWidgetSearchTerms function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdWidgetSearchTerms() {
		return trim($this->wpsd_widget_searchterms);
	}
	
	/**
	 * getWpsdWidgetDailyViews function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdWidgetDailyViews() {
		return trim($this->wpsd_widget_dailyviews);
	}

	/**
	 * getWpsdWidgetCompete function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdWidgetCompete() {
		return trim($this->wpsd_widget_compete);
	}
	
	/**
	 * getWpsdWidgetTrends function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdWidgetTrends() {
		return trim($this->wpsd_widget_trends);		
	}
	
	/**
	 * getWpsdWidgetBlogPulse function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdWidgetBlogPulse() {
		return trim($this->wpsd_widget_blogpulse);
	}
	
	/**
	 * getWpsdWidgetAuthors function.
	 * 
	 * @access public
	 * @return void
	 */
	function getWpsdWidgetAuthors() {	
		return trim($this->wpsd_widget_authors);
	}
	
	/**
	 * getWpsdTrendsType function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdTrendsType() {
		return trim($this->wpsd_trends_type);
	}
	
	/**
	 * getWpsdFeedburnerUri function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdFeedburnerUri() {
		return trim($this->wpsd_feedburner_uri);
	}
	
	/**
	 * getWpsdLastFmUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdLastFmUn() {
		return trim($this->wpsd_lastfm_un);
	}
	
	/**
	 * getWpsdFacebookUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdFacebookUn() {
		return trim($this->wpsd_facebook_un);
	}
	
	/**
	 * getWpsdFlickrUsername function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdFlickrUsername() {
		
		if('' != $this->wpsd_flickr_uri) {
		
			$temp = explode('/', $this->wpsd_flickr_uri);
			
			if(null != $temp[4]) {
			
				$this->wpsd_flickr_un = $temp[4];
			}
		}
	
		return $this->wpsd_flickr_un;
	}
	
	/**
	 * getWpsdDiigoUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdDiigoUn() {
		return trim($this->wpsd_diigo_un);
	}
	
	/**
	 * getWpsdBrazenCareeristUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdBrazenCareeristUn() {
		return trim($this->wpsd_brazencareerist_un);
	}
	
	/**
	 * getWpsdNewsVineUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdNewsVineUn() {
		return trim($this->wpsd_newsvine_un);
	}
	
	/**
	 * getWpsdYoutubeUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdYoutubeUn() {
		return trim($this->wpsd_youtube_un);
	}
	
	/**
	 * getWpsdMyspaceUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdMyspaceUn() {
		return trim($this->wpsd_myspace_un);
	}
	
	/**
	 * getWpsdPosterousUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdPosterousUn() {
		return trim($this->wpsd_posterous_un);
	}
	
	/**
	 * getWpsdPosterousAutoPostEmail function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdPosterousAutoPostEmail() {
		
		if('' == trim($this->wpsd_posterous_autopost_email)) {
			
			if('' != $this->getWpsdPosterousUn()) {
				
				$un = $this->getWpsdPosterousUn();
				
				$this->wpsd_posterous_autopost_email =  "post@{$un}.posterous.com";
			}
		}
		
		return trim($this->wpsd_posterous_autopost_email);
	}
	
	/**
	 * getWpsdPlancastUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdPlancastUn() {
		return trim($this->wpsd_plancast_un);
	}
	
	/**
	 * getWpsdLazyfeedUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdLazyfeedUn() {
		return trim($this->wpsd_lazyfeed_un);
	}
	
	/**
	 * getWpsdSphinnUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdSphinnUn() {
		return trim($this->wpsd_sphinn_un);
	}
	
	/**
	 * getWpsdJaikuUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdJaikuUn() {
		return trim($this->wpsd_jaiku_un);
	}
	
	/**
	 * getWpsdKoornkUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdKoornkUn() {
		return trim($this->wpsd_koornk_un);
	}
	
	/**
	 * getWpsdPlurkUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdPlurkUn() {
		return trim($this->wpsd_plurk_un);
	}
	
	/**
	 * getWpsdHyvesUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdHyvesUn() {
		return trim($this->wpsd_hyves_un);
	}
	
	/**
	 * getWpsdSixentUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdSixentUn() {
		return trim($this->wpsd_sixent_un);
	}
	
	/**
	 * getWpsdXboxUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdXboxUn() {
		return trim($this->wpsd_xbox_un);
	}
	
	/**
	 * getWpsdFoursquareUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdFoursquareUn() {
		return trim($this->wpsd_foursquare_un);
	}
	
	/**
	 * getWpsdDisqusUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdDisqusUn() {
		return trim($this->wpsd_disqus_un);
	}
	
	/**
	 * getWpsdBlipprUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdBlipprUn() {
		return trim($this->wpsd_blippr_un);
	}
	
	/**
	 * getWpsdGoogleWishlistUri function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdGoogleWishlistUri() {
		return trim($this->wpsd_google_wishlist_uri);
	}
	
	/**
	 * getWpsdAmplifyUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdAmplifyUn() {
		return trim($this->wpsd_amplify_un);
	}
	
	/**
	 * getWpsdAmplifyAutoPostEmail function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdAmplifyAutoPostEmail() {
		
		return trim($this->wpsd_amplify_autopost_email);
	}
		
	/**
	 * getWpsdRunkeeperUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdRunkeeperUn() {
		return trim($this->wpsd_runkeeper_un);
	}
	
	/**
	 * getWpsdBlippyUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdBlippyUn() {
		return trim($this->wpsd_blippy_un);
	}
	
	/**
	 * getWpsdWeReadId function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdWeReadId() {
		return trim($this->wpsd_weread_id);
	}
	
	/**
	 * getWpsdEaveUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdEaveUn() {
		return trim($this->wpsd_eave_un);	
	}
	
	/**
	 * getWpsdEavePw function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdEavePw() {
		return trim($this->wpsd_eave_pw);
	}
	
	/**
	 * getWpsdFriendFeedUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdFriendFeedUn() {
		return trim($this->wpsd_friendfeed_un);
	}
	
	/**
	 * getWpsdDiggUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdDiggUn() {
		return trim($this->wpsd_digg_un);
	}
	
	/**
	 * getWpsdSocietyUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdSocietyUn() {
		return trim($this->wpsd_society_un);
	}
	
	/**
	 * getWpsdMylikesUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdMylikesUn() {
		return trim($this->wpsd_mylikes_un);
	}
	
	/**
	 * getWpsdBattlenetUri function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdBattlenetUri() {
		return trim($this->wpsd_battlenet_uri);
	}
	
	/**
	 * getWpsdEducoParkUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdEducoParkUn() {
		return trim($this->wpsd_educopark_un);
	}
	
	/**
	 * getWpsdYahooBuzzUri function.
	 * 
	 * @access public
	 * @deprecated no longer exists
	 * @return string
	 */
	function getWpsdYahooBuzzUri() {
		//return trim($this->wpsd_yahoobuzz_uri);
	}
	
	/**
	 * getWpsdPingfmKey function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdPingfmKey() {
		return trim($this->wpsd_pingfm_key);
	}
	
	/**
	 * getWpsdVimeoUn function.
	 * 
	 * @access public
	 * @return string username
	 */
	function getWpsdVimeoUn() {
		return trim($this->wpsd_vimeo_un);
	}
	
	/**
	 * getWpsdIdenticaUn function.
	 * 
	 * @access public
	 * @return string username
	 */
	function getWpsdIdenticaUn() {
		return trim($this->wpsd_identica_un);
	}
	
	/**
	 * getWpsdFacebookLogin function.
	 * 
	 * @access public
	 * @return string username
	 */
	function getWpsdFacebookLogin() {
		return trim($this->wpsd_facebook_login);
	}
	
	/**
	 * getWpsdFacebookPassword function.
	 * 
	 * @access public
	 * @return string password
	 */
	function getWpsdFacebookPassword() {
		return trim($this->wpsd_facebook_password);
	}
	
	/**
	 * getWpsdBlogCatalogUn function.
	 * 
	 * @access public
	 * @return string username
	 */
	function getWpsdBlogCatalogUn() {
		return trim($this->wpsd_blogcatalog_un);
	}
	
	/**
	 * getWpsdPlaxoUri function.
	 * 
	 * @access public
	 * @return string uri
	 */
	function getWpsdPlaxoUri() {
		return trim($this->wpsd_plaxo_uri);
	}
	
	/**
	 * getWpsdDeliciousSearch function.
	 * 
	 * @access public
	 * @return string search phrase
	 */
	function getWpsdDeliciousSearch() {
		return trim($this->wpsd_delicious_search);
	}
	
	/**
	 * getWpsdDeliciousUri function.
	 * 
	 * @access public
	 * @return string url
	 */
	function getWpsdDeliciousUri() {
		return trim($this->wpsd_delicious_uri);
	}
	
	/**
	 * getWpsdRedditUn function.
	 * 
	 * @access public
	 * @return string username
	 */
	function getWpsdRedditUn() {
		return trim($this->wpsd_reddit_un);
	}
	
	/**
	 * getStumbleUponUn function.
	 * 
	 * @access public
	 * @return string username
	 */
	function getWpsdStumbleUponUn() {
		return trim($this->wpsd_stumbleupon_un);
	}
	
	/**
	 * getWpsdNetlogUn function.
	 * 
	 * @access public
	 * @return string username
	 */
	function getWpsdNetlogUn() {	
		return trim($this->wpsd_netlog_un);
	}
	
	/**
	 * getWpsd99DesignsUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsd99DesignsUn() {
		return trim($this->wpsd_99designs_un);
	}
	
	/**
	 * getWpsdEgoSearch function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdEgoSearch() {
		return trim($this->wpsd_ego_search);
	}
	
	/**
	 * getWpsdQuoraUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdQuoraUn() {	
		return trim($this->wpsd_quora_un);
	}
	
	/**
	 * getWpsdBloggerUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdBloggerUn() {
		return trim($this->wpsd_blogger_un);
	}
	
	/**
	 * getWpsdGetGlueUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdGetGlueUn() {
		return trim($this->wpsd_getglue_un);
	}
	
	/**
	 * getActiveCount function.
	 * 
	 * @access public
	 * @return integer count
	 */
	function getActiveCount() {
	
		$metrics = wpsd_get_metrics_types();
		
		$config_vars = $this->getClassVars();
		
		$count = 0;
		
		if(null != $metrics) { 
			
			foreach($metrics as $k => $v) {
	
				if('' != $config_vars['wpsd_' . $k . '_un'] || '' != $config_vars['wpsd_' . $k . '_uri']) {
							
					$count++;
				}
			}
		}
		
		return $count;
	}
	
	/**
	 * getTotalCount function.
	 * 
	 * @access public
	 * @return integer total
	 */
	function getTotalCount() {
	
		$metrics = wpsd_get_metrics_types();
		
		$config_vars = $this->getClassVars();
		
		$count = 0;
		
		if(null != $metrics) { 
			
			foreach($metrics as $k => $v) {
					
				if(array_key_exists('wpsd_' . $k . '_un', $config_vars) || array_key_exists('wpsd_' . $k . '_uri', $config_vars)) {
					
					$count++;
				}	
			}
		}
		
		return $count;
	}
	
	/**
	 * getWpsdDisableWidgets function.
	 * 
	 * @access public
	 * @return integer
	 */
	function getWpsdDisableWidgets() {
		
		return $this->wpsd_disable_widgets;
	}
	
	/**
	 * getWpsdRoleAuthor function.
	 * 
	 * @access public
	 * @return boolean
	 */
	function getWpsdRoleAuthor() {
		
		return $this->wpsd_role_author;
	}
	
	/**
	 * getWpsdRoleEditor function.
	 * 
	 * @access public
	 * @return boolean
	 */
	function getWpsdRoleEditor() {
		
		return $this->wpsd_role_editor;
	}
	
	/**
	 * getWpsdRoleContributor function.
	 * 
	 * @access public
	 * @return boolean
	 */
	function getWpsdRoleContributor() {
		
		return $this->wpsd_role_contributor;
	}
	
	/**
	 * getWpsdRoleSubscriber function.
	 * 
	 * @access public
	 * @return boolean
	 */
	function getWpsdRoleSubscriber() {
		
		return $this->wpsd_role_subscriber;
	}
	
	/**
	 * getWpsdGooglePlusUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdGooglePlusUn() {
		
		return $this->wpsd_googleplus_un;
	}
	
	/**
	 * getWpsdHunchUn function.
	 * 
	 * @access public
	 * @return string
	 */
	function getWpsdHunchUn() {
		
		return $this->wpsd_hunch_un;
	}
}	
?>