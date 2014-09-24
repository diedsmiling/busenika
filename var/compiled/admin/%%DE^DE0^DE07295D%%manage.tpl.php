<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:17
         compiled from views/product_options/manage.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'views/product_options/manage.tpl', 7, false),array('modifier', 'defined', 'views/product_options/manage.tpl', 63, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('global','vendor','editing_option','no_items','new_option','add_option','new_option','add_option','apply_to_products','global_options'));
?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/file_browser.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php echo smarty_function_script(array('src' => "js/tabs.js"), $this);?>

<?php echo '
<script type="text/javascript">
//<![CDATA[
function fn_check_option_type(value, tag_id)
{
	var id = tag_id.replace(\'option_type_\', \'\');
	$(\'#tab_option_variants_\' + id).toggleBy(!(value == \'S\' || value == \'R\' || value == \'C\'));
	$(\'#required_options_\' + id).toggleBy(!(value == \'I\' || value == \'T\' || value == \'F\'));
	$(\'#extra_options_\' + id).toggleBy(!(value == \'I\' || value == \'T\'));
	$(\'#file_options_\' + id).toggleBy(!(value == \'F\'));
	
	if (value == \'C\') {
		var t = $(\'table\', \'#content_tab_option_variants_\' + id);
		$(\'.cm-non-cb\', t).switchAvailability(true); // hide obsolete columns
		$(\'tbody:gt(1)\', t).switchAvailability(true); // hide obsolete rows

	} else if (value == \'S\' || value == \'R\') {
		var t = $(\'table\', \'#content_tab_option_variants_\' + id);
		$(\'.cm-non-cb\', t).switchAvailability(false); // show all columns
		$(\'tbody\', t).switchAvailability(false); // show all rows
		$(\'#box_add_variant_\' + id).show(); // show "add new variants" box
		
	} else if (value == \'I\' || value == \'T\') {
		$(\'#extra_options_\' + id).show(); // show "add new variants" box
	}
}
//]]>
</script>
'; ?>


<?php ob_start(); ?>

<?php if ($this->_tpl_vars['object'] == 'global'): ?>
	<?php $this->assign('select_languages', true, false); ?>
	<?php $this->assign('rev_delete_id', 'pagination_contents', false); ?>
<?php else: ?>
	<?php $this->assign('rev_delete_id', 'product_options_list', false); ?>
	<?php $this->assign('query_product_id', "&product_id=".($this->_tpl_vars['product_id']), false); ?>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/pagination.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="items-container" id="product_options_list">
<?php $_from = $this->_tpl_vars['product_options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['po']):
?>
	<?php if ($this->_tpl_vars['object'] == 'product' && ! $this->_tpl_vars['po']['product_id']): ?>
		<?php $this->assign('details', "(".(fn_get_lang_var('global', $this->getLanguage())).")", false); ?>
	<?php else: ?>
		<?php $this->assign('details', "", false); ?>
	<?php endif; ?>
	
	<?php if ($this->_tpl_vars['po']['company_id']): ?>
	<?php $this->assign('po_name', ($this->_tpl_vars['po']['option_name'])." (".(fn_get_lang_var('vendor', $this->getLanguage())).": ".($this->_tpl_vars['s_companies'][$this->_tpl_vars['po']['company_id']]['company']).")", false); ?>
	<?php $this->assign('skip_delete', '0', false); ?>
	<?php else: ?>
	<?php $this->assign('po_name', $this->_tpl_vars['po']['option_name'], false); ?>
		<?php if (defined('COMPANY_ID')): ?>
			<?php $this->assign('skip_delete', '1', false); ?>
		<?php endif; ?>
	<?php endif; ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/object_group.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['po']['option_id'],'id_prefix' => '_product_option_','details' => $this->_tpl_vars['details'],'text' => $this->_tpl_vars['po_name'],'status' => $this->_tpl_vars['po']['status'],'table' => 'product_options','object_id_name' => 'option_id','href' => "product_options.update?option_id=".($this->_tpl_vars['po']['option_id']).($this->_tpl_vars['query_product_id']),'href_delete' => "product_options.delete?option_id=".($this->_tpl_vars['po']['option_id']).($this->_tpl_vars['query_product_id']),'rev_delete' => $this->_tpl_vars['rev_delete_id'],'header_text' => (fn_get_lang_var('editing_option', $this->getLanguage())).":&nbsp;".($this->_tpl_vars['po']['option_name']),'skip_delete' => $this->_tpl_vars['skip_delete'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php endforeach; else: ?>

	<p class="no-items"><?php echo fn_get_lang_var('no_items', $this->getLanguage()); ?>
</p>

<?php endif; unset($_from); ?>
<!--product_options_list--></div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/pagination.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="buttons-container">
	<?php ob_start(); ?>
		<?php ob_start(); ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/product_options/update.tpl", 'smarty_include_vars' => array('mode' => 'add','option_id' => '0')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php $this->_smarty_vars['capture']['add_new_picker'] = ob_get_contents(); ob_end_clean(); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => 'add_new_option','text' => fn_get_lang_var('new_option', $this->getLanguage()),'link_text' => fn_get_lang_var('add_option', $this->getLanguage()),'act' => 'general','content' => $this->_smarty_vars['capture']['add_new_picker'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php $this->_smarty_vars['capture']['tools'] = ob_get_contents(); ob_end_clean(); ?>
	<?php if ($this->_tpl_vars['object'] == 'global'): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => 'add_new_option','text' => fn_get_lang_var('new_option', $this->getLanguage()),'link_text' => fn_get_lang_var('add_option', $this->getLanguage()),'act' => 'general')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php else: ?>
		<?php echo $this->_smarty_vars['capture']['tools']; ?>

	<?php endif; ?>

	<?php if ($this->_tpl_vars['product_options'] && $this->_tpl_vars['object'] == 'global'): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('apply_to_products', $this->getLanguage()),'but_role' => 'text','but_href' => "product_options.apply")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>

	<?php echo $this->_tpl_vars['extra']; ?>

</div>

<?php $this->_smarty_vars['capture']['mainbox'] = ob_get_contents(); ob_end_clean(); ?>

<?php if ($this->_tpl_vars['object'] == 'product'): ?>
	<?php echo $this->_smarty_vars['capture']['mainbox']; ?>

<?php else: ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/mainbox.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('global_options', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['mainbox'],'tools' => $this->_smarty_vars['capture']['tools'],'select_language' => $this->_tpl_vars['select_language'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
