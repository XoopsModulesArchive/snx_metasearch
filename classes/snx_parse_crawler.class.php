<?php
// ***************************************************************************//
// SnX-MetaSearch:                                                              //
// by NGUYEN DINH Quoc-Huy (alias SnAKes) (support@qmel.com)                 //
// http://www.qmel.com                                                       //
/**
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
if(!class_exists('snx_parse_crawler')) {
	class SnX_Parse_Crawler {
		var $name = "";
		var $server = "";
		var $path = "";
		var $queryString = "";
		var $send = "";
		var $recv = "";
		var $parseFunction = "";
		var $socket = null;
		var $results = array();
		var $isCached;
		var $snxCache;
		var $cacheFile;

		function SnX_Parse_Crawler($name, $myKeywords, $lang, &$prefs, &$snxCache) {
			$this->isCached = false;
			$this->snxCache = $snxCache;
			$this->cacheFile = "_{$name}_" . urlencode(htmlentities($myKeywords) . '-' . preg_replace("/[^A-Za-z]/", "", $lang));
			$cacheContent = $snxCache->read_cache($this->cacheFile, $prefs["cache_life_time"], $name, false);
			if($cacheContent) {
				$this->results = unserialize($cacheContent);
				$this->isCached = true;
			} else {
				echo "<!-- $cacheContent -->\n";
				echo "<!-- LOADING $name PARSER -->\n";
				if(!require_once("plugins/{$lang}/{$name}.php")) echo "Error including {$lang}{$name} plugins<br>";
			}
		} 

		function parse() {
			if(!$this->isCached) {
				$funct = $this->parseFunction;
				$this->results = $funct($this->recv);
				if(is_array($this->results)) {
					$this->snxCache->write_cache($this->results, $this->cacheFile, $this->name, false);
				}
			}
		} 

		function close() {
			fclose($this->socket);
		} 
	} 
} 

?>