<?php

	/**
	 * Original data taken from:
	 * ftp://ftp.unicode.org/Public/MAPPINGS/VENDORS/MICSFT/PC/CP855.TXT
	 * @param string $string
	 * @return string
	 */
	function charset_decode_cp855($string)
	{
		$mapping = array(
					"\x80" => "\xD1\x92",
					"\x81" => "\xD0\x82",
					"\x82" => "\xD1\x93",
					"\x83" => "\xD0\x83",
					"\x84" => "\xD1\x91",
					"\x85" => "\xD0\x81",
					"\x86" => "\xD1\x94",
					"\x87" => "\xD0\x84",
					"\x88" => "\xD1\x95",
					"\x89" => "\xD0\x85",
					"\x8A" => "\xD1\x96",
					"\x8B" => "\xD0\x86",
					"\x8C" => "\xD1\x97",
					"\x8D" => "\xD0\x87",
					"\x8E" => "\xD1\x98",
					"\x8F" => "\xD0\x88",
					"\x90" => "\xD1\x99",
					"\x91" => "\xD0\x89",
					"\x92" => "\xD1\x9A",
					"\x93" => "\xD0\x8A",
					"\x94" => "\xD1\x9B",
					"\x95" => "\xD0\x8B",
					"\x96" => "\xD1\x9C",
					"\x97" => "\xD0\x8C",
					"\x98" => "\xD1\x9E",
					"\x99" => "\xD0\x8E",
					"\x9A" => "\xD1\x9F",
					"\x9B" => "\xD0\x8F",
					"\x9C" => "\xD1\x8E",
					"\x9D" => "\xD0\xAE",
					"\x9E" => "\xD1\x8A",
					"\x9F" => "\xD0\xAA",
					"\xA0" => "\xD0\xB0",
					"\xA1" => "\xD0\x90",
					"\xA2" => "\xD0\xB1",
					"\xA3" => "\xD0\x91",
					"\xA4" => "\xD1\x86",
					"\xA5" => "\xD0\xA6",
					"\xA6" => "\xD0\xB4",
					"\xA7" => "\xD0\x94",
					"\xA8" => "\xD0\xB5",
					"\xA9" => "\xD0\x95",
					"\xAA" => "\xD1\x84",
					"\xAB" => "\xD0\xA4",
					"\xAC" => "\xD0\xB3",
					"\xAD" => "\xD0\x93",
					"\xAE" => "\xC2\xAB",
					"\xAF" => "\xC2\xBB",
					"\xB0" => "\xE2\x96\x91",
					"\xB1" => "\xE2\x96\x92",
					"\xB2" => "\xE2\x96\x93",
					"\xB3" => "\xE2\x94\x82",
					"\xB4" => "\xE2\x94\xA4",
					"\xB5" => "\xD1\x85",
					"\xB6" => "\xD0\xA5",
					"\xB7" => "\xD0\xB8",
					"\xB8" => "\xD0\x98",
					"\xB9" => "\xE2\x95\xA3",
					"\xBA" => "\xE2\x95\x91",
					"\xBB" => "\xE2\x95\x97",
					"\xBC" => "\xE2\x95\x9D",
					"\xBD" => "\xD0\xB9",
					"\xBE" => "\xD0\x99",
					"\xBF" => "\xE2\x94\x90",
					"\xC0" => "\xE2\x94\x94",
					"\xC1" => "\xE2\x94\xB4",
					"\xC2" => "\xE2\x94\xAC",
					"\xC3" => "\xE2\x94\x9C",
					"\xC4" => "\xE2\x94\x80",
					"\xC5" => "\xE2\x94\xBC",
					"\xC6" => "\xD0\xBA",
					"\xC7" => "\xD0\x9A",
					"\xC8" => "\xE2\x95\x9A",
					"\xC9" => "\xE2\x95\x94",
					"\xCA" => "\xE2\x95\xA9",
					"\xCB" => "\xE2\x95\xA6",
					"\xCC" => "\xE2\x95\xA0",
					"\xCD" => "\xE2\x95\x90",
					"\xCE" => "\xE2\x95\xAC",
					"\xCF" => "\xC2\xA4",
					"\xD0" => "\xD0\xBB",
					"\xD1" => "\xD0\x9B",
					"\xD2" => "\xD0\xBC",
					"\xD3" => "\xD0\x9C",
					"\xD4" => "\xD0\xBD",
					"\xD5" => "\xD0\x9D",
					"\xD6" => "\xD0\xBE",
					"\xD7" => "\xD0\x9E",
					"\xD8" => "\xD0\xBF",
					"\xD9" => "\xE2\x94\x98",
					"\xDA" => "\xE2\x94\x8C",
					"\xDB" => "\xE2\x96\x88",
					"\xDC" => "\xE2\x96\x84",
					"\xDD" => "\xD0\x9F",
					"\xDE" => "\xD1\x8F",
					"\xDF" => "\xE2\x96\x80",
					"\xE0" => "\xD0\xAF",
					"\xE1" => "\xD1\x80",
					"\xE2" => "\xD0\xA0",
					"\xE3" => "\xD1\x81",
					"\xE4" => "\xD0\xA1",
					"\xE5" => "\xD1\x82",
					"\xE6" => "\xD0\xA2",
					"\xE7" => "\xD1\x83",
					"\xE8" => "\xD0\xA3",
					"\xE9" => "\xD0\xB6",
					"\xEA" => "\xD0\x96",
					"\xEB" => "\xD0\xB2",
					"\xEC" => "\xD0\x92",
					"\xED" => "\xD1\x8C",
					"\xEE" => "\xD0\xAC",
					"\xEF" => "\xE2\x84\x96",
					"\xF0" => "\xC2\xAD",
					"\xF1" => "\xD1\x8B",
					"\xF2" => "\xD0\xAB",
					"\xF3" => "\xD0\xB7",
					"\xF4" => "\xD0\x97",
					"\xF5" => "\xD1\x88",
					"\xF6" => "\xD0\xA8",
					"\xF7" => "\xD1\x8D",
					"\xF8" => "\xD0\xAD",
					"\xF9" => "\xD1\x89",
					"\xFA" => "\xD0\xA9",
					"\xFB" => "\xD1\x87",
					"\xFC" => "\xD0\xA7",
					"\xFD" => "\xC2\xA7",
					"\xFE" => "\xE2\x96\xA0",
					"\xFF" => "\xC2\xA0");
		
		$outStr = '';
    	for ($i = 0, $len = strlen($string); $i < $len; $i++)
    	{
    		$outStr .= (array_key_exists($string{$i}, $mapping))?$mapping[$string{$i}]:$string{$i};
		}
		
		return $outStr;
	}

?>