<?php

// PHP5 usually has bundled support for JSON encoding/decoding
// but if it's not the case, we'll use Services_JSON from PEAR

function fixtime(&$rec) {
	if (isset($rec["event_timetill"])) {
		$tt=$rec["event_timetill"];
		$ttime=fromsql($tt);
		$h=intval(date("H",$ttime));
		$m=intval(date("i",$ttime));
		$s=intval(date("s",$ttime));
		if (($h+$m+$s)==0) {
			$ltime=$ttime-86400;
			$rec["event_timetill"]=date("Y-m-d",$ltime)." 24:00:00";
		}
	}
}

function todt($str,$adj=false,$tz=0) {
	$tstamp=$tz+mktime(0,0,0,substr($str,4,2),substr($str,6,2),substr($str,0,4))+($adj?86399:0);
	return(date("Y-m-d H:i:s",$tstamp));
}

function fromsql($str) {
	$t=mktime(substr($str,11,2),substr($str,14,2),substr($str,17,2),substr($str,5,2),substr($str,8,2),substr($str,0,4));
	return($t);
}

function tosql($t){
	$str = date("Y-m-d H:i:s", $t);
	return ($str);
}
function addsql($date,$mins) {
	return (tosql(fromsql($date)+$mins));
}

/**
 * @param array $array
 * @param int[optional] $deep
 * @return string
 */
function dumpArray(&$array, $deep = 1)
{
	if (!is_array($array)) 
	{
		return (string) '"'.$array.'"';
	}
	
	$crlf = "\r\n";
	$tab = str_repeat("\t", $deep);
	$ctab = str_repeat("\t", $deep - 1); 
	$out = 'array() '.$crlf.$ctab.'{'.$crlf;
	foreach ($array as $key => $value)
	{
		$out .= is_string($key) ? $tab.'"'.$key.'" = ' : $tab.$key.' = ';
		if (is_array($value))
		{
			$out .= dumpArray($value, $deep + 1).$crlf;
		}
		else
		{
			$out .= is_string($value) ? '"'.$value.'"'.$crlf : $value.$crlf;
		}
	}
	
	return $out.$ctab.'}';
}

/**
 * @return string
 */
function dumpGet()
{
	$_GET = isset($_GET) ? $_GET : array();
	return '$_GET = '.dumpArray($_GET);
}

if (!function_exists("json_encode")) {
	require_once("json.php");
	function json_encode($val) {
		$json = new Services_JSON();
		return ($json->encode($val));
	}
	function json_decode($val) {
		$json = new Services_JSON();
		return ($json->decode($val));
	}
}

// This is to solve the problem of transforming unicode values
// escape'd in JavaScript back to normal utf-8 notation
// @ http://ru2.php.net/manual/en/function.urldecode.php
function decode_url($str)
{
	$res = '';

	$i = 0;
	$max = strlen($str) - 6;
	while ($i <= $max)
	{
		$character = $str[$i];
		if ($character == '%' && $str[$i + 1] == 'u')
		{
			$value = hexdec(substr($str, $i + 2, 4));
			$i += 6;

			if ($value < 0x0080) // 1 byte: 0xxxxxxx
				$character = chr($value);
			else if ($value < 0x0800) // 2 bytes: 110xxxxx 10xxxxxx
				$character =
	            chr((($value & 0x07c0) >> 6) | 0xc0)
    	      . chr(($value & 0x3f) | 0x80);
			else // 3 bytes: 1110xxxx 10xxxxxx 10xxxxxx
	        $character =
    	        chr((($value & 0xf000) >> 12) | 0xe0)
        	  . chr((($value & 0x0fc0) >> 6) | 0x80)
	          . chr(($value & 0x3f) | 0x80);
		}
		else
		$i++;
		
		$res .= $character;
	}

	return $res . substr($str, $i);
}
?>