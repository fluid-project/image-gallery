<?php
if (!defined('FLUID_IG_INCLUDE_PATH')) { exit; }

define('FLUID_IG_DEVEL', 1);

/* get the base url	*/
if (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on')) {
	$server_protocol = 'https://';
} else {
	$server_protocol = 'http://';
}

$dir_deep	 = substr_count(FLUID_IG_INCLUDE_PATH, '..');
$url_parts	 = explode('/', $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
$_base_href	 = array_slice($url_parts, 0, count($url_parts) - $dir_deep-1);
$_base_href	 = $server_protocol . implode('/', $_base_href).'/';

//if (($temp = strpos($_base_href, AT_PRETTY_URL_HANDLER)) > 0){
//	$endpos = $temp;
//} else {
	$endpos = strlen($_base_href); 

//}
$_base_href	 = substr($_base_href, 0, $endpos);
$_base_path  = substr($_base_href, strlen($server_protocol . $_SERVER['HTTP_HOST']));

define('FLUID_IG_BASE_HREF', $_base_href);

function getAllDirs($directory) {
	$result = array();
	$handle =  opendir($directory);
	while ($datei = readdir($handle))
	{
		if (($datei != '.') && ($datei != '..'))
		{
			$file = $directory.$datei;
			if (is_dir($file)) {
				$result[] = $file;
			}
		}
	}
	closedir($handle);
	return $result;
}

/**
* Enables deletion of directory if not empty
* @access  public
* @param   string $dir		the directory to delete
* @return  boolean			whether the deletion was successful
*/
function remove_dir($dir) {
	if(!$opendir = @opendir($dir)) {
		return false;
	}
	
	while(($readdir=readdir($opendir)) !== false) {
		if (($readdir !== '..') && ($readdir !== '.')) {
			$readdir = trim($readdir);

			clearstatcache(); /* especially needed for Windows machines: */

			if (is_file($dir.'/'.$readdir)) {
				if(!@unlink($dir.'/'.$readdir)) {
					return false;
				}
			} else if (is_dir($dir.'/'.$readdir)) {
				/* calls itself to clear subdirectories */
				if(!remove_dir($dir.'/'.$readdir)) {
					return false;
				}
			}
		}
	} /* end while */

	@closedir($opendir);
	
	if(!@rmdir($dir)) {
		return false;
	}
	return true;
}

/**
 * Scan through the given $folder, remove the sub-folders that are older than the given seconds.
 * @param $folder - string, the path to the folder
 *        $secs_to_alive  integer, seconds to keep the subfolder alive
 */
function clean_history($folder, $secs_to_alive) {
	$check_point = strtotime("-".$secs_to_alive." seconds"); 

	$allDirs = getAllDirs($folder);
    
	$highestKnown = 0;
	foreach ($allDirs as $dir) {
		$currentValue = filectime($dir);
		$currentMValue = filemtime($dir);

		if ($currentMValue > $currentValue) {
			$currentValue = $currentMValue;
		}
          
		if ($currentValue < $check_point) {
			remove_dir($dir);
		}
     }
     return true;
}

/**
 * Ensure the uniqueness of the file name ($file_name) in folder ($folder).
 * If the file with the same name already exists in the folder, attach suffix "-N" to the name.
 * For instance, if "1.jpg" already exists, return "1-1.jpg"; if "1-1.jpg" already exists, return "1-2.jpg"
 * @param $file_name: the file name to check
 *        $folder: the folder that the file resides
 * @return a unique file name
 */
function get_unique_name($file_name, $folder){
	if (file_exists($folder.$file_name)) {
		$prefix = substr($file_name, 0, strrpos($file_name, '.'));
		$extension = substr($file_name, strrpos($file_name, '.') + 1);
		
		$pos_of_dash = strrpos($prefix, '-');
		
		if ($pos_of_dash > 0) {
			// The renamed file with the counter already exists. Increment the counter in the file name by 1
			$str_before_dash = substr($prefix, 0, $pos_of_dash);
			$int_after_dash = intval(substr($prefix, $pos_of_dash + 1));
			$rtn = $str_before_dash.'-'.($int_after_dash+1).'.'.$extension;
		} else {
			// The first renamed file
			$rtn = $prefix.'-1.'.$extension;
		}
		return $rtn;
	} else {
		return $file_name;
	}
}

/**
 * Return error msg with http status code 400
 */
function return_error($err_string) {
    header("HTTP/1.0 400 Bad Request");
    header("Status: 400");
    echo "<html><body><p>".$errString."</p></body></html>";
}

/**
 * Return success msg with http status code 200
 */
function return_success($success_string) {
    echo "<html><body><p>".$success_string."</p></body></html>";
}

/**
 * This function is used for printing variables for debugging.
 * @access  public
 * @param   mixed $var	The variable to output
 * @param   string $title	The name of the variable, or some mark-up identifier.
 * @author  Joel Kronenberg
 */
function debug($var, $title='') {
	if (!defined('FLUID_IG_DEVEL') || !FLUID_IG_DEVEL) {
		return;
	}
	
	echo '<pre style="border: 1px black solid; padding: 0px; margin: 10px;" title="debugging box">';
	if ($title) {
		echo '<h4>'.$title.'</h4>';
	}
	
	ob_start();
	print_r($var);
	$str = ob_get_contents();
	ob_end_clean();

	$str = str_replace('<', '&lt;', $str);

	$str = str_replace('[', '<span style="color: red; font-weight: bold;">[', $str);
	$str = str_replace(']', ']</span>', $str);
	$str = str_replace('=>', '<span style="color: blue; font-weight: bold;">=></span>', $str);
	$str = str_replace('Array', '<span style="color: purple; font-weight: bold;">Array</span>', $str);
	echo $str;
	echo '</pre>';
}

/**
 * This function is used for printing variables into log file for debugging.
 * @access  public
 * @param   mixed $var	The variable to output
 * @param   string $log	The location of the log file. If not provided, use the default one.
 * @author  Cindy Qi Li
 */
function debug_to_log($var, $log='') {
	if (!defined('FLUID_IG_DEVEL') || !FLUID_IG_DEVEL) {
		return;
	}
	
	if ($log == '') $log = FLUID_IG_TEMP_DIR. 'debug.log';
	
	$handle = fopen($log, 'a');
	fwrite($handle, "\n\n");
	fwrite($handle, date("F j, Y, g:i a"));
	fwrite($handle, "\n");
	fwrite($handle, var_export($var,1));
	
	fclose($handle);
}
?>
