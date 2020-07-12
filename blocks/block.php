<?php
include_once(XOOPS_ROOT_PATH."/modules/snx_metasearch/includes/common_functions.php");

function disp_ms_block() {
	include_once(XOOPS_ROOT_PATH."/modules/snx_metasearch/includes/prefs.php");
	global $myLang;

	$myLang = (isset($_GET['mslang']) ? preg_replace("/[^A-Za-z]/", "", $_GET['mslang']) : $prefs['default_lang']);
	
	$block = array();
	$block['title'] = "SnX MetaSearch";
	$block['content'] = "";
	
	$block['content'] .= show_header() . form($kw = "") . show_footer();

	return $block;
}

?>
