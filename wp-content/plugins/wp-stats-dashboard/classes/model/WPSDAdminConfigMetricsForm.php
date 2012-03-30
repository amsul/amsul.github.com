<?php
/**
 * WPSDAdminConfigForm model object.
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 2.8
 * @package wp-stats-dashboard
 */
include_once('WPSDBaseForm.php');
class WPSDAdminConfigMetricsForm extends WPSDBaseForm {

	/**
	 * exclude_opts
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access public
	 */
	var $exclude_opts = array();
	
	// Options.
	var $wpsd_option_engagement;
	var $wpsd_option_pagerank;
	var $wpsd_option_backlinks;
	var $wpsd_option_socialgraph;
	var $wpsd_option_alexa;
	var $wpsd_option_technorati;
	var $wpsd_option_delicious;
	var $wpsd_option_compete;
	var $wpsd_option_yahoo;
	var $wpsd_option_mozrank;
	var $wpsd_option_postrank;
	var $wpsd_option_digg;
	var $wpsd_option_twitter;
	var $wpsd_option_reddit;
	var $wpsd_option_backtype;
	var $wpsd_option_stumbleupon;
	var $wpsd_option_blogcatalog;
	var $wpsd_option_bing;
	var $wpsd_option_linkedin;
	var $wpsd_option_bitly;
	var $wpsd_option_klout;
	var $wpsd_option_feedburner;
	var $wpsd_option_lastfm;
	var $wpsd_option_facebook;
	var $wpsd_option_flickr;
	var $wpsd_option_diigo;
	var $wpsd_option_brazencareerist;
	var $wpsd_option_newsvine;
	var $wpsd_option_youtube;
	var $wpsd_option_myspace;
	var $wpsd_option_wordpress;
	var $wpsd_option_posterous;
	var $wpsd_option_plancast;
	var $wpsd_option_lazyfeed;
	var $wpsd_option_sphinn;
	var $wpsd_option_jaiku;
	var $wpsd_option_koornk;
	var $wpsd_option_plurk;
	var $wpsd_option_hyves;
	//var $wpsd_option_sixent;
	var $wpsd_option_xbox;
	var $wpsd_option_foursquare;
	var $wpsd_option_disqus;
	var $wpsd_option_blippr;
	var $wpsd_option_amplify;
	var $wpsd_option_runkeeper;
	var $wpsd_option_blippy;
	var $wpsd_option_weread;
	var $wpsd_option_buzz;
	var $wpsd_option_eave;
	var $wpsd_option_friendfeed;
//	var $wpsd_option_society;
	var $wpsd_option_mylikes;
	var $wpsd_option_battlenet;
	var $wpsd_option_educopark;
	//var $wpsd_option_yahoobuzz;
	var $wpsd_option_vimeo;
	var $wpsd_option_identica;
	var $wpsd_option_plaxo;
	var $wpsd_option_blogpulse;
	var $wpsd_option_netlog;
	var $wpsd_option_99designs;
	var $wpsd_option_quora;
	var $wpsd_option_blogger;
	var $wpsd_option_getglue;
	var $wpsd_option_googlebot;
	var $wpsd_option_archive;
	var $wpsd_option_w3c;
	var $wpsd_option_views;
	var $wpsd_option_age;
	var $wpsd_option_est;
	var $wpsd_option_powered;
	var $wpsd_option_socialmention;
	var $wpsd_option_googleplus;
	var $wpsd_option_hunch;
	var $wpsd_option_peerindex;
	
	var $active_opts_count = 0;
	var $total_opts_count = 0;
	
	/**
	 * WPSDAdminConfigMetricsForm function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDAdminConfigMetricsForm(){

		parent::WPSDBaseForm();

		if($this->setFormValues()){

			$this->saveOptions();
		}

		$this->loadOptions();	
		
		$this->exclude_opts = array(
			'engagement', 
			'pagerank', 
			'backlinks', 
			'socialgraph', 
			'alexa', 
			'compete', 
			'mozrank', 
			'postrank', 
			'bing', 
			'views', 
			'age', 
			'est', 
			'powered');

	}
	
	/**
	 * getOpts function.
	 * 
	 * @access public
	 * @return array
	 */
	function getOpts() {
		
		$opts = array();
		
		$config_vars = $this->getClassVars();
				
		if(null != $config_vars && is_array($config_vars)) {
			
			$metrics = wpsd_get_metrics_types();
			
			$this->total_opts_count = count($metrics) - count($this->exclude_opts);
			
			foreach($metrics as $k => $v) {
				
				if(isset($config_vars['wpsd_option_' . $k])) {
							
					$opts[$k] = $config_vars['wpsd_option_' . $k];
					
					if($opts[$k]) { 
						
						if(!in_array($k, $this->exclude_opts)) { 
							
							$this->active_opts_count++; 
						} 
					}
				}
			}
		}
		
		return $opts;
	}
	
	/**
	 * getActiveOpts function.
	 * 
	 * @access public
	 * @return integer active opts
	 */
	function getActiveOpts() {
		
		return $this->active_opts_count;
	}
	
	/**
	 * getTotalOpts function.
	 * 
	 * @access public
	 * @return integer total opts
	 */
	function getTotalOpts() {
		
		return $this->total_opts_count;
	}
}
?>