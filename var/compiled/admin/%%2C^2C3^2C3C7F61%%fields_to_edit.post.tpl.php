<?php /* Smarty version 2.6.18, created on 2014-09-15 23:43:02
         compiled from addons/discussion/hooks/categories/fields_to_edit.post.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'addons/discussion/hooks/categories/fields_to_edit.post.tpl', 6, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('discussion_title_category'));
?>
<?php  ob_start();  ?>
<li class="select-field">
	<input type="checkbox" value="discussion_type" id="discussion_type" name="selected_fields[extra][]" checked="checked" class="checkbox cm-item-s" />
	<label for="discussion_type"><?php echo fn_get_lang_var('discussion_title_category', $this->getLanguage()); ?>
</label>
</li><?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>
<?php  ob_end_flush();  ?>