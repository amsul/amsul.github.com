<?php 
		if(in_array('engagement', $enabled)) { 
		
			$e = new WPSDEngagement();
			
			$eng = $e->getEngagement();
			
			$eng = str_replace('.', '', str_replace(',', '', $eng));
	
			if($eng < 100) {
			
				echo '<li class="wpsd_toggle alternate"><span class="metric_label">';
				_e('Engagement:', 'wpsd');
				echo '</span>&nbsp;<span class="metric_red">' . $eng . '</span>';			
				
				echo '<ul class="wpsd_toggle_contents">';
			
				if($eng < 10) {
					echo '<li class="highlight">';
					_e('The engagement is too low. Add a comment form to allow visitors to respond to your articles. This will improve the unique content on your website which
					gives you a better ranking in google.', 'wpsd');
					echo '</li>';
				} else {
					echo '<li class="highlight">';
					_e('The engagement is too low.. Try to make commenting on your site more user friendly. Comments will add to the unique content on your website which gives you a better
					ranking in Google.', 'wpsd');
					echo '</li>';
				}	
				
				echo '</ul>';
				echo '</li>';
			}
		}
?>