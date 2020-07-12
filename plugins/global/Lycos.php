<?php
	// Variables initialisation
	$this->name = "Lycos";
	$this->server = "search.lycos.com";
	$this->path = "default.asp";
	$this->queryString = "loc=searchbox&tab=web&adf=&query=%%KEYWORDS%%&submit.x=0&submit.y=0&submit=Search";
	
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

	// Creating anonymous parsing function from the parsing function file's content
	$this->parseFunction = create_function('$buffer', $functionContent);
	
	// Creating the query string to send to the search engine
	$this->send = "GET /{$this->path}?" . preg_replace("/%%KEYWORDS%%/", $myKeywords, $this->queryString) . " HTTP/1.0\nHost: {$this->server}\nUser-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7) Gecko/20040626 Firefox/0.8\n\n";
	
	// Making connection to the search engine
	$this->socket = fsockopen($this->server, 80, $errno, $errstr, 30);
	
	// Setting timeout when reading the socket in stream mode.
	stream_set_timeout($this->socket, 1);
?>