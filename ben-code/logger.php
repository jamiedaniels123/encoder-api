<?php

class Logger {
	private $endpoint;
	
	function __construct($endpoint = null) {
		$this->endpoint = $endpoint;
	}
	
	function log($ident = null, $code, $line, $data) {
		if ($ident == null) {
			$ident = chop(`hostname`);
		}
		
		$message = array(
			"line" => $line,
			"data" => $data
		);
		
		$url = $this->endpoint . $ident;
		exec("curl -d \"flags=" . $code . "&message=" . str_replace('"', '\"', json_encode($message)) . "\" " . $url . " > /dev/null &");
	}
}

?>