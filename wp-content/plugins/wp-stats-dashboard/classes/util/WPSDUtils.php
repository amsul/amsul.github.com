<?php
/**
 * WPSD Functions.
 *
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 1.7
 * @package wp-stats-dashboard
 */

/**
 * Get image by name.
 * 
 * @param $name
 * @return string html
 */
function wpsd_get_img($name, $style='') {
	
	$src = WPSD_PLUGIN_URL . '/resources/images/';
	
	$src .= "{$name}.png";
	
	if('' != $style) {

		$style = " style=\"{$style}\" ";
	}
	
	return "<img src=\"{$src}\" alt=\"{$name}\"{$style}/>";
}

/**
 * Show curl error.
 */
function wpsd_curl_error() {
?>
<div id="message" class="updated fade"><p>
<strong><?php _e('CURL extension not installed:','wpsd'); ?></strong>
&nbsp;<?php _e('you must have CURL extension enabled in your php configuration','wpsd');?>.
</p></div>
<?php 	
}

/**
 * Show writable error.
 * 
 * @param $path
 */
function wpsd_writable_error($path) {

	$activate_url = wp_nonce_url('plugins.php?action=activate&plugin=' . WPSD_FILE, 'activate-plugin_' . WPSD_FILE);

	$reactivate_button = sprintf('<input type="button" value="re-activate plugin" onclick="top.location.href = \'%s\'" />', addslashes($activate_url));

	if (file_exists($path)) {

		$error = sprintf('<strong>%s</strong> is not write-able, please run following command:<br /><strong style="color: #f00;">chmod 777 %s</strong><br />then %s.', $path, $path, $reactivate_button);
	} else {

		$error = sprintf('<strong>%s</strong> could not be created, please run following command:<br /><strong style="color: #f00;">chmod 777 %s</strong><br />then %s.', $path, dirname($path), $reactivate_button);
	}

	echo $error;

	/*if (w3_check_open_basedir($path)) {
		if (file_exists($path)) {
		$error = sprintf('<strong>%s</strong> is not write-able, please run following command:<br /><strong style="color: #f00;">chmod 777 %s</strong><br />then %s.', $path, $path, $reactivate_button);
		} else {
		$error = sprintf('<strong>%s</strong> could not be created, please run following command:<br /><strong style="color: #f00;">chmod 777 %s</strong><br />then %s.', $path, dirname($path), $reactivate_button);
		}
		} else {
		$error = sprintf('<strong>%s</strong> could not be created, <strong>open_basedir</strong> restriction in effect, please check your php.ini settings:<br /><strong style="color: #f00;">open_basedir = "%s"</strong><br />then %s.', $path, ini_get('open_basedir'), $reactivate_button);
		}

		//  w3_activate_error($error);*/
}

/**
 * Get cache path.
 *
 * @return string cache path
 */
function wpsd_get_cache_path() {

	if (!is_dir(WPSD_CACHE_PATH)) {

		if (@mkdir(WPSD_CACHE_PATH, 0755)) {

			@chmod(WPSD_CACHE_PATH, 0755);
		}
		else {

			if(!is_dir(WPSD_CACHE_PATH_ALT)) {

				if (@mkdir(WPSD_CACHE_PATH_ALT, 0755)) {

					@chmod(WPSD_CACHE_PATH_ALT, 0755);

					return WPSD_CACHE_PATH_ALT;
				}
				else {
						
					//wpsd_writable_error(WPSD_CACHE_PATH);
						
					return false;
				}
			}
			else {

				return WPSD_CACHE_PATH_ALT;
			}
		}
	} else {
		
		if(is_writable(WPSD_CACHE_PATH)) {
		
			return WPSD_CACHE_PATH;
		} 
		else {
			
			if(is_writable(WPSD_CACHE_PATH_ALT)) {
				
				return WPSD_CACHE_PATH_ALT;
				
			}
		}
	}
}

/**
 * wpsd_create_cache_files function.
 * 
 * @access public
 * @return boolean file exists
 */
function wpsd_create_cache_files() {

	$cache_path = wpsd_get_cache_path();

	if($cache_path && is_writable($cache_path)){

		$blog_id = get_option('wpsd_blog_id');

		$cookie_filename = md5($blog_id);

		if(!is_dir($cache_path . 'wp-stats-dashboard')) {
				
			@mkdir($cache_path . 'wp-stats-dashboard');
		}
			
		if(!file_exists($cache_path . 'wp-stats-dashboard/' . $cookie_filename)){
				
			@touch($cache_path . 'wp-stats-dashboard/' . $cookie_filename);
		}

		if(!file_exists($cache_path . 'wp-stats-dashboard/' . $cookie_filename . '_cache')){

			@touch($cache_path . 'wp-stats-dashboard/' . $cookie_filename . '_cache');
		}

		return file_exists($cache_path . 'wp-stats-dashboard/' . $cookie_filename . '_cache');
			
	}

	return false;
}

/**
 * Create HTML-code for a dropdown
 * containing a number of options.
 *
 * @param $name   string  The name of the select field.
 * @param $values hash    The values for the options by their names
 *                        eg. $values["value-1"] = "option label 1";
 * @param $selected  string The value of the selected option.
 * $attr Optional attributes (eg. onChange stuff)
 *
 * @return string The HTML code for a option construction.
 */
function wpsd_html_dropdown($name, $values, $selected = "", $attr = ""){
	
	foreach($values as $key => $value) {

		$options .= "\t<option ".(($key == $selected) ? "selected=\"selected\"" : "")." value=\"".$key."\">".$value."&nbsp;&nbsp;</option>\n";
	}
	
	return "<select name=\"".$name."\"  id=\"".$name."\" $attr>\n".$options."</select>\n";
}

/**
 * Get script url.
 * @return url
 * @access public
 */
function wpsd_get_settings_url() {
	
	$url = get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=wp-stats-dashboard';
	
	return $url;
}

/**
 * Write cache.
 * @param string $output output to cache
 * @access public
 */
function wpsd_write_cache($output = '') {

	$cache_dir = wpsd_get_cache_path();

	if(wpsd_create_cache_files()) {
		
		$blog_id = get_option('wpsd_blog_id');

		$cache_filename = md5($blog_id) . '_cache';
	
		$filename = $cache_dir  . 'wp-stats-dashboard/' . $cache_filename;
		
		$f = fopen($filename, 'w' );

		fwrite($f, $output); // write cache.

		fclose($f);
	}
}

/**
 * Read cache.
 * @return cache results
 */
function wpsd_read_cache() {

	$cache_dir = wpsd_get_cache_path();

	$blog_id = get_option('wpsd_blog_id');

	$cache_filename = md5($blog_id) . '_cache';

	$filename = $cache_dir  . 'wp-stats-dashboard/' . $cache_filename;

	$expire_seconds = 60;

	$contents = '';

	//echo ' ' . (time() - filemtime($filename));

	// Clear cache.
	if(file_exists($filename) && (time() - filemtime($filename)) >= $expire_seconds ){

		wpsd_clear_cache();

		$contents = wpsd_retrieve_stats(); // get new results.
	} 
	elseif(file_exists($filename)) { // Read cache.

		if(filesize($filename) > 0) {

			$handle = fopen($filename, 'r');

			$contents = fread($handle, filesize($filename));

			fclose($handle);

		} else {

			$contents = wpsd_retrieve_stats();
		}
	} 
	else { // Create new cache.

		$contents = wpsd_retrieve_stats();
	}
	return $contents;
}

/**
 * Clear cache.
 */
function wpsd_clear_cache() {

	wpsd_write_cache();
}

/**
 * Retrieve dashboard stats.
 * @return result
 * @access public
 */
function wpsd_retrieve_stats($url = '') {

	if(function_exists('curl_init')) {
				
		wpsd_login(); // Login.
		
		$blog_id = get_option('wpsd_blog_id');
		
		$cookie = wpsd_get_cookie();
		
		$write_cache = false; 
		
		if('' == $url) {
		
			$url = 'http://dashboard.wordpress.com/wp-admin/index.php?page=estats&blog=' . $blog_id . '&day=' . date('Y-m-d');
		
			$write_cache = true;
		} 
		else {
			
			$url .= '&blog=' . $blog_id;
		}
		
		// Manual redirect.
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.8) Gecko/20100727 Firefox/3.6.8");
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, get_bloginfo('wpurl') . '/wp-admin/');
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
		
		$result = curl_exec ($ch);
		
		curl_close($ch);
		
		if($write_cache) { 
		
			wpsd_write_cache($result);
		}	
	} 
	else {
		
		wpsd_curl_error();
		
	}
	
	return $result;
}

/**
 * wpsd_get_cookie function.
 * 
 * @access public
 * @return void
 */
function wpsd_get_cookie() {
	
	$blog_id = get_option('wpsd_blog_id');

	$cookie_filename = md5($blog_id);

	$cache_path = wpsd_get_cache_path();

	$cache_dir = $cache_path . 'wp-stats-dashboard';
	
	return $cache_dir . '/' . $cookie_filename;
}

/**
 * wpsd_get_cache_dir function.
 * 
 * @access public
 * @return void
 */
function wpsd_get_cache_dir() {
	
	$cache_path = wpsd_get_cache_path();

	$cache_dir = $cache_path . 'wp-stats-dashboard';
	
	return $cache_dir . '/';
}

/**
 * wpsd_get_login_postdata function.
 * 
 * @access public
 * @return void
 */
function wpsd_get_login_postdata() {
	
	$form = new WPSDAdminConfigForm();

	$un = trim($form->getWpsdUn());

	$pw = trim($form->getWpsdPw());
	
	return "log={$un}&pwd={$pw}&testcookie=1&redirect_to=wp-admin/index.php?page=stats";
}

/**
 * wpsd_get_login_url function.
 * 
 * @access public
 * @return void
 */
function wpsd_get_login_url() {
	
	return 'http://dashboard.wordpress.com/wp-login.php';
}

/**
 * WordPress.com Login.
 *
 * @return string login result data
 */
function wpsd_login() {
	
	$postdata =  wpsd_get_login_postdata();

	$cookie = wpsd_get_cookie();

	if(function_exists('curl_init')) {

		// Login.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, wpsd_get_login_url());
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.8) Gecko/20100727 Firefox/3.6.8");
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_REFERER, get_bloginfo('wpurl') . '/wp-admin/');
		curl_setopt($ch, CURLOPT_POST, 1);
		$result = curl_exec ($ch);		
		curl_close($ch);
	} 
	
	// Error.
	if('' != trim($result)) {
		
		return false;
	}
	
	return true;
}

/**
 * wpsd_get_all_time_views function.
 * 
 * @access public
 * @return integer views
 */
function wpsd_get_all_time_views() {

	wpsd_login();
	
	$blog_id = get_option('wpsd_blog_id');
	
	$graph_xml_url = "http://dashboard.wordpress.com/wp-admin/index.php?page=stats&unit=1&blog={$blog_id}";
		
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_COOKIEFILE,  wpsd_get_cookie());
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.8) Gecko/20100727 Firefox/3.6.8");
	curl_setopt($ch, CURLOPT_REFERER, get_bloginfo('wpurl') . '/wp-admin/index.php?page=stats');
	curl_setopt($ch, CURLOPT_URL, $graph_xml_url);
	
	$result = curl_exec ($ch);
	
	curl_close ($ch);
			
	preg_match('@<span>([0-9]+)</span> views all-time@', str_replace(',', '', $result), $matches);
	
	if(null != $matches[1]) { 
		
		return str_replace(',','', $matches[1]);
	}
	
	return 0;
}


/**
 * wpsd_get_chart_data function.
 * 
 * @access public
 * @return string chart data
 */
function wpsd_get_chart_data() {
	
	// Login.
	wpsd_login();
	
	$blog_id = get_option('wpsd_blog_id');
	
	$graph_xml_url = "http://dashboard.wordpress.com/wp-includes/charts/stats-data.php?blog={$blog_id}&page=stats&unit=1&width=670&height=265";

	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_COOKIEFILE,  wpsd_get_cookie());
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.8) Gecko/20100727 Firefox/3.6.8");
	curl_setopt($ch, CURLOPT_REFERER, get_bloginfo('wpurl') . '/wp-admin/index.php?page=stats');
	curl_setopt($ch, CURLOPT_URL, $graph_xml_url);
	
	$result = curl_exec ($ch);
	
	curl_close ($ch);
			
	return $result;
}

/**
 * wpsd_get_chart_xy function.
 * 
 * @access public
 * @return void
 */
function wpsd_get_chart_xy() {

	$data = wpsd_get_chart_data();

	preg_match('#x_labels=(.*?)\n#i', $data, $matches);
	
	$x_axis = explode(',', str_replace('&', '', $matches[1]));
	
	preg_match('#values=(.*?)\n#i', $data, $matches);
	
	$y_axis = explode(',', str_replace('&', '', $matches[1]));
	
	return array($x_axis, $y_axis);
}

/**
 * wpsd_blacklisted function. check dns bl.
 * 
 * @access public
 * @param mixed $ip
 * @return mixed
 */
function wpsd_is_blacklisted($ip) {
    $dnsbl_lists = array("bl.spamcop.net", "list.dsbl.org", "sbl-xbl.spamhaus.org", "dnsbl.sorbs.net", "pbl.spamhaus.org", "zen.spamhaus.org", "zombie.dnsbl.sorbs.net");
    if ($ip && preg_match('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#', $ip)) {  
        $reverse_ip = implode(".", array_reverse(explode(".", $ip))); 
        $on_win = substr(PHP_OS, 0, 3) == "WIN" ? 1 : 0;
        foreach ($dnsbl_lists as $dnsbl_list){
            if (function_exists("checkdnsrr")) {
          
                if (checkdnsrr($reverse_ip . "." . $dnsbl_list . ".", "A")) {
                    return $reverse_ip . "." . $dnsbl_list;
                } 
            } else if ($on_win == 1) {
                $lookup = "";
                @exec("nslookup -type=A " . $reverse_ip . "." . $dnsbl_list . ".", $lookup);
                foreach ($lookup as $line) {
                    if (strstr($line, $dnsbl_list)) {
                        return $reverse_ip . "." . $dnsbl_list;
                    }
                }
            } 
        }
    }
    return false;
}

/**
 * wpsd_get_user_role function.
 * 
 * @access public
 * @return string
 */
function wpsd_get_user_role() {
	global $current_user;
	
	$user_role = false;
	
	$user_roles = $current_user->roles;
	
	if(null != $user_roles && is_array($user_roles))
		$user_role = array_shift($user_roles);
	
	return $user_role;
}

/**
 * wpsd_has_access function.
 * 
 * @access public
 * @return void
 */
function wpsd_has_access() {
	
	$role_access = false;
	
	$form = new WPSDAdminConfigForm();
	$role_author = $form->getWpsdRoleAuthor();
	$role_editor = $form->getWpsdRoleEditor();
	$role_subscriber = $form->getWpsdRoleSubscriber();
	$role_contributor = $form->getWpsdRoleContributor();
	
	$user = wp_get_current_user();
	
	if(null != $user) {
	
		if($user->caps['editor'] && $role_editor) $role_access = true;
		else if($user->caps['author'] && $role_author) $role_access = true;
		else if($user->caps['contributor'] && $role_contributor) $role_access = true;
		else if($user->caps['subscriber'] && $role_subscriber) $role_access = true;
	}
	
	return $role_access; 
}

/**
 * wpsd_sanitize function.
 * 
 * @access public
 * @param mixed $data
 * @return string
 */
function wpsd_sanitize($data) {
	
	$data = trim(htmlentities(strip_tags($data)));

	if (get_magic_quotes_gpc())
		$data = stripslashes($data);

	$data = mysql_real_escape_string($data);

	return $data;
}

/** 
 * wpsd_get_post_stats function. draft function TODO: clean
 * 
 * @access public
 * @param mixed $post_id
 * @return void
 */
function wpsd_get_post_stats($post_id) {

	$blog_id = get_option('wpsd_blog_id');
		
	$url = get_bloginfo('wpurl') . "/wp-admin/admin.php?page=stats&view=post&post={$post_id}&blog={$blog_id}&noheader&chart=flot-stats-post";
		
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_COOKIEFILE,  wpsd_get_cookie());
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.8) Gecko/20100727 Firefox/3.6.8");
	curl_setopt($ch, CURLOPT_REFERER, get_bloginfo('wpurl') . '/wp-admin/index.php?page=stats');
	curl_setopt($ch, CURLOPT_URL, $url);
	
	$result = curl_exec ($ch);
	
	curl_close ($ch);
			
	return $result;
}
/**
 * wpsd_jtip function.
 * 
 * @access public
 * @param mixed $tip
 * @return void
 */
function wpsd_jtip($title, $tip) {
?>
<span class="formInfo"><a id="<?php echo md5($title . $tip); ?>" name="<?php echo $title; ?>" href="<?php echo admin_url('admin-ajax.php'); ?>?action=wpsd_jtip&width=375&jtip=<?php echo urlencode($tip); ?>" class="jTip">?</a></span>
<?php 
}

/**
 * get_wpsd_jtip function.
 * 
 * @access public
 * @param mixed $title
 * @param mixed $tip
 * @return string anchor
 */
function get_wpsd_jtip($title, $tip, $value = '?') {

	ob_start();
?>
	<a id="<?php echo md5($title . $tip); ?>" name="<?php echo $title; ?>" href="<?php echo admin_url('admin-ajax.php'); ?>?action=wpsd_jtip&width=375&jtip=<?php echo urlencode($tip); ?>" class="jTip"><?php echo $value; ?></a>
<?php 
	
	$temp = ob_get_contents();

	ob_end_clean();
	
	return $temp;
}

/**
 * wpsd_go function.
 * 
 * @access public
 * @param mixed $link
 * @param string $title. (default: '')
 * @return void
 */
function wpsd_go($link, $title = '') {
?>
	<a href="<?php echo $link; ?>" target="_blank" title="<?php echo $title; ?>"><?php echo wpsd_get_img('link-go-icon', 'vertical-align:bottom;width:24px;height:24px;'); ?></a>
<?php	
}

/**
 * wpsd_get_ip function.
 * 
 * @access public
 * @return string ip
 */
function wpsd_get_ip() {
  
  	$url = parse_url(get_bloginfo('wpurl'));
  
    $ip = gethostbyname($url['host']);
    
  	return $ip;
}

/**
 * wpsd_get_author_klout_score function.
 * 
 * @access public
 * @param mixed $user_id
 * @return integer
 */
function wpsd_get_author_klout_score($user_id) {
	
	$o = new WPSDUserMetrics($user_id);
	
	return $o->getKloutScore();
}
/**
 * wpsd_get_author_twitter_ratio function.
 * 
 * @access public
 * @param mixed $user_id
 * @return integer
 */
function wpsd_get_author_twitter_ratio($user_id) {
	
	$o = new WPSDUserMetrics($user_id);
	
	return $o->getTwitterRatio(); 
}
/**
 * wpsd_get_author_post_count function.
 * 
 * @access public
 * @param mixed $user_id
 * @return integer
 */
function wpsd_get_author_post_count($user_id) {

	$o = new WPSDUserMetrics($user_id);
	
	return $o->getPostCount();
}
/**
 * wpsd_get_author_comments_received_count function.
 * 
 * @access public
 * @param mixed $user_id
 * @return integer
 */
function wpsd_get_author_comments_received_count($user_id) {
	
	$o = new WPSDUserMetrics($user_id);
	
	return $o->getCommentsReceived();
}
/**
 * wpsd_get_author_wpsd_score function.
 * 
 * @access public
 * @param mixed $user_id
 * @return integer
 */
function wpsd_get_author_wpsd_score($user_id) {

	$o = new WPSDUserMetrics($user_id);
	
	return round($o->getWpsdScore(), 2);
}
?>