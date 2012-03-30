<?php
if(in_array('twitter', $enabled)) {

	$t = new WPSDTwitter();

	$a = $t->getFollowers();

	$b = $t->getFollowing();
	
	$un = $t->getUsername();

	if($a > 0 && $b > 0) {

		$ratio = round($b / $a, 2);

		if($ratio >= 0.4) $mtype = 'red';
		else $mtype= 'green';

		echo '<li class="wpsd_toggle alternate"><span class="metric_label">';
		_e('Twitter F/F ratio:', 'wpsd');
		echo '</span>&nbsp;<span class="metric_' . $mtype . '">' . $ratio . '</span>';
		
		$search = new WPSDTwitterSearch("@{$un}");

		$results = $search->results();
	
		echo '<ul class="wpsd_toggle_contents">';
		
		if(null != $results && is_array($results)) {

			foreach($results as $r) {

				printf('<li class="highlight">%s - %s - %s</li>', $r->from_user, $r->created_at, $r->text);
			}		
		}

		if($ratio >= 0.4) {
			echo '<li class="highlight">';
			_e('Try to <a href="http://www.theunfollowapp.com" target="_blank" title="The unfollow app" rel="external">unfollow as many users as possible</a> to improve your twitter profile quality.', 'wpsd');
			echo '</li>';
		}
		
			echo '</ul>';
		
		echo '</li>';
	}
	else {

		echo '<li>';
		_e('It seems you do not own a <a href="http://twitter.com" target="_blank" title="Twitter">twitter.com</a> profile. Create one to improve interaction with your audience.', 'wpsd');
		echo '</li>';
	}
}
?>