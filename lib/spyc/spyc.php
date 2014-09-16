<?php
/**
   * Spyc -- A Simple PHP YAML Class
   * @version 0.3
   * @author Chris Wanstrath <chris@ozmm.org>
   * @author Vlad Andersen <vlad@oneiros.ru>
   * @link http://spyc.sourceforge.net/
   * @copyright Copyright 2005-2006 Chris Wanstrath
   * @license http://www.opensource.org/licenses/mit-license.php MIT License
   * @package Spyc
   */
/**
   * The Simple PHP YAML Class.
   *
   * This class can be used to read a YAML file and convert its contents
   * into a PHP array.  It currently supports a very limited subsection of
   * the YAML spec.
   *
   * Usage:
   * <code>
   *   $parser = new Spyc;
   *   $array  = $parser->load($file);
   * </code>
   * @package Spyc
   */
class YAML_Parser {

	function serialize($array)
	{

		if (!is_array($array)) {
			return '';
		}

		return YAML_Parser::_serialize($array);
	}

	function unserialize($line)
	{
		$line = trim($line);
		if (strlen($line) <= 1) {
			return array();
		}
		return YAML_Parser::_unserialize($line);
	}

	function _unserialize($value)
	{
		if (strpos($value, '#') !== false) {
			$value = trim(preg_replace('/#(.+)$/','',$value));
		}
		
		if (preg_match('/^("(.*)"|\'(.*)\')/', $value, $matches)) {
			$value = (string)preg_replace('/(\'\'|\\\\\')/', "'", end($matches));
			$value = preg_replace('/\\\\"/','"', $value);
		} elseif (preg_match('/^\\[(.+)\\]$/', $value, $matches)) {
			// Inline Sequence
			// Take out strings sequences and mappings
			$explode = YAML_Parser::_escape($matches[1]);

			// Propogate value array
			$value  = array();
			foreach ($explode as $v) {
				$value[] = YAML_Parser::_unserialize($v);
			}
		} elseif (strpos($value,':') !== false && !preg_match('/^{(.+)/', $value)) {
			// It's a map
			$array = explode(':',$value);
			$key   = trim($array[0]);
			array_shift($array);
			$value = trim(implode(': ',$array));
			$value = YAML_Parser::_unserialize($value);
			$value = array($key => $value);
		} elseif (preg_match("/{(.+)}$/",$value,$matches)) {
			// Inline Mapping
			// Take out strings sequences and mappings
			$explode = YAML_Parser::_escape($matches[1]);

			// Propogate value array
			$array = array();
			foreach ($explode as $v) {
				$array = $array + YAML_Parser::_unserialize($v);
			}
			$value = $array;
		} elseif (strtolower($value) == 'null' or $value == '' or $value == '~') {
			$value = null;
		} elseif (preg_match ('/^[0-9]+$/', $value)) {
			$value = (int)$value;
		/*} elseif (in_array(strtolower($value), array('true', 'on', '+', 'yes', 'y'))) {
			$value = true;
		} elseif (in_array(strtolower($value), array('false', 'off', '-', 'no', 'n'))) {
			$value = false;
			*/
		} elseif (is_numeric($value)) {
			$value = (float)$value;
		} else {
			// Just a normal string, right?
		}

		return $value;
	}

	function _serialize($array)
	{
		$result = array();
		$int = false;
		if (is_array($array)) {
			foreach ($array as $k => $v) {
				if (is_int($k)) {
					$int = false;
				}

				if (!empty($v)) {
					if (is_array($v)) {
						$v = YAML_Parser::_serialize($v);
						$result[] = ($k . ': ' . $v);
					} else {
						$result[] = ($int == true) ? $v : ($k . ': "' . $v . '"');
					}
				}
			}
		}

		return ($int ? '[' : '{') . implode(', ', $result) . ($int ? ']' : '}');
	}

	function _escape($inline)
	{
	// There's gotta be a cleaner way to do this...
	// While pure sequences seem to be nesting just fine,
	// pure mappings and mappings with sequences inside can't go very
	// deep.  This needs to be fixed.

		$saved_strings = array();

		// Check for strings
		$regex = '/(?:(")|(?:\'))((?(1)[^"]+|[^\']+))(?(1)"|\')/';
		if (preg_match_all($regex,$inline,$strings)) {
			$saved_strings = $strings[0];
			$inline = preg_replace($regex,'YAMLString',$inline);
		}
		unset($regex);

		// Check for sequences
		if (preg_match_all('/\[(.+)\]/U',$inline,$seqs)) {
			$inline = preg_replace('/\[(.+)\]/U','YAMLSeq',$inline);
			$seqs = $seqs[0];
		}

		// Check for mappings
		$maps = array();
		if (preg_match_all('/{(.+)}/U',$inline, $_data)) {
			while (strpos($inline, '{') !== false) {
				$_f = strrpos($inline, '{');
				$_l = strpos(substr($inline, $_f), '}') + $_f + 1;
				$map = substr($inline, $_f, $_l - $_f);
				if (!empty($map)) {
					array_push($maps, $map);
				}
				$inline = substr_replace($inline, 'YAMLMap', $_f, $_l - $_f);
				
			}
		}

		$explode = explode(', ',$inline);

		// Re-add the sequences
		if (!empty($seqs)) {
			$i = 0;
			foreach ($explode as $key => $value) {
				if (strpos($value,'YAMLSeq') !== false) {
					$explode[$key] = str_replace('YAMLSeq',$seqs[$i],$value);
					++$i;
				}
			}
		}

		// Re-add the mappings
		if (!empty($maps)) {
			$maps = array_reverse($maps);
			$i = 0;
			foreach ($explode as $key => $value) {
				if (strpos($value,'YAMLMap') !== false) {
					$explode[$key] = str_replace('YAMLMap',$maps[$i],$value);
					while (strpos($explode[$key], 'YAMLMap')) {
						$i++;
						$explode[$key] = str_replace('YAMLMap',$maps[$i],$explode[$key]);
					}
					++$i;
				}
			}
		}

		// Re-add the strings
		if (!empty($saved_strings)) {
			$i = 0;
			foreach ($explode as $key => $value) {
				while (strpos($value,'YAMLString') !== false) {
					$explode[$key] = preg_replace('/YAMLString/',$saved_strings[$i],$value, 1);
					++$i;
					$value = $explode[$key];
				}
			}
		}

		return $explode;
	}
}
?>