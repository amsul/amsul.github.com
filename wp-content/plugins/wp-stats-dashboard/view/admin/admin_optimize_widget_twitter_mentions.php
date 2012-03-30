<?php 
		if(in_array('twitter-search', $enabled)) { 
			
			$temp = parse_url(get_bloginfo('url'));
			
			$host = str_replace('www.', '', $temp['host']);
			
			$search = new WPSDTwitterSearch($host);
			
			$results = $search->results();
			
			$result_count = count($results);
			
			$mtype = 'green';
			if($result_count == 0) $mtype = 'red';
			
			echo '<li class="wpsd_toggle wpsd_toggle_twitter_search"><span class="metric_label">';
			
			printf('%s (%d) %s', __('Your blog has been mentioned', 'wpsd'), $result_count, __('times over the last few weeks..', 'wpsd'));
			
			echo '</span> <span class="metric_'.$mtype.'">' . $result_count . '</span>';
			
		//	printf('<span class="metric_green">%d</span>', $result_count);
			
			if(null != $results && is_array($results)) { 
				
				echo '<ul class="wpsd_toggle_contents wpsd_toggle_twitter_search_contents">';
			
				foreach($results as $r) { 
			
					printf('<li class="highlight">%s - %s - %s</li>', $r->from_user, $r->created_at, $r->text);
				}
			
				echo '</ul>';
			}
			
			echo '</li>';
		}
?>