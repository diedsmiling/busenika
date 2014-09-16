<?php /* Smarty version 2.6.18, created on 2014-09-15 23:41:06
         compiled from views/product_features/manage.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'views/product_features/manage.tpl', 7, false),array('modifier', 'escape', 'views/product_features/manage.tpl', 43, false),array('modifier', 'unescape', 'views/product_features/manage.tpl', 57, false),array('modifier', 'defined', 'views/product_features/manage.tpl', 93, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('ungroupped_features','editing_product_feature','editing_group','editing_product_feature','no_data','add_new_group','add_group','add_new_feature','add_feature','add_new_group','add_group','add_new_feature','add_feature','product_features'));
?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/file_browser.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php echo smarty_function_script(array('src' => "js/tabs.js"), $this);?>

<?php echo smarty_function_script(array('src' => "js/picker.js"), $this);?>


<?php ob_start(); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/product_features/components/product_features_search_form.tpl", 'smarty_include_vars' => array('dispatch' => "product_features.manage")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php echo '
<script type="text/javascript">
//<![CDATA[
function fn_check_product_feature_type(value, tab_id)
{
	var t = $(\'#content_\' + tab_id);
	$(\'#\' + tab_id).toggleBy(!(value == \'S\' || value == \'M\' || value == \'N\' || value == \'E\'));
	// display/hide images
	$(\'.cm-extended-feature\', t).toggleBy(value != \'E\');
	if (value != \'E\') {
		$(\'tr[id^=extra_feature_]\', t).hide();
		$(\'img[id^=off_extra_feature_]\', t).hide();
		$(\'img[id^=on_extra_feature_]\', t).show();
		$(\'img[id^=off_st_]\', t).hide();
		$(\'img[id^=on_st_]\', t).show();
	}

	if (value == \'N\') {
		$(\'.cm-feature-value\', t).addClass(\'cm-value-integer\');
	} else {
		$(\'.cm-feature-value\', t).removeClass(\'cm-value-integer\');
	}
}
//]]>
</script>
'; ?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/pagination.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $this->assign('r_url', smarty_modifier_escape($this->_tpl_vars['config']['current_url'], 'url'), false); ?>

<div class="items-container" id="update_features_list">
<?php if ($this->_tpl_vars['features']): ?>

	<?php if ($this->_tpl_vars['has_ungroupped']): ?>
		<div class="object-group clear">
			<div class="float-left object-name">
				<?php echo fn_get_lang_var('ungroupped_features', $this->getLanguage()); ?>

			</div>
		</div>
		
		<?php $_from = $this->_tpl_vars['features']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['p_feature']):
?>
			<?php if ($this->_tpl_vars['p_feature']['feature_type'] != 'G'): ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/object_group.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['p_feature']['feature_id'],'details' => smarty_modifier_unescape($this->_tpl_vars['p_feature']['feature_description']),'text' => $this->_tpl_vars['p_feature']['description'],'status' => $this->_tpl_vars['p_feature']['status'],'hidden' => true,'href' => "product_features.update?feature_id=".($this->_tpl_vars['p_feature']['feature_id'])."&amp;return_url=".($this->_tpl_vars['r_url']),'object_id_name' => 'feature_id','table' => 'product_features','href_delete' => "product_features.delete?feature_id=".($this->_tpl_vars['p_feature']['feature_id']),'rev_delete' => 'pagination_contents','header_text' => (fn_get_lang_var('editing_product_feature', $this->getLanguage())).":&nbsp;".($this->_tpl_vars['p_feature']['description']),'element' => "-elements")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
	<?php endif; ?>
	
	<?php $_from = $this->_tpl_vars['features']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['gr_feature']):
?>
		<?php if ($this->_tpl_vars['gr_feature']['feature_type'] == 'G'): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/object_group.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['gr_feature']['feature_id'],'details' => smarty_modifier_unescape($this->_tpl_vars['gr_feature']['feature_description']),'text' => $this->_tpl_vars['gr_feature']['description'],'status' => $this->_tpl_vars['gr_feature']['status'],'hidden' => true,'href' => "product_features.update?feature_id=".($this->_tpl_vars['gr_feature']['feature_id'])."&amp;return_url=".($this->_tpl_vars['r_url']),'object_id_name' => 'feature_id','table' => 'product_features','href_delete' => "product_features.delete?feature_id=".($this->_tpl_vars['gr_feature']['feature_id']),'rev_delete' => 'pagination_contents','header_text' => (fn_get_lang_var('editing_group', $this->getLanguage())).":&nbsp;".($this->_tpl_vars['gr_feature']['description']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
			<?php if ($this->_tpl_vars['gr_feature']['subfeatures']): ?>
				<?php $_from = $this->_tpl_vars['gr_feature']['subfeatures']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['subfeature']):
?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/object_group.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['subfeature']['feature_id'],'details' => smarty_modifier_unescape($this->_tpl_vars['subfeature']['feature_description']),'text' => $this->_tpl_vars['subfeature']['description'],'status' => $this->_tpl_vars['subfeature']['status'],'hidden' => true,'href' => "product_features.update?feature_id=".($this->_tpl_vars['subfeature']['feature_id'])."&amp;return_url=".($this->_tpl_vars['r_url']),'object_id_name' => 'feature_id','table' => 'product_features','href_delete' => "product_features.delete?feature_id=".($this->_tpl_vars['subfeature']['feature_id']),'rev_delete' => 'pagination_contents','header_text' => (fn_get_lang_var('editing_product_feature', $this->getLanguage())).":&nbsp;".($this->_tpl_vars['subfeature']['description']),'element' => "-elements")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php endforeach; endif; unset($_from); ?>
			<?php endif; ?>
	
		<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
<?php else: ?>
	<p class="no-items"><?php echo fn_get_lang_var('no_data', $this->getLanguage()); ?>
</p>
<?php endif; ?>
<!--update_features_list--></div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/pagination.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="buttons-container">
	<?php ob_start(); ?>
		<?php ob_start(); ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/product_features/update.tpl", 'smarty_include_vars' => array('feature' => "",'mode' => 'add','is_group' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php $this->_smarty_vars['capture']['add_new_picker'] = ob_get_contents(); ob_end_clean(); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => 'add_new_group','text' => fn_get_lang_var('add_new_group', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['add_new_picker'],'link_text' => fn_get_lang_var('add_group', $this->getLanguage()),'act' => 'general')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

		<?php ob_start(); ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/product_features/update.tpl", 'smarty_include_vars' => array('feature' => "",'mode' => 'add')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php $this->_smarty_vars['capture']['add_new_picker_2'] = ob_get_contents(); ob_end_clean(); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => 'add_new_feature','text' => fn_get_lang_var('add_new_feature', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['add_new_picker_2'],'link_text' => fn_get_lang_var('add_feature', $this->getLanguage()),'act' => 'general')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php $this->_smarty_vars['capture']['tools'] = ob_get_contents(); ob_end_clean(); ?>
	<?php if (! defined('COMPANY_ID')): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => 'add_new_group','text' => fn_get_lang_var('add_new_group', $this->getLanguage()),'link_text' => fn_get_lang_var('add_group', $this->getLanguage()),'act' => 'general')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => 'add_new_feature','text' => fn_get_lang_var('add_new_feature', $this->getLanguage()),'link_text' => fn_get_lang_var('add_feature', $this->getLanguage()),'act' => 'general')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
</div>

<?php $this->_smarty_vars['capture']['mainbox'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/mainbox.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('product_features', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['mainbox'],'tools' => $this->_smarty_vars['capture']['tools'],'title_extra' => $this->_smarty_vars['capture']['title_extra'],'select_languages' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>