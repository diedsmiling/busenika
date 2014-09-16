<?php /* Smarty version 2.6.18, created on 2014-09-15 23:43:02
         compiled from views/categories/manage.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'views/categories/manage.tpl', 3, false),array('modifier', 'fn_url', 'views/categories/manage.tpl', 7, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('no_items','text_select_fields2edit_note','modify_selected','delete_selected','edit_selected','choose_action','select_fields_to_edit','add_category','add_category','categories'));
?>

<?php echo smarty_function_script(array('src' => "js/picker.js"), $this);?>


<?php ob_start(); ?>

<form action="<?php echo fn_url(""); ?>
" method="post" name="category_tree_form">
<div class="items-container multi-level">
	<?php if ($this->_tpl_vars['categories_tree']): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/categories/components/categories_tree.tpl", 'smarty_include_vars' => array('header' => '1','parent_id' => $this->_tpl_vars['category_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php else: ?>
		<p class="no-items"><?php echo fn_get_lang_var('no_items', $this->getLanguage()); ?>
</p>
	<?php endif; ?>
</div>

<?php ob_start(); ?>
	<div class="object-container">
		<p><?php echo fn_get_lang_var('text_select_fields2edit_note', $this->getLanguage()); ?>
</p>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/categories/components/categories_select_fields.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>

	<div class="buttons-container">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('modify_selected', $this->getLanguage()),'but_meta' => "cm-process-items",'but_name' => "dispatch[categories.store_selection]",'cancel_action' => 'close')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
<?php $this->_smarty_vars['capture']['select_fields_to_edit'] = ob_get_contents(); ob_end_clean(); ?>

<div class="buttons-container buttons-bg">
	<?php if ($this->_tpl_vars['categories_tree']): ?>
	<div class="float-left">
		<?php ob_start(); ?>
		<ul>
			<li><a class="cm-confirm cm-process-items" name="dispatch[categories.m_delete]" rev="category_tree_form"><?php echo fn_get_lang_var('delete_selected', $this->getLanguage()); ?>
</a></li>
			<li><a onclick="jQuery.show_picker('select_fields_to_edit', '', '.object-container');"><?php echo fn_get_lang_var('edit_selected', $this->getLanguage()); ?>
</a></li>
		</ul>
		<?php $this->_smarty_vars['capture']['tools_list'] = ob_get_contents(); ob_end_clean(); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[categories.m_update]",'but_role' => 'button_main')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('prefix' => 'main','hide_actions' => true,'tools_list' => $this->_smarty_vars['capture']['tools_list'],'display' => 'inline','link_text' => fn_get_lang_var('choose_action', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => 'select_fields_to_edit','text' => fn_get_lang_var('select_fields_to_edit', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['select_fields_to_edit'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
	<?php endif; ?>
	
	<div class="float-right">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('tool_href' => "categories.add",'prefix' => 'bottom','hide_tools' => 'true','link_text' => fn_get_lang_var('add_category', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
</div>

<?php ob_start(); ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('tool_href' => "categories.add",'prefix' => 'top','hide_tools' => 'true','link_text' => fn_get_lang_var('add_category', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $this->_smarty_vars['capture']['tools'] = ob_get_contents(); ob_end_clean(); ?>

</form>

<?php $this->_smarty_vars['capture']['mainbox'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/mainbox.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('categories', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['mainbox'],'tools' => $this->_smarty_vars['capture']['tools'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>