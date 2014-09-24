<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:17
         compiled from views/products/components/products_update_options.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'views/products/components/products_update_options.tpl', 6, false),array('function', 'script', 'views/products/components/products_update_options.tpl', 57, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('global_options','vendor','apply_as_link','apply','add_global_option','add_global_option','forbidden_combinations','allowed_combinations','option_combinations','text_options_no_inventory','note','option_combinations'));
?>

<?php ob_start(); ?>
	<?php if ($this->_tpl_vars['global_options']): ?>
		<?php ob_start(); ?>
		<form action="<?php echo fn_url(""); ?>
" method="post" name="apply_global_option">
		<input type="hidden" name="product_id" value="<?php echo $this->_tpl_vars['_REQUEST']['product_id']; ?>
" />
		<input type="hidden" name="selected_section" value="options" />
							
		<div class="object-container">
			<div class="form-field">
				<label for="global_option_id"><?php echo fn_get_lang_var('global_options', $this->getLanguage()); ?>
:</label>
				<select name="global_option[id]" id="global_option_id">
					<?php $_from = $this->_tpl_vars['global_options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['option_id'] => $this->_tpl_vars['option_']):
?>
						<option value="<?php echo $this->_tpl_vars['option_id']; ?>
"><?php echo $this->_tpl_vars['option_']['option_name']; ?>
<?php if ($this->_tpl_vars['option_']['company_id']): ?> (<?php echo fn_get_lang_var('vendor', $this->getLanguage()); ?>
: <?php echo $this->_tpl_vars['s_companies'][$this->_tpl_vars['option_']['company_id']]['company']; ?>
)<?php endif; ?></option>
					<?php endforeach; endif; unset($_from); ?>
				</select>
			</div>

			<div class="form-field">
				<label for="global_option_link"><?php echo fn_get_lang_var('apply_as_link', $this->getLanguage()); ?>
:</label>
				<input type="hidden" name="global_option[link]" value="N" />
				<input type="checkbox" name="global_option[link]" id="global_option_link" value="Y" class="checkbox" />
			</div>
		</div>

		<div class="buttons-container">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('apply', $this->getLanguage()),'but_name' => "dispatch[products.apply_global_option]",'cancel_action' => 'close')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>

		</form>
		<?php $this->_smarty_vars['capture']['add_global_option'] = ob_get_contents(); ob_end_clean(); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => 'add_global_option','text' => fn_get_lang_var('add_global_option', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['add_global_option'],'link_text' => fn_get_lang_var('add_global_option', $this->getLanguage()),'act' => 'general')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>

	<?php if ($this->_tpl_vars['product_options']): ?>
		<?php if ($this->_tpl_vars['product_data']['exceptions_type'] == 'F'): ?>
			<?php $this->assign('except_title', fn_get_lang_var('forbidden_combinations', $this->getLanguage()), false); ?>
		<?php else: ?>
			<?php $this->assign('except_title', fn_get_lang_var('allowed_combinations', $this->getLanguage()), false); ?>
		<?php endif; ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => $this->_tpl_vars['except_title'],'but_href' => "product_options.exceptions?product_id=".($this->_tpl_vars['product_data']['product_id']),'but_role' => 'text')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php if ($this->_tpl_vars['has_inventory']): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('option_combinations', $this->getLanguage()),'but_href' => "product_options.inventory?product_id=".($this->_tpl_vars['product_data']['product_id']),'but_role' => 'text')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php else: ?>
			<?php ob_start(); ?>
				<div class="object-container">
					<?php echo fn_get_lang_var('text_options_no_inventory', $this->getLanguage()); ?>

				</div>
			<?php $this->_smarty_vars['capture']['notes_picker'] = ob_get_contents(); ob_end_clean(); ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('act' => 'button','id' => 'content_option_combinations','text' => fn_get_lang_var('note', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['notes_picker'],'link_text' => fn_get_lang_var('option_combinations', $this->getLanguage()),'but_href' => "product_options.inventory?product_id=".($this->_tpl_vars['product_data']['product_id']),'but_role' => 'text','extra_act' => 'notes')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>
	<?php endif; ?>
<?php $this->_smarty_vars['capture']['extra'] = ob_get_contents(); ob_end_clean(); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/product_options/manage.tpl", 'smarty_include_vars' => array('object' => 'product','extra' => $this->_smarty_vars['capture']['extra'],'product_id' => $this->_tpl_vars['_REQUEST']['product_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>