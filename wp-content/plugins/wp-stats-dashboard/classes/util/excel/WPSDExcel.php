<?php
/**
 * XLSExporter.
 * Exporting of model objects to an excel file.
 * Output: office xml file;
 *
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 * @subpackage util.excel
 */
class WPSDExcel {

	var $rows = array();
	var $rendered = false;
	var $output;
	var $use_header = false;
	var $crlf = "\r\n";
	var $mime_type = 'application/vnd.ms-excel';
	var $filename = 'report.xls';

	/**
	 * WPSDExcel function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDExcel() {

	}

	/**
	 * Render the office xml output.
	 * @access private
	 */
	function render(){
		$header = "<?xml version=\"1.0\"?> {$this->crlf}" .
		"<ss:Workbook xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"> {$this->crlf}" .
		"<ss:Worksheet ss:Name=\"Sheet1\"> {$this->crlf}" .
		"<ss:Table> {$this->crlf}";
		$footer = "</ss:Table></ss:Worksheet></ss:Workbook> {$this->crlf}";
		$output = $header;
		if(count($this->rows) > 0){
			foreach($this->rows as $row){
				if(count($row) > 0) {
					$output .= "<ss:Row>{$this->crlf}";
					foreach($row as $value){
						$value = trim($value);
						if($value == ""){
							$value = " ";
						}
						if($this->stringContains($value," ")){
							$value = "{$value}";
						}
						$output .= "<ss:Cell> {$this->crlf}";
						$output .= "<ss:Data ss:Type=\"String\">{$value}</ss:Data> {$this->crlf}";
						$output .= "</ss:Cell> {$this->crlf}";
					}
					$output .= " </ss:Row> {$this->crlf}";
				}
			}
		}
		$output .= $footer;
		$this->output = $output;
		$this->rendered = true;
	}

	/**
	 * Write XLS file.
	 * @access public
	 */
	function writeXLS($filepath){
		$handle = fopen($filepath, "w+");
		if(!$handle) return false;
		$text = utf8_encode($this->fetch());
		$text = "\xEF\xBB\xBF{$text}";
		fputs($handle, $text);
		fclose($handle);
		return true;
	}

	/**
	 * Add a row to output.
	 * @access public
	 */
	function addRow($columns = array()){
		
		if(null != $columns && is_array($columns)) {
			
			$row = array();

			foreach($columns as $column_value) {
			
				if(is_array($column_value)){
	
					if(count($column_value) > 0){
						
						$new_column_value = '';
						
						foreach($column_value as $cat){

							$new_column_value .= $cat." ";
						}
						
						$column_value = $new_column_value;
						
						settype($column_value, 'string');
					}
				}
				else {
					
					$row[] = $column_value;
				}
			}
			
			if($this->use_header && count($header_row) > 0) // add header.
				$this->rows[] = $header_row;
			
			$this->rows[] = $row;
		}
	}

	/**
	 *
	 * Add a named header row to the file.
	 * @param boolean $use optional
	 * @access public
	 */
	function useHeader($use = true){
		
		$this->use_header = $use;
	}

	/**
	 * Get number of rows.
	 * @return integer numberofrows
	 * @access public
	 */
	function getRowCount(){
		if($this->use_header)
		return count($this->rows)-1;
		else return count($this->rows);
	}

	/**
	 * Add header column.
	 * @param String $column_name
	 */
	function addHeaderColumn($column_name){

	}

	/**
	 * Add Column.
	 * @param String $value
	 */
	function addColumn($value){

	}

	/**
	 * Generate the xml and fetch the output.
	 * @return string output
	 * @access public
	 */
	function fetch(){
		if(!$this->rendered) $this->render();
		return $this->output;
	}

	/**
	 * Output file.
	 * @param String $mime_type
	 * @param String $filename
	 * @access public
	 */
	function outputFile($mime_type, $filename){

		if($mime_type == ''){
			die ("Mime type is null " . " File: " . __FILE__ . " on line: " . __LINE__);
		}

		if($filename == ''){
			die ("Filename is null " . " File: " . __FILE__ . " on line: " . __LINE__);
		}

		header("Content-type: {$mime_type}");
		header("Content-Disposition: attachment; filename={$filename}");
		ob_start();
		echo $this->fetch();
		ob_end_flush();
		exit;
	}

	/**
	 * Set mime type.
	 * @param String $mime_type
	 * @access public
	 */
	function setMimeType($mime_type){
		
		$this->mime_type = $mime_type;
	}

	/**
	 * Set filename.
	 * @param String $filename
	 * @access public
	 */
	function setFilename($filename){
		
		$this->filename = $filename;
	}

	/**
	 * Download file.
	 * @access public
	 */
	function downloadFile(){
		
		if($this->mime_type != '' && $this->filename != ''){

			$this->outputFile($this->mime_type, $this->filename);
		}
	}

	/**
	 * Test if string begins with.
	 * @param string $str
	 * @param string $sub
	 * @return boolean test
	 * @access public
	 */
	function stringBeginsWith( $str, $sub ) {
		$str = strtolower($str);
		$sub = strtolower($sub);
		if(( substr( $str, 0, strlen( $sub ) ) == $sub ))return true;
		return false;
	}

	/**
	 * Test if string ends with.
	 * @param string $str
	 * @param string $sub
	 * @return boolean test
	 * @access public
	 */
	function stringEndsWith( $str, $sub ) {
		$str = strtolower($str);
		$sub = strtolower($sub);
		if(( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub ))return true;
		return false;
	}

	/**
	 * Test if string contains.
	 * @param string $str
	 * @param string $sub
	 * @return boolean test
	 * @access public
	 */
	function stringContains($str,$sub){
		if(stristr($str, $sub) == false)return false;
		return true;
	}
}
?>