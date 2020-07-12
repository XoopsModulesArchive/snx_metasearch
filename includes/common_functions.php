<?php
function toUTF8($string) {
	global $prefs;
	if(!$prefs['convert_to_utf8']) return utf8_encode($string);
	if(!(preg_match('%^(?:[\x09\x0A\x0D\x20-\x7E]|[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})*$%xs', $string) === 1)) {
		return utf8_encode($string);
	}
	return $string;
}

function form(&$keywords) {
	global $_GET;
	global $myLang;

	$return = "<center><form name=\"search\" action=\"".XOOPS_URL."/modules/snx_metasearch/index.php\" method=\"get\">\n";
	$return .= _SnX_MS_KEYWORDS . ": <input name=\"keywords\" size=\"40\" value=\"" . html_entity_decode(urldecode($keywords)) . "\"><input type=\"submit\" name=\"submit\" value=\"Rechercher\">\n";	
	$return .= "<input type=\"hidden\" name=\"op\" value=\"search\"><br>\n";
	$return .= "<input type=\"radio\" name=\"mslang\" value=\"global\"".($myLang=="global" ? " checked" : "").">"._SnX_MS_GLOBAL." <input type=\"radio\" name=\"mslang\" value=\"fr\"".($myLang=="fr" ? " checked" : "").">"._SnX_MS_FRENCH." <input type=\"radio\" name=\"mslang\" value=\"local\"".($myLang=="local" ? " checked" : "").">"._SnX_MS_LOCAL."\n";
	$return .= "</form></center>\n";	
	return $return;
}

function show_results(&$search_results, $keywords) {
	global $start_time;
	global $crawlers;
	global $prefs;
	
	$words = array();
	$keywords = urldecode($keywords);
	$words = split(" ", $keywords);
	
	$return = "<script language=\"javascript\">var nbOfURLs = ".count($search_results)."</script>\n";
	$return .= "<br><br>";
	$return .= "<table border=\"0\" width=\"80%\">\n";
	$return .= "<tr><td valign=\"top\">\n";
	$return .= "<table border=\"0\">\n";
	$return .= "<tr><td bgcolor=\"#eeeeee\" colspan=\"3\" valign=\"middle\"><div style=\"height: 20px; padding-top: 5px; padding-left: 5px;\">".sprintf(_SnX_MS_RESULTS, count($search_results), stripslashes($_GET['keywords']), round(microtime_float()-$start_time, 2)) ."</div></td></tr>\n";
	
	if(!empty($prefs['adsense_in_results'])) {
/*		$find = array('--border_color--', '--bg_color--', '--link_color--', '--url_color--', '--text_color--');
		$replace = array('FFFFFF', 'FFFFFF', 'darkgreen', '', '');
		$adsense = str_replace($find, $replace, $prefs['adsense_in_results']);*/
		$adsense = $prefs['adsense_in_results'];
		$return .= "<tr><td colspan=\"3\">$adsense</td></tr>\n";
		$return .= "<tr><td colspan=\"3\">&nbsp;</td></tr>\n";
	}
	
	while(list($url, $u_data) = each($search_results)) {
		$u_data['desc'] = toUTF8($u_data['desc']);
		$u_data['titre'] = toUTF8($u_data['titre']);
		$u_data['desc'] = rtrim($u_data['desc']);
		$u_data['titre'] = rtrim($u_data['titre']);
		$u_data['desc_hl'] = $u_data['desc'];
		$u_data['titre_hl'] = $u_data['titre'];
		
		if($prefs["hilite_keywords"]) {
			foreach($words as $word) {
				$word = ereg_replace("[\"\']", "", $word);
				$u_data['desc_hl'] = eregi_replace("($word)", "<font color=\"{$prefs['hilite_color']}\">\\1</font>", $u_data['desc_hl']);
				$u_data['titre_hl'] = eregi_replace("($word)", "<font color=\"{$prefs['hilite_color']}\">\\1</font>", $u_data['titre_hl']);
			}
		}
		$return .= "<tr>";
		if($prefs["show_thumbshots"])
			$return .= "<td rowspan=\"" . ($prefs["show_description"] ? "3" : "2") . "\"><div style=\"width: 112px; height: 82px; text-align: center; border: #bbbbbb 3px solid\"><a href=\"{$u_data['url']}\" onmousedown=\"return href_rewrite(this);\" target=\"_blank\"\"><img src=\"http://open.thumbshots.org/image.pxf?url=".urlencode($u_data['url'])."\" onLoad=\"myThumbShots[myThumbShots.length] = this; myURLs[myURLs.length]='".urlencode($u_data['url'])."';\" border=\"3\" title=\"{$u_data['titre']}\" alt=\"{$u_data['titre']}\"></a></div></td>";
		$return .= "<td colspan=\"2\" style=\"height: 15px;\"><b>Rank: </b><span title=\"Goloom Ranking ({$u_data['points']})\" style=\"cursor: default;\">GR{$u_data['position']}</span><br><a href=\"{$u_data['url']}\" onmousedown=\"return href_rewrite(this);\" target=\"_blank\" title=\"{$u_data['titre']}\"><b>{$u_data['titre_hl']}</b></a></td></tr>";
		if($prefs["show_description"])
			$return .= "<tr><td><div style=\"width: 14px\"></div></td><td align=\"justify\">{$u_data['desc_hl']}</td></tr>";
		$return .= "<tr><td ><div style=\"width: 14px\"></div></td><td><font color=\"darkgreen\">" . (strlen($u_data['url'])>$prefs["max_display_link_length"] ? substr($u_data['url'], 0, $prefs["max_display_link_length"]) . "..." : $u_data['url']) . "</font></td></tr>";
		$return .= "<tr><td colspan=\"2\"><div style=\"height: 24px;\"></div></td></tr>\n";
	}
	$return .= "</table>\n";
	$return .= "</td>";
	if(!empty($prefs['adsense'])) {
		$return .= "<td>{$prefs['adsense']}</td>";	
	}
	$return .= "</tr></table>";
	$return .= "<script language=\"javascript\">start_thumbshots_checking();</script>\n";
	return $return;
}

function show_header() {
	return "<center><img src=\"".XOOPS_URL."/modules/snx_metasearch/images/logo.jpg\" alt=\"SnX Metasearch\" title=\"SnX Metasearch\"></center>\n";
}

function show_footer() {
	return "<br><br><br><center>Powered by SnX-MetaSearch Xoops Module.<br><a href=\"http://www.goloom.com/\" title=\"Goloom Annuaire lien dur & Metamoteur\">Goloom Metamoteur et Annuaire gratuit</a></center>";
}
?>
