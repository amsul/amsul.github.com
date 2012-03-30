<?php 
/**
 * Trends type.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.7
 * @package wp-stats-dashboard
 * @subpackage view
 */

$trend_types = array(
	__('Pagerank', 'wpsd') => 1,
	__('Alexa', 'wpsd') => 2,
	__('Technorati', 'wpsd') => 3,
	__('Compete', 'wpsd') => 5,
	__('Mozrank', 'wpsd') => 7,
	__('Postrank', 'wpsd') => 8,
	__('Twitter Followers', 'wpsd') => 9,
	__('Engagement', 'wpsd') => 10,
	__('LinkedIn Connections', 'wpsd') => 11,
	__('Backlinks', 'wpsd') => 12,
	__('SocialGraph Incoming', 'wpsd') => 13,
	__('Bit.ly Clicks', 'wpsd') => 14,
	__('Bing link Count', 'wpsd') => 15,
	__('Klout Score', 'wpsd') => 16,
	__('FeedBurner Circulation', 'wpsd') => 17,
	__('Last.fm Friends', 'wpsd') => 18,
	__('Facebook Fans', 'wpsd') => 19,
	__('StumbleUpon Views', 'wpsd') => 21,
//	__('Daily Views', 'wpsd') => 22,
	__('Youtube Video Views', 'wpsd') => 23,
	__('Myspace Friends', 'wpsd') => 24,
//	__('WordPress Likes', 'wpsd') => 25,
	__('Runkeeper Total Distance', 'wpsd') => 26,
	__('Google Plus Followers', 'wpsd') => 27,
	__('PeerIndex', 'wpsd') => 28,
//	__('Twitter Following / Followers Ratio', 'wpsd') => 29
);
ksort($trend_types);
?>
<select name="wpsd_trends_type" style="width:100%;margin:5px 0px;" <?php if(isset($onchange)) echo $onchange; ?>>
<?php foreach($trend_types as $label => $index): ?>
	<option value="<?php echo $index; ?>"<?php if($wpsd_trends_type==$index): echo ' selected="selected"'; endif; ?>><?php echo $label; ?></option>
<?php endforeach; ?>
</select>