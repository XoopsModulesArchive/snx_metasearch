<? exit;
$return = "";
$tabBuffer = explode("\n", $buffer);

foreach($tabBuffer as $buffer) {
	switch(true) {
		case preg_match("/\<li\>\<div\>\<a class=yschttl  href=\"(.*?)\">(.*?)\<\/a\>/", $buffer, $match):
			$url = urldecode(preg_replace("/^.*?\*\*/", "", $match[1]));
			$return[$url]["titre"] = strip_tags(rtrim($match[2]));
		break;
		
		case preg_match("/\<div class=yschabstr\>(.*?)\<\/div\>/", $buffer, $match):
			$return[$url]["desc"]= strip_tags(rtrim($match[1]));
		break;
	}
}

return $return;
?>
