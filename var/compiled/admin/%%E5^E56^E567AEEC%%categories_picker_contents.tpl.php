<?php /* Smarty version 2.6.18, created on 2014-09-15 23:41:30
         compiled from pickers/categories_picker_contents.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'pickers/categories_picker_contents.tpl', 12, false),array('modifier', 'fn_url', 'pickers/categories_picker_contents.tpl', 54, false),array('modifier', 'default', 'pickers/categories_picker_contents.tpl', 56, false),array('modifier', 'defined', 'pickers/categories_picker_contents.tpl', 74, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('categories','text_items_added','add_categories','choose','add_categories','add_categories_and_close'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title><?php echo fn_get_lang_var('categories', $this->getLanguage()); ?>
</title>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/styles.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/scripts.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if (! $this->_tpl_vars['_REQUEST']['extra']): ?>
<script type="text/javascript">
//<![CDATA[
lang.text_items_added = '<?php echo smarty_modifier_escape(fn_get_lang_var('text_items_added', $this->getLanguage()), 'javascript'); ?>
';
var display_type = '<?php echo smarty_modifier_escape($this->_tpl_vars['_REQUEST']['display'], 'javascript'); ?>
';
<?php echo '
	function fn_add_js_category(hide, close)
	{
		var d_form = document.forms[\'categories_form\'];
		if(!d_form){
			return false;
		}
		var categories = {};

		if ($(\'input.cm-item:checked\', $(d_form)).length > 0) {
			if (!close) {
				$(\'input.cm-item:checked\', $(d_form)).each( function() {
					var id = $(this).val();
					categories[id] = $(\'#category_\' + id).text();
				});
				parent.window.jQuery.add_js_item(categories, \'c\', null, hide);
			}

			if (display_type != \'radio\') {
				jQuery.showNotifications({\'notification\': {\'type\': \'N\', \'title\': lang.notice, \'message\': lang.text_items_added, \'save_state\': false}});
			}
		}
	}
'; ?>

//]]>
</script>
<?php endif; ?>
</head>

<body class="picker-body">
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/loading_box.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="hidden"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/notification.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>

<?php if ($this->_tpl_vars['categories_tree']): ?>
<?php if ($this->_tpl_vars['_REQUEST']['extra']): ?>
    <?php $this->assign('_extra', "?".($this->_tpl_vars['_REQUEST']['extra']), false); ?>
<?php endif; ?>
<form action="<?php echo fn_url(($this->_tpl_vars['index_script']).($this->_tpl_vars['_extra'])); ?>
" method="post" name="categories_form">

<div class="items-container multi-level"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/categories/components/categories_tree_simple.tpl", 'smarty_include_vars' => array('header' => '1','form_name' => 'discounted_categories_form','checkbox_name' => smarty_modifier_default(@$this->_tpl_vars['_REQUEST']['checkbox_name'], 'categories_ids'),'parent_id' => $this->_tpl_vars['category_id'],'display' => $this->_tpl_vars['_REQUEST']['display'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>

<div class="buttons-container hidden">
<?php if ($this->_tpl_vars['_REQUEST']['extra']): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_id' => 'add_item','but_text' => fn_get_lang_var('add_categories', $this->getLanguage()),'but_meta' => "cm-parent-window cm-process-items",'but_name' => 'submit','but_role' => 'submit')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
	<?php if ($this->_tpl_vars['_REQUEST']['display'] == 'radio'): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_id' => 'add_item','but_text' => fn_get_lang_var('choose', $this->getLanguage()),'but_meta' => "cm-no-submit",'but_name' => 'submit','but_role' => 'submit','but_onclick' => "fn_add_js_category(true, false);")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php else: ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_id' => 'add_item','but_text' => fn_get_lang_var('add_categories', $this->getLanguage()),'but_name' => 'submit','but_onclick' => "fn_add_js_category(false, false);",'but_role' => 'submit','but_meta' => "cm-process-items cm-no-submit")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_id' => 'add_item_close','but_name' => 'submit','but_text' => fn_get_lang_var('add_categories_and_close', $this->getLanguage()),'but_onclick' => "fn_add_js_category(true, false);",'but_role' => 'action','but_meta' => "cm-process-items cm-no-submit")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
<?php endif; ?>
</div>

</form>
<?php endif; ?>

<?php if (defined('TRANSLATION_MODE')): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/translate_box.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
</body>

</html>