<?php
/**
 * UADetector.class.php
 * Description: a PHP4 class for browser and spider identification
 * Updated: 2011-07-13
 * @version 0.9a5
 * @author Helene Duncker <techfromhel@gmail.com>
 * Author URI: http://techfromhel.webege.com/
 *
 * @copyright Copyright (c) 2009-2011 Helene Duncker
 * @license http://www.gnu.org/licenses/gpl.txt GNU General Public License
 */
/**
 * Usage: include_once(uadetector.class.php); 
 *	  $useragent = new UADetector(); //returns object(16)
 *
 * Note: UADetector attempts to find the actual browser in use. This may 
 *    cause the "name" field to differ from "emulation" field when 
 *    user-agent "spoofing" is detected. Use the appropriate field 
 *    for your application type:
 *      a) "Name" field is best for information and statistics only.
 *      b) "Emulation" field is best for UI and customizations by browser type.
 *
 * Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
 */
class UADetector {
	var $name = '';  	//browser or spider name, not null
	var $version = '';	//browser or spider version, if available
	var $os = '';    	//operating system+version, if available
	var $platform = '';	//operating system or device platform
	var $emulation = '';	//emulated browser plus major version#
	var $language = '';	//language code (not locale), if available
	var $device = '';	//PC, PDA, Phone, TV, if available
	var $model = '';	//Device manufacturer model, if available
	var $resolution = '';	//screen size (MMMxNNN), if in user-agent
	var $subscribers = '';	//feed subscriber count, if in user-agent
	var $is_mobile = false;
	var $is_browser = false;
	var $is_robot = false;
	var $is_spammer = false; //spam script injection code present
	var $agent = '';  	//user-agent (unspoofed, if possible)
	var $agenttype = '';	//browser or spider type, not null:
				//  B=Browser, 
				//  F=feedreader (could also have type=R) 
				//  R=robot (spider|archiver|validator), 
				//  S=Spammer/Script injector 
	//var $done_browsers = false;	//private
	//var $done_spiders = false;	//private
	//var $is_active_agent = false;	//private 

	/**
	 * For PHP 4 compatibility.
	 * @access	public
	 * @param 	string $ua (optional)
	 * @return	none
	 */
	function uadetector($ua="") {
		$this->__construct($ua);
	}

	/**
	 * Main Contructor Class
	 * Sets User Agent information such as name, version, ...
	 * @constructor
	 * @access	public
	 * @param 	string $ua (optional)
	 * @return	object(16)
	 */
	function __construct($ua="") {
		global $wdebug_mode, $do_profile;
		$do_profile= false;
		if ($wdebug_mode && function_exists('profiler_beginSection')) {
			$do_profile = true;
			profiler_beginSection('(Subtot)uaDetector');
		}
		$this->is_browser = false;
		$this->is_mobile = false;
		$this->is_robot = false;
		$this->is_spammer = false;

		//initialize private booleans
		$this->done_browsers = false; //true after isBrowserAgent() is parsed
		$this->done_spiders = false; //true after isSpiderAgent() is parsed
		$this->is_active_agent = false;	//true when agent is in http header
		//set agent, if not parameter
		if (empty($ua)) { 
			$this->setDeviceUA();
			$ua = $this->agent;
			$this->is_active_agent = true;	//ok to read http headers
		} else {
			$ua = trim($ua);
			$this->agent = $ua;
		}

		//Detect the brower/spider...
		//exclude invalid agents from full check
		if (!$this->isValidAgent($ua) && empty($this->name)) {
			$this->isUnknownAgent($ua);
		} else {
		//check user-agent data and set all variables...
			if ($this->isTopAgent()===false) {
			if ($this->isBrowserAgent()===false) {
			if ($this->isSpiderAgent()===false) {
				$this->isWTF();
			}}}
		}

		//set booleans
		if ($this->agenttype == "B") {
			$this->is_browser = true;
		} elseif ($this->agenttype == "R") {
			$this->is_robot = true;
		} elseif ($this->agenttype == "S") {
			$this->is_spammer = true;
		}
		if (empty($this->is_mobile) && $this->platform == "WAP") {
			$this->is_mobile = true;
		//TODO: assume mobile if screen width <400 & >132 ??
		}

		//set browser emulation field
		if (empty($this->emulation)) {
			$this->setEmulation();
		}
		//lastly, unset temporary private booleans...
		unset($this->done_browsers,$this->done_spiders,$this->is_active_agent);

		if ($do_profile) {
			profiler_endSection('(Subtot)uaDetector');
		}
		return;
	} //end function __construct
	
	/**
	 * Check user agent against a known list of top user agents
	 * @return associative array containing agent details
	 */
	function isTopAgent($agent="") {
		//NOTE: Top agents are based on recent log data from 
		//  "WassUp", a web statistics plugin for WordPress 2.2+ 
		//   available at http://www.wpwp.org
		global $wdebug_mode, $do_profile;

		// User agent parameter or class variable is required.
		$ua="";
		$is_current_ua = false;
		if (empty($agent)) {
			list($ua,$is_current_ua) = $this->isCurrentAgent();
		} else {
			$ua=$agent;
		}
		if (empty($ua)) {  //nothing to check
			return false;
		}
		if ($do_profile) {
			profiler_beginSection('____ua::isTopAgent');
		}
		$os="";
		$top_ua = array('name'=>"",'version'=>"",'os'=>"",'platform'=>"",'language'=>"",'agenttype'=>"",'resolution'=>"");

		// #1 Googlebot 
		if (preg_match("#^Mozilla/\d\.\d\s\(compatible;\sGooglebot/(\d\.\d);[\s\+]+http\://www\.google\.com/bot\.html\)$#i",$ua,$match)>0) {
			$top_ua['name'] = "Googlebot";
			$top_ua['version']=$match[1];
			$top_ua['agenttype']= "R";
		// #2 IE 8|7|6 on Windows7|2008|Vista|XP|2003|2000
		} elseif (preg_match('#^Mozilla/\d\.\d\s\(compatible;\sMSIE\s(\d+)(?:\.\d+)+;\s(Windows\sNT\s\d\.\d(?:;\sW[inOW]{2}64)?)(?:;\sx64)?;?(?:\sSLCC1;?|\sSV1;?|\sGTB\d;|\sTrident/\d\.\d;|\sFunWebProducts;?|\s\.NET\sCLR\s[0-9\.]+;?|\s(Media\sCenter\sPC|Tablet\sPC)\s\d\.\d;?|\sInfoPath\.\d;?)*\)$#',$ua,$match)>0) {
			$top_ua['name'] = 'IE';
			$top_ua['version'] = $match[1];
			$top_ua['platform']='Windows'; 
			$os = $match[2];
			$top_ua['os'] = $this->winOSversion($os);
			$top_ua['agenttype']= 'B';
			if (!empty($match[3])) {
				$top_ua['device'] = $match[3];
			}
		// #3 Firefox and other Mozilla browsers on Windows
		} elseif (preg_match('#^Mozilla/\d\.\d\s\(Windows;\sU;\s(.+);\s([a-z]{2}(?:\-[A-Za-z]{2})?);\srv\:\d(?:\.\d+)+\)\sGecko/\d+\s([A-Za-z\-0-9]+)/(\d+(?:\.\d+)+)(?:\s\(.*\))?$#',$ua,$match)>0) {
			$top_ua['name'] = $match[3];
			$top_ua['version'] = $match[4];
			$top_ua['language']=$match[2];
			$top_ua['platform']="Windows"; 
			$os = $match[1];
			$top_ua['os'] = $this->winOSversion($os);
			$top_ua['agenttype']= 'B';
		// #4 Yahoo!Slurp
		} elseif (preg_match('#^Mozilla/\d\.\d\s\(compatible;\s(Yahoo\!\s([A-Z]{2})?\s?Slurp)/?(\d\.\d)?;\shttp\://help\.yahoo\.com/.*\)$#i',$ua,$match)>0) {
			$top_ua['name'] = $match[1];
			if (!empty($match[3])) { 
				$top_ua['version']=$match[3];
			}
			if (!empty($match[2])) { 
				$top_ua['language']=$match[2];
			}
			$top_ua['agenttype']= 'R';
		// #5 BingBot
		} elseif (preg_match('#^Mozilla/\d\.\d\s\(compatible;\sbingbot/(\d\.\d)[^a-z0-9]+http\://www\.bing\.com/bingbot\.htm.$#',$ua,$match)>0) {
			$top_ua['name'] = 'BingBot';
			if (!empty($match[1])) { 
				$top_ua['name'].=$match[1];
			}
			if (!empty($match[2])) { 
				$top_ua['version']=$match[2];
			}
			$top_ua['agenttype']= 'R';
		// #6 FeedBurner
		} elseif (preg_match('#^FeedBurner/(\d\.\d)\s\(http\://www\.FeedBurner\.com\)$#',$ua,$match)>0) {
			$top_ua['name'] = 'FeedBurner';
			$top_ua['version']=$match[1];
			$top_ua['agenttype']= 'F';
		// #7 Wordpress
		} elseif (preg_match('#^WordPress/(?:wordpress(\-mu)\-)?(\d\.\d+)(?:\.\d+)*(?:\-[a-z]+)?(?:\;\shttp\://[a-z0-9_\.\:\/]+)?$#',$ua,$match)>0) {
			$top_ua['name'] = 'Wordpress';
			if (!empty($match[1])) { 
				$top_ua['name']=$top_ua['name'].$match[1];
			}
			$top_ua['version']=$match[2];
			$top_ua['agenttype']= 'U';
		// #8 Firefox and Gecko browsers on Mac|*nix|OS/2 etc...
		} elseif (preg_match('#^Mozilla/\d\.\d\s\((Macintosh|X11|OS/2);\sU;\s(.+);\s([a-z]{2}(?:\-[A-Za-z]{2})?)(?:-mac)?;\srv\:\d(?:.\d+)+\)\sGecko/\d+\s([A-Za-z\-0-9]+)/(\d+(?:\.[0-9a-z\-\.]+))+(?:(\s\(.*\))(?:\s([A-Za-z\-0-9]+)/(\d+(?:\.\d+)+)))?$#',$ua,$match)>0) {
			$top_ua['name'] = $match[4];
			$top_ua['version'] = $match[5];
			$top_ua['language']=$match[3];
			$top_ua['platform']=$match[1];
			$os = $match[2];
			if (!empty($match[7])) { 
				$top_ua['name'] = $match[7];
				$top_ua['version'] = $match[8];
				$os=$os." ".$match[4]." ".$match[5];
			} elseif (!empty($match[6])) { 
				$os=$os.$match[6];
			}
			list($top_ua['os']) = $this->OSversion($os,$top_ua['platform'],$ua);
			$top_ua['agenttype']= 'B';
		// #9 Safari and Webkit-based browsers on all platforms
		} elseif (preg_match('#^Mozilla/\d\.\d\s\(([A-Za-z0-9/\.]+);\sU;?\s?(.*);\s?([a-z]{2}(?:\-[A-Za-z]{2})?)?\)\sAppleWebKit/[0-9\.]+\+?\s\((?:KHTML,\s)?like\sGecko\)(?:\s([a-zA-Z0-9\./]+(?:\sMobile)?)/?[A-Z0-9]*)?\sSafari/([0-9\.]+)$#',$ua,$match)>0) {
			$top_ua['name'] = 'Safari';
			if (!empty($match[4])) { $vers = $match[4]; } 
			else { $vers = $match[5]; }
			$browser = $this->webkitVersion($vers,$ua);
			if (!empty($browser) && is_array($browser)) {
				$top_ua['name'] = $browser['name'];
				$top_ua['version'] = $browser['version'];
			}
			if (empty($match[2])) {
				$os = $match[1];
			} else {
				$top_ua['platform'] = $match[1];
				$os = $match[2];
			}
			if ($top_ua['platform'] == 'Windows') {
				$top_ua['os'] = $this->winOSversion($os);
			} else {
				list($top_ua['os']) = $this->OSversion($os,$top_ua['platform'],$ua);
			}
			$top_ua['language']=$match[3];
			$top_ua['agenttype']= 'B';

		// #10 Google Chrome browser on all platforms with or without language string
		} elseif (preg_match('#^Mozilla/\d+\.\d+\s(?:[A-Za-z0-9\./]+\s)?\((?:([A-Za-z0-9/\.]+);(?:\sU;)?\s?)?([^;]*)(?:;\s[A-Za-z]{3}64)?;?\s?([a-z]{2}(?:\-[A-Za-z]{2})?)?\)\sAppleWebKit/[0-9\.]+\+?\s\((?:KHTML,\s)?like\sGecko\)(?:\s([A-Za-z0-9_\-]+[^i])/([A-Za-z0-9\.]+)){1,3}(?:\sSafari/[0-9\.]+)?$#',$ua,$match)>0) {
			$top_ua['name'] = $match[4];
			$top_ua['version'] = $match[5];
			if (empty($match[2])) {
				$os = $match[1];
			} else {
				$top_ua['platform'] = $match[1];
				$os = $match[2];
			}
			if ($top_ua['platform'] == 'Windows') {
				$top_ua['os'] = $this->winOSversion($os);
			} else {
				list($top_ua['os']) = $this->OSversion($os,$top_ua['platform'],$ua);
			}
			if (!empty($match[3])) {
				$top_ua['language']=$match[3];
			}
			$top_ua['agenttype']= 'B';
		}
		//check http header for user agent spoofing and for os and screen resolution
		if ($is_current_ua) {
			list($name,$os,$platform,$resolution,$uatype) = $this->getHeaderData();
			if (!empty($name)) {
				$top_ua['name'] = $name;
				if (!empty($uatype)) $top_ua['agenttype'] = $uatype;
			}
			if (!empty($os)) $top_ua['os'] = $os;
			if (!empty($resolution)) $top_ua['resolution'] = $resolution;
		}
		//set class vars and return array
		if (!empty($top_ua['name'])) {
			//if agent is class var, set other class vars.
			if (empty($agent)) {
				$this->setClassVars($top_ua);
			}
		} else {
			$top_ua=false;
		}
		if ($wdebug_mode) {
			echo "\n".'<br />uadetector: top_ua->name='.$top_ua['name']."&nbsp; ".'top_ua->os='.$top_ua['os']; //debug
			if ($do_profile) {
				profiler_endSection('____ua::isTopAgent');
			}
		}
		return ($top_ua);
	} //end function isTopAgent

	/**
	 * detect browsers 
	 * @access public
	 * @param string (optional)
	 * @return array (associative)
	 */
	function isBrowserAgent($agent="") {
		global $wdebug_mode, $do_profile;
		$ua="";
		$is_current_ua = false;
		if (empty($agent)) {
			list($ua,$is_current_ua) = $this->isCurrentAgent();
		} else {
			$ua=$agent;
		}
		if (empty($ua)) {  //nothing to check
			return false;
		}
		//##detect browsers
		$browser = array('name'=>"",'version'=>"",'os'=>"",'platform'=>"",'language'=>"",'agenttype'=>"B",'resolution'=>"",'device'=>"",'model'=>"", 'emulation'=>"");

		//spiders are not detected here, so exclude user agents that are likely spiders (ie. contains an email or URL, or spider-like keywords)
		if (isset($this->done_spiders) && !$this->done_spiders && preg_match('#(robot|bot[\s\-_\/\)]|bot$|blog|checker|crawl|feed|fetcher|libwww|[^\.e]link\s?|parser|reader|spider|verifier|href|https?\://|.+(?:\@|\s?at\s?)[a-z0-9_\-]+(?:\.|\s?dot\s?)|www[0-9]?\.[a-z0-9_\-]+\..+|\/.+\.(s?html?|aspx?|php5?|cgi))#i',$ua)>0) {
			//not spider if embedded browser or is a browser add-on such as spyware or translator
			if (preg_match('#(embedded\s?(WB|Web\sbrowser)|dynaweb|bsalsa\.com|muuk\.co|translat[eo]r?)#i',$ua)==0) {
				return false;
			}
		}
		if ($do_profile) {
			profiler_beginSection('_____ua::isBrowser');
		}
		//### Step 1: check for mobile or embedded browsers
		$ismobile = false;
		$wap = $this->isMobileAgent($ua);
		if (!empty($wap) && is_array($wap)) {
			$ismobile = true;
			$browser['name'] = $wap['name'];
			$browser['version'] = $wap['version'];
			$browser['device'] = $wap['device'];
			$browser['model'] = $wap['model'];
			$browser['os'] = $wap['os'];
			$browser['platform'] = "WAP";
			if (!empty($wap['language'])) $browser['language'] = $wap['language'];
		}
		//### Step 2: Check for MSIE-based browsers
		if (!$ismobile || empty($browser['name'])) {
			if (preg_match('/compatible(?:\;|\,|\s)+MSIE\s(\d+)(\.\d+)+(.*)/',$ua,$pcs)>0) {
				if ($wdebug_mode) {
					echo "\nMatch for MSIE-based browser<br/>";
				}
				$browser['name'] = 'IE';
				$browser['version'] = $pcs[1];
				$browser['emulation'] = rtrim('IE'." ".$this->majorVersion($pcs[1]));
				$iestring = $pcs[3];
				//differentiate IE from IE-based and IE-masked browsers or spiders
				if (preg_match('/\s(AOL|America\sOnline\sBrowser)\s(\d+(\.\d+)*)/',$iestring,$pcs)>0) {
					$browser['name'] = 'AOL';
					$browser['version'] = $pcs[2];

				} elseif (preg_match('#\s(Opera|Netscape|Crazy\sBrowser)/?\s?(\d+(?:\.\d+)*)#',$iestring,$pcs)>0) {
					$browser['name'] = $pcs[1];
					$browser['version'] = $pcs[2];

				} elseif (preg_match('/\s(Avant|Orca)\sBrowser;/',$iestring,$pcs)>0) {
					$browser['name'] = $pcs[1];
					$browser['version'] = "";

				} elseif (preg_match('/Windows\sCE;\s?IEMobile\s(\d+)(\.\d+)*\)/i',$iestring,$pcs)>0) {
					$browser['name'] = 'IEMobile';
					$browser['version'] = $pcs[1];
					$browser['os'] = 'WinCE';
					$browser['platform'] = 'WAP';
					$ismobile=true;
				} elseif (preg_match('#\s(\d+x\d+)?\;?\s?(?:WebTV|MSNTV)(?:/|\s)([0-9\.]+)*#i',$iestring,$pcs)>0) {
					$browser['name'] = "MSNTV";
					$browser['version'] = $pcs[2];
					$browser['platform'] = 'Embedded';
					$browser['device'] = 'TV';
					if (!empty($pcs[1])) {
						$browser['resolution'] = $pcs[1];
					}
				}

		//### Step 3: Check for All Other browsers
			//Opera browsers
			} elseif (preg_match('#Opera[/ ]([0-9\.]+)#',$ua,$pcs)>0) {
				$browser['name'] = 'Opera';
				$browser['version'] = $pcs[1];

			//Firefox-based browsers (Camino, Flock) (find before FF)
			} elseif (preg_match('#[^a-z](Camino|Flock|Galeon|Orca)/(\d+[\.0-9a-z]*)#',$ua,$pcs)>0) {
				if ($wdebug_mode) {
					echo "\nMatch for Firefox-based browser<br/>";
				}
				$browser['name'] = $pcs[1];
				$browser['version'] = $pcs[2];

			//other Gecko-type browsers (incl. Firefox)
			} elseif (preg_match('#Gecko/\d+\s([a-z0-9_\- ]+)/(\d+[\.0-9a-z]*)(?:$|[^a-z0-9_\-]+([a-z0-9_\- ]+)/(\d+[\.0-9a-z]*)|[^a-z0-9_\-]*\(.*\))#i',$ua,$pcs)>0) {
				if ($wdebug_mode) {
					echo "\nMatch for Gecko-type browser<br/>";
				}
				$browser['name'] = $pcs[1];
				$browser['version'] = $pcs[2];
				if (!empty($pcs[3]) && stristr($pcs[3],"Firefox")!==false) {
					$browser['name'] = 'Firefox';
					$browser['version'] = $pcs[4];
				}
			//Firefox browser
			} elseif (preg_match('#[^a-z](Fire(?:fox|bird))/?(\d+[\.0-9a-z]*)?#',$ua,$pcs)>0) {
				if ($wdebug_mode) {
					echo "\nMatch for Firefox browser<br/>";
				}
				$browser['name'] = $pcs[1];
				if (!empty($pcs[2])) {
					$browser['version'] = $pcs[2];
				}
			//Mozilla browser (like FF, but nothing after "rv:" or "Gecko")
			} elseif (preg_match('/^Mozilla\/\d\.\d.+\srv\:(\d[\.0-9a-z]+)[^a-z0-9]+(?:Gecko\/\d+)?$/i',$ua,$pcs)>0) {
				if ($wdebug_mode) {
					echo "\nMatch for Mozilla browser<br/>";
				}
				$browser['name'] = 'Mozilla';
				if (!empty($pcs[1])) {
					$browser['version'] = $pcs[1];
				}

			//WebKit-based browsers
			} elseif (preg_match('#^Mozilla/\d\.\d\s\((?:([a-z]{3,}.*\s)?([a-z]{2}(?:\-[A-Za-z]{2})?)?)\)\sAppleWebKit/[0-9\.]+\+?\s\([a-z, ]*like\sGecko[a-z\; ]*\)\s([a-zA-Z0-9\./]+(?:\sMobile)?/?[A-Z0-9]*)?(\sSafari/([0-9\.]+))?$#',$ua,$pcs)>0) {
				if ($wdebug_mode) {
					echo "\nMatch for WebKit-based browser<br/>";
				}
				if (!empty($pcs[3])) { $vers = $pcs[3]; } 
				else { $vers = $pcs[5]; }
				$webkit = $this->webkitVersion($vers,$ua);
				if (!empty($webkit['name'])) {
					$browser['name'] = $webkit['name'];
					$browser['version'] = $webkit['version'];
				}
				if (!empty($pcs[2])) {
					$browser['language'] = $pcs[2];
				}
		
			//Text-only browsers Lynx, ELinks...(yep, they still exist)
			} elseif (preg_match("#^(E?Links|Lynx|(?:Emacs\-)?w3m)[^a-z0-9]+([0-9\.]+)?#i",$ua,$pcs)) {
				if ($wdebug_mode) {
					echo "\nMatch for text browser<br/>";
				}
				$browser['name'] = $pcs[1];
				if (!empty($pcs[2])) {
					$browser['version'] = $pcs[2];
				}

			//Some obscure browsers
			} elseif (preg_match("#(?:^|[^a-z0-9])(ActiveWorlds|Dillo|OffByOne)[/\sv\.]*([0-9\.]+)?#i",$ua,$pcs)) {
				$browser['name'] = $pcs[1];
				if (!empty($pcs[2])) {
					$browser['version'] = $pcs[2];
				} else {
					$browser['version'] = "";
				}
			}
			//TODO: Embedded web browsers (EZLinks, bsalsa)
			//
			if (empty($browser['name'])) {
			//Any browser that use the word "browser" in agent
			if (preg_match("#([a-z0-9]+)[\- _\.]Browser[/ v\.]*([0-9\.]+)?#i",$ua,$pcs)) {
				$browser['name'] = $pcs[1];
				if (!empty($pcs[2])) {
					$browser['version'] = $pcs[2];
				} else {
					$browser['version'] = "";
				}

			//simple alphanumeric strings are usually a crawler
			} elseif (preg_match("#^([a-z]+[\s_]?[a-z]*)[\-/]?([0-9\.]+)*$#",$ua,$pcs)>0) {
				$browser['name']=trim($pcs[1]);
				if (!empty($pcs[2])) { 
					$browser['version']=$pcs[2];
				}
				if (empty($browser['os']) && $browser['platform'] != "WAP" && stristr($pcs[1],'mozilla')===false) {
					$browser['agenttype']= "R";
				}
			}
			}
		} //end if (!$ismobile)

		//get operating system
		if (empty($browser['os']) && !empty($browser['name']) && $browser['agenttype']=="B") {
			list($browser['os'],$platform) = $this->OSversion('',$browser['platform'],$ua);
			if (!empty($platform) && empty($browser['platform'])) {
				$browser['platform'] = $platform;
			}
			//if (empty($browser['os']) && empty($browser['platform']) {
			//	$browser['os'] = "unknown";
			//}
		}

		//check http header for user agent spoofing and for os and screen resolution
		if ($is_current_ua) {
			list($name,$os,$platform,$resolution,$uatype) = $this->getHeaderData();
			if (!empty($name)) {
				$browser['name'] = $name;
				if (!empty($uatype)) $browser['agenttype'] = $uatype;
			}
			if (!empty($os)) $browser['os'] = $os;
			if (!empty($resolution)) $browser['resolution'] = $resolution;
		}

		if ($browser['agenttype'] == "B" && empty($browser['language'])) {
			$browser['language'] = $this->detectLanguage($ua);
			if (!empty($browser['resolution'])) {
				if (!empty($wap) || $browser['platform'] == 'WAP') {
					$browser['resolution'] = $this->detectResolution($ua,'WAP');
				} else {
					$browser['resolution'] = $this->detectResolution($ua);
				}
			}
		}
		//check for script injection in user-agent string
		if ($this->isSpammer($ua) !== false) {
			if (function_exists('__')) {
				$browser['name'] = __("Script Injection Bot");
			} else {
				$browser['name'] = "Script Injection Bot";
			}
			$browser['agenttype']= "S";
		}
		if ($wdebug_mode) {
			echo "\n".'<br />uadetector: browser->name='.$browser['name']."&nbsp; ".'browser->os='.$browser['os']; //debug
		}
		if (!empty($browser['name'])) {
			$browser['name'] = rtrim($browser['name'],'_- ');
			if (empty($agent)) {
				$this->setClassVars($browser);
				if ($ismobile || $browser['platform'] == 'WAP' || strstr($browser['name'],' Mobile')!==false || strstr($browser['name'],' Mini')!==false) {
					$this->is_mobile = true;
				}
			}
		} else {
			$browser=false;
		}
		$this->done_browsers=true;
		if ($do_profile) {
			profiler_endSection('_____ua::isBrowser');
		}
		return $browser;
	} //end function isBrowserAgent

	/**
	 * detect mobile device browsers or other embedded browsers
	 * @access public
	 * @param string
	 * @return array(browser, device, model, platform)
	 */
	function isMobileAgent($agent="") {
		global $wdebug_mode, $do_profile;
		$ua="";
		$is_current_ua = false;
		if (empty($agent)) {
			list($ua,$is_current_ua) = $this->isCurrentAgent($agent);
		} else {
			$ua=$agent;
		}
		if (empty($ua)) {  //nothing to check
			return false;
		}
		if ($do_profile) {
			profiler_beginSection('____ua::isMobileUA');
		}
		$ismobile = false;
		//$device = $ua;
		$wap = array('name'=>"", 'version'=>"",
			'device'=>"", 'model'=>"",
			'os'=>"", 'platform'=>"WAP");
		//detect known mobile browsers

		//Android-based devices
		if (preg_match("#^(?:([a-z0-9\-\s_]{3,})\s)?Mozilla/\d\.\d\s\([a-z\;\s]+Android\s([0-9\.]+)(?:\;\s([a-z]{2}(?:\-[A-Za-z]{2})?)\;)?.*Gecko\)\s([a-zA-Z0-9\./]+(?:\sMobile)?/?[A-Z0-9]*)?(\sSafari/([0-9\.]+))?#i",$ua,$pcs)) {
			if (!empty($pcs[4])) { $vers = $pcs[4]; } 
			else { $vers = $pcs[6]; }
			$webkit = $this->webkitVersion($vers,$ua);
			if (!empty($webkit['name'])) {
				$wap['name'] = $webkit['name'];
				$wap['version'] = $webkit['version'];
			}
			$wap['os'] = "Android";
			if (!empty($pcs[2])) $wap['os'] .= " ".$this->majorVersion($pcs[2]);
			if (!empty($pcs[3])) $wap['language'] = $pcs[3];
			if (!empty($pcs[1])) $wap['device'] = $pcs[1];

		//Windows Mobile browsers
		} elseif (preg_match('#Windows\sCE;\s?IEMobile\s(\d+)(\.\d+)*\)#i',$ua,$pcs)>0) {
			$wap['name'] = 'IEMobile';
			$wap['version'] = $pcs[1];
			$wap['os'] = 'WinCE';
		//Opera Mini/mobile browsers
		} elseif (preg_match('#(Opera\s(?:Mini|Mobile))[/ ]([0-9\.]+)#',$ua,$pcs)>0) {
			$wap['name'] = $pcs[1];
			$wap['version'] = $pcs[2];

		//NetFront and other mobile/embedded browsers
		} elseif (preg_match("#(NetFront|NF\-Browser)/([0-9\.]+)#i",$ua,$pcs)) {
			$wap['name'] = "NetFront";
			$wap['version'] = $pcs[2];
		} elseif (preg_match("#[^a-z0-9](Bolt|Iris|Jasmine|Minimo|Novarra\-Vision|Polaris)/([0-9\.]+)#i",$ua,$pcs)) {
			$wap['name'] = $pcs[1];
			$wap['version'] = $pcs[2];
		} elseif (preg_match("#(UP\.browser|SMIT\-Browser)/([0-9\.]+)#i",$ua,$pcs)) {
			$wap['name'] = $pcs[1];
			$wap['version'] = $pcs[2];
		} elseif (preg_match("#\((jig\sbrowser).*\s([0-9\.]+)[^a-z0-9]#i",$ua,$pcs)) {
			$wap['name'] = $pcs[1];
			$wap['version'] = $pcs[2];
		} elseif (preg_match("#[^a-z]Obigo#i",$ua,$pcs)) {
			$wap['name'] = 'Obigo';
		} elseif (preg_match("#openwave(\suntrusted)?/([0-9\.]+)#i",$ua,$pcs)) {
			$wap['name'] = 'OpenWave';
			$wap['version'] = $pcs[2];
		}
		if (!empty($wap['name'])) {
			$ismobile = true;
		}

		//known mobile devices...
		if (preg_match('#(alcatel|amoi|blackberry|docomo\s|htc|ipaq|kindle|kwc|lge|lg\-|mobilephone|motorola|nexus\sone|nokia|PDA|Palm|Samsung|sanyo|smartphone|SonyEricsson|\st\-mobile|vodafone|zte)[/\-_\s]?((?:\d|[a-z])+\d+[a-z]*)*#i',$ua,$pcs)>0) {
			$ismobile = true;
			$wap['device'] = trim($pcs[1],'-_ /');
			if (!empty($pcs[2])) { 
				$wap['model'] = $pcs[2];
			}
			if ($pcs[1] == "KWC") { 
				$wap['device'] == "Kyocera phone";
				$wap['model'] == $pcs[0];
			}
			if (empty($wap['name'])) {
				$wap['name'] = $pcs[1];
			}
		}
		//check if user-agent has mobile profile
		if (!$ismobile) {
			if (preg_match('#(J2ME/MIDP|Profile/MIDP|Danger\sHiptop|\sOpenWeb\s\d)#i',$ua)>0) {
				$ismobile=true;

			//check if browser HTTP header has a mobile profile
			} elseif ($is_current_ua) {
				$header_profile =array('X_WAP_PROFILE','PROFILE','13_PROFILE','56_PROFILE');
  				foreach ($header_profile AS $wap_profile) {
    				if (!empty($_SERVER["HTTP_{$wap_profile}"])) {
					//has a user-agent profile header, so it's probably a mobile device
					$ismobile=true;
      					break 1;
				}
				}
				//check for wireless transcoder gateways
				if (!$ismobile && !empty($_SERVER["HTTP_VIA"]) && preg_match('/([^a-z0-9]WAP|mobile)/i',$_SERVER["HTTP_VIA"])>0) {
					$ismobile=true;
				}
			}
			//TODO: check for wireless transcoder service user agents
			if (!$ismobile && preg_match('#wireless\stranscoder#i',$ua)>0) {
				$ismobile=true;
			}
		}
		//set os = device, if missing
		if ($ismobile) {
			if (!empty($wap['device'])) {
				if (empty($wap['name'])) {
					$wap['name'] = $wap['device'];
				} elseif (empty($wap['os'])) {
					$wap['os'] = $wap['device'];
				}
			}
		}
		if ($wdebug_mode) {
			echo "\n".'<br />uadetector: wap->name='.$wap['name']."&nbsp; ".'wap->device='.$wap['device']; //debug
		}
		if ($do_profile) {
			profiler_endSection('____ua::isMobileUA');
		}
		if ($ismobile) {
			if (empty($wap['os'])) $wap['os'] = "WAP";
			return $wap;
		} else {
			return false;
		}
	} //end function isMobileAgent

	/**
	 * detect crawlers, feed readers, link checkers, and other spiders
	 * @access public
	 * @param string (optional)
	 * @return array (associative)
	 */
	function isSpiderAgent($agent="") {
		global $wdebug_mode, $do_profile;
		$ua="";
		$is_current_ua = false;
		list($ua,$is_current_ua) = $this->isCurrentAgent($agent);
		if (empty($ua)) {  //nothing to check
			return false;
		}
		if ($do_profile) {
			profiler_beginSection('______ua::isSpider');
		}
		//##detect spiders
		$spider = array('name'=>"",'version'=>"",'os'=>"",'platform'=>"",'language'=>"",'agenttype'=>"R",'subscribers'=>"");
		// #11 FriendFeedBot
		if (preg_match('#^Mozilla/\d\.\d\s\(compatible;\sFriendFeedBot/([0-9\.]+);\s\+Http\://friendfeed\.com/about/bot\)$#',$ua,$match)>0) {
			$spider['name'] = 'FriendFeedBot';
			$spider['version']=$match[1];
			$spider['agenttype']= 'F';
		
		// #12 Twiceler
		} elseif (preg_match('#^Mozilla/\d\.\d\s\(Twiceler\-(\d\.\d)\shttp://www\.cuill?\.com/twiceler/robot\.html\)$#',$ua,$match)>0) {
			$spider['name'] = 'Twiceler';
			$spider['version']=$match[1];
			$spider['agenttype']= 'R';

		// #13 FeedFetcher Google
		} elseif (preg_match('#^Feedfetcher\-Google[;\s\(\+]+http\://www\.google\.com/feedfetcher\.html[;\)\s]+(?:(\d)\ssubscriber)?#',$ua,$match)>0) {
			$spider['name'] = 'FeedFetcher-Google';
			if (!empty($match[1])) {
				$spider['subscribers']= $match[1];
			}
			$spider['agenttype']= 'F';

		//Twitterfeed
		} elseif (preg_match('/[^a-z]twitterfeed/i',$ua,$match)>0) {
			$spider['name'] = 'Twitterfeed';
			$spider['agenttype']= 'F';

		// Nutch spiders
		} elseif (preg_match('#^([a-z]+)?/?nutch\-([0-9\.]+)#i',$ua,$match)>0) {
			if (!empty($match[1])) {
				$spider['name']= $match[1];
			} else {
				$spider['name']= 'Nutch';
			}
			$spider['version']=$match[2];
			$spider['platform'] = "Nutch";

		// Larbin spiders
		} elseif (preg_match('#^larbin[\-_\s\/]?(v?[0-9\.]+)?#i',$ua,$match)>0) {
			$spider['name']= 'Larbin';
			$spider['platform'] = "larbin";
			if (!empty($match[1])) {
				$spider['version']=$match[1];
			}
		} elseif (preg_match('#^([a-z_]+)[\-\s\/]?(v?[0-9\.]+)?[^a-z]+larbin([0-9\.]+)\@#i',$ua,$match)>0) {
			$spider['name']= $match[1];
			$spider['platform'] = "larbin";
			if (!empty($match[2])) {
				$spider['version']=$match[2];
			}

		// #Yahoo spiders
		} elseif (preg_match('#^Mozilla/\d\.\d[^a-z0-9_\-]+(Yahoo[\-\!\s_]+[a-z]+)/?([0-9\.]+)?[^a-z0-9_\-]+.+yahoo.*\.com#i',$ua,$match)>0) {
			$spider['name'] = $match[1];
			if (!empty($match[2])) {
				$spider['version']=$match[2];
			}

		// #Microsoft winHTTP-based spiders
		} elseif (preg_match('#WinHTTP#i',$ua,$match)>0) {
			$spider['name'] = "WinHTTP";

		// #Apple CFNetwork-based spiders
		} elseif (preg_match('#^((?:[a-z]|\%20)+)\/?([0-9\.]+).*[^a-z0-9]CFNetwork\/?([0-9\.]+)#',$ua,$match)>0) {
			$spider['name'] = $match[1];
			if (!empty($match[2])) {
				$spider['version']=$match[2];
			}

		// #Caching agents, Proxy server agents
		} elseif (preg_match('/^Mozilla\/\d\.\d\s\(compatible\;\s(HTTrack|ICS)(?:\s(\d\.[a-z0-9]+))?[^a-z0-9\s]/',$ua,$match)>0) {
			$spider['name'] = $match[1];
			if (!empty($match[2])) {
				$spider['version']=$match[2];
			}
			if (strlen($match[1]) < 5) {
				$spider['name'] = $match[1]. " Spider";
			}

		//TODO: Libwww spiders
		//TODO: Trackback agents from blogs on MovableType, Drupal, DotNetNuke,...

		// #Assume bot if user-agent includes a url (http|www) with a name repeated
		} elseif (preg_match('/^(?:Mozilla\/.*compatible[^a-z]*)?(([a-z]{3,})[\-\s_]?(?:bot|crawl|robot|spider|parser|reader)?[a-z]*)[^a-z^0-9]+v?\s?([0-9\.]+)?.*[^a-z]+(?:http|www).*[^a-z]+(?:\2|\3)\/?(?:\.?[a-z]+)?\.(?:com|net|org|html?|aspx?|[a-z]{2})/i',$ua,$match)>0) {
			$spider['name'] = $match[1];
			if (!empty($match[3])) {
				$spider['version'] = $match[3];
			}
		} elseif (preg_match('#^Mozilla\/\d\.\d\s\(compatible;\s([a-z_ ]+)(?:[-/](\d+\.\d+))?;\s.?http://(?:www\.)?[a-z]+(?:[a-z\.]+)\.(?:[a-z]{2,4})/?[a-z/]*(?:\.s?html?|\.php|\.aspx?)?\)$#i',$ua,$match)>0) {
			$spider['name'] = $match[1];
			if (!empty($match[2])) {
				$spider['version'] = $match[2];
			}

		// #Assume bot if user-agent 1st word and a contact domain are the same name, ex: Feedburner-feedburner.com, CazoodleBot, 
		} elseif (preg_match('/([a-z\_\s\.]+)[\s\/\-_]?(v?[0-9\.]+)?.*(?:http\:\/\/|www\.)(\1)\.[a-z0-9_\-]+/i',$ua,$match)>0) {
			$spider['name'] = $match[1];
			if (!empty($match[2])) {
				$spider['version'] = $match[2];
			}
		// #Assume bot if one-word user-agent+http address
		} elseif (preg_match('/^([a-z\_\.]+)[\s\/\-_]?(v?[0-9\.]+)?[\s\(\+]*(?:http\:\/\/|www\.)[a-z0-9_\-]+\.[a-z0-9_\-\.]+\)?/i',$ua,$match)>0) {
			$spider['name'] = $match[1];
			if (!empty($match[2])) {
				$spider['version'] = $match[2];
			}
		// #Assume bot if name+http://name...
		} elseif (preg_match('/([a-z]+[a-z0-9]{2,})[\s\/\-]?([0-9\.]+)?[^a-z]+[^0-9]*http\:.*\/(\1)[^a-z]/i',$ua,$match)>0) {
			$spider['name'] = $match[1];
			if (!empty($match[2])) {
				$spider['version'] = $match[2];
			}

		// #Assume bot if name+name@emaildomain...
		} elseif (preg_match('/([a-z]+[a-z0-9]{2,})[\s\/\-]?([0-9\.]+)?.*[^a-z0-9](\1)@[a-z0-9\-_]{2,}\.[a-z0-9\-_]{2,}/i',$ua,$match)>0) {
			$spider['name'] = $match[1];
			if (!empty($match[2])) {
				$spider['version'] = $match[2];
			}

		// #Assume bot if user-agent includes contact email
		} elseif (preg_match('#^Mozilla\/\d\.\d\s\(compatible;\s([a-z_ ]+)(?:[-/](\d+\.\d+))?;\s[^a-z0-9]?([a-z0-9\.]+@[a-z0-9]+\.[a-z]{2,4})\)$#i',$ua,$match)>0) {
			$spider['name'] = $match[1];
			if (!empty($match[2])) {
				$spider['version'] = $match[2];
			}
		} elseif (preg_match('/^(([a-z]+)\s?(bot|crawler|robot|spider|\s[a-z]+)?)[\/\-\s_](v?[0-9\.]+)?.*[^a-z]+(?:\1|\2|\3)(?:\@|\s?at\s?)[a-z\-_]{2,}(?:\.|\s?dot\s)[a-z]{2,4}/i',$ua,$match)>0) {
			$spider['name'] = $match[1];
			if (!empty($match[4])) {
				$spider['version'] = $match[4];
			}
		} elseif (preg_match('/^(([a-z]+)\s?(bot|crawler|robot|spider|\s[a-z]+)?)[\/\-\s_](v?[0-9\.]+)?.*[^a-z]+[a-z\-_]+(?:\@|\s?at\s?)(?:\1|\2|\3)(?:\.|\s?dot\s)[a-z]{2,4}/i',$ua,$match)>0) {
			$spider['name'] = $match[1];
			if (!empty($match[4])) {
				$spider['version'] = $match[4];
			}
		} elseif (preg_match('/^([a-z]+)[\/\-\s_](v?[0-9\.]+)?.*[a-z0-9_\.]+(?:\@|\sat\s)[a-z0-9\-_]+(?:\.|\s?dot\s)[a-z]{2,4}[^a-z]/i',$ua,$match)>0) {
			$spider['name'] = $match[1];
			if (!empty($match[2])) {
				$spider['version'] = $match[2];
			}

		// Assume bot if user-agent contains (http|www)name 
		//   followed by name(+)/version. Ex: Daumoa spider
		} elseif (preg_match('/(?:http|www[a-z0-9]?)[^a-z].*[^a-z]([a-z0-9\-_]{4,}).*\.(?:com|net|org|biz|info|html?|aspx?|[a-z]{2})[^a-z0-9]+(\1[a-z_\-]+)[\/|\s|v]+([\d\.]+)/i',$ua,$match)>0) {
			$spider['name'] = $match[2];
			$spider['version'] = $match[3];

		// #Assume bot if one-word user-agent.
		} elseif (preg_match('/^([a-z\_\.]+)[\s\/\-_]?(v?[0-9\.]+)?$/i',$ua,$match)>0) {
			$spider['name'] = $match[1];
			if (!empty($match[2])) {
				$spider['version'] = $match[2];
			}
		// Assume bot if user-agent contains (http|www)name 
		//   followed by name(+)/version. Ex: Daumoa spider
		} elseif (preg_match('/(?:http|www[a-z0-9]?)[^a-z].*[^a-z]([a-z0-9\-_]{4,}).*\.(?:com|net|org|biz|info|html?|aspx?|[a-z]{2})[^a-z0-9]+(\1[a-z_\-]+)[\/|\s|v]+([\d\.]+)/i',$ua,$match)>0) {
			$spider['name'] = $match[2];
			$spider['version'] = $match[3];

		// #Assume bot if single-word user-agent.
		} elseif (preg_match('/^([a-z\_\.]+)[\s\/\-_]?(v?[0-9\.]+)?$/i',$ua,$match)>0) {
			$spider['name'] = $match[1];
			if (!empty($match[2])) {
				$spider['version'] = $match[2];
			}

		//Spiders with bot, spider, or crawler in name plus version
		} elseif (preg_match('#(\w+[\s\-_]?(?:bot|crawler|checker|feed|parser|reader|spider|verifier))(?:[\/\s\-\:_])?v?([0-9\.]+)#i',$ua,$match)>0) {
			$spider['name'] = $match[1];
			$spider['version']=$match[2];
		
		//Spiders with bot, reader, spider, or crawler with no version#
		} elseif (preg_match('#([a-z\s]*(?:blog|feed|site)?[a-z\s\-_]*(?:bot|checker|crawler|reader|parser|spider|verifier))(?:$|^[a-z])#i',$ua,$match)>0) {
			$spider['name'] = $match[1];
			$spider['version']="";
		

		//Some obscure spiders
		} elseif (preg_match('#(\spowermarks)\/([0-9\.]+)#i',$ua,$match)>0) {
			$spider['name'] = $match[1];
			$spider['version'] = $match[2];
		} else {
			if (preg_match("#(robot|bot[\s\-_\/]|bot$|crawl|spider|feed[\s\-_\/]|feed$|fetcher|parser|reader|href|[^\.e]link[\s\-_\/]|linkcheck|checker|http\:\/\/|[^a-z]www[0-9]?\.[a-z0-9_\-]+\.[a-z]{2,3}[^a-z])#i",$ua)>0) {
				$spider['name'] = "Unknown Spider";
				$spider['agenttype']= "R";
			} elseif (preg_match("#([a-z0-9_]+(?:\@|\sat\s)[a-z0-9_\-]+(?:\.|\sdot\s)|\/.+\.(?:html?|aspx?|php5?|cgi))#i",$ua)>0) {
				$spider['name'] = "Unknown Spider";
				$spider['agenttype']= 'R';
			}
		}
		//distinguish feed readers from other spiders
		if (!empty($spider['name'])) {
			if ($spider['agenttype'] != "F" && preg_match("/(feed|rss|atom|xml)/i",$ua)>0) {
				$spider['agenttype']= "F";
				if (stristr($spider['name'],$unknown)!==false) { 
					$spider['name'] = "Unknown Feedreader";
				}
			}
			if (empty($spider['subscribers'])  && preg_match("/([0-9]{1,10})\s?subscriber/i",$ua,$subscriber) > 0) {
				// It's a feedreader with some subscribers
				$spider['subscribers'] = $subscriber[1];
				$spider['agenttype']= "F";
			}

			//Some spiders give OS information
			if (empty($spider['os'])) {
				if (!$is_current_ua) {
					list($spider['os'],$platform) = $this->OSversion($ua);
				} else {
					list($spider['os'],$platform) = $this->OSversion();
				}
				if (!empty($platform) && empty($spider['platform'])) {
					$spider['platform'] = $platform;
				}
			}
			$spider['name'] = rtrim($spider['name'],'_- ');
		} //end if empty(spider[name])

		// #Check for spam and script injection attempts
		if ($this->isSpammer($ua) !== false) {
			$spider['name'] = _e("Script Injection Bot");
			$spider['agenttype']= "S";
		}
		if ($wdebug_mode) {
			echo "\n".'<br />uadetector: spider->name='.$spider['name']."&nbsp; "; //debug
		}
		if (empty($spider['name'])) {
			$spider=false;
		} else {
			$this->setClassVars($spider);
		}
		$this->done_spiders = true;
		if ($do_profile) {
			profiler_endSection('______ua::isSpider');
		}
		return $spider;
	} //end function isSpiderAgent

	/**
	 * check if user-agent is a feed and find number of subscribers
	 * @return array
	 */
	function isFeed($feed_name,$ua="") {
		if (empty($ua)) {
			if (!empty($feed_name)) { 
				$ua=$feed_name;
				$feed_name="";
			} else {
				$ua = $this->agent;
			}
		}
		//separate feed readers from spiders
		if (preg_match("/([0-9]+)\s?subscriber/i",$ua,$subscriber) >0) {
			// It's a feedreader with some subscribers
			$feed['subscribers'] = $subscriber[1];
			$feed['agenttype']= "F";
		} elseif (preg_match("/(feed|rss)/i",$ua)>0) {
			$feed['agenttype']= "F";
		}
		if (!empty($feed['agenttype'])) {
			if (!empty($feed_name)) {
				$feed['name'] = $feed_name;
			}
			return $feed;
		} else {
			return false;
		}
	} //end function isFeed

	/**
	 * Check string for obvious signs of spam, hack, and script
	 *   injection attempts
	 * @access public
	 * @return boolean
	 */
	function isSpammer($agent="") {
		if (empty($agent)) { $ua = $this->agent; }
		else { $ua=$agent; }
		$spambot=false;
		//## Find obvious script injection bots 
		if (stristr($ua,'location.href')!==FALSE) {
			$spambot=true;
		} elseif (preg_match('/(<|&lt;|&#60;|%3C)script/i',$ua)>0) {
			$spambot=true;
		} elseif (preg_match('/(<|&lt;|&#60;|%3C)a(\s|%20|&#32;|\+)+href/i',$ua)>0) {
			$spambot=true;
		} elseif (preg_match('/(select|update).*( |%20|%#32;|\+)from( |%20|%#32;|\+)/i',$ua)>0) {
			$spambot=true;
		} elseif (preg_match('/(drop|alter)(?:\s|%20|%#32;|\+)table/i',$ua)>0) {
			$spambot=true;
		}

		return $spambot;
	} //end function isSpammer

	/**
	 * Try to identify a mystery browser|spider by re-checking 
	 *   'isBrowserAgent()' or by using PHP's 'get_browser' with server
	 *   Browscap file in 'getBrowscap()'.
	 * @access private
	 * @return none
	 */
	function isWTF($ua="") {
		global $wdebug_mode, $do_profile;
		//recheck browsers or check PHP's browser capabilities file
		if (isset($this->done_browsers) && !$this->done_browsers) {
			return $this->isBrowserAgent($ua);
		} else {
			//return false;
			if ($do_profile) {
				profiler_beginSection('_________ua::isWTF');
			}
			$unknown_agent = $this->getBrowscap($ua);
			if (!empty($unknown_agent['name'])) {
				$this->setClassVars($unknown_agent);
			}
			if ($wdebug_mode) {
				echo "\n".'<br />uadetector: unknown_agent->name='.$unknown_agent['name']."&nbsp; "; //debug
			}
			if ($do_profile) {
				profiler_endSection('_________ua::isWTF');
			}
		}
	}

	/**
	 * Find operating system and platform from string (or user-agent)
	 * @access public
	 * @return array(os_type, platform, device)
	 */
	function OSversion($os="",$platform="",$ua="") {
		global $wdebug_mode, $do_profile;
		if ($do_profile) {
			profiler_beginSection('_____ua::OSversion');
		}
		$is_current_ua = false;
		if (empty($os)) { 
			if (empty($ua)) {
				list($ua, $is_current_ua) = $this->isCurrentAgent();
			}
			$os=$ua;
		}
		//some browsers (IEMobile) show OS in HTTP header, use when available
		if ($is_current_ua) {
			if (!empty($_SERVER['HTTP_UA_OS'])) {
				$os = $_SERVER['HTTP_UA_OS'];
			}
		}
		$os_type = "";
		$device = "";
		if (!empty($os)) { 
		if (preg_match('/(Windows|Win|NT)[0-9;\s\)\/]/',$os)>0) {
			$os_type = $this->winOSversion($os);
			if (!$os_type) { $os_type = "Windows"; }
			$platform="Windows";
		} elseif (strpos($os,'Intel Mac OS X')!==FALSE || strpos($os,'PPC Mac OS X')!==FALSE) {
			$platform = 'Macintosh';
			$os_type = 'MacOSX';
			$device='PC';

		//iPhone OS similar to OSX, so test before OSX to identify
		} elseif (preg_match('/\siPhone\sOS\s(\d+)?(?:_\d)*/i',$os,$match)>0) {
			$version="";
			if (!empty($match[1])) { $version = $match[1]; }
			if (strpos($os,'iPod')!==FALSE) {
				$os_type = 'iPhone OS';
				$platform = "WAP";
				$device = 'iPod Touch';
			} else {
				$os_type = 'iPhone OS';
				$platform = "WAP";
				$device = 'iPhone'." $version";
			}
		} elseif (stristr($os,'iPhone')!==FALSE) {
				$os_type = 'iPhone';
				$platform = "WAP";

		} elseif (strpos($os,'Mac OS X')!==FALSE) {
			if (!empty($platform)) {
				$os_type = "{$platform}";
			} else {
				$os_type = 'MacOSX';
				$platform = 'Macintosh';
			}
		} elseif (preg_match('/Android\s?([0-9\.]+)?/',$os)>0) {
			$os_type = 'Android';	//Google Android
			if (!empty($match[1])) $version = $match[1];
			$platform = 'WAP';	//Linux
		} elseif (preg_match('/[^a-z0-9](BeOS|BePC|Zeta)[^a-z0-9]/',$os)>0) {
			$os_type = 'BeOS';
		} elseif (preg_match('/[^a-z0-9](Commodore\s?64)[^a-z0-9]/i',$os)>0) {
			$os_type = 'Commodore64';
		} elseif (preg_match('/[^a-z0-9]Darwin\/?([0-9\.]+)/i',$os,$match)>0) {
			$os_type = "Darwin";
			$version = $match[1];
			if (preg_match('/(MacBook|iMac|Macintosh|powerpc-apple)/i',$os)>0){
				$platform = 'Macintosh';
				$device=$match[1];
			} else {
				$platform = 'Unix';
			}
		} elseif (preg_match('/[^a-z0-9]Darwin[^a-z0-9]/i',$os,$match)>0) {
			$os_type = "Darwin";
			$platform="Unix";
		} elseif (preg_match('/((?:Free|Open|Net)BSD)\s?(?:[ix]?[386]+)?\s?([0-9\.]+)?/',$os,$match)>0) {
			$os_type = $match[1];
			if (!empty($match[2])) $version = $match[2];
			$platform="Unix";
		//find Linux os brand and version
		} elseif (preg_match('/(?:(i[0-9]{3})\s)?Linux\s*((?:i[0-9]{3})?\s*(?:[0-9]\.[0-9]{1,2}\.[0-9]{1,2})?\s*(?:[ix][0-9_]{3,})?)?(?:.+[\s\(](Android|CentOS|Debian|Fedora|Gentoo|Mandriva|PCLinuxOS|SuSE|[KX]?ubuntu)[\s\/\-\)]+(\d+[a-z0-9\.]*)?)?/i',$os,$match)>0) {
			$os_type = 'Linux';
			if (!empty($match[3])) {
				$os_type = $match[3];
				//only Ubuntu has non-browser version# after name
				if (!empty($match[4]) && stristr($linuxos,'ubuntu')!==false) { 
					$version=$match[4];
				}
			} elseif (!empty($match[2])) { 
				$version = $match[2];
			} elseif (!empty($match[1])) { 
				$version = $match[1];
			}
			$platform = 'Linux';
		} elseif (preg_match('/Linux/i',$os,$match)>0) {
			$os_type = $this->linuxOSversion($os);
			$platform = "Linux";
		} elseif (preg_match('/(Mac_PowerPC|Macintosh)/',$os)>0) {
			$os_type = 'MacPPC';
			$platform = 'Macintosh';
		} elseif (preg_match('/Nintendo\s(Wii|DSi?)?/i',$os,$match)>0) {
			$os_type = 'Nintendo';
			$device = 'Nintendo';
			if (!empty($match[1])) $device .= " ".$match[1];
		} elseif (preg_match('/[^a-z0-9_\-]MS\-?DOS[^a-z]([0-9\.]+)?/i',$os,$match)>0) {
			$os_type = 'MS-DOS';	//yep, it's still around
			if (!empty($match[1])) { $version = $match[1]; }
		} elseif (preg_match('/[^a-z0-9_\-]OS\/2[^a-z0-9_\-].+Warp\s([0-9\.]+)?/i',$os,$match)>0) {
			$os_type = 'OS/2 Warp';
			if (!empty($match[1])) { $version = $match[1]; }
		} elseif (stristr($os,'PalmOS')!==FALSE) {
				$os_type = 'PalmOS';
				$platform = 'WAP';
		} elseif (preg_match('/PLAYSTATION\s(\d+)/i',$os,$match)>0) {
				$os_type = 'Playstation';
				$version = $match[1];
				$device = 'Playstation';
		} elseif (preg_match('/IRIX\s*([0-9\.]+)?/i',$os,$match)>0) {
				$os_type = 'SGI Irix';
				if (!empty($match[1])) {
					$version = $match[1];
				}
				$platform="Unix";
		} elseif (preg_match('/SCO_SV\s([0-9\.]+)?/i',$os,$match)>0) {
				$os_type = 'SCO Unix';
				if (!empty($match[1])) {
					$version = $match[1];
				}
				$platform="Unix";
		} elseif (preg_match('/Solaris\s?([0-9\.]+)?/i',$os,$match)>0) {
				$os_type = 'Solaris';
				if (!empty($match[1])) {
					$version = $match[1];
				}
				$platform="Unix";
		} elseif (preg_match('/SunOS\s?(i?[0-9\.]+)?/i',$os,$match)>0) {
				$os_type = 'SunOS';
				if (!empty($match[1])) {
					$version = $match[1];
				}
		} elseif (preg_match('/SymbianOS\/([0-9\.]+)/i',$os,$match)>0) {
				$os_type = 'SymbianOS';
				$version = $match[1];
				$platform = "WAP";
		} elseif (preg_match('/[^a-z]Unixware\s(\d+(?:\.\d+)?)?/i', $ua)) {
				$os_type = 'Unixware';
				if (!empty($match[1])) {
					$version = $match[1];
				}
				$platform = "Unix";
		} elseif (preg_match('/\(PDA(?:.*)\)(.*)Zaurus/i',$os)>0) {
				$os_type = 'Zaurus';	//Sharp Zaurus
				$platform = "WAP";
		} elseif (preg_match('/[^a-z]Unix/i', $ua)) {
				$os_type = 'Unix';	//Unknown unix os
				$platform = "Unix";
		} elseif (preg_match('#^Mozilla/\d\.\d\s\(([a-z0-9]+);\sU;\s(([a-z0-9]+)(?:\s([a-z0-9\.\s]+))?);#i',$os,$match)>0) {
				$platform = $match[1];
				$os_type = $match[3];
				if (!empty($match[4])) { 
					$version = $match[4];
				}
		} else {
			$os_type = $this->linuxOSversion($os);
			if ($os_type) { 
				$platform = 'Linux';
			} elseif (preg_match('/[^a-z0-9_\-]OS\/2[^a-z0-9_\-]/i',$os,$match)>0) {
				//test last because "OS/2" string is not unique in user-agents
				$os_type = "OS/2";
			} else { 
				$os_type = "";
			}
		}
		}
		//TODO: Amiga?,OpenWeb?,openOS? 
		if (empty($os_type) && !empty($platform)) {
			$os_type = $platform;
		}
		if ($do_profile) {
			profiler_endSection('_____ua::OSversion');
		}
		return (array($os_type,$platform,$device));
	}  //end function OSversion

	/**
	 * Find Microsoft operating system from string (os/user-agent)
	 * @access public
	 * @return string
	 */
	function winOSversion($os) {
		if (empty($os)) { return false; }
		$winos = "";
		if (strstr($os,'Windows NT 6.2')) {
			$winos = 'Win8';
		} elseif (strstr($os,'Windows NT 6.1')) {
			$winos = 'Win7';
		} elseif (strstr($os,'Windows NT 6.0')) {
			$winos = 'WinVista'; //also win2008 server 
		} elseif (strstr($os,'Windows NT 5.2')) {
			$winos = 'Win2003';
		} elseif (strstr($os,'Windows NT 5.1')) {
			$winos = 'WinXP';
		} elseif (strstr($os,'Windows NT 5.0') || strstr($os,'Windows 2000')) {
			$winos = 'Win2000';
		} elseif (strstr($os,'Windows ME')) {
			$winos = 'WinME';
		} elseif (preg_match('/Win(?:dows\s)?NT\s?([0-9\.]+)?/',$os,$match)>0) {
			$winos = 'WinNT';
			if (!empty($match[1])) { $winos .= " ".$match[1]; }
		} elseif (preg_match('/(?:Windows95|Windows 95|Win95|Win 95)/',$os)>0) {
			$winos = 'Win95';
		} elseif (preg_match('/(?:Windows98|Windows 98|Win98|Win 98|Win 9x)/',$os)>0) {
			$winos = 'Win98';
		} elseif (preg_match('/(?:WindowsCE|Windows CE|WinCE|Win CE)[^a-z0-9]+(?:.*Version\s([0-9\.]+))?/i',$os)>0) {
			$winos = 'WinCE';
			if (!empty($match[1])) { $winos .= " ".$match[1]; }
		} elseif (preg_match('/(Windows|Win)\s?3\.\d[; )\/]/',$os)>0) {
			$winos = 'Win3.x';
		} elseif (preg_match('/(Windows|Win)[0-9; )\/]/',$os)>0) {
			$winos = 'Windows';
		}
		if (strstr($os,'WOW64') || strstr($os,'Win64') || strstr($os,'x64')) {
			$winos = $winos.' x64';
		}
		return ($winos);
	} //end function winOSversion

	/**
	 * Find the name of a Linux-based operating system from string (os)
	 *  @access public
	 *  @return string
	 */
	function linuxOSversion($os="") {
		global $wdebug_mode, $do_profile;
		if (empty($os)) { $os = $this->agent; }
		if (empty($os)) { return false; }
		if ($do_profile) {
			profiler_beginSection('_ua::LinuxOSverson');
		}
		$linuxos = "";
		$platform = "";
		$version="";
		
		//distinguish between different linux os's 
		//TODO: Mandriva?, Remi?,
		if (preg_match('/[^a-z0-9](Android|CentOS|Debian|Fedora|Gentoo|Mandriva|PCLinuxOS|SuSE)[^a-z]/',$os,$match)>0) {
			$linuxos = $match[1];
		} elseif (preg_match('/[^a-z0-9]CentOS[^a-z]/i', $os)) {
			$linuxos = "CentOS";
		} elseif (preg_match('/[^a-z0-9]Debian/i', $os)) {
			$linuxos = "Debian";
		} elseif (preg_match('#Gentoo#i', $os)) {
			$linuxos = "Gentoo";
		} elseif (preg_match('#Kanotix#i', $os)) {
			$linuxos = "Kanotix";
		} elseif (preg_match('#Knoppix#i', $os)) {
			$linuxos = "Knoppix";
		} elseif (preg_match('#[^a-z0-9]Mandrake[^a-z0-9]#i',$os)){
			$linuxos = "Mandrake";
		} elseif (preg_match('#MEPIS#i', $os)) {
			$linuxos = "MEPIS Linux";
		} elseif (preg_match('/[^a-z]pclos([0-9\.]+)?/i',$os,$match)>0) {
			$linuxos = "PCLinuxOS";
			if (!empty($match[1])) { $version = $match[1]; }
		} elseif (preg_match('/[^a-z]LinuxOS[^a-z]/i',$os)) {
			$linuxos = "LinuxOS";	//motorola embedded
		} elseif (preg_match('/Red\s?Hat^[a-z]/i', $os)) {
			$linuxos = "RedHat";
		} elseif (preg_match('#Slackware#i', $os)) {
			$linuxos = "Slackware";
		} elseif (preg_match('/[^a-z0-9]SuSE/i', $os)) {
			$linuxos = "SuSE";
		//Ubuntu, Kubuntu, Xubuntu
		} elseif (preg_match('#([kx]?Ubuntu)[^a-z]?(\d+[\.0-9a-z]*)?#i',$os,$match)>0) {
			$linuxos = $match[1];
			if (!empty($match[2])) { 
				$version = $match[2];
			}
		} elseif (preg_match('#Xandros#i', $os)) {
			$linuxos = "Xandros";
		}

		if ($do_profile) {
			profiler_endSection('_ua::LinuxOSverson');
		}
		if (empty($linuxos)) {
			return false;
		} else {
			return $linuxos;
		}
	} //end function linuxOSversion

	/**
	 * Find name of a webkit-based browser and it's version# from 
	 * string, $webkit_string or user-agent. Defaults to Safari.
	 *  @access public
	 *  @return associative array (browser, version)
	 */
	function webkitVersion($webkit="",$ua="") {
		global $wdebug_mode;
		$browser = "Safari";
		$vers = "";
		if ($wdebug_mode) {
			echo "<br />webkit=".$webkit."\n"; //debug
		}
		if (empty($webkit)) { 
			return false;
		} elseif (preg_match("#^([a-zA-Z]+)/([0-9]+(?:[A-Za-z\.0-9]+))(\sMobile)?#",$webkit,$match)>0) {
			if ($match[1] != "Version") { //Chrome,Iron,Shiira
				$browser = $match[1];
			}
			$vers = $match[2];
			if ($vers == "0") { $vers = ""; }
			if (!empty($match[3])) { //Mobile browser
				$vers .= $match[3];
			}
		} elseif (is_numeric($webkit)) {
			$webkit_num = (int)($webkit-0.5);
			if ($webkit_num > 533) { $vers = "5"; }
			elseif ($webkit_num > 525) { $vers = "4"; }
			elseif ($webkit_num > 419) { $vers = "3"; }
			elseif ($webkit_num > 312) { $vers = "2"; }
			elseif ($webkit_num > 85) { $vers = "1"; }
			else { $vers = ""; } //beta version, 0.x
		} //end else !empty($webkit)
		return array('name'=>$browser,'version'=>$vers);
	}  //end function webkitVersion

	/**
	 * check HTTP headers for browser, resolution, platform, and
	 *  os data separate from user agent
	 * @access private
	 * @return array 
	 */
	function getHeaderData() {
		$name="";
		$os="";
		$platform="";
		$resolution="";
		$uatype="";
		//os and screen resolution is sometimes given in IE and IEMobile header
		if (!empty($_SERVER['HTTP_UA_OS'])) {
			$os = $this->OSversion($_SERVER['HTTP_UA_OS']);
		}
		if (!empty($_SERVER['HTTP_UA_PIXELS'])) {
			$resolution = str_replace('X','x',$_SERVER['HTTP_UA_PIXELS']);
		} elseif (!empty($_SERVER['HTTP_X_UP_DEVCAP_SCREENPIXELS'])) {
			$resolution = str_replace(',','x',$_SERVER['HTTP_X_UP_DEVCAP_SCREENPIXELS']);
		}
		//note: Skyfire mobile browser has same UA as Firefox, so check for separate Skyfire headers
		if (!empty($_SERVER['HTTP_X_SKYFIRE_VERSION'])) {
			$name = 'Skyfire';
			$platform = 'WAP';
			if (!empty($_SERVER['HTTP_X_SKYFIRE_SCREEN'])) {
				$resolution = preg_replace(',','x',$_SERVER['HTTP_X_SKYFIRE_SCREEN']);
	 		}
			$uatype="B";
		}
		return array($name,$os,$platform,$resolution,$uatype);
	} //end function getHeaderData

	/**
	 * Check for device's real user-agent in header because user-agent
	 *  may be spoofed or from a mobile transcoder.
	 * @access private
	 * @param none
	 * @return string 
	 */
	function setDeviceUA() {
		$ua = trim($_SERVER['HTTP_USER_AGENT']);
		$wap=false;
		$browser='';
		//TODO: get real user-agent in case visitor is using a mobile transcoder or an emulated agent
		if (!empty($_SERVER['HTTP_X_DEVICE_USER_AGENT'])) {
			$real_ua = trim($_SERVER['HTTP_X_DEVICE_USER_AGENT']);
		} elseif (!empty($_SERVER['HTTP_X_ORIGINAL_USER_AGENT'])) {
			$real_ua = trim($_SERVER['HTTP_X_ORIGINAL_USER_AGENT']);
		} elseif (!empty($_SERVER['HTTP_X_MOBILE_UA'])) {
			$real_ua = trim($_SERVER['HTTP_X_MOBILE_UA']);
			$wap=true;
		} elseif (!empty($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])) {
			$real_ua = trim($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']);
			$wap=true;
			$browser='Opera Mini';
		}
		if (!empty($real_ua) && (strlen($real_ua)>=5 || empty($ua))) {
			$ua = $real_ua;
		}
		$this->agent = $ua;
		if ($wap) { $this->platform = 'WAP'; }
		if (!empty($browser) && empty($this->name)) { 
			$this->name = $browser;
			$this->agenttype = 'B';
		}
	} //end function setDeviceUA

	/**
	 * Check PHP browscap file for browser and platform (last resort)
	 * @access public
	 * @return array
	 */
	function getBrowscap($ua="") {
		global $wdebug_mode;
		if (empty($ua)) $ua = $this->agent;
		$browsercap = false;
		$browser = array('name'=>"",'version'=>"",'os'=>"",'platform'=>"",'agenttype'=>"B");
		if (empty($ua)) { return false; }
		//TODO: include a copy of the browscap.ini file (lite vers.)
		//if (ini_get("browscap") == "" && file_exists(__FILE__."browscap.ini")) {
		//	ini_set("browscap",__FILE__."browscap.ini");
		//}
		if (ini_get("browscap")!="") {
			$browsercap = get_browser($ua,true);
			if ($wdebug_mode) {
				echo "\n<br/>Browsercap=";
				print_r($browsercap);
				echo "\n<br />";
			}
		}
		if (is_array($browsercap)) {
			if (empty($browsercap['browser']) || $browsercap['browser']=='Default Browser') {
				if (!empty($browsercap['parent'])) {
					$browser['name'] = $browsercap['parent'];
				}
			} else {
				$browser['name'] = $browsercap['browser'];
			}
			if (!empty($browser['name'])) {
			if (!empty($browsercap['platform']) && stristr($browsercap['platform'],'unknown') === false) {
				$browser['os'] = $browsercap['platform'];
				if (!empty($browsercap['spider'])) {
					$browser['agenttype']="R";
				}
			} elseif (!empty($browser['name'])) {	//spider when no platform
				$browser['agenttype']="R";
			}
			if (!empty($browsercap['version']) && $browsercap['spider'] == FALSE) {
				$browser['version'] = $browsercap['version'];
			}
			}
		} //end if ini_get
		return $browser;
	} //end function getBrowscap

	/**
	 * get the browser emulation informtion
	 * @access private
	 * @param none
	 * @return string
	 */
	function setEmulation() {
		if ($this->is_browser || $this->agenttype=='B') {	
			//default is same browser
			$this->emulation = $this->name." ".$this->majorVersion($this->version); 
		}
		//find emulation from a spoofed user-agent
		if ($this->is_active_agent && $this->agent!=rtrim($_SERVER['HTTP_USER_AGENT'])) {
			$this->is_active_agent = false; //so don't set class variables
			$emulation=$this->isTopAgent($_SERVER['HTTP_USER_AGENT']);
			if (empty($emulation)) {
				//check for browsers only, omit spiders 
				$this->done_browsers = false; //temp;
				$this->done_spiders = true; //temp;
				$emulation=$this->isBrowserAgent($_SERVER['HTTP_USER_AGENT']);
			}
			if (!empty($emulation['name']) && $emulation['agenttype']=='B') {
				$this->emulation = rtrim($emulation['name'].' '.$this->majorVersion($emulation['version']));
			}
			$this->is_active_agent = true; //reset
		} elseif (preg_match('/(Firefox|MSIE|Safari)[\/\s]*([0-9\.]+)?/i',$this->agent,$match)>0) {
			if (stristr($match[1],'MSIE')!==false) { 
				$match[1] = 'IE';
			}
			if ($this->name!=$match[1]) {
				$this->emulation = $match[1];
				if (!empty($match[2])) {
					if (stristr($match[1],'Safari')!==false) {
						$emulation = $this->webkitVersion($match[2]);
						if (!empty($emulation['version'])) {
							$this->emulation .= " ".$emulation['version'];
						}
					} else {
						$this->emulation .= " ".$this->majorVersion($match[2]);
					}
				}
			}
		}
		return $this->emulation;
	} //end function setEmulation

	/**
	 * get language code from user-agent string if it exists
	 * @access public
	 * @return string
	 */
	function detectLanguage($agent="") {
		$language="";
		$ua="";
		$is_current_ua = false;
		if (empty($agent)) {
			list($ua,$is_current_ua) = $this->isCurrentAgent();
		} else {
			$ua=$agent;
		}
		if (preg_match("/(?:\s|;|\[)(([a-z]{2})(?:\-([a-zA-Z]{2}))?)(?:;|\]|\))/",$ua,$match)>0) {
			$language = $match[1];
		} elseif ($is_current_ua) {
			$language="";
			//if (!empty($_SERVER('HTTP_ACCEPT_LANGUAGE'))) {
			//	$language = $match[1];
			//}
		}
		return $language;
	}

	/**
	 * get screen resolution from user-agent string if exists
	 * @return string
	 */
	function detectResolution($ua,$wap=false) {
		$is_current_ua = false;
		if (empty($ua)) {
			$ua = $this->agent;
			$is_current_ua = $this->is_active_agent;
		}
		$resolution="";
		if (preg_match("#screen(?:res)?[ -/](\d{3,4}[x\*]\d{3,4})#",$ua,$pcs)>0) {
			$resolution = str_replace('*','x',$pcs[1]);
		} elseif (!empty($wap) && preg_match("#[ ;](\d{3,4}x\d{3,4})([;)x ]|$)#",$ua,$pcs)>0) {
			$resolution = $pcs[1];
		}
		return $resolution;
	} //end function detectResolution


	function setClassVars($assoc) {
		//if 'name' already exists, then new 'name' is emulation
		if (empty($assoc['emulation']) && empty($this->emulation)) {
			if (!empty($this->name) && $this->name!=$assoc['name']) {
				if ($assoc['agenttype']=="B") {
					$this->emulation = rtrim($assoc['name']." ".$this->majorVersion($assoc['version']));
					$assoc['name'] = $this->name;
					$assoc['version'] = $this->version;
					$assoc['agenttype'] = $this->agenttype;
				}
			}
		}
		foreach($assoc as $key => $value) {
			if (empty($this->$key)) {
				$this->$key = $value;
			}
		}
	}

	/**
	 * Get the major version # from a version string argument
	 *  @access public
	 *  @return string
	 */
	function majorVersion($versionstring="") {
		if (empty($versionstring)) {
			$versionstring = $this->version;
		}
		$version=0;
		if (!empty($versionstring)) {
			$n = strpos($versionstring,'.');
			if ($n >0) {
				$version= (int) substr($versionstring,0,$n);
				if ($version == 0) { 
					$version = '0.'.substr($versionstring,$n+1,1);
				}
			}
			if ($n == 0) {
				$p = strpos($versionstring,'.',$n+1);
				if ($p>0) {
					$version = '0.'.substr($versionstring,0,$p);
				} elseif (is_numeric($versionstring)) {
					$version = $versionstring;
				}
			}
		}
		if ($version > 0) {
			return $version;
		} else {
			return $versionstring;
		}
	} //end majorVersion

	/**
	 * check if user-agent string is the current active browser
	 *  @access private
	 *  @param string (required)
	 *  @return array
	 */
	function isCurrentAgent($agent="") {
		$ua="";
		$is_current_ua=false;
		if (empty($agent)) { 
			if (!empty($this->agent)) { 
				$ua = $this->agent;
				$is_current_ua = $this->is_active_agent;
			} else { 
				$ua = rtrim($_SERVER['HTTP_USER_AGENT']);
				$is_current_ua = true;
			}
		//} else {
		//	$ua = $agent;
		//	if ($agent == $this->agent) {
		//		$is_current_ua = $this->is_active_agent;
		//	}
		}
		return array($ua,$is_current_ua);
	} //end function isCurrentAgent

	/**
	 * check user-agent string for sufficient data
	 *  @access private
	 *  @param string (required)
	 *  @return boolean
	 */
	function isValidAgent($ua) {
		$is_valid=true;
		if (empty($ua)) {	 	//blank data
			$is_valid=false;
		} elseif (strlen($ua)<5) { 	//data too small
			$is_valid=false;
		}
		return $is_valid;
	} //end function isValidAgent

	/**
	 * check for obvious signs of user-agent spoofing
	 *  @access private
	 *  @param string (required)
	 *  @return boolean
	 */
	function isSpoofedAgent($ua) {
		$is_spoofed=false;
		//likely spoofed if contains the words "user agent"
		if (stristr('user agent',$ua)!==false) {
			$is_spoofed = true;
		}
		return $is_spoofed;
	} //end function isSpoofedAgent

	/**
	 * assign a name to an unknown user agent
	 *  @access private
	 *  @param string (required)
	 *  @return array (2 fields: 'name','agenttype')
	 */
	function isUnknownAgent($ua) {
		$unknown = array('name'=>"Unknown Spider",'agenttype'=>"R");	//assume robot
		$agent = trim($ua,'./\;-&":;? ><,#@~`%$+=');
		if (!empty($agent)) {
			if (strlen($agent)<5) { 	//data too small
				$unknown['name'] = $agent;
			}
			//TODO: use 1st word (not mozilla) for spider name?
		}
		$this->setClassVars($unknown);
		return $unknown;
	} //end function setUnknownAgent

} //end class UADetector
?>
