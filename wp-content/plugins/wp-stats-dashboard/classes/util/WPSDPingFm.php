<?php
/**
 * WPSDPingFm class.
 *
 * @author dligthart <info@daveligthart.com>
 * @package wp-stats-dashboard
 * @subpackage util
 */
class WPSDPingFm
{
	/**
	 * api_key. Default static.
	 *
	 * (default value: '')
	 *
	 * @var string
	 * @access public
	 */
	var $api_key = ''; // TODO: need app key.

	/**
	 * app_key. Provided by user.
	 *
	 * (default value: '')
	 *
	 * @var string
	 * @access public
	 */
	var $app_key = '';

	/**
	 * WPSDPingFm function.
	 *
	 * @access public
	 * @return void
	 */
	function WPSDPingFm($app_key)
	{
		$this->app_key = $app_key;
	}

	/**
	 * callMethod function.
	 *
	 * @access public
	 * @param mixed   $service
	 * @param array   $fields. (default: array())
	 * @return void
	 */
	function callMethod($service, $fields = array())
	{
		if (!isset($this->app_key)) {

			return array('status' => FALSE);
		}

		$ch = curl_init();

		curl_setopt_array($ch, array(
				CURLOPT_CONNECTTIMEOUT => 2,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_POST => TRUE,
				CURLOPT_USERAGENT => 'pingfm 0.1',
			));

		curl_setopt_array($ch, array(
				CURLOPT_POSTFIELDS => $fields + array('user_app_key' => $this->user_app_key, 'api_key' => $this->api_key, 'debug' => (int)$this->debug),
				CURLOPT_URL => 'http://api.ping.fm/v1/'. $service,
			));

		$xml = curl_exec($ch);

		curl_close($ch);

		//$xml = simplexml_load_string($rawXML);

		// Check the status.
		//$status = ($xml['status'] == 'OK');

		return array('status' => $status, 'response' => $xml);
	}

	/**
	 * validate function.
	 *
	 * @access public
	 * @return void
	 */
	function validate()
	{
		$validates = $this->callMethod('user.validate');

		return $validates['status'];
	}
	
	/**
	 * postStatus function.
	 * 
	 * @access public
	 * @param mixed $body
	 * @param mixed $title. (default: NULL)
	 * @param mixed $services. (default: NULL)
	 * @return void
	 */
	function postStatus($body, $title = NULL, $services = NULL)
	{	
		$post_method = 'status';
	
		$fields = array('post_method' => $post_method, 'body' => $body);
		
		if ($title) {
			
			$fields['title'] = $title;
		}
		
		if ($services) {
			
			$fields['service'] = implode(',', $services);
		}
		
		$response = $this->callMethod('user.post', $fields);
		
		return $response['status'];
	}
}
?>