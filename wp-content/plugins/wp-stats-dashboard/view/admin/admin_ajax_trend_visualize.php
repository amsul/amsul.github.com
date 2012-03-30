<?php
/**
 * Trends with visualize jquery.
 * 
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 * @subpackage view
 */
 if(null != $set) {
 	 	
	   foreach($set as $date => $value) {
	   	
	   		$dataSet[$date] = number_format($value,0,'.','');	   	
	   }
   }
   
   $dataSet = array_reverse($dataSet, true); 
 ?>
 
 <table id="wpsd-trend-visualize" class="accessHide">
	<caption><?php _e('day(x), amount(y)'); ?></caption> 
	<thead>
		<tr>
	<?php 
	
	 foreach($dataSet as $date => $data){
           if ($firstDate == "")
              $firstDate = strtotime($date);

           $dayDiff = round((strtotime($date)-$firstDate) / (60 * 60 * 24));
			
           if ($dayDiff > $maxDay)
              $maxDay = $dayDiff;
?>		

		<th scope="col"><?php echo date("d",strtotime($date)); ?></th>
	
<?php               
   } 
?>
	</tr>
	</thead>

		
	<tbody>
		<tr>
			<!--  <th scope="row"><?php _e('Trend', 'wpsd'); ?></th> -->
			
			<?php 
	
	 foreach($dataSet as $date => $data){
           if ($firstDate == "")
              $firstDate = strtotime($date);

           $dayDiff = round((strtotime($date)-$firstDate) / (60 * 60 * 24));
			
           if ($dayDiff > $maxDay)
              $maxDay = $dayDiff;
?>		

		<td><?php echo $data; ?></td>
	
<?php               
   } 
?>		
		</tr>
  </tbody>
  </table>
  
 <style type="text/css">
 
 /*demo styles*/
table#wpsd-trend-visualize {width: 400px; height: 200px; margin-left: 50px; }
table.accessHide { position: absolute; left: -999999px; display:none; }
table#wpsd-trend-visualize td, table#wpsd-trend-visualize th {  font-size: 1.2em; padding: 2px; width: 13%; }
table#wpsd-trend-visualize th { background-color:#f4f4f4; display:none; } 
table#wpsd-trend-visualize caption { font-size: 1.2em; }

/*visualize extension styles*/
/*plugin styles*/
.visualize { border: 1px solid #888; position: relative; background: #f9f9f9; margin-left:50px; margin-bottom:40px;}
.visualize canvas { position: absolute;  }
.visualize ul,.visualize li { margin: 0; padding: 0;}

/*table title, key elements*/
.visualize .visualize-info { padding: 3px 5px; background: #fafafa; border: 1px solid #888; position: absolute; top: -10px; right: 10px; opacity: .8; }
.visualize .visualize-title { display: block; color: #333; margin-bottom: 3px;  font-size: 0.9em; }
.visualize ul.visualize-key { list-style: none;  }
.visualize ul.visualize-key li { list-style: none; float: left; margin-right: 10px; padding-left: 10px; position: relative;}
.visualize ul.visualize-key .visualize-key-color { width: 6px; height: 6px; left: 0; position: absolute; top: 50%; margin-top: -3px;  }
.visualize ul.visualize-key .visualize-key-label { color: #000; }

/*pie labels*/
.visualize-pie .visualize-labels { list-style: none; }
.visualize-pie .visualize-label-pos, .visualize-pie .visualize-label { position: absolute;  margin: 0; padding:0; }
.visualize-pie .visualize-label { display: block; color: #fff; font-weight: bold; font-size: 1em; }
.visualize-pie-outside .visualize-label { color: #000; font-weight: normal; }

/*line,bar, area labels*/
.visualize-labels-x,.visualize-labels-y { position: absolute; left: 0; top: 0; list-style: none; }
.visualize-labels-x li, .visualize-labels-y li { position: absolute; bottom: 0; }
.visualize-labels-x li span.label, .visualize-labels-y li span.label { position: absolute; color: #555;  }
.visualize-labels-x li span.line, .visualize-labels-y li span.line {  position: absolute; border: 0 solid #ccc; }
.visualize-labels-x li { height: 100%; }
.visualize-labels-x li span.label { top: 100%; margin-top: 5px; }
.visualize-labels-x li span.line { border-left-width: 1px; height: 100%; display: block; }
.visualize-labels-x li span.line { border: 0;} /*hide vertical lines on area, line, bar*/
.visualize-labels-y li { width: 100%;  }
.visualize-labels-y li span.label { right: 100%; margin-right: 5px; display: block; width: 100px; text-align: right; }
.visualize-labels-y li span.line { border-top-width: 1px; width: 100%; }
.visualize-bar .visualize-labels-x li span.label { width: 100%; text-align: center; }
 </style> 
  
 <script type="text/javascript">
 jQuery(function(){

	var pw = jQuery('#wpstatsdashboard_widget').width() - 90;
		//alert(pw);
 	jQuery('#wpsd-trend-visualize').visualize({type: 'line', width: pw + 'px', colors:['#0D84C8']});
 }); 
 </script>