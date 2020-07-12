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
 * Cette biblioth�que est libre, vous pouvez la redistribuer et/ou la modifier
 * selon les termes de la Licence Publique G�n�rale GNU Limit�e publi�e par la 
 * Free Software Foundation (version 2 ou bien toute autre version ult�rieure 
 * choisie par vous).
 * 
 * Cette biblioth�que est distribu�e car potentiellement utile, mais SANS
 * AUCUNE GARANTIE, ni explicite ni implicite, y compris les garanties de 
 * commercialisation ou d'adaptation dans un but sp�cifique. Reportez-vous 
 * � la Licence Publique G�n�rale GNU Limit�e pour plus de d�tails.
 * 
 * Vous devez avoir re�u une copie de la Licence Publique G�n�rale GNU Limit�e
 * en m�me temps que cette biblioth�que; si ce n'est pas le cas, �crivez � la 
 * Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston,
 * MA 02111-1307, �tats-Unis.
 */
if(!class_exists('snx_cache')) {
	class SnX_Cache {

		function SnX_Cache() {
		} 

		function read_cache($cacheFile, $cache_life_time = 3600, $id = "", $uncompress = true) {
			if(empty($id)) $id = time();
			if($cache_life_time > 0 && file_exists("cache/{$cacheFile}") && (time() - filemtime("cache/{$cacheFile}")) < $cache_life_time) {
				echo "<!-- READING FROM CACHE [$id] -->\n";
				$handle = fopen("cache/{$cacheFile}", "r");
				if($handle) {
					$buffer = "";
					while(!feof($handle)) {
						$buffer .= fgets($handle, 2048);
					} 
					fclose($handle);
					if($uncompress) return gzuncompress($buffer);
						else return $buffer;
				} else {
					echo "Error opening cache file.<br>";
					return false;
				} 
			}
			return false;
		} 

		function write_cache(&$content, $cacheFile, $id = "", $compress = true) {
			echo "<!-- WRITING TO CACHE [$id] -->\n";
			$handleW = fopen("cache/{$cacheFile}", "w");
			if($handleW) {
				if($compress) fputs($handleW, gzcompress(serialize($content)));
					else fputs($handleW, serialize($content));
				fclose($handleW);
			} else {
				echo "Error creating cache file [$id]<br>";
			} 
		} 
	} 
} 

?>
