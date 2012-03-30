<?php
/**
 * WPSDCurl.
 * @author Dave Ligthart
 * @version 0.1
 * @license http://www.gnu.org/licenses/gpl.html GPL
 */

 /**
  	This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class WPSDCurl {

	/** @var String http code. */
	var $httpCode;
	/** @var String last error. */
  	var $lastError;
  	/** @var String last req. */
 	var $lastReq;
 	/** @var object ch. */
  	var $ch  = NULL;
  	/** @var String url. */
  	var $url = NULL;
  	/** @var String xml. */
  	var $xml = NULL;
  	/** @var String out. */
  	var $out = NULL;
  	/** @var String content. */
  	var $content = NULL;
  	/** @var boolean logging. */
  	var $logging = TRUE;
  	/** @var object log. */
  	var $log = NULL;

	/**
   	 * DLMenuCurl constructor.
   	 * @param 	String	$url
     * @param	String	$user
     * @param	String	$password
     * @access public
     */
	function WPSDCurl($url, $user = NULL, $password = NULL) {
	    if( !function_exists('curl_init') )  {
	    	echo 'PHP ERROR: Libcurl not installed...';
	    }

	    $this->url = $url;
	    $this->ch  = curl_init();

		/**
		 * Init curl.
		 */
	    curl_setopt($this->ch, CURLOPT_URL,            $this->url);
	    curl_setopt($this->ch, CURLOPT_TIMEOUT,        60);
	    curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, TRUE);
	    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($this->ch, CURLOPT_HTTPHEADER,     array('Content-type: app/xml'));
	    curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, TRUE);

		/**
		 * Check for http user, pw.
		 */
	    if( ('' != $user) && ('' != $password) ) {
	    	curl_setopt($this->ch, CURLOPT_USERPWD, "{$user}:{$password}");
	    }
  	}

  	/**
  	 * Do http post.
  	 * @param	String	$xml
  	 * @param	Array	$header
  	 * @return	String	output.
  	 * @access public
  	 */
	function http_post($xml, $headers = NULL) {
	    $this->xml = $xml;

	    curl_setopt($this->ch, CURLOPT_POST, TRUE);
	    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $xml);

	    if ($headers) {
	    	curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
	    }

	    $this->out       = curl_exec($this->ch);
	    $this->httpCode  = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
	    $this->lastError = curl_error($this->ch);
	    $this->lastReq   = curl_getinfo($this->ch);

	    curl_close($this->ch);

	    unset($this->ch);

	    return $this->out;
  	}

	/**
	 * Do Http put.
	 * @param	String	$xml
	 * @param	Array	$headers
	 * @return	String	output.
	 * @access public
	 */
	function http_put($xml, $headers = NULL) {
   		$this->xml = $xml;

   	 	$putData   = tmpfile();
		fwrite($putData, $xml);
   		fseek($putData, 0);
    	curl_setopt($this->ch, CURLOPT_PUT, TRUE );

		if ($headers) {
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		}

	    curl_setopt($this->ch, CURLOPT_INFILE, $putData);
	    curl_setopt($this->ch, CURLOPT_INFILESIZE, strlen($xml));

	    $this->out       = curl_exec(    $this->ch );
	    $this->httpCode  = curl_getinfo( $this->ch, CURLINFO_HTTP_CODE);
	    $this->lastError = curl_error(   $this->ch );
	    $this->lastReq   = curl_getinfo( $this->ch );
	    $this->content   = curl_getinfo( $this->ch, CURLINFO_CONTENT_TYPE);

		curl_close($this->ch);

    	unset($this->ch);

    	return $this->out;
  	}

	/**
	 * Do Http get.
	 * @param	Array	$headers
	 * @return	String	output.
	 * @access public
	 */
	function http_get($headers = NULL) {
   		$this->xml = "";

    	curl_setopt($this->ch, CURLOPT_HTTPGET, true);
		if ($headers) {
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		}

    	$this->out       = curl_exec($this->ch);
   	 	$this->httpCode  = curl_getinfo( $this->ch, CURLINFO_HTTP_CODE);
    	$this->lastError = curl_error($this->ch);
    	$this->lastReq   = curl_getinfo($this->ch);

		curl_close($this->ch);

    	unset($this->ch);

   		return $this->out;
  	}

	/**
	 * Do Http delete.
	 * @param	Array	$headers
	 * @return	String	output.
	 * @access public
	 */
	function http_delete($headers = NULL) {
    	$this->xml = NULL;

	    curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

		if ($headers) {
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		}

    	$this->out       = curl_exec(    $this->ch );
    	$this->httpCode  = curl_getinfo( $this->ch, CURLINFO_HTTP_CODE);
    	$this->lastError = curl_error(   $this->ch );
    	$this->lastReq   = curl_getinfo( $this->ch );

    	curl_close($this->ch);

   	 	unset($this->ch);

    	return $this->out;
  	}
}
?>