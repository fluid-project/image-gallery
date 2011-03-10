<?php
if (!defined('FLUID_IG_INCLUDE_PATH')) { exit; }

define('FLUID_IG_DEVEL', 1);

// get the protocol
if (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on')) {
	$server_protocol = 'https://';
} else {
	$server_protocol = 'http://';
}

// Calculate the base href
$dir_deep	 = substr_count(FLUID_IG_INCLUDE_PATH, '..');
$url_parts	 = explode('/', $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
$_base_href	 = array_slice($url_parts, 0, count($url_parts) - $dir_deep-1);
$_base_href	 = $server_protocol . implode('/', $_base_href).'/';

$endpos = strlen($_base_href); 

$_base_href	 = substr($_base_href, 0, $endpos);
$_base_path  = substr($_base_href, strlen($server_protocol . $_SERVER['HTTP_HOST']));

define('FLUID_IG_BASE_HREF', $_base_href);

/**
 * Get the list of all the sub-directories in the given directory
 * @access public
 * @param  string $directory       the directory to search in
 * @return an array of all the sub-directories
 */
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
* Enables the deletion of a directory even if it is not empty
* @access  public
* @param   string $directory		the directory to delete
* @return  boolean			whether the deletion was successful
*/
function remove_dir($directory) {
	if(!$opendir = @opendir($directory)) {
		return false;
	}
	
	while(($readdir=readdir($opendir)) !== false) {
		if (($readdir !== '..') && ($readdir !== '.')) {
			$readdir = trim($readdir);

			clearstatcache(); /* especially needed for Windows machines: */

			if (is_file($directory.'/'.$readdir)) {
				if(!@unlink($directory.'/'.$readdir)) {
					return false;
				}
			} else if (is_dir($directory.'/'.$readdir)) {
				/* calls itself to clear subdirectories */
				if(!remove_dir($directory.'/'.$readdir)) {
					return false;
				}
			}
		}
	} /* end while */

	@closedir($opendir);
	
	if(!@rmdir($directory)) {
		return false;
	}
	return true;
}

/**
 * Scan through the given $directory, remove the sub-folders that are older than the given seconds.
 * @access public
 * @param  string $directory         the path to the folder
 *         integer $secs_to_live     the seconds that the folder should not be deleted since its creation 
 * @return boolean          
 */
function clean_history($directory, $secs_to_live) {
	$check_point = strtotime("-".$secs_to_live." seconds"); 

	$allDirs = getAllDirs($directory);
    
	$highestKnown = 0;
	foreach ($allDirs as $one_dir) {
		$currentValue = filectime($one_dir);
		$currentMValue = filemtime($one_dir);

		if ($currentMValue > $currentValue) {
			$currentValue = $currentMValue;
		}
          
		if ($currentValue < $check_point) {
			remove_dir($one_dir);
		}
     }
     return true;
}

/**
 * Ensure the uniqueness of the file name, $file_name, in the directory $directory.
 * If the file with the same name already exists in the directory, attach suffix "-N" to the name. 
 * N is a incremented number calculated base on the existing file names.
 * For instance, if "1.jpg" already exists, return "1-1.jpg"; if "1-1.jpg" already exists, return "1-2.jpg"
 * @param string $file_name      the file name to check
 *        string $directory      the folder that the file resides
 * @return string, a unique file name
 */
function get_unique_name($file_name, $directory){
	if (file_exists($directory.$file_name)) {
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
		return get_unique_name($rtn, $directory);
	} else {
		return $file_name;
	}
}

/**
 * Return error msg with http status code 400
 * @access public
 * @param  string err_string          the error message
 */
function return_error($err_string) {
    header("HTTP/1.0 400 Bad Request");
    header("Status: 400");
    echo $err_string;
}

/**
 * Return success msg with http status code 200
 * @access public
 * @param  $success_string        the success message
 */
function return_success($success_string) {
    echo "<html><body><p>".$success_string."</p></body></html>";
}

/**
 * This function is used for printing variables for debugging.
 * @access public
 * @param  mixed $var	    The variable to output
 * @param  string $title	The name of the variable, or some mark-up identifier.
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
 * @param   mixed $var	    The variable to output
 * @param   string $log	    The location of the log file. If not provided, use the default one.
 */
function debug_to_log($var, $log='') {
	if (!defined('FLUID_IG_DEVEL') || !FLUID_IG_DEVEL) {
		return;
	}
	
	if ($log == '') $log = 'temp/debug.log';
	
	$handle = fopen($log, 'a');
	fwrite($handle, "\n\n");
	fwrite($handle, date("F j, Y, g:i a"));
	fwrite($handle, "\n");
	fwrite($handle, var_export($var,1));
	
	fclose($handle);
}
?>
