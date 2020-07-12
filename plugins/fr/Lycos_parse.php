<? exit;
$return = array();
$buffer = preg_replace("/[\n\r]/", "", $buffer);
$buffer = preg_replace("/\<li/", "\n<li", $buffer);
$buffer = preg_replace("/\<\/li\>/", "<\/li>\n", $buffer);
$tabBuffer = explode("\n", $buffer);

foreach($tabBuffer as $buffer) {
	switch(true) {
		case preg_match("/\<li\>\<a title=\"\" (.*?)href=\"(.*?)\"\>(.*?)\<\/a\>(.*?)\<\/font>\<br \/\>(.*?)\<span class=\"siteurl\"\>(.*?)$/", $buffer, $match):
			$url = urldecode(preg_replace("/^.*?\&u\=/", "", $match[2]));
			$return[$url]["titre"] = strip_tags(rtrim($match[3]));
			$return[$url]["desc"]= strip_tags(rtrim($match[5]));
		break;
	}
}

return $return;
?>