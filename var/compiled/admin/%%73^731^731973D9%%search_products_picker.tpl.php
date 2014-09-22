<?php /* Smarty version 2.6.18, created on 2014-09-22 23:31:47
         compiled from pickers/search_products_picker.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'explode', 'pickers/search_products_picker.tpl', 4, false),array('modifier', 'fn_get_views', 'pickers/search_products_picker.tpl', 9, false),array('modifier', 'escape', 'pickers/search_products_picker.tpl', 15, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('any_of','add','or_saved_search'));
?>

<?php if ($this->_tpl_vars['search']['p_ids']): ?>
	<?php $this->assign('product_ids', explode(",", $this->_tpl_vars['search']['p_ids']), false); ?>
<?php endif; ?>
<div class="info-line">
	<?php echo fn_get_lang_var('any_of', $this->getLanguage()); ?>
&nbsp;
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/products_picker.tpl", 'smarty_include_vars' => array('data_id' => 'added_products','but_text' => fn_get_lang_var('add', $this->getLanguage()),'item_ids' => $this->_tpl_vars['product_ids'],'input_name' => 'p_ids','type' => 'links','no_container' => true,'picker_view' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php $this->assign('views', fn_get_views('products'), false); ?>
	<?php if ($this->_tpl_vars['views']): ?>
	<?php echo fn_get_lang_var('or_saved_search', $this->getLanguage()); ?>
:&nbsp;
	<select name="product_view_id">
		<option value="0">--</option>
		<?php $_from = $this->_tpl_vars['views']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['f']):
?>
			<option value="<?php echo $this->_tpl_vars['f']['view_id']; ?>
" <?php if ($this->_tpl_vars['search']['product_view_id'] == $this->_tpl_vars['f']['view_id']): ?>selected="selected"<?php endif; ?>><?php echo smarty_modifier_escape($this->_tpl_vars['f']['name']); ?>
</option>
		<?php endforeach; endif; unset($_from); ?>
	</select>
	<?php endif; ?>
</div>