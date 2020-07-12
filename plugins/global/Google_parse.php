<? exit;
$return = array();
$buffer = ereg_replace("[\n\r]", "", $buffer);
$buffer = ereg_replace("<p", "\n<p", $buffer);
$buffer = ereg_replace("</a>", "</a>\n", $buffer);
$buffer = ereg_replace("<td", "\n<td", $buffer);
$tabBuffer = explode("\n", $buffer);
foreach($tabBuffer as $buffer) {
	switch(true) {
		case preg_match("/\<p class=g\>\<a href=\"(.*?)\"\>(.*?)\<\/a\>/", $buffer, $match):
			$url = $match[1];
			$return[$url]["titre"]= strip_tags(rtrim($match[2]));
		break;
		
		case preg_match("/\<td class=j\>\<font size=-1\>(.*?)\<font/", $buffer, $match):
			$return[$url]["desc"]= strip_tags(rtrim($match[1]));
		break;
	}
}

return $return;
?>
