<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:17
         compiled from addons/buy_together/hooks/product_picker/table_header.post.tpl */ ?>
<?php
fn_preload_lang_vars(array('price','discount','value','discounted_price'));
?>
<?php  ob_start();  ?>
<?php if ($this->_tpl_vars['controller'] == 'buy_together' || $this->_tpl_vars['extra_mode'] == 'buy_together'): ?>
	<th><?php echo fn_get_lang_var('price', $this->getLanguage()); ?>
</th>
	<th><?php echo fn_get_lang_var('discount', $this->getLanguage()); ?>
</th>
	<th><?php echo fn_get_lang_var('value', $this->getLanguage()); ?>
</th>
	<th><?php echo fn_get_lang_var('discounted_price', $this->getLanguage()); ?>
</th>
<?php endif; ?><?php  ob_end_flush();  ?>