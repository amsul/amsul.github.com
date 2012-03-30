<?php 

if(in_array('facebook', $enabled)) { 
		
	// Facebook.
	
	$facebook = new WPSDFaceBook();
			
	$metrics = $facebook->getLikeMetrics();
			
	if(null != $metrics && is_array($metrics) && isset($metrics['like'])) {
			
		$mtype = 'green';
		
		$l = $metrics['like'];
		
		if($l < 25) $mtype = 'red';
		
		echo '<li class="wpsd_toggle wpsd_toggle_twitter_search"><span class="metric_label">';
			
		_e('Facebook Homepage Likes', 'wpsd');
			
		echo '</span> <span class="metric_'.$mtype.'">' . $l . '</span>';
			
		echo '<ul class="wpsd_toggle_contents wpsd_toggle_twitter_search_contents">';
			
		printf('<li class="highlight">%s</li>', __('The number of homepage likes is too small. Try placing a Facebook like button on your homepage or put it in a more prominent position.', 'wpsd'));		
		
		echo '</ul>';	
		
		echo '</li>';			
	}			
}
?>