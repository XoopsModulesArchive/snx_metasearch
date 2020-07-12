<? exit;
$return = array();
$tabBuffer = explode("\n", $buffer);

foreach($tabBuffer as $buffer) {
	switch(true) {
		case preg_match("/\<a class=\"sm\" (.*?)href=\"(.*?)\" (.*?)\>(.*?)\<\/a\>(.*?)\<span class=\"lsg\"\>(.*?)$/", $buffer, $match):
			$url = urldecode(preg_replace("/^.*?\&u\=/", "", $match[2]));
			$return[$url]["titre"] = strip_tags(rtrim($match[4]));
			$return[$url]["desc"]= strip_tags(rtrim($match[5]));
		break;
	}
}

return $return;
?>