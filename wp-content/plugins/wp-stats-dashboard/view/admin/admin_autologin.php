<?php
/**
 * Auto login into Wordpress.com Stats.
 * @version 0.1
 * @author dligthart <info@daveligthart.com>
 * @package wp-stats-dashboard
 */
$result = wpsd_read_cache();

switch($view) {
	case 'generalstats':

		echo '<!-- WP-Stats-Dashboard - START General Stats -->';
		// Parse General Stats
		$pattern = '<div id="generalblog".*?>(.*?)<\/div>';
		preg_match_all('/'.$pattern.'/s', $result, $matches);
		$generalStats = preg_replace('/<h3>(.*?)<\/h3>/s', '<h4>General Blog Stats</h4>', $matches[1][0]);
		echo $generalStats;
		echo '<!-- WP-Stats-Dashboard - STOP General Stats-->';

	break;

	case 'referrers':

		echo '<br/><!-- WP-Stats-Dashboard - START Referrers -->';
		// Parse Referrers
		$pattern = '<div id="referrers".*?>(.*?)<\/div>';
		preg_match_all('/'.$pattern.'/s', $result, $matches);

		$ref = preg_replace('/<h4>(.*?)<\/h4>/s', '<h5>$1</h5>', $matches[1][0]);
		$ref = preg_replace('/<h3>(.*?)<\/h3>/s', '<h4>Referrers</h4>', $ref);

		echo $ref;
		echo '<!-- WP-Stats-Dashboard - STOP Referrers-->';

	break;

	case 'postviews':

		echo '<br/><!-- WP-Stats-Dashboard - START Post views -->';
		// Parse posts views.
		$pattern = '<div id="postviews".*?>(.*?)<\/div>';
		preg_match_all('/'.$pattern.'/s', $result, $matches);

		$pv = preg_replace('/<h4>(.*?)<\/h4>/s', '<h5>$1</h5>', $matches[1][0]);
		$pv = preg_replace('/<h3>(.*?)<\/h3>/s', '<h4>Post views</h4>', $pv);

		echo $pv;
		echo '<!-- WP-Stats-Dashboard - STOP Post views-->';
	break;

	case 'clicks':

		echo '<br/><!-- WP-Stats-Dashboard - START Clicks -->';
		// Parse Clicks.
		$pattern = '<div id="clicks".*?>(.*?)<\/div>';
		preg_match_all('/'.$pattern.'/s', $result, $matches);

		$clicks = preg_replace('/<h4>(.*?)<\/h4>/s', '<h5>$1</h5>', $matches[1][0]);
		$clicks = preg_replace('/<h3>(.*?)<\/h3>/s', '<h4>Clicks</h4>', $clicks);

		echo $clicks;
		echo '<!-- WP-Stats-Dashboard - STOP Clicks -->';

	break;

	case 'searchterms':

		echo '<br/><!-- WP-Stats-Dashboard - START Search terms-->';
		// Parse Search Terms.
		$pattern = '<div id="searchterms".*?>(.*?)<\/div>';
		preg_match_all('/'.$pattern.'/s', $result, $matches);

		$st = preg_replace('/<h4>(.*?)<\/h4>/s', '<h5>$1</h5>', $matches[1][0]);
		$st = preg_replace('/<h3>(.*?)<\/h3>/s', '<h4>Search Terms</h4>', $st);

		echo $st;
		echo '<!-- WP-Stats-Dashboard - STOP Search terms -->';

	break;

	default:
	//	echo $result;
	break;
}


// Remove cookie.
if(file_exists($cookie)) {
//unlink($cookie);
}
exit;
?>