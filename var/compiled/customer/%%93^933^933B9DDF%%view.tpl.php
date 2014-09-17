<?php /* Smarty version 2.6.18, created on 2014-09-17 00:24:29
         compiled from views/products/view.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'hook', 'views/products/view.tpl', 9, false),array('modifier', 'fn_get_product_details_layout', 'views/products/view.tpl', 10, false),)), $this); ?>

<?php ob_start(); ?><?php $this->_smarty_vars['capture']['val_hide_form'] = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?><?php $this->_smarty_vars['capture']['val_capture_options_vs_qty'] = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?><?php $this->_smarty_vars['capture']['val_capture_buttons'] = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?><?php $this->_smarty_vars['capture']['val_separate_buttons'] = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?><?php $this->_smarty_vars['capture']['val_no_ajax'] = ob_get_contents(); ob_end_clean(); ?>

<?php $this->_tag_stack[] = array('hook', array('name' => "products:layout_content")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php if ($this->_tpl_vars['addons']['recurring_billing']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/recurring_billing/hooks/products/layout_content.pre.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => fn_get_product_details_layout("blocks/product_templates/", $this->_tpl_vars['product']['product_id']), 'smarty_include_vars' => array('product' => $this->_tpl_vars['product'],'show_sku' => true,'show_rating' => true,'show_old_price' => true,'show_price' => true,'show_list_discount' => true,'show_clean_price' => true,'details_page' => true,'show_discount_label' => true,'show_product_amount' => true,'show_product_options' => true,'hide_form' => $this->_smarty_vars['capture']['val_hide_form'],'show_qty' => true,'min_qty' => true,'show_edp' => true,'show_add_to_cart' => true,'show_list_buttons' => true,'but_role' => 'action','capture_buttons' => $this->_smarty_vars['capture']['val_capture_buttons'],'capture_options_vs_qty' => $this->_smarty_vars['capture']['val_capture_options_vs_qty'],'separate_buttons' => $this->_smarty_vars['capture']['val_separate_buttons'],'block_width' => true,'no_ajax' => $this->_smarty_vars['capture']['val_no_ajax'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>