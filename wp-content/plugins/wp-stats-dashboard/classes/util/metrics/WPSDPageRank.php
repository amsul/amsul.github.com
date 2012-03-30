<?php
/**
 * WPSDPageRank. Get google pagerank.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.4
 * @package wp-stats-dashboard
 */
class WPSDPageRank extends WPSDStats {

	var $address = '';

	/**
	 * WPSDPageRank.
	 */
	function WPSDPageRank() {
		
		parent::WPSDStats();
	}

	/* The following functions are from PageRank Lookup v1.1 by HM2K (http://www.hm2k.com/projects/pagerank/).  These functions were developed based on the algorithm at http://pagerank.gamesaga.net/
	 */

	//convert a string to a 32-bit integer
	function StrToNum($Str, $Check, $Magic) {
		$Int32Unit = 4294967296;  // 2^32

		$length = strlen($Str);
		for ($i = 0; $i < $length; $i++) {
			$Check *= $Magic;
			/* If the float is beyond the boundaries of integer (usually +/- 2.15e+9 = 2^31),
			 the result of converting to integer is undefined
			 refer to http://www.php.net/manual/en/language.types.integer.php */
			if ($Check >= $Int32Unit) {
				$Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit));
				$Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check;
			}
			$Check += ord($Str{$i});
		}
		return $Check;
	}

	//genearate a hash for a url
	function HashURL($String) {
		$Check1 = $this->StrToNum($String, 0x1505, 0x21);
		$Check2 = $this->StrToNum($String, 0, 0x1003F);

		$Check1 >>= 2;
		$Check1 = (($Check1 >> 4) & 0x3FFFFC0 ) | ($Check1 & 0x3F);
		$Check1 = (($Check1 >> 4) & 0x3FFC00 ) | ($Check1 & 0x3FF);
		$Check1 = (($Check1 >> 4) & 0x3C000 ) | ($Check1 & 0x3FFF);

		$T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) <<2 ) | ($Check2 & 0xF0F );
		$T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000 );

		return ($T1 | $T2);
	}

	//genearate a checksum for the hash string
	function CheckHash($Hashnum) {
		$CheckByte = 0;
		$Flag = 0;

		$HashStr = sprintf('%u', $Hashnum) ;
		$length = strlen($HashStr);

		for ($i = $length - 1;  $i >= 0;  $i --) {
			$Re = $HashStr{$i};
			if (1 === ($Flag % 2)) {
				$Re += $Re;
				$Re = (int)($Re / 10) + ($Re % 10);
			}
			$CheckByte += $Re;
			$Flag ++;
		}

		$CheckByte %= 10;
		if (0 !== $CheckByte) {
			$CheckByte = 10 - $CheckByte;
			if (1 === ($Flag % 2) ) {
				if (1 === ($CheckByte % 2)) {
					$CheckByte += 9;
				}
				$CheckByte >>= 1;
			}
		}

		return '7'.$CheckByte.$HashStr;
	}

	/**
	 * Get pagerank.
	 * @param string $url
	 * @return pagerank
	 * @access public
	 */
	function getPageRank() {
		
		if($this->isOutdated()) {
		
			$domain = $this->getNormalizedUrl(get_bloginfo('url'));
			
			if('' != $domain) {
				
				$this->set_cache('pr', $this->getpr($domain));
			}
		} 
		
		return $this->get_cache('pr');
	}

	/**
	 * @param $url
	 * @return unknown_type
	 */
	function getch($url) {
		return $this->CheckHash($this->HashURL($url));
	}

	/**
	 * @param $url
	 * @return unknown_type
	 */
	function getpr($url) {
		$googlehost='toolbarqueries.google.com';
		$googleua='Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.6) Gecko/20060728 Firefox/1.5';
		$ch = $this->getch($url);
		$fp = @fsockopen($googlehost, 80, $errno, $errstr, 30);
		if ($fp) {
			$out = "GET /tbr?client=navclient-auto&ch=$ch&features=Rank&q=info:{$url} HTTP/1.1\r\n";
			$out .= "User-Agent: {$googleua}\r\n";
			$out .= "Host: {$googlehost}\r\n";
			$out .= "Connection: Close\r\n\r\n";

			fwrite($fp, $out);
			
			while (!feof($fp)) {
				$data = fgets($fp, 128);
				$pos = strpos($data, "Rank_");
				if($pos === false){} else{
					$pr=substr($data, $pos + 9);
					$pr=trim($pr);
					$pr=str_replace("\n",'',$pr);
					return $pr;
				}
			}
			fclose($fp);
		}
	}
}
?>