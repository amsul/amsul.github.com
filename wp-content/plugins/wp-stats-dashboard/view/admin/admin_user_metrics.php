<?php 
/**
 * User Metrics View.
 *
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
?>

		<td align="center">
			<?php echo $rank; ?>
		</td>
		<td align="center">
			<strong>
			<?php echo round($metrics->getWpsdScore(),2); ?>
			</strong>
		</td>
		
		<td align="center">
			<?php echo $author; ?>
		</td>

	    <td align="center">  
	        <span class="value"><?php echo $metrics->getPostCount(); ?></span>
	 	 </td>  

	    <td align="center">
	        <span class="value"><?php echo $metrics->getCommentsReceived(); ?></span>
	    </td>

	   	<td align="center"> 
	        <span class="value"><?php echo $metrics->getCommentsPlaced(); ?></span>
	    </td>
	    
	    <td align="center">
	        <span class="value"><?php echo $metrics->getKloutScore(); ?></span>
	    </td>
	    
	    <td align="center">
	    	<span class="value"><?php echo $metrics->getTwitterRatio(); ?></span>
	    </td align="center">
