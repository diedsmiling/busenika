<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty join_javascript outputfilter plugin
 *
 * File:     outputfilter.join_javascript.php<br>
 * Type:     outputfilter<br>
 * Name:     join_javascript<br>
 * Date:     Jan 03, 2008<br>
 * Purpose:  join togther javascript into a single file
 * Install:  Drop into the plugin directory, call
 *           <code>$smarty->load_filter('output','join_javascript');</code>
 *           from application. You should specify your cachedir below.
 * @author   Leon Chevalier <http://aciddrop.com>
 * @version  1
 * @param string
 * @param Smarty
 */
 
include_once(DIR_LIB . 'packer/class.JavaScriptPacker.php');
function smarty_outputfilter_join_javascript($source, &$smarty)
{
	// output filter called for every display() and fetch()
	// we need to process .js only on final template
	if (!stripos($source, '<head>')) {
		return $source;
	}
	
	return _joiner_js(array(
	// dir where compiled js will be saved
	'cache_dir'=> DIR_CACHE,
	
	// dir with compiled js accessible from web
	'web_cache_dir'=> Registry::get('config.cache_path'),
	
	// we search for <http_path>/file.js and replace them by <fs_path>/file.js when we search&compile js files
	'http_path' => Registry::get('config.http_path'),
	'fs_path' => DIR_ROOT,
	
	// do we need to use Dean Edwards packer?
	'pack' => true,	
	'tag' => 'script',
	'type' => 'text/javascript',
	'self_close' => false), $source);		
}

function _joiner_js($options, $source) 
{
	
	
	preg_match("!<head>.*?</head>!is", $source, $matches);	
	
	
	if (empty($matches)) {
		return $source;
	}

	// we don't need to compress files that are commented out
	$comment1 = "<!--.*?<script.*?-->";
	$matches[0] = preg_replace('/' . $comment1 . '/s', '', $matches[0]); 
	preg_match_all("!<script[^>]+text/javascript[^>]+>(</script>)?!is", $matches[0], $matches);
		

			
	$script_array = $matches[0];

	if (is_array($script_array)) {

		
		//Remove empty sources
		foreach ($script_array as $key=>$value) {
			preg_match("!src=\"(.*?)\"!is", $value, $src);
			if (!$src[1]) {
				unset($script_array[$key]);
			}
		}
		$script_array = array_values($script_array);
		//Get the cache hash
		$cache_file = md5(implode("_", $script_array));
	

		//Check if the cache file exists
		if (file_exists($options['cache_dir'] . $cache_file . ".js")) {			
			$source = _remove_scripts_js($script_array, $source);
			$source = str_replace("@@marker@@","<script type=\"" . $options['type'] . "\" src=\"" . $options['web_cache_dir'] . "$cache_file.js\"></script>", $source);
			return $source;
		}
								
			//Create file
			foreach ($script_array as $key=>$value) {
				//Get the src
				preg_match("!src=\"(.*?)\"!is", $value, $src);
				$current_src = $options['fs_path'] . str_replace($options['http_path'], '', $src[1]);			
							
				//Get the code
				if (file_exists($current_src)) {
					$contents .= file_get_contents($current_src) . "\n";				   
					if ($key == 0) { //Remove script 
						$source = str_replace($value, "@@marker@@", $source); 
					} else {
						$source = str_replace($value, "", $source);
					}
				} else {
					die("No $current_src ");
				}
			}		
		
		
		//Write to cache and display
		if ($contents) {			
			// dean edwards ogada!
			if ($options['pack']) {
				$packer = new JavaScriptPacker($contents);
				$contents = $packer->pack();
			}  
			
			if ($fp = fopen($options['cache_dir'] . $cache_file . '.js', 'wb')) {
				fwrite($fp, $contents);
				fclose($fp);
				@chmod($options['cache_dir'] . $cache_file . '.js', DEFAULT_FILE_PERMISSIONS);
				
				//Create the link to the new file
				$newfile = "<script type=\"" . $options['type'] . "\"src=\"" . $options['web_cache_dir'] . "$cache_file.js\"";
				
				if ($options['rel']) {
					$newfile .= "rel=\"" . $options['rel'] . "\"";
				}
				
				if ($options['self_close']) {
					$newfile .= " />";
				} else {
					$newfile .= "></script>";
				}
				
				
				$source = str_replace("@@marker@@", $newfile, $source);
			} 
		}
	
	}

	return $source;
}


function _remove_scripts_js($script_array, $source) 
{
	
	foreach ($script_array as $key=>$value) {	
		if ($key == 0) { //Remove script			
			$source = str_replace($value, "@@marker@@", $source); 
		} else {	
			$source = str_replace($value, "", $source);
		}
	}	

	return $source;
}


?>
