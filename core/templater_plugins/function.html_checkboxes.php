<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {html_checkboxes} function plugin
 *
 * File:       function.html_checkboxes.php<br>
 * Type:       function<br>
 * Name:       html_checkboxes<br>
 * Date:       24.Feb.2003<br>
 * Purpose:    Prints out a list of checkbox input types<br>
 * Input:<br>
 *           - name       (optional) - string default "checkbox"
 *           - values     (required) - array
 *           - options    (optional) - associative array
 *           - checked    (optional) - array default not set
 *           - separator  (optional) - ie <br> or &nbsp;
 *           - output     (optional) - the output next to each checkbox
 *           - assign     (optional) - assign the output as an array to this variable
 * Examples:
 * <pre>
 * {html_checkboxes values=$ids output=$names}
 * {html_checkboxes values=$ids name='box' separator='<br>' output=$names}
 * {html_checkboxes values=$ids checked=$checked separator='<br>' output=$names}
 * </pre>
 * @link http://smarty.php.net/manual/en/language.function.html.checkboxes.php {html_checkboxes}
 *      (Smarty online manual)
 * @author     Christopher Kvarme <christopher.kvarme@flashjab.com>
 * @author credits to Monte Ohrt <monte at ohrt dot com>
 * @version    1.0
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_html_checkboxes($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

    $name = 'checkbox';
    $values = null;
    $options = null;
    $selected = null;
    $separator = '';
    $labels = true;
    $output = null;

    $extra = '';

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'name':
            case 'separator':
                $$_key = $_val;
                break;

            case 'labels':
                $$_key = (bool)$_val;
                break;

            case 'options':
                $$_key = (array)$_val;
                break;

            case 'values':
            case 'output':
                $$_key = array_values((array)$_val);
                break;

            case 'checked':
            case 'selected':
				$_val = !is_array($_val) ? explode(',', $_val) : $_val;
                $selected = array_map('strval', array_values((array)$_val));
                break;

            case 'checkboxes':
                $smarty->trigger_error('html_checkboxes: the use of the "checkboxes" attribute is deprecated, use "options" instead', E_USER_WARNING);
                $options = (array)$_val;
                break;

            case 'assign':
                break;

            case 'columns':
                break;

            default:
                if(!is_array($_val)) {
                    $extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
                } else {
                    $smarty->trigger_error("html_checkboxes: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;
        }
    }

    if (!isset($options) && !isset($values))
        return ''; /* raise error here? */

    settype($selected, 'array');
    $_html_result = array();

	$i = 0;

	$per_row = (!empty($params['columns'])) ? $params['columns'] : 1;

	$_html_result[] = '<table cellpadding="0" cellspacing="0" border="0">';
	if (isset($options)) {
		$total = sizeof($options);
        foreach ($options as $_key=>$_val) {
			$i++;
			if (($i - 1) % $per_row == 0) {
				$_html_result[] = '<tr>';
			}
            $_html_result[] = '<td>'.smarty_function_html_checkboxes_output($name, $_key, $_val, $selected, $extra, $separator, $labels).'&nbsp;</td>';

			if ($i % $per_row == 0) {
				$_html_result[] = '</tr>';
			}

			if ($i == $total && ($j = $i % $per_row) != 0) {
				for ($l = 1; $l <= ($per_row - $j); $l++) {
					$_html_result[] = '<td>&nbsp;</td>';
				}
				$_html_result[] = '</tr>';
			}
		}
    } else {
        foreach ($values as $_i=>$_key) {
			$i++;
			if (($i - 1) % $per_row == 0) {
				$_html_result[] = '<tr>';
			}
			$_val = isset($output[$_i]) ? $output[$_i] : '';
            $_html_result[] = '<td>'.smarty_function_html_checkboxes_output($name, $_key, $_val, $selected, $extra, $separator, $labels).'&nbsp;</td>';
			if ($i % $per_row == 0) {
				$_html_result[] = '</tr>';
			}

			if ($i == $total && ($j = $i % $per_row) != 0) {
				for ($l = 1; $l <= ($per_row - $j); $l++) {
					$_html_result[] = '<td>&nbsp;</td>';
				}
				$_html_result[] = '</tr>';
			}

        }

    }
	$_html_result[] = '</table>';

    if(!empty($params['assign'])) {
        Registry::get('smarty')->assign($params['assign'], $_html_result);
    } else {
        return implode("\n",$_html_result);
    }

}

function smarty_function_html_checkboxes_output($name, $value, $output, $selected, $extra, $separator, $labels) {
    $_output = '';
    if ($labels) $_output .= '<label class="label-html-checkboxes">';
    $_output .= '<input type="checkbox" class="html-checkboxes" name="'
        . smarty_function_escape_special_chars($name) . '[]" value="'
        . smarty_function_escape_special_chars($value) . '"';

    if (in_array((string)$value, $selected)) {
        $_output .= ' checked="checked"';
    }
    $_output .= $extra . ' />' . $output;
    if ($labels) $_output .= '</label>';
    $_output .=  $separator;

    return $_output;
}

?>
