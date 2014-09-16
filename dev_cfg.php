<?php
/* CrhomePhp */


/* you face a god */

$gods = array(
	'178.168.54.138', /* dcs office */
	'178.168.64.253', /* lazarev home */	
);
if(in_array($_SERVER['REMOTE_ADDR'], $gods))
	define('GOD', true);
else
	define('GOD', false);

if(GOD){
	ini_set('display_errors', 2);	
	require DIR_LIB. 'god/god.lib.php';
	$god = new God('Kirill');
}	
	
	
		
	
?>
