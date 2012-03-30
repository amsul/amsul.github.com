<?php 

	if(in_array('pagerank', $enabled)) { 
		
			$pr = new WPSDPageRank();
			$rank = $pr->getPagerank();
		
			if($rank < 3) $mtype = 'red';
			else $mtype = 'green';
		
			echo '<li class="wpsd_toggle alternate"><span class="metric_label">';
			_e('Pagerank:', 'wpsd');
			echo '</span>&nbsp;<span class="metric_'.$mtype.'">' . $rank . '</span>';			
		
			echo '<ul class="wpsd_toggle_contents">';		
			if($rank < 3) {

				echo '<li class="highlight">';
				_e('Your pagerank is quite low. Have you thought about <a href="http://www.daveligthart.com/products/" target="_blank" style="font-weight:bold;" title="SEO, SMO, Performance optimization service">optimizing</a> / SEO / SMO / Performance ? This will get you a better organic ranking in google.', 'wpsd');
				echo '</li>';
			} 
			else {
			
				echo '<li class="highlight">';
				_e('Your pagerank is fine. Have you thought about <a href="http://www.daveligthart.com/products/" target="_blank" style="font-weight:bold;"  title="SEO, SMO, Performance optimization service">optimizing</a> 
				your social media profiles to get more visitors and improve interaction with your audience?', 'wpsd');
				echo '</li>';		
			}
			echo '</ul>';
			echo '</li>';
		}
?>