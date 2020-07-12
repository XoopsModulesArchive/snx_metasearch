<? exit;
$return = "";
$buffer = preg_replace("/[\n\r]/", "", $buffer);
$buffer = preg_replace("/\<a/", "\n<a", $buffer);
$buffer = preg_replace("/\<\/a\>/", "</a>\n", $buffer);
$tabBuffer = explode("\n", $buffer);

foreach($tabBuffer as $buffer) {
	switch(true) {
		case preg_match("/\<a class=\'res\' href=\'(.*?)\'>(.*?)\<\/a\>/", $buffer, $match):
			$url = urldecode(preg_replace("/^.*?\*\*/", "", $match[1]));
			$return[$url]["titre"] = strip_tags(rtrim($match[2]));
		break;
		
		case preg_match("/\<br\>\<span class=s\>(.*?)\<\/span\>/", $buffer, $match):
			$return[$url]["desc"]= strip_tags(rtrim($match[1]));
		break;
	}
}

return $return;
?>