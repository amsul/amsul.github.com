<?php
/* try {

	$u = $_POST['wpsd_google_un'];
	$p = $_POST['wpsd_google_pw'];

	$ss = new Google_Spreadsheet($u,$p);
	$ss->useSpreadsheet($_POST['wpsd_export_google_ss_name']);

	// if not setting worksheet, "Sheet1" is assumed
	// $ss->useWorksheet("worksheetName");
		
	// Update stats.
	for($i=1; $i<=$factory->last; $i++) {

		$rows = $dao->getStats($i);

		if(null != $rows) {

			foreach($rows as $r) {

				if(null != $r) {

					$ss->addRow(array('date'=>$r->wpsd_trends_date, 'type'=>$factory->getStatsType($i), 'number'=>$r->wpsd_trends_stats));
				}
			}
		}
	}
	//if ($ss->addRow($row)) echo "Form data successfully stored using Google Spreadsheet";
	//else echo "Error, unable to store spreadsheet data";

} catch(Exception $e) {

	$this->render_error( $e->getMessage());
		
} */
?>