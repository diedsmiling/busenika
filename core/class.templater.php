<?php
/***************************************************************************
*                                                                          *
*    Copyright (c) 2004 Simbirsk Technologies Ltd. All rights reserved.    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

//
// $Id: class.templater.php 10568 2010-09-01 12:57:16Z 2tl $
//

if ( !defined('AREA') )	{ die('Access denied');	}

require(DIR_LIB . 'templater/Smarty.class.php');
require(DIR_LIB . 'templater/Smarty_Compiler.class.php');

fn_define('SMARTY_CUSTOM_PLUGINS', DIR_CORE . 'templater_plugins');
fn_define('SMARTY_CUSTOM_CLASS', DIR_CORE . 'class.templater.php');


class Templater extends Smarty
{
	var $a = array();
    var $plugins_dir = array(SMARTY_CUSTOM_PLUGINS, 'plugins');
    var $compiler_file = SMARTY_CUSTOM_CLASS;
    var $compiler_class = 'Templater_Compiler';
    var $lang_code = CART_LANGUAGE;

    function _smarty_include($params)
    {
		if ($this->debugging) {
            $_params = array();
            require_once(SMARTY_CORE_DIR . 'core.get_microtime.php');
            $debug_start_time = smarty_core_get_microtime($_params, $this);
            $this->_smarty_debug_info[] = array('type'      => 'template',
                                                  'filename'  => $params['smarty_include_tpl_file'],
                                                  'depth'     => ++$this->_inclusion_depth);
            $included_tpls_idx = count($this->_smarty_debug_info) - 1;
        }

		// Extract array with parameters
		if (!empty($params['smarty_include_vars']['params_array'])) {
			foreach ($params['smarty_include_vars']['params_array'] as $k => $v) {
				$this->_tpl_vars[$k] = $v;
			}
			unset($params['smarty_include_vars']['params_array']);
		}

        // Get customization mode info
		if ($this->customization) {
        	$this->_smarty_customization_info[] = array('filename' => $params['smarty_include_tpl_file'], 'depth' => ($this->debugging ? $this->_inclusion_depth : ++$this->_inclusion_depth));
		}

        $this->_tpl_vars = array_merge($this->_tpl_vars, $params['smarty_include_vars']);

	    // Load addons if necessary
		if (strpos($params['smarty_include_tpl_file'], 'addons/') === 0) {
			$path_array = explode('/', $params['smarty_include_tpl_file']);
			if (fn_load_addon($path_array[1]) == false) {
				return false;
			}
		}

		// Substitute current skin area
		$_skin_name = ($this->_tpl_vars['skin_area'] == 'mail' || $this->_tpl_vars['skin_area'] == 'customer') ? Registry::get('settings.skin_name_customer') : Registry::get('config.skin_name');
		$this->template_dir = DIR_ROOT . '/skins/' . $_skin_name . '/' . $this->_tpl_vars['skin_area'];
		$this->_tpl_vars['images_dir'] = Registry::get('config.current_path') . '/skins/' . $_skin_name . '/' . $this->_tpl_vars['skin_area'] . '/images';
		$this->_tpl_vars['skin_dir'] = Registry::get('config.current_path') . '/skins/' . $_skin_name . '/' . $this->_tpl_vars['skin_area'];

		// config vars are treated as local, so push a copy of the
        // current ones onto the front of the stack
        array_unshift($this->_config, $this->_config[0]);

        $_smarty_compile_path = $this->_get_compile_path($params['smarty_include_tpl_file']);


        if ($this->_is_compiled($params['smarty_include_tpl_file'], $_smarty_compile_path)
            || $this->_compile_resource($params['smarty_include_tpl_file'], $_smarty_compile_path))
        {
            include($_smarty_compile_path);
        }

        // pop the local vars off the front of the stack
        array_shift($this->_config);

        if ($this->debugging) {
            // capture time for debugging info
            $_params = array();
            require_once(SMARTY_CORE_DIR . 'core.get_microtime.php');
            $this->_smarty_debug_info[$included_tpls_idx]['exec_time'] = smarty_core_get_microtime($_params, $this) - $debug_start_time;
        } else {
        	$this->_inclusion_depth--;
		}

        if ($this->caching) {
            $this->_cache_info['template'][$params['smarty_include_tpl_file']] = true;
        }
    }
             
	function display($tpl, $to_screen = true,$compile_id = NULL)
	{
		if (defined('AJAX_REQUEST')) {
			// Decrease amount of templates to parse if we're using ajax request
			$tpl = $tpl == 'index.tpl' ? (defined('PARSE_ALL')? $tpl : $this->get_var('content_tpl')) : $tpl;
		}

		// Pass navigation to templates
		$this->assign('navigation', Registry::get('navigation'));
		
		if ($to_screen == true) {
			if (ini_get('zlib.output_compression') == '' && strpos(@$_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false && !defined('AJAX_REQUEST')) {
				//ob_start('ob_gzhandler');
			}
			parent::display($tpl);
		} else {
			return $this->fetch($tpl);
		}
	}

	//
	// Overload base method to forbid array assigning
	//
	function assign($tpl_var, $value = null, $escape = true)
	{
		fn_update_lang_objects($tpl_var, $value);

		if (is_array($tpl_var)){
			$this->trigger_error("Assigning by array is not implemented");
		} else {
			if ($tpl_var != '') {
				$this->_tpl_vars[$tpl_var] = ($escape) ? fn_html_escape($value) : $value;
			}
		}
	}

    function _parse_resource_name(&$params)
    {
		$params['resource_name'] = fn_addon_template_overrides($params['resource_name'], $this);

		return parent::_parse_resource_name($params);

    }


	// ----------- Custom methods --------------------------


	//
	// Get template variable
	//
	function get_var($var, $default = NULL)
	{
		if (!isset($this->_tpl_vars[$var])) {
			$this->_tpl_vars[$var] = ($default === NULL) ? array() : $default;
		}

		return fn_html_escape($this->_tpl_vars[$var], true);
	}

	function setLanguage($lang_code)
	{
		$this->lang_code = $lang_code;
	}

	function getLanguage()
	{
		return $this->lang_code;
	}

	//
	// Checks if inline blocks are changed and parent template should be recompiled
	//
	function check_inline_blocks($resources = array())
	{
		$result = false;
		foreach ($resources as $res => $ts) {
			$_smarty_compile_path = $this->_get_compile_path($res);

			if (!file_exists($_smarty_compile_path) || $ts < filemtime($this->template_dir . '/' . $res)) {
				if ($this->_is_compiled($res, $_smarty_compile_path) == false) {
					$this->_compile_resource($res, $_smarty_compile_path);
				}
				$result = true;
			}
		}

		return $result;
	}

	//
	// This filter wraps templates which don't have {include} tags inside into ob_start/ob_end_flush functions
	// to speed-up content displaying
	//
	function prefilter_output_buffering($content, &$compiler)
	{

		if (strpos($content, '{include ') === false) {
			return "{php} ob_start(); {/php}" . $content . "{php} ob_end_flush(); {/php}";
		}

		return $content;
	}

	//
	// This filter adds unique field to all forms to protect from CSFR attacks
	//
	function prefilter_security_hash($content, &$compiler)
	{
		$content = str_replace('</form>', '<input type="hidden" name="security_hash" value="{""|fn_generate_security_hash}" /></form>', $content);

		return $content;
	}

	//
	// This filter adds unique field to all forms to protect from CSFR attacks
	//
	function prefilter_form_validator($content, &$compiler)
	{
		static $it = 0;
		if (strpos($content, '<form') !== false) {
			$content = str_replace('<form', '{capture name="fc"}<form', $content);
			$content = str_replace('</form>', '</form>{/capture}{$smarty.capture.fc|store_validator}', $content);
		}
		
		return $content;
	}

	//
	// This filter gets all available language variables in templates and puts their retrieving to the template start
	//
	function postfilter_translation($content, &$compiler)
	{
		if (preg_match_all('/fn_get_lang_var\(\'(\w*)\', \$this->getLanguage\(\)\)/i', $content, $matches)) {
			return "<?php\nfn_preload_lang_vars(array('" . implode("','", $matches[1]) . "'));\n?>\n" . $content;
		}

		return $content;
	}

	function outputfilter_translate_wrapper($content, &$compiler)
	{
		$pattern = '/\<(input|img)[^>]*?(\[lang name\=([\w-]+?)( [cm\-pre\-ajx]*)?\](.*?)\[\/lang\])[^>]*?\>/';
		if (preg_match_all($pattern, $content, $matches)) {
			foreach ($matches[0] as $k => $m) {
				$phrase_replaced = str_replace($matches[2][$k], $matches[5][$k], $matches[0][$k]);
				if (strpos($m, 'class="') !== false) {
					$class_added = str_replace('class="', 'class="cm-translate lang_' . $matches[3][$k] . $matches[4][$k] . ' ', $phrase_replaced);
				} else {
					$class_added = str_replace($matches[1][$k], $matches[1][$k] . ' class="cm-translate lang_' . $matches[3][$k] . $matches[4][$k] . '"', $phrase_replaced);
				}
				$content = str_replace($matches[0][$k], $class_added, $content);
			}
		}

		$pattern = '/(\<(textarea|option)[^<]*?)\>(\[lang name\=([\w-]+?)( [cm\-pre\-ajx]*)?\](.*?)\[\/lang\])[^>]*?\>/is';
		if (preg_match_all($pattern, $content, $matches)) {
			foreach ($matches[0] as $k => $m) {
				$phrase_replaced = str_replace($matches[3][$k], $matches[6][$k], $matches[0][$k]);
				if (strpos($m, 'class="') !== false) {
					$class_added = str_replace('class="', 'class="cm-translate lang_' . $matches[4][$k] . $matches[5][$k] . ' ', $phrase_replaced);
				} else {
					$class_added = str_replace('<' . $matches[2][$k], '<' . $matches[2][$k] . ' class="cm-translate lang_' . $matches[4][$k] . $matches[5][$k] . '"', $phrase_replaced);
				}
				$content = str_replace($matches[0][$k], $class_added, $content);
			}
		}

		$pattern = '/<title>(.*?)<\/title>/is';
		$pattern_inner = '/\[(lang) name\=([\w-]+?)( [cm\-pre\-ajx]*)?\](.*?)\[\/\1\]/is';
		preg_match($pattern, $content, $matches);
		$phrase_replaced = $matches[0];
		$phrase_replaced = preg_replace($pattern_inner, '$4', $phrase_replaced);
		$content = str_replace($matches[0], $phrase_replaced, $content);

		$pattern = '/(?<=>)[^<]*?\[(lang) name\=([\w-]+?)( [cm\-pre\-ajx]*)?\](.*?)\[\/\1\]/is';
		$pattern_inner = '/\[(lang) name\=([\w-]+?)( [cm\-pre\-ajx]*)?\]((?:(?>[^\[]+)|\[(?!\1[^\]]*\]))*?)\[\/\1\]/is';
		$replacement = '<font class="cm-translate lang_$2$3">$4</font>';
		while (preg_match($pattern, $content, $matches)) {
			$phrase_replaced = $matches[0];
			while (preg_match($pattern_inner, $phrase_replaced)) {
				$phrase_replaced = preg_replace($pattern_inner, $replacement, $phrase_replaced);
			}
			$content = str_replace($matches[0], $phrase_replaced, $content);
		}

		$pattern = '/\[(lang) name\=([\w-]+?)( [cm\-pre\-ajx]*)?\](.*?)\[\/\1\]/';
		$replacement = '$4';
		$content = preg_replace($pattern, $replacement, $content);

		return $content;
	}


	function prefilter_form_tooltip($content, &$compiler)
	{
		$pattern = '/\<label[^>]*\>.*(\{\$lang\.([^\}]+)\}[^:\<]*).*\<\/label\>/';
		
		if (preg_match_all($pattern, $content, $matches)) {
			
			$cur_templ = $compiler->_current_file;

			$template_pattern = '/([^\/\.]+)/';
			$template_name = '';
			$ignored_names = array('tpl');
			
			if (preg_match_all($template_pattern, $cur_templ, $template_matches)) {
				foreach ($template_matches[0] as $k => $m) {
					if (!in_array($template_matches[1][$k], $ignored_names)) {
						$template_name .= $template_matches[1][$k] . '_';
					}
				}
			}

			$template_pref = 'tt_' . $template_name;
			$template_tooltips = fn_get_lang_vars_by_prefix($template_pref);

			foreach ($matches[0] as $k => $m) {
				$field_name = $matches[2][$k];
				
				preg_match("/(^[a-zA-z0-9][a-zA-Z0-9_]*)/", $field_name, $name_matches);
				if (@strlen($name_matches[0]) != strlen($field_name)) {
					continue;
				}
				
				$label = $matches[1][$k];

				$template_lang_var = $template_pref . $field_name;
				$common_lang_var = 'ttc_' . $field_name;
				
				if (isset($_REQUEST['stt'])) {
					$template_text = isset($template_tooltips[$template_lang_var]) ? '{$lang.' . $template_lang_var . '}' : '';
					$common_tip = fn_get_lang_var($common_lang_var);
					$common_text = '';
					if ($common_tip != '_' . $common_lang_var) {
						$common_text = '{$lang.' . $common_lang_var . '}';
					}

					$tooltip_text = sprintf("%s: %s <br/> %s: %s", $common_lang_var, $common_text, $template_lang_var, $template_text);
					$tooltip = '{capture name="tooltip"}' . $tooltip_text . '{/capture}{include file="common_templates/tooltip.tpl" params="cm-tooltip-focus" tooltip=$smarty.capture.tooltip"}';
				} else {
					if (isset($template_tooltips[$template_lang_var])) {
						$tooltip_text = '$lang.' . $template_lang_var;
					} else {
						$tooltip = fn_get_lang_var($common_lang_var);
						if ($tooltip == '_' . $common_lang_var || empty($tooltip_text)) {
							continue;
						}
						$tooltip_text = '$lang.' . $common_lang_var;
					}


					$tooltip = '{include file="common_templates/tooltip.tpl" tooltip=' . $tooltip_text . '}';
				}
				$tooltip_added = str_replace($label, $label . $tooltip, $matches[0][$k]);

				$content = str_replace($matches[0][$k], $tooltip_added, $content);				
			}

			$content .= '{script src="js/jquery.simpletip-1.3.1.js"}';
		}

		return $content;
	}

	function prefilter_hook($content, &$compiler)
	{
		$pattern = '/\{hook( name="([^"}]+)")\}((?:(?>[^\{]+)|\{(?!hook[^\}]*\}))*?)\{\/hook\}/is';
		$cur_templ = $compiler->_current_file;
		$positions = array('pre', 'post', 'override');
		$tmp = array();
		$cnt = 1000;

		while (preg_match($pattern, $content, $matches)) {
			$cnt--;
			$tmp[] = $cnt;
			$override_prefix = '{tmp_prefilter#' . $cnt . $matches[1] .'}';
			$close_tag = '{/tmp_prefilter#' . $cnt .'}';
			$override_suffix = $close_tag;
			$hook_body = $matches[3];
			$tpl_name = str_replace(':', '/', $matches[2]);
			foreach (Registry::get('addons') as $i => $v) {
				foreach ($positions as $pos) {
					$tpl = 'addons/' . $i . '/hooks/' . $tpl_name . '.' . $pos . '.tpl';
					
					if ($this->template_exists($tpl) && strpos($cur_templ, $tpl) === false) {
						
						if ($pos == 'pre') {
							$hook_body = '{if $addons.' . $i . '.status == "A"}{include file="' . $tpl . '"}{/if}' . $hook_body;
						} elseif ($pos == 'post') {
							$hook_body .= '{if $addons.' . $i . '.status == "A"}{include file="' . $tpl . '"}{/if}';
						} elseif ($pos == 'override') {
							$override_prefix = '{if $addons.' . $i . '.status == "A"}{include file="' . $tpl . '" assign="addon_content"}{else}{assign var="addon_content" value=""}{/if}{if $addon_content|trim}{$addon_content}{else}' . $override_prefix;
							$override_suffix .= '{/if}';
						}
					}
				}
			}
			$content = preg_replace($pattern, $override_prefix . $hook_body . $override_suffix, $content, 1);
		}

		foreach ($tmp as $key) {
			$pattern = '/tmp_prefilter#' . $key . '/is';
			$content = preg_replace($pattern, 'hook', $content);
		}

		return $content;
	}

	function prefilter_template_wrapper($content, &$compiler)
	{
		$cur_templ = fn_addon_template_overrides($compiler->_current_file, $compiler);
		$ignored_template = array('index.tpl', 'common_templates/pagination.tpl', 'views/categories/components/menu_items.tpl');
		if (!in_array($cur_templ, $ignored_template)) {
			$content = '{capture name="template_content"}' . $content . '{/capture}{if $smarty.capture.template_content|trim}{if $auth.area == "A"}<span class="cm-template-box" template="' . $cur_templ . '" id="{set_id name=' . $cur_templ . '}"><img class="cm-template-icon hidden" src="{$images_dir}/icons/layout_edit.gif" width="16" height="16" alt="" />{$smarty.capture.template_content}<!--[/tpl_id]--></span>{else}{$smarty.capture.template_content}{/if}{/if}';
		}

		return $content;
	}

	function outputfilter_template_ids($content, &$compiler)
	{
		$pattern = '/(\<head\>.*?)(\<span[^<>]*\>|\<\/span\>|\<img[^<>]*\>|\<!--[\w]*--\>)+?(.*?\<\/head\>)/is';
		while (preg_match($pattern, $content, $match)) {
			$content = str_replace($match[0], $match[1] . $match[3], $content);
		}
		$pattern = '/\<span[^<>]*\>|\<\/span\>|\<img[^<>]*\>|\<!--[\w]*--\>/is';
		$glob_pattern = '/\<script[^<>]*\>.*?\<\/script\>/is';
		if (preg_match_all($glob_pattern, $content, $matches)) {
			foreach ($matches[0] as $k => $m) {
				$replace_script = preg_replace($pattern, '', $matches[0][$k]);
				$content = str_replace($matches[0][$k], $replace_script, $content);
			}
		}

		static $template_ids;
		if (!isset($template_ids)) {
			$template_ids = array();
		}

		$pattern = '/\[(tpl_id) ([^ ]*)\]((?:(?>[^\[]+)|\[(?!\1[^\]]*\]))*?)\[\/\1\]/is';
		while (preg_match($pattern, $content, $matches)) {
			$id = 'te' . md5($matches[2]);
			if (empty($template_ids[$matches[2]])) {
				$template_ids[$matches[2]] = 1;
			} else {
				$template_ids[$matches[2]]++;
				$id .= '_' . $template_ids[$matches[2]];
			}
			$content = preg_replace($pattern, $id . '${3}' . $id, $content, 1);
		}

		return $content;
	}

	//
	// This filter include templates which have no {include} tags inside to the parent template
	//
	function prefilter_inline($source, &$compiler)
	{
		$compiler->_inline_cache = array();
		$output = preg_replace_callback('!' . preg_quote($this->left_delimiter, '!') . 'include (.*)' . preg_quote($this->right_delimiter, '!') . '!Us', array($compiler, '_prefilter_inline_callback'), $source);

		if (!empty($complier->_inline_cache)) {
			$output = "{php}\n
				\$rname = !empty(\$resource_name) ? \$resource_name : \$params['smarty_include_tpl_file'];
				if (\$this->compile_check && empty(\$inline_no_check[\$rname])) {
					if (\$this->check_inline_blocks(" . var_export($compiler->_inline_cache, true) .")) {
						\$_smarty_compile_path = \$this->_get_compile_path(\$rname);
						\$this->_compile_resource(\$rname, \$_smarty_compile_path);
						\$inline_no_check[\$rname] = true;
						include \$_smarty_compile_path;
						return;
					}
				}
			{/php}" . $output;

			$complier->_inline_cache = array();
		}

		return $output;
	}
}

/**
 * Template compiling class
 * @package Smarty
 */
class Templater_Compiler extends Smarty_Compiler {
	//
	// Overload base method to output script tags directly (in capture)
	//
    function _compile_include_tag($tag_args)
    {
        $attrs = $this->_parse_attrs($tag_args);
        $arg_list = array();

        if (empty($attrs['file'])) {
            $this->_syntax_error("missing 'file' attribute in include tag", E_USER_ERROR, __FILE__, __LINE__);
        }

        foreach ($attrs as $arg_name => $arg_value) {
            if ($arg_name == 'file') {
                $include_file = $arg_value;
                continue;
            } else if ($arg_name == 'assign') {
                $assign_var = $arg_value;
                continue;
            }
            if (is_bool($arg_value))
                $arg_value = $arg_value ? 'true' : 'false';
            $arg_list[] = "'$arg_name' => $arg_value";
        }

        $output = '<?php ';

        if (isset($assign_var)) {
			$buffer = "'" . md5($attrs['file']) . "'";
			$output .= "ob_start(); \$this->_in_capture[] = $buffer;\n"; // zeke: get capture info to output script tags directly. FIXME
        }

        $output .=
            "\$_smarty_tpl_vars = \$this->_tpl_vars;";


        $_params = "array('smarty_include_tpl_file' => " . $include_file . ", 'smarty_include_vars' => array(".implode(',', (array)$arg_list)."))";
        $output .= "\$this->_smarty_include($_params);\n" .
        "\$this->_tpl_vars = \$_smarty_tpl_vars;\n" .
        "unset(\$_smarty_tpl_vars);\n";

        if (isset($assign_var)) {
            $output .= "\$this->_tpl_vars[" . $assign_var . "] = ob_get_contents(); ob_end_clean(); array_pop(\$this->_in_capture); if (!empty(\$this->_scripts[$buffer])) { echo implode(\"\\n\", \$this->_scripts[$buffer]); unset(\$this->_scripts[$buffer]); }\n"; //zeke
        }

        $output .= ' ?>';

        return $output;

    }

	//
	// Overload base method to parse $lang variables
	//
    function _parse_var($var_expr)
    {
		$_output = parent::_parse_var($var_expr);

		if (strpos($_output, "\$this->_tpl_vars['lang']") !== false) {
			$__tmp = str_replace("\$this->_tpl_vars['lang'][", 'fn_get_lang_var(', $_output);
			$__tmp{strlen($__tmp)-1} = ',';
			$__tmp .= ' $this->getLanguage())';
			$_output = $__tmp;
			unset($__tmp);
		}

        return $_output;
    }

	//
	// Overload base method to get rid of @ sign to process arrays at once
	//
    function _parse_modifiers(&$output, $modifier_string)
    {
		return parent::_parse_modifiers($output, preg_replace('/\|(?!@)/', '|@', $modifier_string));
    }

	//
	// Overload base method in order to prevent using deprecated variables.
	//
	function _compile_smarty_ref(&$indexes)
	{
		$_ref = substr($indexes[0], 1);

		// post, get and env variables are disabled
		if (in_array($_ref, array ('post', 'get', 'env'))) {
			$this->_syntax_error("access for \$smarty.$_ref variables is disabled, please use \$smarty.request", E_USER_WARNING, __FILE__, __LINE__);
		}

		// Access to request variable is provided via escaped one
		if ($_ref == 'request') {
	        array_shift($indexes);
	        return '$this->_tpl_vars[\'_REQUEST\']';
		}

		return parent::_compile_smarty_ref($indexes);
	}

	// -------- Custom methods ----------------
	function _prefilter_inline_callback($match)
	{
		$ld = $this->left_delimiter;
		$rd = $this->right_delimiter;
		$source_content = '';

		$_attrs = $this->_parse_attrs($match[1]);

		if (isset($_attrs['assign'])) { // Do not inline template if it has "assign" parameter
			return $match[0];
		}

		if (!isset($_attrs['file'])) {
			$this->syntax_error('[inline] missing file-parameter');
			return false;
		}


		$resource_name = $this->_dequote($_attrs['file']);
		unset($_attrs['file']);

		if (strpos($resource_name, '$') !== false) {
			return $match[0];
		}

		if (isset($_attrs['assign'])) {
			$assign = $_attrs['assign'];
			unset($_attrs['assign']);
		} else {
			$assign = null;
		}

		$source_content .= $ld.'php'.$rd;
		$source_content .= "\$__parent_tpl_vars = \$this->_tpl_vars;";

		if (!empty($_attrs)) {

			$source_content .= "\$this->_tpl_vars = array_merge(\$this->_tpl_vars, array(";
			foreach ($_attrs as $_name => $_value) {
				$source_content .= "'$_name' => $_value, ";
			}
			$source_content .= '));';
		}

		$source_content .= $ld.'/php'.$rd;

		$params = array(
			'resource_name' => $resource_name,
			'quiet' => true,
		);

		if ($this->_fetch_resource_info($params)) {
			// remove comments
			$params['source_content'] = preg_replace('~\{\*(.*?)\*}~', '', $params['source_content']);

			// if we do not have includes from this template, inline it
			if (strpos($params['source_content'], '{include ') === false) {

				// run template source through prefilter functions
				if (count($this->_plugins['prefilter']) > 0) {
					$current_file = $this->_current_file;
					$this->_current_file = $params['resource_name'];
					$stop_on_next = false;
					foreach ($this->_plugins['prefilter'] as $filter_name => $prefilter) {
						if ($prefilter === false || $stop_on_next == true) {
							continue;
						}
						if ($filter_name == 'prefilter_inline') { // run prefilters up to current one
							$stop_on_next = true;
						}
						if ($prefilter[3] || is_callable($prefilter[0])) {
							$params['source_content'] = call_user_func_array($prefilter[0], array($params['source_content'], &$this));
							$this->_plugins['prefilter'][$filter_name][3] = true;
						} else {
							$this->_trigger_fatal_error("[plugin] prefilter '$filter_name' is not implemented");
						}
					}
					$this->_current_file = $current_file;
				}

				$source_content .= $params['source_content'];
				$this->_inline_cache[$resource_name] = $params['resource_timestamp'];

				// handle assign
				if (isset($assign)) {
					$source_content = $ld.'php'.$rd . 'ob_start();' . $ld.'/php' . $rd . $source_content . $ld.'php'.$rd . "\$this->_tpl_vars[$assign] = ob_get_contents(); ob_end_clean();";
				}
				$source_content .= $ld.'php'.$rd . "if (isset(\$__parent_tpl_vars)) { \$this->_tpl_vars = \$__parent_tpl_vars; unset(\$__parent_tpl_vars);}" . $ld.'/php'.$rd;
			} else {
				return $match[0];
			}

		}

		return $source_content;
	}

}

?>