<?php /* Smarty version 2.6.18, created on 2014-09-16 21:19:57
         compiled from blocks/list_templates/simple_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'blocks/list_templates/simple_list.tpl', 4, false),array('modifier', 'trim', 'blocks/list_templates/simple_list.tpl', 18, false),)), $this); ?>

<?php if ($this->_tpl_vars['product']): ?>
<?php $this->assign('obj_id', smarty_modifier_default(@$this->_tpl_vars['obj_id'], @$this->_tpl_vars['product']['product_id']), false); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/product_data.tpl", 'smarty_include_vars' => array('obj_id' => $this->_tpl_vars['obj_id'],'product' => $this->_tpl_vars['product'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="product-container clear">
	<?php $this->assign('form_open', "form_open_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['form_open']]; ?>

		<?php if ($this->_tpl_vars['item_number'] == 'Y'): ?><strong><?php echo $this->_foreach['products']['iteration']; ?>
.&nbsp;</strong><?php endif; ?>
		<?php $this->assign('name', "name_".($this->_tpl_vars['obj_id']), false); ?><?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['name']]; ?>

		<?php $this->assign('sku', "sku_".($this->_tpl_vars['obj_id']), false); ?><?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['sku']]; ?>

		<?php $this->assign('rating', "rating_".($this->_tpl_vars['obj_id']), false); ?><?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['rating']]; ?>

		
		<div class="prices-container clear">
		<?php if ($this->_tpl_vars['show_old_price'] || $this->_tpl_vars['show_clean_price'] || $this->_tpl_vars['show_list_discount']): ?>
			<div class="float-left product-prices">
				<?php $this->assign('old_price', "old_price_".($this->_tpl_vars['obj_id']), false); ?>
				<?php if (trim($this->_smarty_vars['capture'][$this->_tpl_vars['old_price']])): ?><?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['old_price']]; ?>
&nbsp;<?php endif; ?>
		<?php endif; ?>
		
		<?php if (! trim($this->_smarty_vars['capture'][$this->_tpl_vars['old_price']]) || $this->_tpl_vars['details_page']): ?><p><?php endif; ?>
				<?php $this->assign('price', "price_".($this->_tpl_vars['obj_id']), false); ?>
				<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['price']]; ?>

		<?php if (! trim($this->_smarty_vars['capture'][$this->_tpl_vars['old_price']]) || $this->_tpl_vars['details_page']): ?></p><?php endif; ?>

		<?php if ($this->_tpl_vars['show_old_price'] || $this->_tpl_vars['show_clean_price'] || $this->_tpl_vars['show_list_discount']): ?>
				<?php $this->assign('clean_price', "clean_price_".($this->_tpl_vars['obj_id']), false); ?>
				<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['clean_price']]; ?>

				
				<?php $this->assign('list_discount', "list_discount_".($this->_tpl_vars['obj_id']), false); ?>
				<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['list_discount']]; ?>

			</div>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['show_discount_label']): ?>
			<div class="float-left">
				<?php $this->assign('discount_label', "discount_label_".($this->_tpl_vars['obj_id']), false); ?>
				<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['discount_label']]; ?>

			</div>
		<?php endif; ?>
		</div>

		<?php if ($this->_tpl_vars['capture_options_vs_qty']): ?><?php ob_start(); ?><?php endif; ?>
		<?php $this->assign('product_amount', "product_amount_".($this->_tpl_vars['obj_id']), false); ?>
		<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['product_amount']]; ?>

		
		<?php if ($this->_tpl_vars['show_features'] || $this->_tpl_vars['show_descr']): ?>
			<p class="product-descr"><strong><?php $this->assign('product_features', "product_features_".($this->_tpl_vars['obj_id']), false); ?><?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['product_features']]; ?>
</strong><?php $this->assign('prod_descr', "prod_descr_".($this->_tpl_vars['obj_id']), false); ?><?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['prod_descr']]; ?>
</p>
		<?php endif; ?>
		
		<?php $this->assign('product_options', "product_options_".($this->_tpl_vars['obj_id']), false); ?>
		<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['product_options']]; ?>

		
		<?php $this->assign('qty', "qty_".($this->_tpl_vars['obj_id']), false); ?>
		<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['qty']]; ?>

		
		<?php $this->assign('advanced_options', "advanced_options_".($this->_tpl_vars['obj_id']), false); ?>
		<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['advanced_options']]; ?>

		<?php if ($this->_tpl_vars['capture_options_vs_qty']): ?><?php $this->_smarty_vars['capture']['product_options'] = ob_get_contents(); ob_end_clean(); ?><?php endif; ?>
		
		<?php $this->assign('min_qty', "min_qty_".($this->_tpl_vars['obj_id']), false); ?>
		<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['min_qty']]; ?>

		
		<?php $this->assign('product_edp', "product_edp_".($this->_tpl_vars['obj_id']), false); ?>
		<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['product_edp']]; ?>


		<?php if ($this->_tpl_vars['capture_buttons']): ?><?php ob_start(); ?><?php endif; ?>
		<div class="buttons-container">
			<?php $this->assign('add_to_cart', "add_to_cart_".($this->_tpl_vars['obj_id']), false); ?>
			<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['add_to_cart']]; ?>

			
			<?php $this->assign('list_buttons', "list_buttons_".($this->_tpl_vars['obj_id']), false); ?>
			<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['list_buttons']]; ?>

		</div>
		<?php if ($this->_tpl_vars['capture_buttons']): ?><?php $this->_smarty_vars['capture']['buttons'] = ob_get_contents(); ob_end_clean(); ?><?php endif; ?>
	<?php $this->assign('form_close', "form_close_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['form_close']]; ?>

</div>

<?php endif; ?>