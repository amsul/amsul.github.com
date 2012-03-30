<?php
if (!class_exists('wassup_pagination')) {
class wassup_pagination{
/*
Script Name: *Digg Style Paginator Class
Script URI: http://www.mis-algoritmos.com/2007/05/27/digg-style-pagination-class/
Description: Class in PHP that allows to use a pagination like a digg or sabrosus style.
Script Version: 0.3.2
Author: Victor De la Rocha
Author URI: http://www.mis-algoritmos.com
Revised for Wassup by Helene D.
*/
	/*Default values*/
        var $total_pages;
        var $limit;
        var $target;
        var $page;
        var $adjacents;
        var $showCounter;
        var $className;
        var $parameterName;
        var $urlF ;

        /*Buttons next and previous*/
        var $nextT;
        var $nextI;
        var $prevT;
        var $prevI;

        /*****/
        var $calculate;
	
	#Total items
	function items($value){$this->total_pages = intval($value);}
	
	#how many items to show per page
	function limit($value){$this->limit = intval($value);}
	
	#Page to sent the page value
	function target($value){$this->target = $value;}
	
	#Current page
	function currentPage($value){$this->page = intval($value);}
	
	#How many adjacent pages should be shown on each side of the current page?
	function adjacents($value){$this->adjacents = intval($value);}
	
	#show counter?
	function showCounter($value=""){$this->showCounter=($value===true)?true:false;}

	#to change the class name of the pagination div
	function changeClass($value=""){$this->className=$value;}

	function nextLabel($value){$this->nextT = $value;}
	function nextIcon($value){$this->nextI = $value;}
	function prevLabel($value){$this->prevT = $value;}
	function prevIcon($value){$this->prevI = $value;}

	#to change the class name of the pagination div
	function parameterName($value=""){$this->parameterName=$value;}

	#to change urlFriendly
	function urlFriendly($value="%"){
			if(eregi('^ *$',$value)){
					$this->urlF=false;
					return false;
				}
			$this->urlF=$value;
		}
	
	var $pagination;

	function wassup_pagination(){
                /*Set Default values*/
                $this->total_pages = null;
                $this->limit = null;
                $this->target = "";
                $this->page = 1;
                $this->adjacents = 2;
                $this->showCounter = false;
                $this->className = "pagination";
                $this->parameterName = "pages";
                $this->urlF = false;//urlFriendly

                /*Buttons next and previous*/
                $this->nextT = __("Next","wassup");
                $this->nextI = "&#187;"; //&#9658;
                $this->prevT = __("Previous","wassup");
                $this->prevI = "&#171;"; //&#9668;

                $this->calculate = false;
	}
	function show(){
			if(!$this->calculate)
				if($this->calculate())
					echo "<div class=\"$this->className\">$this->pagination</div>";
		}
	function get_pagenum_link($id){
			if(strpos($this->target,'?')===false)
					if($this->urlF)
							return str_replace($this->urlF,$id,$this->target);
						else
							return "$this->target?$this->parameterName=$id";
				else
					return "$this->target&$this->parameterName=$id";
		}
	
	function calculate(){
			$this->pagination = "";
			$this->calculate == true;
			$error = false;
			if($this->urlF and $this->urlF != '%' and strpos($this->target,$this->urlF)===false){
					//Es necesario especificar el comodin para sustituir
					echo 'Especificaste un wildcard para sustituir, pero no existe en el target<br />';
                                        $error = true;
                                }elseif($this->urlF and $this->urlF == '%' and strpos($this->target,$this->urlF)===false){
                                        echo 'Es necesario especificar en el target el comodin';
                                        $error = true;
                                }
                        if($this->total_pages == null){
                                        echo __("It is necessary to specify the","wassup")." <strong>".__("number of pages","wassup")."</strong> (\$class->items(1000))<br />";
                                        $error = true;
                                }
                        if($this->limit == null){
                                        echo __("It is necessary to specify the","wassup")." <strong>".__("limit of items","wassup")."</strong> ".__("to show per page","wassup")." (\$class->limit(10))<br />";
                                        $error = true;
				}
			if($error)return false;
			
			$n = trim($this->nextT.' '.$this->nextI);
			$p = trim($this->prevI.' '.$this->prevT);
			
			/* Setup vars for query. */
			if($this->page) 
				$start = ($this->page - 1) * $this->limit;             //first item to display on this page
			else
				$start = 0;                                //if no page var is given, set start to 0
		
			/* Setup page vars for display. */
			if ($this->page == 0) $this->page = 1;                    //if no page var is given, default to 1.
			$prev = $this->page - 1;                            //previous page is page - 1
			$next = $this->page + 1;                            //next page is page + 1
			$lastpage = ceil($this->total_pages/$this->limit);        //lastpage is = total pages / items per page, rounded up.
			$lpm1 = $lastpage - 1;                        //last page minus 1
			
			/* 
				Now we apply our rules and draw the pagination object. 
				We're actually saving the code to a variable in case we want to draw it more than once.
			*/
			
			if($lastpage > 1){
					//anterior button
					if($this->page > 1)
							$this->pagination .= "<a href=\"".$this->get_pagenum_link($prev)."\">$p</a>";
						else
							$this->pagination .= "<span class=\"disabled\">$p</span>";
					//pages	
					if ($lastpage < 7 + ($this->adjacents * 2)){//not enough pages to bother breaking it up
							for ($counter = 1; $counter <= $lastpage; $counter++){
									if ($counter == $this->page)
											$this->pagination .= "<span class=\"current\">$counter</span>";
										else
											$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
								}
						}
					elseif($lastpage > 5 + ($this->adjacents * 2)){//enough pages to hide some
							//close to beginning; only hide later pages
							if($this->page < 1 + ($this->adjacents * 2)){
									for ($counter = 1; $counter < 4 + ($this->adjacents * 2); $counter++){
											if ($counter == $this->page)
													$this->pagination .= "<span class=\"current\">$counter</span>";
												else
													$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
										}
									$this->pagination .= "...";
									$this->pagination .= "<a href=\"".$this->get_pagenum_link($lpm1)."\">$lpm1</a>";
									$this->pagination .= "<a href=\"".$this->get_pagenum_link($lastpage)."\">$lastpage</a>";
								}
							//in middle; hide some front and some back
							elseif($lastpage - ($this->adjacents * 2) > $this->page && $this->page > ($this->adjacents * 2)){
									$this->pagination .= "<a href=\"".$this->get_pagenum_link(1)."\">1</a>";
									$this->pagination .= "<a href=\"".$this->get_pagenum_link(2)."\">2</a>";
									$this->pagination .= "...";
									for ($counter = $this->page - $this->adjacents; $counter <= $this->page + $this->adjacents; $counter++)
										if ($counter == $this->page)
												$this->pagination .= "<span class=\"current\">$counter</span>";
											else
												$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
									$this->pagination .= "...";
									$this->pagination .= "<a href=\"".$this->get_pagenum_link($lpm1)."\">$lpm1</a>";
									$this->pagination .= "<a href=\"".$this->get_pagenum_link($lastpage)."\">$lastpage</a>";
								}
							//close to end; only hide early pages
							else{
									$this->pagination .= "<a href=\"".$this->get_pagenum_link(1)."\">1</a>";
									$this->pagination .= "<a href=\"".$this->get_pagenum_link(2)."\">2</a>";
									$this->pagination .= "...";
									for ($counter = $lastpage - (2 + ($this->adjacents * 2)); $counter <= $lastpage; $counter++)
										if ($counter == $this->page)
												$this->pagination .= "<span class=\"current\">$counter</span>";
											else
												$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
								}
						}
					//siguiente button
					if ($this->page < $counter - 1)
							$this->pagination .= "<a href=\"".$this->get_pagenum_link($next)."\">$n</a>";
						else
							$this->pagination .= "<span class=\"disabled\">$n</span>";
						if($this->showCounter)$this->pagination .= "<div class=\"pagination_data\">($this->total_pages ".__("Pages","wassup").")</div>";
				}

			return true;
		}
	} //end class wassup_pagination
} //end if !class_exists('wassup_pagination')

if (!class_exists('Detector')) { 	//in case another app uses this class...
//
// Detector class (c) Mohammad Hafiz bin Ismail 2006
// detect location by ipaddress
// detect browser type and operating system
//
// November 27, 2006
//
// by : Mohammad Hafiz bin Ismail (info@mypapit.net)
// 
// You are allowed to use this work under the terms of 
// Creative Commons Attribution-Share Alike 3.0 License
// 
// Reference : http://creativecommons.org/licenses/by-sa/3.0/
// 
class Detector {
	//var $town;
	//var $state;
	//var $country;
	//var $Ctimeformatode;
	//var $longitude;
	//var $latitude;
	//var $ipaddress;
	//var $txt;

	var $browser;
	var $browser_version;
	var $os_version;
	var $os;
	var $useragent;

	function Detector($ip="", $ua="")
	{	
		// - not used
		//$apiserver="http://showip.fakap.net/txt/";
		//if ($ip != "") {	
		//if (preg_match('/\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/',$ip,$matches))
		//  {
		//    $this->ipaddress=$ip;
		//  }

		//else { $this->ipaddress = "0.0.0.0"; }

		//uncomment this below if CURL doesnt work		

		//$this->txt=file_get_contents($apiserver . "$ip");

		//$wtf=$this->txt;
		//$this->processTxt($wtf);
		//}

		$this->useragent=$ua;
		$this->check_os($ua);
		$this->check_browser($ua);
	}

	//function processTxt($wtf)	//not used

	//{
//	  	$tok = strtok($txt, ',');
	//  	$this->town = strtok($wtf,',');
	//  	$this->state = strtok(',');
	//  	$this->country=strtok(',');
	//  	$this->ccode = strtok(',');
	//  	$this->latitude=strtok(',');
	//  	$this->longitude=strtok(',');
	//}

	function check_os($useragent) {
			$os = "N/A"; 
			$version = "";
			if (preg_match("/Windows NT 5.1/",$useragent,$match)) {
				$os = "WinXP"; $version = "";
			} elseif (preg_match("/Windows NT 5.2/",$useragent,$match)) {
				$os = "Win2003"; $version = "";
			} elseif (preg_match("/Windows NT 6.0/",$useragent,$match)) {
				$os = "WinVista"; $version = "";
			} elseif (preg_match("/(?:Windows NT 5.0|Windows 2000)/",$useragent,$match)) {
				$os = "Win2000"; $version = "";
			} elseif (preg_match("/Windows ME/",$useragent,$match)) {
				$os = "WinME"; $version = "";
			} elseif (preg_match("/(?:WinNT|Windows\s?NT)\s?([0-9\.]+)?/",$useragent,$match)) {
				$os = "WinNT"; $version = $match[1];
			} elseif (preg_match("/Mac OS X/",$useragent,$match)) {
				$os = "MacOSX"; $version = "";
			} elseif (preg_match("/(Mac_PowerPC|Macintosh)/",$useragent,$match)) {
				$os = "MacPPC"; $version = "";
			} elseif (preg_match("/(?:Windows95|Windows 95|Win95|Win 95)/",$useragent,$match)) {
				$os = "Win95"; $version = "";
			} elseif (preg_match("/(?:Windows98|Windows 98|Win98|Win 98|Win 9x)/",$useragent,$match)) {
				$os = "Win98"; $version = "";
			} elseif (preg_match("/(?:WindowsCE|Windows CE|WinCE|Win CE)/",$useragent,$match)) {
				$os = "WinCE"; $version = "";
			} elseif (preg_match("/PalmOS/",$useragent,$match)) {
				$os = "PalmOS";
			} elseif (preg_match("/\(PDA(?:.*)\)(.*)Zaurus/",$useragent,$match)) {
				$os = "Sharp Zaurus";
			} elseif (preg_match("/Linux\s*((?:i[0-9]{3})?\s*(?:[0-9]\.[0-9]{1,2}\.[0-9]{1,2})?\s*(?:i[0-9]{3})?)?/",$useragent,$match)) {
				$os = "Linux"; $version = $match[1];
			} elseif (preg_match("/NetBSD\s*((?:i[0-9]{3})?\s*(?:[0-9]\.[0-9]{1,2}\.[0-9]{1,2})?\s*(?:i[0-9]{3})?)?/",$useragent,$match)) {
				$os = "NetBSD"; $version = $match[1];
			} elseif (preg_match("/OpenBSD\s*([0-9\.]+)?/",$useragent,$match)) {
				$os = "OpenBSD"; $version = $match[1];
			} elseif (preg_match("/CYGWIN\s*((?:i[0-9]{3})?\s*(?:[0-9]\.[0-9]{1,2}\.[0-9]{1,2})?\s*(?:i[0-9]{3})?)?/",$useragent,$match)) {
				$os = "CYGWIN"; $version = $match[1];
			} elseif (preg_match("/SunOS\s*([0-9\.]+)?/",$useragent,$match)) {
				$os = "SunOS"; $version = $match[1];
			} elseif (preg_match("/IRIX\s*([0-9\.]+)?/",$useragent,$match)) {
				$os = "SGI IRIX"; $version = $match[1];
			} elseif (preg_match("/FreeBSD\s*((?:i[0-9]{3})?\s*(?:[0-9]\.[0-9]{1,2})?\s*(?:i[0-9]{3})?)?/",$useragent,$match)) {
				$os = "FreeBSD"; $version = $match[1];
			} elseif (preg_match("/SymbianOS\/([0-9.]+)/i",$useragent,$match)) {
				$os = "SymbianOS"; $version = $match[1];
			} elseif (preg_match("/Symbian\/([0-9.]+)/i",$useragent,$match)) {
				$os = "Symbian"; $version = $match[1];
			} elseif (preg_match("/PLAYSTATION 3/",$useragent,$match)) {
				$os = "Playstation"; $version = 3;
			}

			$this->os = $os;
			$this->os_version = $version;
		}

		function check_browser($useragent) {

			$browser = "";

			if (preg_match("/^Mozilla(?:.*)compatible;\sMSIE\s(?:.*)Opera\s([0-9\.]+)/",$useragent,$match)) {
				$browser = "Opera";
			} elseif (preg_match("/^Opera\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "Opera";
			} elseif (preg_match("/^Mozilla(?:.*)compatible;\siCab\s([0-9\.]+)/",$useragent,$match)) {
				$browser = "iCab";
			} elseif (preg_match("/^iCab\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "iCab";
			} elseif (preg_match("/^Mozilla(?:.*)compatible;\sMSIE\s([0-9\.]+)/",$useragent,$match)) {
				$browser = "IE";
			} elseif (preg_match("/^(?:.*)compatible;\sMSIE\s([0-9\.]+)/",$useragent,$match)) {
				$browser = "IE";
			} elseif (preg_match("/^Mozilla(?:.*)(?:.*)Chrome/",$useragent,$match)) {
				$browser = "Google Chrome";
			} elseif (preg_match("/^Mozilla(?:.*)(?:.*)Safari\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "Safari";
			} elseif (preg_match("/^Mozilla(?:.*)\(Macintosh(?:.*)OmniWeb\/v([0-9\.]+)/",$useragent,$match)) {
				$browser = "Omniweb";
			} elseif (preg_match("/^Mozilla(?:.*)\(compatible; Google Desktop/",$useragent,$match)) {
				$browser = "Google Desktop";
			} elseif (preg_match("/^Mozilla(?:.*)\(compatible;\sOmniWeb\/([0-9\.v-]+)/",$useragent,$match)) {
				$browser = "Omniweb";
			} elseif (preg_match("/^Mozilla(?:.*)Gecko(?:.*?)(?:Camino|Chimera)\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "Camino";
			} elseif (preg_match("/^Mozilla(?:.*)Gecko(?:.*?)Netscape\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "Netscape";
			} elseif (preg_match("/^Mozilla(?:.*)Gecko(?:.*?)(?:Fire(?:fox|bird)|Phoenix)\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "Firefox";
			} elseif (preg_match("/^Mozilla(?:.*)Gecko(?:.*?)Minefield\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "Minefield";
			} elseif (preg_match("/^Mozilla(?:.*)Gecko(?:.*?)Epiphany\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "Epiphany";
			} elseif (preg_match("/^Mozilla(?:.*)Galeon\/([0-9\.]+)\s(?:.*)Gecko/",$useragent,$match)) {
				$browser = "Galeon";
			} elseif (preg_match("/^Mozilla(?:.*)Gecko(?:.*?)K-Meleon\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "K-Meleon";
			} elseif (preg_match("/^Mozilla(?:.*)rv:([0-9\.]+)\)\sGecko/",$useragent,$match)) {
				$browser = "Mozilla";
			} elseif (preg_match("/^Mozilla(?:.*)compatible;\sKonqueror\/([0-9\.]+);/",$useragent,$match)) {
				$browser = "Konqueror";
			} elseif (preg_match("/^Mozilla\/(?:[34]\.[0-9]+)(?:.*)AvantGo\s([0-9\.]+)/",$useragent,$match)) {
				$browser = "AvantGo";
			} elseif (preg_match("/^Mozilla(?:.*)NetFront\/([34]\.[0-9]+)/",$useragent,$match)) {
				$browser = "NetFront";
			} elseif (preg_match("/^Mozilla\/([34]\.[0-9]+)/",$useragent,$match)) {
				$browser = "Netscape";
			} elseif (preg_match("/^Liferea\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "Liferea";
			} elseif (preg_match("/^curl\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "curl";
			} elseif (preg_match("/^links\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "Links";
			} elseif (preg_match("/^links\s?\(([0-9\.]+)/i",$useragent,$match)) {
				$browser = "Links";
			} elseif (preg_match("/^lynx\/([0-9a-z\.]+)/i",$useragent,$match)) {
				$browser = "Lynx";
			} elseif (preg_match("/^Wget\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "Wget";
			} elseif (preg_match("/^Xiino\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "Xiino";
			} elseif (preg_match("/^W3C_Validator\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "W3C Validator";
			} elseif (preg_match("/^Jigsaw(?:.*) W3C_CSS_Validator_(?:[A-Z]+)\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "W3C CSS Validator";
			} elseif (preg_match("/^Dillo\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "Dillo";
			} elseif (preg_match("/^amaya\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "Amaya";
			} elseif (preg_match("/^DocZilla\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "DocZilla";
			} elseif (preg_match("/^fetch\slibfetch\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "FreeBSD libfetch";
			} elseif (preg_match("/^Nokia([0-9a-zA-Z\-.]+)\/([0-9\.]+)/i",$useragent,$match)) {
				$browser="Nokia";
			} elseif (preg_match("/^SonyEricsson([0-9a-zA-Z\-.]+)\/([a-zA-Z0-9\.]+)/i",$useragent,$match)) {
				$browser="SonyEricsson";
			}

			//$version = $match[1];
			//restrict version to major and minor version #'s
			preg_match("/^\d+(\.\d+)?/",$match[1],$majorvers);
			$version = $majorvers[0];

			$this->browser = $browser;
			$this->browser_version = $version;
	}
} //end class Detector
} //end if !class_exists('Detector')

//wassup_get_time is redundant to current_time('timestamp') wordpress function
/* 
function wassup_get_time() {
	$timeright = gmdate("U");
	$offset = (get_option("gmt_offset")*60*60);
	$timeright = ($timeright + $offset) ;
	return $timeright;
} */

/*
# PHP Calendar (version 2.3), written by Keith Devens
# http://keithdevens.com/software/php_calendar
#  see example at http://keithdevens.com/weblog
# License: http://keithdevens.com/software/license
*/
// Currently not used in WassUp it's a next implementation idea
/*
function generate_calendar($year, $month, $days = array(), $day_name_length = 3, $month_href = NULL, $first_day = 0, $pn = array()){
	$first_of_month = gmmktime(0,0,0,$month,1,$year);
	//remember that mktime will automatically correct if invalid dates are entered
	// for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
	// this provides a built in "rounding" feature to generate_calendar()

	$day_names = array(); #generate all the day names according to the current locale
	for($n=0,$t=(3+$first_day)*86400; $n<7; $n++,$t+=86400) #January 4, 1970 was a Sunday
		$day_names[$n] = ucfirst(gmstrftime('%A',$t)); #%A means full textual day name

	list($month, $year, $month_name, $weekday) = explode(',',gmstrftime('%m,%Y,%B,%w',$first_of_month));
	$weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day
	$title   = htmlentities(ucfirst($month_name)).'&nbsp;'.$year;  #note that some locales don't capitalize month and day names

	//Begin calendar. Uses a real <caption>. See http://diveintomark.org/archives/2002/07/03
	@list($p, $pl) = each($pn); @list($n, $nl) = each($pn); #previous and next links, if applicable
	if($p) $p = '<span class="calendar-prev">'.($pl ? '<a href="'.htmlspecialchars($pl).'">'.$p.'</a>' : $p).'</span>&nbsp;';
	if($n) $n = '&nbsp;<span class="calendar-next">'.($nl ? '<a href="'.htmlspecialchars($nl).'">'.$n.'</a>' : $n).'</span>';
	$calendar = '<table class="calendar">'."\n".
		'<caption class="calendar-month">'.$p.($month_href ? '<a href="'.htmlspecialchars($month_href).'">'.$title.'</a>' : $title).$n."</caption>\n<tr>";

	if($day_name_length){ #if the day names should be shown ($day_name_length > 0)
		//if day_name_length is >3, the full name of the day will be printed
		foreach($day_names as $d)
			$calendar .= '<th abbr="'.htmlentities($d).'">'.htmlentities($day_name_length < 4 ? substr($d,0,$day_name_length) : $d).'</th>';
		$calendar .= "</tr>\n<tr>";
	}

	if($weekday > 0) $calendar .= '<td colspan="'.$weekday.'">&nbsp;</td>'; //initial 'empty' days
	for($day=1,$days_in_month=gmdate('t',$first_of_month); $day<=$days_in_month; $day++,$weekday++){
		if($weekday == 7){
			$weekday   = 0; #start a new week
			$calendar .= "</tr>\n<tr>";
		}
		if(isset($days[$day]) and is_array($days[$day])){
			@list($link, $classes, $content) = $days[$day];
			if(is_null($content))  $content  = $day;
			$calendar .= '<td'.($classes ? ' class="'.htmlspecialchars($classes).'">' : '>').
				($link ? '<a href="'.htmlspecialchars($link).'">'.$content.'</a>' : $content).'</td>';
		}
		else $calendar .= "<td>$day</td>";
	}
	if($weekday != 7) $calendar .= '<td colspan="'.(7-$weekday).'">&nbsp;</td>'; #remaining "empty" days

	return $calendar."</tr>\n</table>\n";
}
*/
//Truncate $input string to a length of $max
function stringShortener($input, $max=0, $separator="(...)", $exceedFromEnd=0){
	if(!$input || !is_string($input)){return false;};
	
	//Replace all %-hex chars with literals and trim the input string 
	//  of whitespaces ...because it's shorter and more legible. 
	//  -Helene D. 11/18/07
	$instring = trim(stripslashes(rawurldecode(html_entity_decode($input)))," +\t");	//insecure

	$inputlen=strlen($instring);
	$max=(is_numeric($max))?(integer)$max:$inputlen;
	//if($max>=$inputlen){return $input;};	//caused security loophole ...only $outstring should be returned
	if ($max < $inputlen) {
		$separator=($separator)?$separator:"(...)";
		$modulus=(($max%2));
		$halfMax=floor($max/2);
		$begin="";
		if(!$modulus){$begin=substr($instring, 0, $halfMax);}
		else{$begin=(!$exceedFromEnd)? substr($instring, 0, $halfMax+1) : substr($instring, 0, $halfMax);}
		$end="";
		if(!$modulus){$end=substr($instring,$inputlen-$halfMax);}
		else{$end=($exceedFromEnd)? substr($instring,$inputlen-$halfMax-1) :substr($instring,$inputlen-$halfMax);}
		$extracted=substr($instring, strpos($instring,$begin)+strlen($begin), $inputlen-$max );
		$outstring = $begin.$separator.$end;
		if (strlen($outstring) >= $inputlen) {  //Because "Fir(...)fox" is longer than "Firefox"
			$outstring = $instring;
		}
		//# use WordPress 2.x function attribute_escape and 1.2.x 
		//  function wp_specialchars to make malicious code 
		//  harmless when echoed to the screen
		$outstring=attribute_escape(wp_specialchars($outstring,ENT_QUOTES));
	} else {
		$outstring = attribute_escape(wp_specialchars($instring,ENT_QUOTES));
	}
	return $outstring;
} //end function stringShortener

//# Return a value of true if url argument is a root url and false when
//#  url constains a subdirectory path or query parameters...
//#  - Helene D. 2007
function url_rootcheck($urltocheck) {
	$isroot = false;
	//url must begin with 'http://'
	if (strncasecmp($urltocheck,'http://',7) == 0) {
		$isroot = true;
		$urlparts=parse_url($urltocheck);
		if (!empty($urlparts['path']) && $urlparts['path'] != "/") {
			$isroot=false;
		} elseif (!empty($urlparts['query'])) {
			$isroot=false;
		}
	}
	return $isroot;
}

//#from a page/post url input, output a url with "$blogurl" prepended for 
//#  blogs that have wordpress installed in a separate folder
//#  -Helene D. 1/22/08
function wAddSiteurl($inputurl) {
	$wpurl = rtrim(get_bloginfo('wpurl'),"/");
	$blogurl = rtrim(get_bloginfo('home'),"/");
	if (strcasecmp($blogurl, $wpurl) == 0) {
		$outputurl=$inputurl;
	} elseif (stristr($inputurl,$blogurl) === FALSE && url_rootcheck($blogurl))  {
		$outputurl=$blogurl."/".ltrim($inputurl,"/");
	} else {
		$outputurl=$inputurl;
	}
	$outputurl = rawurldecode(html_entity_decode($outputurl)); //dangerous
	$outputurl = wCleanURL($outputurl);	//safe
	return $outputurl;
}

//sanitize url of potentially dangerous code before display
function wCleanURL($url="") { 
	if (empty($url)) { 
		return;
	}
	//$urlstring = stripslashes($url);
	if (function_exists('esc_url')) {	//#WP 2.8+
		$cleaned_url = esc_url(stripslashes($url));
	} else {
		$cleaned_url = clean_url(stripslashes($url));
	}
	if (empty($cleaned_url)) {	//oops, clean_url chomp
		$cleaned_url = attribute_escape(stripslashes($url));
	}
	return $cleaned_url;
} //end function

//Output wassup records in the old Digg spy style...
function wassup_spiaView ($from_date="",$rows=0,$spytype="",$spy_datasource="") {
	global $wpdb, $wp_version, $wassup_options, $wdebug_mode;

	if (!class_exists('wassupOptions') && file_exists(dirname(__FILE__). '/wassup.class.php')) {
		include_once(dirname(__FILE__). '/wassup.class.php');
	}
	$wassup_options = new wassupOptions;
	if (empty($spytype)) $spytype=$wassup_options->wassup_default_spy_type;
	$whereis=$wassup_options->getKeyOptions("wassup_default_spy_type","sql",$spytype);
	//check for arguments...
	$to_date = current_time("timestamp");
	if (empty($from_date)) {
		$from_date = (int)($to_date - 7);
	}
	if (empty($spy_datasource)) {
		//temp table is default data source unless not exists
		$spy_datasource = $wassup_options->wassup_table . "_tmp";
	}
	if ($wpdb->get_var("SHOW TABLES LIKE '$spy_datasource'") != $spy_datasource) { 
		$spy_datasource = (!empty($wassup_options->wassup_table)?$wassup_options->wassup_table:$wpdb->prefix ."wassup");
	}
	if ($rows == 0 || !is_numeric($rows)) {
		$rows = 12;
	}

	if (!empty($wassup_options->wassup_screen_res)) {
		$screen_res_size = (int) $wassup_options->wassup_screen_res;
	} else { 
		$screen_res_size = 670;
	}
	$max_char_len = ($screen_res_size)/10;
	//set smaller screen_res_size to make room for sidebar in WP2.7+
	if (version_compare($wp_version, '2.7', '>=')) { 
		$screen_res_size = $screen_res_size-160;
		$max_char_len = $max_char_len-16;
	}
	$wpurl = get_bloginfo('wpurl');
	$blogurl = get_bloginfo('home');
	$unclass = "ip";
	//define google geoip record and create javascript marker icon
	$geoip_rec = array('ip'=>"",'latitude'=>"",'longitude'=>"",'city'=>"",'country_code'=>"");
	$geo_markers=0;

	$qryC = $wpdb->get_results("SELECT id, wassup_id, `timestamp`, ip, hostname, searchengine, urlrequested, agent, referrer, spider, feed, username, comment_author, spam FROM $spy_datasource WHERE `timestamp` > $from_date $whereis ORDER BY `timestamp` DESC LIMIT $rows");
	if (!empty($qryC)) {
		//restrict # of rows to display when needed...
		$row_count = 0;
	//display the rows...
	foreach ($qryC as $cv) {
		$unclass = "";
		$ulclass="users";
		$referrer = __('Direct hit','wassup');
		$requesturl="";
		if ($wassup_options->wassup_time_format == "12") {
		   	$timef = gmdate('h:i:s A', $cv->timestamp);
		} else {
		   	$timef = gmdate('H:i:s', $cv->timestamp);
		}
		$ip = @explode(",", $cv->ip);
		if ($cv->referrer != '') {
		   if ($cv->searchengine != "" || stristr($cv->referrer,$wpurl)!=$cv->referrer) { 
		   	if ($cv->searchengine == "") {
				$referrer = '<a href="'.wCleanURL($cv->referrer).'" target=_"BLANK"><span style="font-weight: bold;">'.stringShortener("{$cv->referrer}", round($max_char_len*.8,0)).'</span></a>';
		   	} else {
				 $referrer = '<a href="'.wCleanURL($cv->referrer).'" target=_"BLANK">'.stringShortener("{$cv->referrer}", round($max_char_len*.9,0)).'</a>';
		   	}
		   } else { 
		   	$referrer = __('From your blog','wassup');
		   }
		}
		if (strstr($cv->urlrequested,'[404]')) {  //no link for 404 page
			$requesturl=stringShortener($cv->urlrequested, round($max_char_len*.9,0)+5);
		} else {
			$requesturl='<a href="'.wAddSiteurl("{$cv->urlrequested}").'" target="_BLANK">'.stringShortener("{$cv->urlrequested}", round($max_char_len*.9,0)).'</a>';
		}
		$map_icon = "marker_user.png";
		$visitor = __("Regular visitor","wassup");
		$ulclass="regular";
		if ($cv->spam == "3") { 
		   	$unclass = "sum-spy-spam";
			$ulclass="spider";
                	$visitor = __("Spammer","wassup").": $cv->hostname";
		} elseif (!empty($rk->spam)) { 
		   	$unclass = "sum-spy-spam";
			$ulclass="spider";
			$visitor = __("Spammer","wassup").": $cv->hostname"; 
		} elseif ($cv->spider != "") {
			if ($cv->feed != "") {
				$visitor = __("Feedreader","wassup").": $cv->spider";
			} else {
				$visitor = __("Spider","wassup").": $cv->spider";
			}
			$unclass = "sum-spy-spider";
			$ulclass="spider";
	 		$map_icon = "marker_bot.png";
		} elseif ($cv->username != "") {
			// User is logged in or is a comment's author
			$unclass = "sum-spy-log";
			$ulclass="userslogged";
			$map_icon = "marker_loggedin.png";
			$visitor = __("Logged user","wassup").": $cv->username";
		} elseif ($cv->comment_author != "") {
			$unclass = "sum-spy-aut";
			$ulclass="users";
			$map_icon = "marker_author.png";
			$visitor =  __("Comment author","wassup").": $cv->comment_author";
		} //end if cv->spam
?>
	<div class="sum-spy"><?php
		// Start getting GEOIP info
		$location="";
		$lat = "";
		$lon = "";
		$flag = "";
		if ($ip[0] != $geoip_rec['ip'] && preg_match('/^(127\.0\.0\.1|192\.168\.|10\.10\.)/',$ip[0])==0) {
			//geolocate a new visitor IP...
			$geoip_rec = wGeolocateIP($ip[0]);
			echo "\n\t<!-- heartbeat -->";
			$lat = $geoip_rec['latitude'];
			$lon = $geoip_rec['longitude'];
			$location = wGetLocationname($geoip_rec);
		} elseif ($ip[0] == $geoip_rec['ip']) {
			//previous visit was from same IP, so reuse data
			$lat = $geoip_rec['latitude'];
			$lon = $geoip_rec['longitude'];
			$location = wGetLocationname($geoip_rec);
		}
		if (!empty($geoip_rec['country_code']) && file_exists(WASSUPDIR."/img/flags/".$geoip_rec['country_code'].".png")) {
			$flag = '<img src="'.WASSUPURL.'/img/flags/'.$geoip_rec['country_code'].'.png" />';
		}

		// Print the JS code to add marker on the map
		if ($wassup_options->wassup_geoip_map == 1 && !empty($lon) && !empty($lat)) {
			$markerHtml='<div style="white-space:nowrap"><div class="bubble">'.$visitor.'<br />'.__("IP").": $ip[0]<br />".__("Location","wassup").": $flag $location<br />".__("Request","wassup").": $timef $requesturl".'<br /></div></div>';
			$pan=false;
			$geo_markers=$geo_markers+1;
			if ($geo_markers == 1) { //pan to 1st marker only (last visitor)
				$pan=true;
			}
			wAdd_GeoMarker($cv->id,$lat,$lon,"$markerHtml",$map_icon,$pan);
		} //end if wassup_geoip_map
?>
		<div class="sum-nav-spy">
			<div class="sum-box">
				<span class="sum-spy-ip <?php print $unclass; ?>"><?php echo $ip[0]; ?></span>
			</div>
			<div class="sum-det-spy">
				<span class="det1"><?php echo $requesturl; ?></span>
				<span class="det2"><strong><?php echo $timef; ?> - </strong> <?php
		print $referrer;
		if (!empty($location)) echo "<br />$flag $location\n"; ?>
				</span>
			</div>
		</div>
	</div><!-- /sum-spy --><?php
		} //end foreach
	} else {
		//display visual indicators that Wassup-spy is running
		if (empty($wassup_options->wassup_geoip_map) && (int)$to_date%79 == 0 ) {
			//display "no activity" message occasionally in visitor list
			echo "\n"; ?>
	<div class="sum-rec sum-nav-spy" style="width:auto; padding:3px;">
		<span class="det3"><?php 
			if ($wassup_options->wassup_time_format == "12") {
		   		echo gmdate('h:i:s A', $to_date);
			} else {
		   		echo gmdate('H:i:s', $to_date);
			}
			echo ' - '.__("No visitor activity","wassup");?> &nbsp; &nbsp; :-( &nbsp; </span>
	</div><?php
			echo "\n";
		} //end if empty
	} //end if !empty($qryC)
} //end function wassup_spiaView

/**
 * print javascript to add a marker to a google map
 * @since v1.8
 */
function wAdd_GeoMarker($item_id, $lat, $lon, $markerHtml, $marker_icon, $pan=true) {
	$img_dir = WASSUPURL.'/img';

	echo "\n<script type=\"text/javascript\">\n//<![CDATA[
	var icon$item_id = new GIcon();
	icon$item_id.image = \"$img_dir/$marker_icon\";
	icon$item_id.shadow = \"$img_dir/shadow.png\";
	icon$item_id.iconSize = new GSize(20.0, 34.0);
	icon$item_id.shadowSize = new GSize(38.0, 34.0);
	icon$item_id.iconAnchor = new GPoint(10.0, 17.0);
	icon$item_id.infoWindowAnchor = new GPoint(10.0, 17.0);
	var point = new GLatLng($lat,$lon);
	var marker$item_id = new GMarker(point, icon$item_id);
	map.addOverlay(marker$item_id);
	GEvent.addListener(marker$item_id, 'click', function() {
		marker$item_id.openInfoWindowHtml('$markerHtml');
	});";
	if ($pan) {
		echo "\n
	map.panTo(new GLatLng($lat,$lon),3);";
	}
	echo "
	//]]>\n</script>";
} //end function wAdd_GeoMarker

//return a location name formatted for wassup_spiaView from array argument
//@since v1.8
function wGetLocationname($geoip_rec=array()) {
	if (!empty($geoip_rec['country_name'])) {
		$location = $geoip_rec['country_name'].' ('.strtoupper($geoip_rec['country_code']).')  City: '.$geoip_rec['city'];
		if ($geoip_rec['country_code'] == "us" && !empty($geoip_rec['region_name'])) {
			$location .= ', '.$geoip_rec['region_name'];
		}
	} else {
		$location = "Country: unknown, City: unknown";
	}
	return $location;
}

// Geocoding location with Google Maps
function geocodeWassUp($location, $key) {
	global $wdebug_mode;
	//Three parts to querystring: q= address, output= format, and key
	$address = urlencode($location);
	$api_url = "http://maps.google.com/maps/geo?q=".$address."&output=csv&key=".$key;

	/*
	//$ch = curl_init();
	//curl_setopt($ch, CURLOPT_URL, $api_url);
	//curl_setopt($ch, CURLOPT_HEADER,0);
	//curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	//$data = curl_exec($ch);
	//curl_close($ch);
	*/
	$apidata = wFetchAPIData($api_url);
	$data = explode(',',$apidata);
	if ($wdebug_mode) {
		echo "\n<!--geocodeWassup data: ";
		print_r($data);
		echo "-->" ;
	}
	return $data;
} //end geocodeWassup

/** 
 * get geographic location and coordinates for an IP address via 
 *  freegeoip.net and save data in 'wassup_meta' table.
 * Since version 1.8
 * @return array (ip, location, latitude, longitude, country)
 */
function wGeolocateIP($ip) {
	global $wpdb, $wdebug_mode;

	$wassup_settings=get_option('wassup_settings');
	$wassup_agent = apply_filters('http_headers_useragent',"WassUp/".$wassup_settings['wassup_version']." - www.wpwp.org");
	$geourl = "http://freegeoip.net/json/$ip";
	$geoip = array('ip'=>$ip,'latitude'=>"",'longitude'=>"",'city'=>"",'country_code'=>"");
	$cache_table = (isset($wassup_settings['wassup_table'])?$wassup_settings['wassup_table']."_meta":$wpdb->prefix."_wassup_meta");
	if (!empty($ip)) {
		$geodata="";
		//1st  check for cached copy of geoip in wassup_meta
		if (!empty($wassup_settings['wassup_cache'])) {
			$cache_id = 0;
			$cache_timestamp = 0;
			$wassup_cache = $wpdb->get_results("SELECT * from $cache_table WHERE `wassup_key`='$ip' && `meta_key`='geoip'");
			if (count($wassup_cache)>0 && !empty($wassup_cache[0]->meta_value)) {
				$geodata = unserialize(html_entity_decode($wassup_cache[0]->meta_value));
				$cache_id = $wassup_cache[0]->meta_id;
				$cache_timestamp = $wassup_cache[0]->meta_expire;
			}
			//check for valid data in cache and >1 day for expiration 
			if (empty($geodata['city']) || ($cache_timestamp - time()) < 86400) {
				$geodata = "";
				$cache_timestamp = 0;
			}
		}
		//#Local lookup of geoip: //TODO
		//try PHP geoip extension function 'geoip_record_by_name'
		/* if ((empty($geodata) && function_exists('geoip_record_by_name')) {
			$geodata = geoip_record_by_name($ip);
			if (!empty($geodata)) {
				//TODO assign geoip array fields
				//$geodata['city'] = ;
			}
		} */
		//#Remote lookups of geoip:
		//try Wordpress 'wp_remote_get' or 'cURL' for geoip
		if (empty($geodata['city']) && empty($geoip['city'])) {
		       $geodata=wFetchAPIData($geourl);
		}
		if (!empty($geodata) && !is_array($geodata)) {
			$geodata = xjson_decode($geodata,true);
		}
		//fill geoip record with remote data
		if (!empty($geodata['country_code']) && empty($geoip['city'])){
			$geoip = $geodata;
			$geoip['country_code'] = strtolower($geodata['country_code']);
		}
		//cache record in 'wassup_meta' table with 7-day expire
		if (!empty($geoip['city']) && !empty($geoip['country_code']) && !empty($wassup_settings['wassup_cache'])) {
			$wassup_cache = array( 'meta_id'=>$cache_id,
					'wassup_key'=>$ip,
					'meta_key'=>'geoip',
					'meta_value'=>attribute_escape(serialize($geoip)),
					'meta_expire'=>time()+7*86400);
			if (empty($cache_id)) {
			 	if (method_exists($wpdb,'insert')) {  //WP 2.5+
					$result = $wpdb->insert($cache_table,$wassup_cache);
				}
			} elseif ($cache_timestamp == 0 && method_exists($wpdb,'update')) {
				$result = $wpdb->update($cache_table,$wassup_cache,array("meta_id"=>$cache_id));
			}
		} //end if !empty(geoip['city'])
	} //end if !empty(ip)

	return $geoip;
} //end function wGeolocateIP

/** wGetStats- 
 * Return an associative array containing the top statistics numbers of
 *  "stat_type" from wassup table. Associative array fields are 
 *  'top_count', 'top_item', and optionally, 'top_link', when data is url.
 *  Results are sorted in descending count order and known spam is 
 *  automatically excluded when spam check is enabled in 'Wassup Options'.
 * Input parameters are 'stat_type'=[name of any column in wassup table],
 * and 2 optional parameters: 
 *  stat_limit=N-- limits results to the top N values. Default=10.
 *  stat_condition='mysql where clause'-- usually a date range clause on 
 *  `timestamp`.  Defaults to 24 hours.
 * Used by action.php TopTen and wassup_widget to retrieve statistics data.
 * - Helene D. 2009-03-04
 */
function wGetStats($stat_type, $stat_limit=10, $stat_condition="") {
	global $wpdb, $wdebug_mode;

	$wassup_settings = get_option('wassup_settings');
	$top_ten = unserialize(html_entity_decode($wassup_settings['wassup_top10']));
	$wpurl =  get_bloginfo('wpurl');
	$blogurl =  get_option('home');
	$wtable_name = (!empty($wassup_settings['wassup_table'])?$wassup_settings['wassup_table'] : $wpdb->prefix . "wassup");
	$wtable_tmp_name = $wtable_name . "_tmp";

	if (empty($stat_limit) || !(is_numeric($stat_limit))) {
		$stat_limit=10;
	}
	if (empty($stat_condition)) {
		$to_date = current_time('timestamp');
		$from_date = ((int)$to_date - 24*(60*60)); //24 hours
		$stat_condition = " `timestamp` >= $from_date ";
	}
	//exclude spam if it is being recorded
	if ($wassup_settings['wassup_spamcheck'] == 1) {
		$spam_condition = " AND spam=0";
	} else {
		$spam_condition = "";
	}
	$stat_condition .= $spam_condition; 

	//get the stats data
	//top search phrases...
	if ($stat_type == "searches") {
		$stat_results = $wpdb->get_results("SELECT count(search) AS top_count, search AS top_item, referrer AS top_link, max(`timestamp`) AS visit_timestamp FROM $wtable_name WHERE $stat_condition AND search!='' AND spider='' GROUP BY search ORDER BY top_count DESC, visit_timestamp DESC LIMIT $stat_limit");

	//top external referrers...
	} elseif ($stat_type == "referrers") {
		//exclude internal referrals
		$wurl = parse_url($blogurl);
		$sitedomain = $wurl['host'];
		$exclude_list = $sitedomain;
		if ($wpurl != $blogurl) {
			$wurl = parse_url($wpurl);
			$wpdomain = $wurl['host'];
			$exclude_list .= ",".$wpdomain;
		}
		//exclude external referrers
		if (!empty($top_ten['topreferrer_exclude'])) {
			$exclude_list .= ",".$top_ten['topreferrer_exclude'];
		}
		//create mysql conditional statement to exclude referrers
		$exclude_referrers = "";
		$exclude_array = array_unique(explode(",", $exclude_list));
		foreach ($exclude_array as $exclude_domain) {
			$exclude_domain = preg_replace('/^(https?\:\/\/|www\.)(www\.)?/','',trim($exclude_domain));
			if ($exclude_domain != "" ) {
				if (strstr($exclude_domain,'//:')===false) {
					$exclude_referrers .= " AND referrer NOT LIKE 'http://".$exclude_domain."%' AND referrer NOT LIKE 'http://www.".$exclude_domain."%' AND referrer NOT LIKE '%:".$exclude_domain."%' AND referrer NOT LIKE '%=http://".$exclude_domain."%'";
				} else {
					$exclude_referrers .= " AND referrer NOT LIKE '%".$exclude_domain."%'";
				}
			}
		}
		$stat_results = $wpdb->get_results("SELECT count(*) AS top_count, LOWER(referrer) AS top_item, referrer AS top_link, max(`timestamp`) AS visit_timestamp FROM $wtable_name WHERE $stat_condition AND referrer!='' AND search='' AND spider='' $exclude_referrers GROUP BY top_item ORDER BY top_count DESC, visit_timestamp DESC LIMIT $stat_limit");
		if ($wdebug_mode) {
			echo "\n<pre>exclude_referrers = $exclude_referrers </pre>\n";
		}

	//top url requests...
	} elseif ($stat_type == "urlrequested") {
		$stat_results = $wpdb->get_results("SELECT count(*) AS top_count, LOWER(REPLACE(urlrequested, '/', '')) AS top_group, LOWER(urlrequested) AS top_item, urlrequested AS top_link, max(`timestamp`) AS visit_timestamp FROM $wtable_name WHERE $stat_condition GROUP BY top_group ORDER BY top_count DESC, visit_timestamp DESC LIMIT $stat_limit");

	//top browser...
	} elseif ($stat_type == "browser") {
		$stat_results = $wpdb->get_results("SELECT count(*) AS top_count, SUBSTRING_INDEX(SUBSTRING_INDEX(browser, ' 0.', 1), '.', 1) AS top_item FROM $wtable_name WHERE $stat_condition AND `browser`!='' AND `browser` NOT LIKE 'N/A%' AND `spider`='' GROUP BY top_item ORDER BY top_count DESC LIMIT $stat_limit");

	//top os...
	} elseif ($stat_type == "os") {
		$stat_results = $wpdb->get_results("SELECT count(os) as top_count, `os` AS top_item FROM $wtable_name WHERE $stat_condition AND `os`!='' AND `os` NOT LIKE 'N/A%' AND spider='' GROUP BY top_item ORDER BY top_count DESC LIMIT $stat_limit");

	//top language/locale..
	} elseif ($stat_type == "language" || $stat_type == "locale") {
		$stat_results = $wpdb->get_results("SELECT count(LOWER(language)) as top_count, LOWER(language) as top_item FROM $wtable_name WHERE $stat_condition AND language!='' AND spider='' GROUP BY top_item ORDER BY top_count DESC LIMIT $stat_limit");

	//top visitors...
	} elseif ($stat_type == "visitor" || $stat_type == "visitors") {
		$stat_results = $wpdb->get_results("SELECT count(username) as top_count, username as top_item, '1loggedin_user' as visitor_type, max(`timestamp`) as visit_timestamp FROM $wtable_name WHERE $stat_condition AND username!='' GROUP BY 2 UNION SELECT count(comment_author) as top_count, comment_author as top_item, '2comment_author' as visitor_type, max(`timestamp`) as visit_timestamp FROM $wtable_name WHERE $stat_condition AND username='' AND comment_author!='' GROUP BY 2 UNION SELECT count(hostname) as top_count, hostname as top_item, '3hostname' as visitor_type, max(`timestamp`) as visit_timestamp FROM $wtable_name WHERE $stat_condition AND username='' AND comment_author='' AND spider='' GROUP BY 2 ORDER BY 1 DESC, 3, 2 LIMIT $stat_limit");

	//top postid (post|page)
	} elseif ($stat_type == "postid" || $stat_type == "article") {
		$stat_results = $wpdb->get_results("SELECT count(*) AS top_count, url_wpid AS top_group, post_title AS top_item, urlrequested AS top_link, max(`timestamp`) as visit_timestamp FROM $wtable_name, {$wpdb->prefix}posts WHERE $stat_condition AND url_wpid!='' AND url_wpid!='0' AND url_wpid = {$wpdb->prefix}posts.ID GROUP BY top_group ORDER BY top_count DESC, visit_timestamp DESC LIMIT $stat_limit");

	} else {
		//TODO: check that wp_wassup.$stat_type column exist and is char
		if (!empty($stat_type)) {
			$stat_results = $wpdb->get_results("SELECT count($stat_type) AS top_count, `$stat_type` AS top_item FROM $wtable_name WHERE $stat_condition AND `$stat_type`!='' AND `$stat_type` NOT LIKE 'N/A%' GROUP BY `$stat_type` ORDER BY top_count DESC LIMIT $stat_limit");
		}
	}

	if (!empty($stat_results[0]->top_count)) {
		return $stat_results;
	} else { 
		return array();
	}
} //end function wGetStats

/**
 * Display the top 10 stats in table columns
 * @access public
 * @param string(4)
 * @return none
 */
function wassup_top10view ($from_date="",$to_date="",$max_char_len="",$top_limit=0,$title=false) {
	global $wpdb, $wassup_options;
	if (!class_exists('wassupOptions') && file_exists(dirname(__FILE__). '/wassup.class.php')) {
		include_once(dirname(__FILE__). '/wassup.class.php');
	}
	$wassup_options = new wassupOptions;
	$top_ten = unserialize(html_entity_decode($wassup_options->wassup_top10));
	if (!is_array($top_ten)) $top_ten = $wassup_options->defaultSettings("top10");
	//$table_name = (!empty($wassup_options->wassup_table)? $wassup_options->wassup_table: $wpdb->prefix . "wassup");
	//$table_tmp_name = $table_name . "_tmp";

	$blogurl =  get_bloginfo('home');
	$url = parse_url($blogurl);
	$sitedomain = preg_replace('/^www\./i','',$url['host']);

	//extend php script timeout length for large tables
	if (!ini_get('safe_mode')) {
		$php_timeout = @ini_get("max_execution_time");
		if (is_numeric($php_timeout) && (int)$php_timeout < 120) {
			@set_time_limit(2*60); 	//  ...to 2 minutes
		}
	}

	if (empty($max_char_len)) {
		$max_char_len = (int)($wassup_options->wassup_screen_res/10);
		//make room for WordPress 2.7+ sidebar
		if (version_compare($wp_version, '2.7', '>=')) { 
			$max_char_len = $max_char_len-16;
		}
	}
	//#add an extra width offset when columns count < 6
	$col_count = array_sum($top_ten);
	if ($col_count > 0 && $col_count < 6 ) {
		$widthoffset = (($max_char_len*(6 - $col_count))/$col_count)*.4; //just a guess
	} else { 
		$widthoffset = 0;
	}
	//extend page width to make room for more than 5 columns
	$pagewidth = $wassup_options->wassup_screen_res;
	if ($col_count > 6) {
		$pagewidth = $pagewidth+17*($col_count-6);
	}
	//New in v1.8.3: top_limit in top10 array
	if (empty($top_limit) || !is_numeric($top_limit)) {
		if (!empty($top_ten['toplimit'])) {
			$top_limit = (int) $top_ten['toplimit'];
		} else {
			$top_limit = 10;	//default
		}
	}

	//mysql conditional query...
	$top_condition = '`timestamp` BETWEEN '.$from_date.' AND '.$to_date;
	if (!empty($top_ten['top_nospider'])) {
		$top_condition .= " AND spider=''";
	}
	echo "\n"; ?>
	<div id="toptenchart" style="width:auto;">
	<table width="100%">
	<tr valign="top"><?php
	if (!empty($title)) { ?>
		<th colspan="<?php echo $col_count; ?>"><span style="centered"><?php echo $title; ?></span></th></tr><tr><?php 
	}
	//show a line# column for long data columns
	if ($top_limit > 10) wPrintRowNums($top_limit);

	//#output top 10 searches
	if ($top_ten['topsearch'] == 1) {
		$top_results = wGetStats("searches",$top_limit,$top_condition);
		$char_len = round(($max_char_len*.30)+$widthoffset,0); ?>
		<td style="min-width:<?php echo ($char_len-5); ?>px;">
		<ul class="charts">
		<li class="chartsT"><?php echo _e("TOP QUERY", "wassup"); ?></li> <?php 
		$i=0;
		$ndigits=1;
		if (count($top_results) >0) {
			$ndigits = strlen("{$top_results[0]->top_count}");
		foreach ($top_results as $top10) { 
			echo "\n"; ?>
		<li class="charts"><nobr><?php echo wPadNum($top10->top_count, $ndigits).' <a href="'.$top10->top_link.'" target="_BLANK" title="'.substr($top10->top_item,0,$wassup_options->wassup_screen_res-100).'">'.stringShortener(preg_replace('/'.preg_quote($blogurl,'/').'/i', '', $top10->top_item),$char_len).'</a>'; ?></nobr></li><?php
				$i++;
		}
		}
		//finish list with empty <li> for style consistency
		wListFiller($i,$top_limit,"charts"); ?>
		</ul>
		</td> <?php
	} // end if topsearch

	//#output top 10 referrers
	if ($top_ten['topreferrer'] == 1) {
		//to prevent browser timeouts, send <!--heartbeat--> output
		echo "\n<!--heartbeat-->";
		$top_results = wGetStats("referrers",$top_limit,$top_condition);
		$char_len = round(($max_char_len*.22)+$widthoffset,0); ?>
		<td style="min-width:<?php echo ($char_len-5); ?>px;">
		<ul class="charts">
		<li class="chartsT"><?php _e("TOP REFERRER", "wassup"); ?></li><?php
		$i=0;
		$ndigits=1;
		if (count($top_results) >0) {
			$ndigits = strlen("{$top_results[0]->top_count}");
		foreach ($top_results as $top10) {
			echo "\n"; ?>
		<li class="charts"><nobr><?php echo wPadNum($top10->top_count,$ndigits);
			echo ' <a href="'.clean_url($top10->top_link,'','url').'" title="'.attribute_escape($top10->top_link).'" target="_BLANK">';
			//#cut http:// from displayed url and truncate
			//#   instead of using stringShortener
			echo substr(str_replace("http://", "", attribute_escape($top10->top_item)),0,$char_len);
			if (strlen($top10->top_item) > ($char_len + 7)) { 
			   	echo '...';
			}
			echo '</a>'; ?></nobr></li><?php
			$i++;
		}
		}
		//finish list with empty <li> for styling consistency 
		wListFiller($i,$top_limit,"charts"); ?>
                </ul>
                </td> <?php
	} //end if topreferrer

	//#output top 10 url requests
	if ($top_ten['toprequest'] == 1) {
		echo "\n<!--heartbeat-->";
		$top_results = wGetStats("urlrequested",$top_limit,$top_condition);
		$char_len = round(($max_char_len*.28)+$widthoffset,0); ?>
		<td style="min-width:<?php echo ($char_len-5); ?>px;">
		<ul class="charts">
		<li class="chartsT"><?php _e("TOP REQUEST", "wassup"); ?></li><?php
		$i=0;
		$ndigits=1;
		if (count($top_results) >0) {
			$ndigits = strlen("{$top_results[0]->top_count}");
		foreach ($top_results as $top10) {
			echo "\n"; ?>
		<li class="charts"><nobr><?php echo wPadNum($top10->top_count,$ndigits);
			if (strstr($top10->top_item,'[404]')) { //no link for 404 pages
				echo ' <span class="top10" title="'.substr($top10->top_item,0,$wassup_options->wassup_screen_res-100).'">'.stringShortener(preg_replace('/'.preg_quote($blogurl,'/').'/i', '', $top10->top_item),$char_len).'</span>';
			} else {
				echo ' <a href="'.wAddSiteurl($top10->top_link).'" target="_BLANK" title="'.substr($top10->top_item,0,$wassup_options->wassup_screen_res-100).'">'.stringShortener(preg_replace('/'.preg_quote($blogurl,'/').'/i', '', $top10->top_item),$char_len).'</a>';
			} ?></nobr></li><?php
			$i++;
		}
		}
		//finish list with empty <li> for styling consistency 
		wListFiller($i,$top_limit,"charts"); ?>
		</ul>
		</td><?php 
	} //end if toprequest

	//#get top 10 browsers...
	if ($top_ten['topbrowser'] == 1) {
		echo "\n<!--heartbeat-->";
		$top_results = wGetStats("browser",$top_limit,$top_condition);
		$char_len = round(($max_char_len*.17)+$widthoffset,0); ?>
		<td style="min-width:<?php echo ($char_len-5); ?>px;">
		<ul class="charts">
		<li class="chartsT"><?php _e("TOP BROWSER", "wassup") ?></li><?php
		$i=0;
		$ndigits=1;
		if (count($top_results) >0) {
			$ndigits = strlen("{$top_results[0]->top_count}");
		foreach ($top_results as $top10) {
			echo "\n"; ?>
		<li class="charts"><nobr><?php echo wPadNum($top10->top_count,$ndigits);
			echo ' <span class="top10" title="'.$top10->top_item.'">'.stringShortener($top10->top_item, $char_len).'</span>'; ?></nobr></li><?php
			$i++;
		}
		}
		//finish list with empty <li> for styling consistency 
		wListFiller($i,$top_limit,"charts"); ?>
		</ul>
		</td><?php
	}  //end if topbrowser

	//#output top 10 operating systems...
	if ($top_ten['topos'] == 1) { 
		echo "\n<!--heartbeat-->";
		$top_results = wGetStats("os",$top_limit,$top_condition);
		$char_len = round(($max_char_len*.15)+$widthoffset,0); ?>
		<td style="min-width:<?php echo ($char_len-5); ?>px;">
		<ul class="charts">
		<li class="chartsT"><?php _e("TOP OS", "wassup") ?></li><?php
		$i=0;
		$ndigits=1;
		if (count($top_results) >0) {
			$ndigits = strlen("{$top_results[0]->top_count}");
		foreach ($top_results as $top10) {
			echo "\n"; ?>
		<li class="charts"><nobr><?php echo wPadNum($top10->top_count,$ndigits); ?> <span class="top10" title="<?php echo $top10->top_item; ?>"><?php echo stringShortener($top10->top_item, $char_len); ?></span></nobr></li><?php
			$i++;
		}
		}
		//finish list with empty <li> for styling consistency 
		wListFiller($i,$top_limit,"charts"); ?>
		</ul>
		</td><?php
	} // end if topos
		
	//#output top 10 locales/geographic regions...
	if ($top_ten['toplocale'] == 1) {
		echo "\n<!--heartbeat-->";
		$top_results = wGetStats("language",$top_limit,$top_condition);
		$char_len = round(($max_char_len*.15)+$widthoffset,0); ?>
		<td style="min-width:<?php echo ($char_len-5); ?>px;">
		<ul class="charts">
		<li class="chartsT"><?php _e("TOP LOCALE", "wassup"); ?></li><?php
		$i=0;
		$ndigits=1;
		if (count($top_results) >0) {
			$ndigits = strlen("{$top_results[0]->top_count}");
		foreach ($top_results as $top10) {
			echo "\n"; ?>
		<li class="charts"><nobr><?php echo wPadNum($top10->top_count,$ndigits);
			echo ' <img src="'.WASSUPURL.'/img/flags/'.strtolower($top10->top_item).'.png" alt="" />'; ?>
			<span class="top10" title="<?php echo $top10->top_item; ?>"><?php echo $top10->top_item; ?></span></nobr></li><?php
			$i++;
		}
		}
		//finish list with empty <li> for styling consistency 
		wListFiller($i,$top_limit,"charts"); ?>
		</ul>
		</td><?php
	} // end if toplocale
		
	//#output top visitors
	if ($top_ten['topvisitor'] == 1) {
		echo "\n<!--heartbeat-->";
		$top_results = wGetStats("visitor",$top_limit,$top_condition);
		$char_len = round(($max_char_len*.17)+$widthoffset,0); ?>
		<td style="min-width:<?php echo ($char_len-5); ?>px;">
		<ul class="charts">
		<li class="chartsT"><?php _e("TOP VISITOR", "wassup"); ?></li><?php 
		$i=0;
		$ndigits=1;
		if (count($top_results)>0) {
			$ndigits = strlen("{$top_results[0]->top_count}");
		foreach ($top_results as $top10) { 
			if ($top10->visitor_type == "1loggedin_user")
				$uclass=" userslogged";
			elseif ($top10->visitor_type == "2comment_author")
				$uclass=" users";
			else
				$uclass="";
			echo "\n"; ?>
		<li class="charts"><nobr><?php echo wPadNum($top10->top_count,$ndigits).' <span class="top10'.$uclass.'" title="'.$top10->top_item.'">'.stringShortener($top10->top_item, $char_len).'</span>'; ?></nobr></li><?php
			$i++;
		} //end loop
		}
		//finish list with empty <li> for styling consistency 
		wListFiller($i,$top_limit,"charts"); ?>
		</ul>
		</td>
<?php	} // end if topvisitor

	//#output top article (post|page by id)
	if ($top_ten['toppostid'] == 1) {
		echo "\n<!--heartbeat-->";
		$top_results = wGetStats("postid",$top_limit,$top_condition);
		$char_len = round(($max_char_len*.28)+$widthoffset,0); ?>
		<td style="min-width:<?php echo ($char_len-5); ?>px;">
		<ul class="charts">
		<li class="chartsT"><?php _e("TOP ARTICLE", "wassup"); ?></li><?php
		$i=0;
		$ndigits=1;
		if (count($top_results) >0) {
			$ndigits = strlen("{$top_results[0]->top_count}");
		foreach ($top_results as $top10) {
			echo "\n"; ?>
		<li class="charts"><nobr><?php echo wPadNum($top10->top_count,$ndigits);
			echo ' <a href="'.wAddSiteurl($top10->top_link).'" target="_BLANK" title="'.$top10->top_item.'">'.stringShortener($top10->top_item,$char_len).'</a>'; ?> </nobr></li><?php
			$i++;
		}
		}
		//finish list with empty <li> for styling consistency 
		wListFiller($i,$top_limit,"charts"); ?>
		</ul>
		</td><?php 
	} // end if toppost
	//show a line# column for long data columns
	if ($top_limit > 10 && $col_count > 6) {
		wPrintRowNums($top_limit);
	}
	?></tr>
	</table>
	<span style="font-size:7pt;"> <?php 
	if ($wassup_options->wassup_spamcheck == 1 || !empty($top_ten['top_nospider'])) { ?><br/>*<?php
		if ($wassup_options->wassup_spamcheck == 1 && !empty($top_ten['top_nospider'])) {
			_e("This report excludes spam and spider records","wassup");
		} elseif (!empty($top_ten['top_nospider'])) {
			_e("This report excludes spider records","wassup");
		} else {
			_e("This report excludes spam records","wassup");
		}
	} ?> </span>
	</div> <?php
} //end wassup_top10view

function wListFiller($li_count=0,$li_limit=10,$li_class="charts") {
	//finish a list with empty <li>'s for styling consistency 
	if ($li_count < $li_limit) {
		for ($i=$li_count; $i<$li_limit; $i++) { 
			echo "\n"; ?>
		<li class="<?php echo $li_class; ?>">&nbsp; &nbsp;</li><?php
		}
	}
} //end wListFiller
/*
 * print a table column with line number rows from 1 to "$top_limit"
 * @param integer
 * @output html
 * @return none
 */
function wPrintRowNums($top_limit=10) {
	$ndigits = strlen("{$top_limit}");
	echo "\n"; ?>
		<td style="min-width:8px;">
		<ul class="charts rownums">
		<li class="chartsT">&nbsp;</li><?php
	for ($i=1; $i<= $top_limit; $i++) {
		echo "\n"; ?>
		<li class="charts"><nobr><?php echo wPadNum($i, $ndigits); ?></nobr></li><?php
	} ?>
		</td><?php
} //end function

/**
 * return html code to pad an integer ($li_number) with spaces to match a
 * width of $li_width
 * @param integer (2)
 * @return string (html)
 */
function wPadNum($li_number, $li_width=1) {
	$numstr = (int)$li_number;
	$ndigits = strlen("$numstr");
	$padding = '';
	if ($ndigits < $li_width) {
		for ($i=$ndigits; $i < $li_width; $i++) $padding .= '&nbsp;';
	}
	$padhtml = '<span class="fixed">'."$padding{$numstr}</span>";
	return ($padhtml);
}

// How many digits have an integer -- quicker to use 'strlen' function
// function digit_count($n, $base=10) {
//  if($n == 0) return 1;
//  if($base == 10) {
//    # using the built-in log10(x)
//    # might be more accurate than log(x)/log(10).
//    return 1 + floor(log10(abs($n)));
//  }else{
//    # here  logB(x) = log(x)/log(B) will have to do.
//   return 1 + floor(log(abs($n))/ log($base));
//  }
//}

//Round the integer to the next near 10
function roundup($value) {
	//$dg = digit_count($value);
	$numstr = (int)$value;
	$dg = strlen("$numstr");
	if ($dg <= 2) {
		$dg = 1;
	} else {
		$dg = ($dg-2);
	}
	return (ceil(intval($value)/pow(10, $dg))*pow(10, $dg)+pow(10, $dg));
}

function Gchart_data($Wvisits, $pages=null, $atime=null, $type, $charttype=null, $axes=null, $chart_loc=null) {
	global $wdebug_mode;
	$chartAPIdata = false;
// Port of JavaScript from http://code.google.com/apis/chart/
// http://james.cridland.net/code
   // First, find the maximum value from the values given
   if ($axes == 1) {
	$maxValue = roundup(max(array_merge($Wvisits, $pages)));
	//$maxValue = roundup(max($Wvisits));
	$halfValue = ($maxValue/2); 
	$maxPage = $maxValue;
   } else {
	$maxValue = roundup(max($Wvisits));
	$halfValue = ($maxValue/2);
	$maxPage = roundup(max($pages));
	$halfPage = ($maxPage/2);
   }

   // A list of encoding characters to help later, as per Google's example
   $simpleEncoding = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

   $chartData = "s:";

	// Chart type has two datasets
	if ($charttype == "main") {
		$label_time = "";
		for ($i = 0; $i < count($Wvisits); $i++) {
			$currentValue = $Wvisits[$i];
			$currentTime = $atime[$i];
			$label_time.=str_replace(' ', '+', $currentTime)."|";
			if ($currentValue > -1) {
				$chartData.=substr($simpleEncoding,61*($currentValue/$maxValue),1);
			} else {
				$chartData.='_';
			}
		}
		//no x-axis labels in widgets
		if ($chart_loc == "dashboard" || $chart_loc == "widget"){
			$label_time="|";
		}
		// Add pageviews line to the chart
		if (count($pages) != 0) {
			$chartData.=",";
			for ($i = 0; $i < count($pages); $i++) {
				$currentPage = $pages[$i];
				$currentTime = $atime[$i];
     
				if ($currentPage > -1) {
					$chartData.=substr($simpleEncoding,61*($currentPage/$maxPage),1);
				} else {
					$chartData.='_';
				}
			}
		}
		// Return the chart data - and let the Y axis to show the maximum value
   		if ($axes == 1) {
			$chartAPIdata=$chartData."&chxt=x,y&chxl=0:|".$label_time."1:|0|".$halfValue."|".$maxValue."&chxs=0,6b6b6b,9";
		} else {
			$chartAPIdata=$chartData."&chxt=x,y,r&chxl=0:|".$label_time."1:|0|".$halfValue."|".$maxValue."|2:|0|".$halfPage."|".$maxPage."&chxs=0,6b6b6b,9";
		}
	
	// Chart type has one one dataset
	// It's unused now
	} else {
		for ($i = 0; $i < count($Wvisits); $i++) {
			$currentValue = $Wvisits[$i];
			$currentTime = $atime[$i];
			$label_time.=str_replace(' ', '+', $currentTime)."|";

			if ($currentValue > -1) {
				$chartData.=substr($simpleEncoding,61*($currentValue/$maxValue),1);
			} else {
				$chartData.='_';
			}
		}
		$chartAPIdata=$chartData."&chxt=x,y&chxl=0:|".$label_time."|1:|0|".$halfValue."|".$maxValue."&chxs=0,6b6b6b,9";
	}
	return $chartAPIdata;

} //end function

// Used to show main visitors details query, to count items and to extract data for main chart
class WassupItems {
	// declare variables
        var $tableName;
        var $from_date;
        var $to_date;
        var $searchString;
        var $whereis;
        var $ItemsType;
        var $Limit;
        var $Last;
	var $WpUrl;

	/* Constructor */
	function wassupitems($table_name,$date_from,$date_to,$whereis=null,$limit=null) {
		global $wpdb, $wassup_options, $wdebug_mode;
		if (empty($wassup_options->wassup_table)) {
			$wassup_options = new wassupOptions;
		}
		if (empty($table_name)) {
			$table_name = $wassup_options->wassup_table;
		}
		if (empty($date_to) || !is_numeric($date_to)) {
			$date_to = current_time('timestamp');
		}
		if ($date_from == "" || !is_numeric($date_from)) {
			//$date_from =  strtotime('-24 hours', $date_to);
			if ($table_name == $wassup_options->wassup_table) {
				//use default range from wassup_options
				if (!empty($wassup_options->wassup_time_period)) {
					$last = $wassup_options->wassup_time_period;
				} else {
					$last = 1;
				}
				$date_from = $date_to - (int)(($last*24)*3600);
			} else {
				$date_from =  $date_to - 3*60; //3 minutes
			}
		}
		$this->tableName = $table_name;
		$this->from_date = $date_from;
		$this->to_date = $date_to;
		$this->whereis = $whereis;
		$this->limit = $limit;
	}
	/* Methods */
	// Function to show main query and count items
        function calc_tot($Type, $Search="", $specific_where_clause=null, $distinct_type=null) {
		global $wpdb, $wassup_options, $wdebug_mode;

                $this->ItemsType = $Type;
		$this->searchString = $Search;
		$ss = "";
		if (!empty($Search) || !empty($specific_where_clause)) {
			$ss = $this->buildSearch($Search,$specific_where_clause);
		}

		// Switch by every (global) items type (visits, pageviews, spams, etc...)
                switch ($Type) {
                        // This is the MAIN query to show the chronology
		case "main":
			//## Extend mysql wait timeout to 2.5 minutes and extend
			//#  php script timeout to 3 minutes to prevent script
			//#  hangs with large tables on slow server.
			if (!ini_get('safe_mode')) @set_time_limit(3*60);
			$results = $wpdb->query("SET wait_timeout = 160");

			//TODO: use a subquery for MySQL 5+
			//main query
			//  - retrieve one row per wassup_id with timestamp = max(timestamp) (ie. latest record)
			// "sql_buffer_result" select option helps in cases where it takes a long time to retrieve results.  -Helene D. 2/29/09
			$qry = sprintf("SELECT SQL_BUFFER_RESULT *, max(`timestamp`) as max_timestamp, min(`timestamp`) as min_timestamp, count(wassup_id) as page_hits FROM %s WHERE `timestamp` >= %s %s %s GROUP BY wassup_id ORDER BY max_timestamp DESC %s",
				$this->tableName,
				$this->from_date, 
				$ss,
				$this->whereis,
				$this->Limit);
			$results = $wpdb->get_results($qry);
			if (empty($results) || !is_array($results)) { //try without buffer
				$qry = sprintf("SELECT *, max(`timestamp`) as max_timestamp, min(`timestamp`) as min_timestamp, count(wassup_id) as page_hits FROM %s WHERE `timestamp` >= %s %s %s GROUP BY wassup_id ORDER BY max_timestamp DESC %s",
					$this->tableName,
					$this->from_date, 
					$ss,
					$this->whereis,
					$this->Limit);
				$results = $wpdb->get_results($qry);
			}
			//return $results;
			break;
		case "count":
			// These are the queries to count the items hits/pages/spam
			$qry = sprintf("SELECT COUNT(%s `wassup_id`) AS itemstot FROM %s WHERE `timestamp` >= %s %s %s",
					$distinct_type,
					$this->tableName,
					$this->from_date,
					$ss,
					$this->whereis);
			$results = $wpdb->get_var($qry);
			//$itemstot = $wpdb->get_var($qry);
			//return $itemstot;
			break;
		case "main-ip":		//TODO
			// These are the queries to count the hits/pages/spam by ip
			$qry = sprintf("SELECT *, max(`timestamp`) as max_timestamp, min(`timestamp`) as min_timestamp, count(`ip`) as page_hits FROM %s WHERE `timestamp` >= %s %s %s GROUP BY ip ORDER BY max_timestamp DESC %s",
					$this->tableName,
					$this->from_date, 
					$ss,
					$this->whereis,
					$this->Limit);
			$results = $wpdb->get_results($qry);
			break;
		case "count-ip":	//TODO
			// These are the queries to count the hits/pages/spam by ip
			$qry = sprintf("SELECT COUNT(%s `ip`) AS itemstot FROM %s WHERE `timestamp` >= %s %s %s",
					$distinct_type,
					$this->tableName,
					$this->from_date,
					$ss,
					$this->whereis);
			$results = $wpdb->get_var($qry);
			break;
		} //end switch
		if (!empty($results)) {
			return $results;
		} else {
			return false;
		}
	} //end function calc_tot

	// $Ctype = chart's type by time
	// $Res = resolution
	// $Search = string to add to where clause
        function TheChart($Ctype, $Res, $chart_height, $Search="", $axes_type, $chart_bg, $chart_loc="page", $chart_group="") {
		global $wpdb, $wassup_options, $wdebug_mode;

		$mysqlversion=substr(mysql_get_server_info(),0,3);
		$cache_table = (!empty($wassup_options->wassup_table)?$wassup_options->wassup_table."_meta":$wpdb->prefix."wassup_meta");
		$this->searchString = $Search;
		if (is_numeric($Ctype)) {
			$this->Last = $Ctype;
		} else {
			$Ctype=1;	/* default to 24-hour chart */
		}
		$chart_url="";
		$chart_key="";	//for cache record key
		$cache_id=0;	//for cache record unique id

		//First check for cached chart
		if (!empty($wassup_options->wassup_cache)) {
			//create chart_id for cached charts
			$chart_key = "$chart_loc{$Res}x{$chart_height}-{$axes_type}{$chart_group}{$Ctype}{$Search}".intval(date('i')/15).date('HdmY');
			//TODO
			$chart_cache = $wpdb->get_results("SELECT * from $cache_table WHERE `wassup_key`='$chart_key' && `meta_key`='chart'");
			if (count($chart_cache)>0 && !empty($wassup_cache[0]->meta_value)) {
				$chart_url = html_entity_decode($wassup_cache[0]->meta_value);
				$cache_id = $wassup_cache[0]->meta_id;
				if ($wdebug_mode) {
					echo "\n<!-- Cached chart found. cache_id=$cache_id -->\n";
				}
			}
		}

		//Second..create new chart
		if (empty($chart_url)) {
			//Add Search variable to WHERE clause
			$ss="";
			if (!empty($Search)) {
				$ss = $this->buildSearch($Search);
			}
                	$hour_todate = $this->to_date;

		//# MySql 'FROM_UNIXTIME' converts a UTC timestamp to a
		//#  datetime value localized to MySQL's session timezone. 
		//#  Since `timestamp` was already localized before insert,
		//#  any datetime translation using MySQL's 'FROM_UNIXTIME'
		//#  must be converted to UTC/GMT afterwards to get an 
		//#  accurate datetime value for Wordpress.
		//Important Note: Since Wordpress v2.8.3, PHP timezone was
		//  modified within Wordpress in a manner that could cause
		//  a mismatch between PHP timezone and MySQL timezone 
		//  when Wordpress is in a different timezone from it's 
		//  host server. This change triggered charts timeline
		//  errors in Wassup 1.7.2.1. 
		//  Since Wassup v1.8, a new argument 'tzoffset' was added
		//  to 'wassupOptions::getMySQLsetting()' that calculates
		//  MySQL's time offset by subtracting Mysql NOW() from 
		//  Wordpress current_time(), removing the problematic
		//  timezone value from the equation.
		$UTCoffset = $wassup_options->getMySQLsetting("tzoffset");
		if (empty($UTCoffset)) $UTCoffset = "+0:00"; //GMT
		//$WPoffset = (int)(get_option("gmt_offset")*60*60);
		//$PHPoffset = (int)date('Z');
		//
		//#for US/Euro date display: USA Timezone=USA date format.
		//TODO: Use date format in Wordpress to determine x-axis format
		if (in_array(date('T'), array("ADT","AST","AKDT","AKST","CDT","CST","EDT","EST","HADT","HAST","MDT","MST","PDT","PST"))) { 
			$USAdate = true;
		} else {
			$USAdate = false;
		}
		$hour_fromdate = $this->from_date;
		$point_label = array();
		$x_divisor=1;
		$x_increment = 3600;	//1 hour increments in timeline
		$x_grid=8.33;
		$x_groupformat = "%Y%m%d%H%i";
		$wp_groupformat = 'YmdHi';
		$cache_time=300; //5-minute cache
		// Options by chart type
		switch ($Ctype) {
		case ".05":
		case ".1":
			$cTitle = __("Last 1 Hour", "wassup");
			$x_axes_label = "%H:%i";
			$wp_timeformat = 'H:i';
			$x_points = 12;		//no. of x-axis points
			$x_increment = 300;	//5 minute increments
			$x_divisor = $x_increment;
			$cache_time=180; //3-minute cache
			break;
		case ".25":
		case ".4":
			$cTitle = __("Last 6 Hours", "wassup");
			$x_axes_label = "%H:%i";
			$wp_timeformat = 'H:i';
			$x_points = 12;		//number of x-axis points
			$x_increment = 30*60;	//30 minute increments
			$x_divisor = $x_increment;
			$cache_time=300; //5-minute cache
			break;
		case "7":
			$cTitle = __("Last 7 Days", "wassup");
			$x_groupformat = "%Y%m%d";
			$wp_groupformat = 'Ymd';
			if ($USAdate) { 
				$x_axes_label = "%a %b %d";
				$wp_timeformat = 'D M d';
			} else { 
				$x_axes_label = "%a %d %b";
				$wp_timeformat = 'D d M';
			}
			$x_points = 7;
			$x_increment = 24*60*60; //24-hour increments
			break;
		case "14":
			$cTitle = __("Last 2 Weeks", "wassup");
			$x_groupformat = "%Y%m%d";
			$wp_groupformat = 'Ymd';
			if ($USAdate) { 
				$x_axes_label = "%a %b %d";
				$wp_timeformat = 'D M d';
			} else { 
				$x_axes_label = "%a %d %b";
				$wp_timeformat = 'D d M';
			}
			$x_points = 14;
			$x_increment = 24*60*60; //24-hour increments
			break;
		case "30":
			$cTitle = __("Last Month", "wassup");
			$x_groupformat = "%Y%m%d";
			$wp_groupformat = 'Ymd';
			if ($USAdate) { 
				$x_axes_label = " %b %d";
				$wp_timeformat = 'M d';
			} else { 
				$x_axes_label = "%d %b";
				$wp_timeformat = 'd M';
			}
			$x_points = 30; //30
			$x_increment = 24*60*60; //24-hour increments
			break;
		case "90":
			$cTitle = __("Last 3 Months", "wassup");
			$x_groupformat = "%Y%u";
			$wp_groupformat = 'YW';
			if ($USAdate) { 
				$x_axes_label = " %b %d";
				$wp_timeformat = 'M d';
			} else { 
				$x_axes_label = "%d %b";
				$wp_timeformat = 'd M';
			}
			$x_points = 12; //could be 13
			$x_increment = 24*3600*7; //1-week increments
			break;
		case "180":
			$cTitle = __("Last 6 Months", "wassup");
			$x_groupformat = "%Y%m";
			$wp_groupformat = 'Ym';
			$x_axes_label = " %b %Y";
			$x_points = 0; //6
			break;
		case "365":
			$cTitle = __("Last Year", "wassup");
			$x_groupformat = "%Y%m";
			$wp_groupformat = 'Ym';
			$x_axes_label = "%b %Y";
			$x_points = 0; //12
			break;
		case "0":
			$cTitle = __("All Time", "wassup");
			$x_groupformat = "%Y%m";
			$x_axes_label = "%b %Y";
			$x_points = 0; //unknown number of x-axis points
			break;
		case "1":
		default:
			$cTitle = __("Last 24 Hours", "wassup");
			$x_groupformat = "%Y%m%d%H";
			$wp_groupformat = 'YmdH';
			$x_axes_label = "%H:00";
			$wp_timeformat = 'H:00';
			$x_points = 24;		//no. of x-axis points
			$x_increment = 60*60;	//1-hour increments
			$x_divisor = $x_increment;
		}

		//create Wordpress labels to replace the MySQL x-axis labels which could be incorrect due to PHP/MySQL timezone mismatch issues
		if ($x_points >0 && $hour_fromdate >0) {
			$points_end = current_time('timestamp')+60; 
			for ($i=0;$i<$x_points;$i++) {
				$x_timestamp=((int)(($hour_fromdate+(($i+1)*$x_increment))/$x_divisor))*$x_divisor;
				if ($x_timestamp < $points_end) {
					if ($x_divisor > 1) {
						$tgroup[] = $x_timestamp;
					} else {
						$tgroup[] = gmdate($wp_groupformat,$x_timestamp);
					}
					$tlabel[] = gmdate($wp_timeformat,$x_timestamp);
				}
			}
			if ($wdebug_mode) {
				echo "\n<!-- \$x-points= ".implode("|",$tlabel)."\n";
				echo " \$tgroup=".implode("|",$tgroup)."-->";
			}
		}
		//if ($hour_fromdate == "") $hour_fromdate = strtotime("-24 hours", $hour_todate);

		if ($x_divisor > 1) {
		$qry = sprintf("SELECT COUNT( DISTINCT `wassup_id` ) AS items, COUNT(`wassup_id`) AS pages, CAST(`timestamp`/$x_divisor AS UNSIGNED)*$x_divisor AS xgroup, DATE_FORMAT(CONVERT_TZ( FROM_UNIXTIME(CAST((`timestamp`+0)/$x_divisor AS UNSIGNED)*$x_divisor), '%s', '+0:00'), '%s') as thedate FROM %s WHERE `timestamp` > %s %s %s GROUP BY 3 ORDER BY `timestamp`",
			$UTCoffset,
			$x_axes_label,
			$this->tableName,
			$hour_fromdate, 
			$this->whereis,
			$ss); 
		} else {
		$qry = sprintf("SELECT COUNT( DISTINCT `wassup_id` ) AS items, COUNT(`wassup_id`) AS pages, DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(CAST(`timestamp` AS UNSIGNED)), '%s', '+0:00'), '%s') AS xgroup, DATE_FORMAT(CONVERT_TZ( FROM_UNIXTIME(CAST(`timestamp` AS UNSIGNED)), '%s', '+0:00'), '%s') as thedate FROM %s WHERE `timestamp` > %s %s %s GROUP BY 3 ORDER BY `timestamp`",
			$UTCoffset,
			$x_groupformat,
			$UTCoffset,
			$x_axes_label,
			$this->tableName,
			$hour_fromdate, 
			$this->whereis,
			$ss); 
		}
		if ($wdebug_mode) {
			echo "\n<!-- \$query= $qry-->\n";
		}
		$qry_result = $wpdb->get_results($qry,ARRAY_A);
		// Extract arrays for Visits, Pages and X_Axis_Label
		$chart_points = count($qry_result);
		if ($chart_points > 0) {
			//MySQL results have sufficient data points
			if ($chart_points >= $x_points-1 || empty($tlabel)) {
				//use MySQL labels
				foreach ($qry_result as $bhits) {
					$y_hits[] = $bhits['items'];
					$y_pages[] = $bhits['pages'];
					$x_label[] = $bhits['thedate'];
					$x_group[] = $bhits['xgroup']; //debug
				}
			//MySQL results have missing data because of zero
			// hits in timeline...manually insert missing zeros
			} else {
				//combine Wordpress & MySQL labels
				$i=0;
				foreach ($qry_result as $bhits) {
					while ($i <= $x_points-1 && $bhits['xgroup'] > $tgroup[$i]) {
						//add 0-points to data
						$y_hits[] = 0;
	                			$y_pages[] = 0;
						$x_label[] = $tlabel[$i];
						$i=$i+1;
					}
					$y_hits[] = $bhits['items'];
	                		$y_pages[] = $bhits['pages'];
					$x_label[] = $bhits['thedate'];
					$x_group[] = $bhits['xgroup']; //debug
					$i = $i+1;
				}
			}
			//prune overcrowded x-axis labels //TODO
			//if (count($x_label) > 20 && $chart_width < 1000) {
			//}
			if ($wdebug_mode) {
				echo "\n<!-- \$x-group= ".implode("|",$x_group);
				echo "\n \$x-labels= ".implode("|",$x_label)."-->\n";
			}
			//change chart grid if number of x-axis points!=12
			$lablcount = count($x_label)-1;
			if ($lablcount == 7 || $lablcount == 14) {
				$x_grid=7.15;
			} elseif ($lablcount == 6) {	//5?
				$x_grid=10;
			} elseif ($lablcount == 9) {
				$x_grid=9.1;	//1 year, 6 hours
			} elseif ($lablcount == 11) {
				$x_grid=9.1;
			} elseif ($lablcount == 13) {
				$x_grid=7.7;	//90 days
			} elseif ($lablcount == 23) {
				$x_grid=8.67;	//24 hours
			} elseif ($lablcount == 31) {
				$x_grid=6.45;
			}
			// generate url for google chart image 
			$chart_url ="http://chart.apis.google.com/chart?chf=".$chart_bg."&chtt=".urlencode($cTitle)."&chls=4,1,0|2,6,2&chco=1111dd,FF6D06&chm=B,1111dd30,0,0,0&chg={$x_grid},25,1,5&cht=lc&chs={$Res}x{$chart_height}&chd=".Gchart_data($y_hits, $y_pages, $x_label, $x_groupformat, "main", $axes_type, $chart_loc);
		}
		} //end if empty($chart_url)
			if (!empty($chart_url)) {
			//cache chart url in wassup_meta table for up to 5 minutes
			if (!empty($chart_key) && $cache_id==0) {
				$wassup_cache = array('meta_id'=>$cache_id,
					'wassup_key'=>$chart_key,
					'meta_key'=>'chart',
					'meta_value'=>attribute_escape($chart_url),
					'meta_expire'=>time()+$cache_time);
			 	if (method_exists($wpdb,'insert')) {  //WP 2.5+
					$result = $wpdb->insert($cache_table,$wassup_cache);
				}
			}
			return $chart_url;
		} else {
			return false;
		}
	} //end theChart

	//  buildSearch() added to protect against Sql injection code 
	//  in user-input parameter: "Search".  -Helene D. 2/27/09
	function buildSearch($Search,$specific_where_clause=null) {
		global $wpdb;
		$ss="";
		//create the Search portion of a MySql WHERE clause 
		if (!empty($Search)) {
			//escape chars that have special meaning in mysql 'like' [%\]
			if (function_exists('like_escape')) {	//WP 2.5+ function
                		$searchString = like_escape(trim($Search));
			} else {
				$searchString = str_replace(array("%", "_"), array("\\%", "\\_"), trim($Search));
			}
			$searchParam = mysql_real_escape_string($searchString);

			// Create the Search portion of MySQL WHERE clause
			$ss = sprintf(" AND (`ip` LIKE '%%%s%%' OR `hostname` LIKE '%%%s%%' OR `urlrequested` LIKE '%%%s%%' OR `agent` LIKE '%%%s%%' OR `referrer` LIKE '%%%s%%' OR `username` LIKE '%s%%' OR `comment_author` LIKE '%s%%')",
				$searchParam,
				$searchParam,
				$searchParam,
				$searchParam,
				$searchParam,
				$searchParam,
				$searchParam);
		} //if $Search
		if (!empty($specific_where_clause)) {
			$ss .= " ".trim($specific_where_clause);
		}
		return $ss;
	} //end function buildSearch

} //end class WassupItems

// Class to check if a previous comment with a specific IP was detected as SPAM by Akismet default plugin
class wassup_checkComment {
        //var $tablePrefix; //not used

	function isSpammer ($authorIP) {
		global $wpdb;
		$spam_comment=0;
		if (!empty($authorIP)) {
			$spam_comment = $wpdb->get_var("SELECT COUNT(comment_ID) AS spam_comment FROM ".$wpdb->prefix."comments WHERE comment_author_IP='$authorIP' AND comment_approved='spam'");
		}
		return $spam_comment;
	}
	/**
	 * new in 1.8: check for referrer spam that is also comment spam
	 */
	function isRefSpam($referrerURL) {
		global $wpdb;
		$spam_comment=0;
		if (!empty($referrerURL)) {
			$spam_comment = $wpdb->get_var("SELECT COUNT(comment_ID) AS spam_comment FROM ".$wpdb->prefix."comments WHERE comment_author_url='$referrerURL' AND comment_approved='spam'");
		}
		return $spam_comment;
	}
} //end wassup_checkComment

/** 
 * A class for wassup CURL operations.
 * @since v1.8
 */
class wcURL {
	var $data = array();

	function doRequest($method, $url, $vars) {
		if (function_exists('curl_init')) {
			$wassup_settings=get_option('wassup_settings');
			$wassup_agent = apply_filters('http_headers_useragent',"WassUp/".$wassup_settings['wassup_version']." - www.wpwp.org");
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, false); //data only
			curl_setopt($ch, CURLOPT_USERAGENT, $wassup_agent);
  			curl_setopt($ch, CURLOPT_ENCODING, "");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_LOW_SPEED_TIME, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 20);
			if (ini_get('open_basedir')=="") { //causes error
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			}
			if ($method == 'POST') {
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
			}
			$data = curl_exec($ch);
		
			$this->data = curl_getinfo($ch);
			$this->data['content'] = $data;
			$this->data['error'] = curl_error($ch);
			curl_close($ch);
			if (($this->data['error'] == '') && ($this->data['http_code'] < 400)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	} //end doRequest

	function get($url) {
		return $this->doRequest('GET', $url, 'NULL');
	}

	function post($url, $vars) {
		// vars is urlencoded string of field/value pairs, eg:field1=value1&field2=value2
		return $this->doRequest('POST', $url, $vars);
	}
	
	function getInfo($field) {
		if (isset($this->data[$field])) {
			return $this->data[$field];
		}
		else {
			return null;
		}
	}
	
	function getData() {
		return $this->data['content'];
	}
} //end class wcURL

/**
 * Retrieve data from a web service API via a url query
 * @access public
 * @param string
 * @return string
 * @since v1.8
 */
function wFetchAPIData($api_url) {
	global $wdebug_mode;

	$wassup_settings=get_option('wassup_settings');
	$wassup_agent = apply_filters('http_headers_useragent',"WassUp/".$wassup_settings['wassup_version']." - www.wpwp.org");

	$apidata="";
	//try Wordpress WP 2.7+ function 'wp_remote_get' for api results
	if (function_exists('wp_remote_get')) {
		$opts = array('user-agent'=>"$wassup_agent");
		$api_remote=wp_remote_get($api_url,$opts);
		if (!empty($api_remote['body'])) {
			$apidata = $api_remote['body'];
		} elseif (!empty($api_remote['response'])) {
			$apidata = "no data";
		}
		$api_method='wp_remote_get';	//debug
	}
	//try cURL extension to get api results
	if (empty($apidata)) {
		$curl = new wcURL;
		if ($curl->get($api_url)) {
			$apidata = $curl->getData();
		}
		$api_method='wcURL';	//debug
	} 
	// try 'file_get_contents' to get api results
	if (empty($apidata) && ini_get('allow_url_fopen')== true) {
		// context stream compatible with PHP 5.0.0+
		if (version_compare(PHP_VERSION,"5.0.0",">=")) {
			$opts = array(	'http'=>array(
    					'method'=>"GET",
					'header'=>"User-agent: ".$wassup_agent."\r\n"));
			$context = stream_context_create($opts);
			// Open file using HTTP headers set above
			$apidata = @file_get_contents($api_url, false, $context);
		} else {
			$apidata = @file_get_contents($api_url, false);
		}
		$api_method='file_get_contents';	//debug
	}
	if ($wdebug_mode) {
		echo "\n<!-- <br>API Fetch using $api_method data: "; //debug
		print_r($apidata);
		echo "-->\n";
	}
	return $apidata;

} //end function wFetchAPIData

/**
 * Convert simple JSON data into a PHP object (default) or associative 
 *   array. Emulates 'json_decode' function from PHP 5.2+ 
 * @author: Helene Duncker <http://techfromhel.com>
 * @param string,boolean
 * @return (array or object)
 * Since Wassup v1.8 2010-09-13
 */
function xjson_decode($json,$to_array=false) { 
	$x=false;
	if (!empty($json) && strpos($json,'{"')!==false) {
		$out = '$x='.str_replace(array('{','":','}'),array('array(','"=>',')'),$json);
		eval($out.';');
		if (!$to_array) $x = (object) $x;
	}
	return $x;
} //end function xjson_decode 
