<?php /* Smarty version 2.6.18, created on 2014-09-16 22:59:29
         compiled from views/products/global_update.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'views/products/global_update.tpl', 5, false),array('modifier', 'replace', 'views/products/global_update.tpl', 36, false),array('block', 'hook', 'views/products/global_update.tpl', 32, false),array('function', 'script', 'views/products/global_update.tpl', 45, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('global_update_description','price','list_price','in_stock','products','text_all_items_included','products','apply','global_update'));
?>

<?php ob_start(); ?>

<form action="<?php echo fn_url(""); ?>
" method="post" name="global_update_form" class="cm-form-highlight"/>

<p><?php echo fn_get_lang_var('global_update_description', $this->getLanguage()); ?>
</p>

<div class="form-field">
	<label for="gu_price"><?php echo fn_get_lang_var('price', $this->getLanguage()); ?>
:</label>
	<input type="text" id="gu_price" name="update_data[price]" size="6" value="" class="input-text-medium" />
	<select name="update_data[price_type]">
		<option value="A" ><?php echo $this->_tpl_vars['currencies'][$this->_tpl_vars['primary_currency']]['symbol']; ?>
</option>
		<option value="P" >%</option>
	</select>
</div>

<div class="form-field">
	<label for="gu_list_price"><?php echo fn_get_lang_var('list_price', $this->getLanguage()); ?>
:</label>
	<input type="text" id="gu_list_price" name="update_data[list_price]" size="6" value="" class="input-text-medium" />
	<select name="update_data[list_price_type]">
		<option value="A" ><?php echo $this->_tpl_vars['currencies'][$this->_tpl_vars['primary_currency']]['symbol']; ?>
</option>
		<option value="P" >%</option>
	</select>
</div>

<div class="form-field">
	<label for="gu_in_stock"><?php echo fn_get_lang_var('in_stock', $this->getLanguage()); ?>
:</label>
	<input type="text" id="gu_in_stock" name="update_data[amount]" size="6" value="" class="input-text" />
</div>

<?php $this->_tag_stack[] = array('hook', array('name' => "products:global_update")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php if ($this->_tpl_vars['addons']['reward_points']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/reward_points/hooks/products/global_update.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('products', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/products_picker.tpl", 'smarty_include_vars' => array('type' => 'links','input_name' => "update_data[product_ids]",'no_item_text' => smarty_modifier_replace(fn_get_lang_var('text_all_items_included', $this->getLanguage()), "[items]", fn_get_lang_var('products', $this->getLanguage())))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="buttons-container buttons-bg">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('apply', $this->getLanguage()),'but_role' => 'button_main','but_name' => "dispatch[products.global_update]")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

</form>

<?php $this->_smarty_vars['capture']['mainbox'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/mainbox.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('global_update', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['mainbox'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>