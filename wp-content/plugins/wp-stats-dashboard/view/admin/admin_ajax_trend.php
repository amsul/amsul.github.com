<?php 
/**
 * Trends with google charts.
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
   
   $firstDate = "";
   $googleChartDataY = "";
   $googleChartDataX = "";
   $maxDay = 0;

   foreach($dataSet as $date => $data){
           if ($firstDate == "")
              $firstDate = strtotime($date);

           $dayDiff = round((strtotime($date)-$firstDate) / (60 * 60 * 24));
			
           if ($dayDiff > $maxDay)
              $maxDay = $dayDiff;

           $googleChartDataX .= $dayDiff . ",";
           $googleChartDataY .= $data. ",";
           $googleChartLabsX .= "|". date("d",strtotime($date));
   }
	   
   $axisScale    = "&chds=".min($dataSet).",".max($dataSet);
   
   if(min($dataSet) == max($dataSet)) {
   	
   		$axisScale    = "&chds=".(min($dataSet)-1).",".(max($dataSet)+1);
   }
   
   $axisLabels   = "&chxt=x,y,x,y&chxl=0:{$googleChartLabsX}|1:|".min($dataSet)."|".(((min($dataSet)+max($dataSet))/2)) ."|".(max($dataSet))."|2:||{$label}||3:|||";
   $dataLabels   = "&chm=D,21759B,0,-1,1|o,000000,0,-1,6";
   $dataPoints   = "&chd=t:".substr($googleChartDataY,0,-1);
?>
<img src="http://chart.apis.google.com/chart?<?php 	
										// Generate chart.
										echo "cht=lc";
										echo "&chs=350x150";
										echo '&chco=999999';
										//echo '&chxr=0,0,10|0,0,' . max($dataSet);
										echo $axisScale;
										echo $dataPoints;
										echo $axisLabels;
										echo $dataLabels;
										echo '&chxs=0,999999,10,0,lt,1,999999,10,1,lt';
										echo '&chg=20,50,1,5';
										?>" alt="social metrics graph" />