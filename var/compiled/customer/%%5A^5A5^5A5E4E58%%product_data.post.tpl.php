<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:13
         compiled from addons/buy_together/hooks/products/product_data.post.tpl */ ?>
<?php  ob_start();  ?>
<?php if ($this->_tpl_vars['bt_chain'] || $this->_tpl_vars['bt_id']): ?>
	<div class="object-container cm-reload-<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" id="buy_together_options_update_<?php echo $this->_tpl_vars['bt_chain']; ?>
_<?php echo $this->_tpl_vars['bt_id']; ?>
">
		<?php $this->assign('product_options', "product_options_".($this->_tpl_vars['obj_id']), false); ?>
		<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['product_options']]; ?>

	<!--buy_together_options_update_<?php echo $this->_tpl_vars['bt_chain']; ?>
_<?php echo $this->_tpl_vars['bt_id']; ?>
--></div>
<?php endif; ?><?php  ob_end_flush();  ?>