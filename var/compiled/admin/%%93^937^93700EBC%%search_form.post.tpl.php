<?php /* Smarty version 2.6.18, created on 2014-09-15 23:39:41
         compiled from addons/bestsellers/hooks/products/search_form.post.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'addons/bestsellers/hooks/products/search_form.post.tpl', 6, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('sales_amount'));
?>
<?php  ob_start();  ?>
<div class="search-field">
	<label for="sales_amount_from"><?php echo fn_get_lang_var('sales_amount', $this->getLanguage()); ?>
:</label>
	<input type="text" name="sales_amount_from" id="sales_amount_from" value="<?php echo $this->_tpl_vars['search']['sales_amount_from']; ?>
" onfocus="this.select();" class="input-text" />&nbsp;&ndash;&nbsp;<input type="text" name="sales_amount_to" value="<?php echo $this->_tpl_vars['search']['sales_amount_to']; ?>
" onfocus="this.select();" class="input-text" />
</div><?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>
<?php  ob_end_flush();  ?>