<?php
/**
 * SnX MetaSearch For Xoops v2                                           
 * by NGUYEN DINH Quoc-Huy (alias SnAKes) (support@qmel.com)                 
 * http://www.goloom.com                                                       
 * 
 * Copyright (C) 2004 NGUYEN DINH Quoc-Huy (SnAKes)
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * 
 * 
 * 
 * Copyright (C) 2004 NGUYEN DINH Quoc-Huy (SnAKes)
 * Cette bibliothèque est libre, vous pouvez la redistribuer et/ou la modifier
 * selon les termes de la Licence Publique Générale GNU Limitée publiée par la 
 * Free Software Foundation (version 2 ou bien toute autre version ultérieure 
 * choisie par vous).
 * 
 * Cette bibliothèque est distribuée car potentiellement utile, mais SANS
 * AUCUNE GARANTIE, ni explicite ni implicite, y compris les garanties de 
 * commercialisation ou d'adaptation dans un but spécifique. Reportez-vous 
 * à la Licence Publique Générale GNU Limitée pour plus de détails.
 * 
 * Vous devez avoir reçu une copie de la Licence Publique Générale GNU Limitée
 * en même temps que cette bibliothèque; si ce n'est pas le cas, écrivez à la 
 * Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston,
 * MA 02111-1307, États-Unis.
 */
include_once("../../mainfile.php");
include_once("includes/prefs.php");
include_once("classes/snx_parse_crawler.class.php");
include_once("classes/snx_cache.class.php");
include_once("includes/common_functions.php");

function array_sort($array, $key) {
	for ($i = 0; $i < sizeof($array); $i++) {
		$sort_values[$i] = $array[$i][$key];
	} 
	asort ($sort_values);
	reset ($sort_values);
	while (list ($arr_key, $arr_val) = each ($sort_values)) {
		$sorted_arr[] = $array[$arr_key];
	} 
	return $sorted_arr;
} 

function doSearch() {
	global $crawlers;
	global $start_time;
	global $prefs;
	global $search_results;
	global $PR;

	$loop = true;

	// Sending the query string
	reset($crawlers);
	while(list($crawler, $c_data) = each($crawlers)) {
		if(!$c_data->isCached && !empty($c_data->send)) {
			fwrite($c_data->socket, $c_data->send);
			stream_set_timeout($c_data->socket, 1);
		} 
	} 
	
	$runTime = time();
	$nbRead = 0;
	$countCrawlers = count($crawlers); 
	// Receiving the response
	
	while($countCrawlers && $loop) {
		reset($crawlers);
		while(list($crawler, $c_data) = each($crawlers)) {
			if(!$c_data->isCached) {
				if(is_resource($c_data->socket)) {
					$recv = fread($c_data->socket, 4096);
					$readInfo = stream_get_meta_data($c_data->socket);
					if($readInfo['timed_out']) {
						echo "<!-- {$c_data->name} timed out -->\n";
						$countCrawlers--;
						$c_data->close();
						unset($crawlers[$crawler]);
						if(time() - $runTime > 15) {
							echo "<!-- global time out -->\n";
							$loop = false;
							break;
						}
					} else {
						if(!empty($recv)) $crawlers[$crawler]->recv .= $recv;
						if(feof($c_data->socket)) {
							$countCrawlers--;
							$c_data->close();
						} 
					}
				} 
			} else {
				$countCrawlers--;
			}
		} 
		// Setting global timeout
		if(time() - $runTime > 15) {
			echo "<!-- global time out -->\n";
			$loop = false;
		}
	} 
	
	// Analysing the response and preparing to display the results
	reset($crawlers);
	flush();

	foreach($crawlers as $crawler) {
		if(!$crawler->isCached) {
			$crawler->parse();
			$points = 0;
			if(is_array($crawler->results)) {
				while(list($url, $u_data) = each($crawler->results)) {
					if(!isset($search_results["$url"])) {
						$search_results = array_merge($search_results, array($url => array("position" => 0, "pagerank" => "", "points" => 0, "url" => $url, "titre" => "", "desc" => "")));
					}
					if(empty($search_results["$url"]["titre"])) $search_results["$url"]["titre"] = $u_data["titre"];
					if(empty($search_results["$url"]["desc"])) $search_results["$url"]["desc"] = $u_data["desc"];
					$search_results["$url"]["points"] += 10 - $points;

					$points++;
				} 
			} else {
				echo "<!-- error with {$crawler->name} -->\n";
			}
		} else {
			echo "{$crawler->name}<br>";
		}
	} 
	
	reset($search_results);
	while(list($url, $u_data) = each($search_results)) {
		if($search_results["$url"]["pagerank"] == "") {
//			echo "<!-- GETTING PAGERANK $url -->\n";
//			$pr = rtrim($PR->getrank($url));
//			$pr = 0;
//			if(empty($pr)) $pr = "0";
//			$search_results["$url"]["position"] += $u_data['points'] * ($pr + 1);
//			$search_results["$url"]["pagerank"] = $pr;
			$search_results["$url"]["position"] = $u_data['points'];
		}
	} 

	arsort($search_results);

} 

function search(&$keywords) {
	global $myLang;
	global $prefs;
	global $snxCache;
	global $search_results;
	

	$cacheFile = urlencode(htmlentities($keywords) . '-' . preg_replace("/[^A-Za-z]/", "", $myLang));
	
	$content = $snxCache->read_cache($cacheFile, $prefs["cache_life_time"], "RESULTS", false);
	if($content) {
		$search_results = unserialize($content);
	}
	echo "<!-- SENDING REQUEST -->\n";
	doSearch();

	
//	} 
	echo form($keywords);
	echo show_results($search_results, $keywords);
	$snxCache->write_cache($search_results, $cacheFile, "RESULTS", false);
} 

function microtime_float() {
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
} 

/**
 * MAIN SECTION
 */
$start_time = microtime_float();

if ($xoopsConfig['startpage'] == $xoopsModule->dirname()) {
	$xoopsOption['show_rblock'] = 1;
	include(XOOPS_ROOT_PATH . "/header.php");
	if (empty($HTTP_GET_VARS['start'])) {
		make_cblock();
		echo "<br />";
	} 
} else {
	$xoopsOption['show_rblock'] = 0;
	include(XOOPS_ROOT_PATH . "/header.php");
} 

global $HTTP_POST_VARS, $HTTP_GET_VARS;

$op = (isset($HTTP_GET_VARS['op']) ? $HTTP_GET_VARS['op'] : (isset($HTTP_POST_VARS['op']) ? $HTTP_POST_VARS['op'] : ""));
$myLang = (isset($_GET['mslang']) ? preg_replace("/[^A-Za-z]/", "", $_GET['mslang']) : $prefs['default_lang']);
$snxCache = new SnX_Cache();
$search_results = array();

echo "<script language=\"javascript\">
function href_rewrite(link){ link.href='redirect.php?url=' + escape(link.href); return true; }

var myThumbShots = new Array();
var myURLs = new Array();

function thumbshots_check(image, url) {
	if(image.width < 10) {
		image.src = 'http://thumbnails.alexa.com/image_server.cgi?size=small&url=' + url;
		image.width = 111;
	}
	if(image.width > 111) {
		image.width = 111;
	}
	if(image.height > 82) {
		image.height = 82;
	}
}

function start_thumbshots_checking() {
	if(myThumbShots.length < nbOfURLs) {
		window.setTimeout(\"start_thumbshots_checking()\", 500);
	} else {
		for(i=0; i<myThumbShots.length; i++) {
			thumbshots_check(myThumbShots[i], myURLs[i]);
		}
	}
}
</script>\n";
echo show_header();

$myKeywords = urlencode(stripslashes($_GET['keywords']));

switch ($op) {
	case "search":
		if($myLang == 'local') {
			header("Location: " . XOOPS_URL . "/search.php?query=" . urlencode($_GET['keywords']) . "&action=results&submit=Recherche");
			exit;
		} 

		$crawlers = array();
		$crawlers["Google"] = new snx_parse_crawler("Google", $myKeywords, $myLang, $prefs, $snxCache);
		$crawlers["Yahoo"] = new snx_parse_crawler("Yahoo", $myKeywords, $myLang, $prefs, $snxCache);
		$crawlers["Altavista"] = new snx_parse_crawler("Altavista", $myKeywords, $myLang, $prefs, $snxCache);
		$crawlers["Lycos"] = new snx_parse_crawler("Lycos", $myKeywords, $myLang, $prefs, $snxCache);

		search($myKeywords);

		$xoopsTpl->assign('xoops_pagetitle', "Meta Recherche Internet - " . stripslashes($_GET['keywords']));
		break;

	default:
		echo form($myKeywords);
		break;
} 

echo show_footer();

include_once (XOOPS_ROOT_PATH . "/footer.php");

?>