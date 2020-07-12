<?php
	// Variables initialisation
	$this->name = "Altavista";
	$this->server = "fr.altavista.com";
	$this->path = "web/results";
	$this->queryString = "itag=ody&q=%%KEYWORDS%%&kgs=1&kls=0&dis=1";
	
	// Including parsing function file
	$handle = fopen("plugins/{$lang}/{$this->name}_parse.php", "r");
	if($handle) {
		$functionContent = "";
		fgets($handle, 2048);
		while(!feof($handle)) {
			$buffer = rtrim(fgets($handle, 2048));
			$functionContent .= ($buffer != '?>' ? $buffer : "");
		}
		fclose($handle);
	} else {
		echo "Error opening {$this->name}_parse.php<br>";
		return;
	}
	
	// Creating anonymous function from the parsing function file's content
	$this->parseFunction = create_function('$buffer', $functionContent);
	
	// Creating the query string to send to the search engine
	$this->send = "GET /{$this->path}?" . preg_replace("/%%KEYWORDS%%/", $myKeywords, $this->queryString) . " HTTP/1.0\nHost: {$this->server}\n\n";
	
	// Making connection to the search engine
	$this->socket = fsockopen($this->server, 80, $errno, $errstr, 30);
	
	// Setting timeout when reading the socket in stream mode.
	stream_set_timeout($this->socket, 1);
?>