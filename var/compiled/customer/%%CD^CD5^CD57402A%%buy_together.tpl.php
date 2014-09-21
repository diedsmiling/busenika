<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:14
         compiled from addons/buy_together/blocks/product_tabs/buy_together.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'addons/buy_together/blocks/product_tabs/buy_together.tpl', 5, false),array('modifier', 'fn_url', 'addons/buy_together/blocks/product_tabs/buy_together.tpl', 12, false),array('modifier', 'unescape', 'addons/buy_together/blocks/product_tabs/buy_together.tpl', 96, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('save_and_close','specify_options','specify_options','items','save_and_close','specify_options','specify_options','items','total_list_price','price_for_all','add_all_to_cart','sign_in_to_view_price'));
?>

<?php if ($this->_tpl_vars['chains']): ?>
	<?php echo smarty_function_script(array('src' => "js/picker.js"), $this);?>

	<?php echo smarty_function_script(array('src' => "js/jquery.easydrag.js"), $this);?>

	
	<div id="content_buy_together">
	
	<?php $_from = $this->_tpl_vars['chains']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['chain']):
?>
		<?php $this->assign('obj_prefix', "bt_".($this->_tpl_vars['chain']['chain_id']), false); ?>
		<form <?php if ($this->_tpl_vars['settings']['DHTML']['ajax_add_to_cart'] == 'Y' && ! $this->_tpl_vars['no_ajax']): ?>class="cm-ajax"<?php endif; ?> action="<?php echo fn_url(""); ?>
" method="post" name="chain_form_<?php echo $this->_tpl_vars['chain']['chain_id']; ?>
" enctype="multipart/form-data">
		<input type="hidden" name="result_ids" value="cart_status,wish_list" />
		<?php if (! $this->_tpl_vars['stay_in_cart']): ?>
			<input type="hidden" name="redirect_url" value="<?php echo $this->_tpl_vars['config']['current_url']; ?>
" />
		<?php endif; ?>
		<input type="hidden" name="product_data[<?php echo $this->_tpl_vars['chain']['product_id']; ?>
_<?php echo $this->_tpl_vars['chain']['chain_id']; ?>
][chain]" value="<?php echo $this->_tpl_vars['chain']['chain_id']; ?>
" />
		<input type="hidden" name="product_data[<?php echo $this->_tpl_vars['chain']['product_id']; ?>
_<?php echo $this->_tpl_vars['chain']['chain_id']; ?>
][product_id]" value="<?php echo $this->_tpl_vars['chain']['product_id']; ?>
" />
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['chain']['name'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<div class="chain-content clear">
			<div class="chain-products scroll-x clear nowrap">
			<?php if ($this->_tpl_vars['chain']['products']): ?>
				<div class="chain-product">
					<a href="<?php echo fn_url("products.view?product_id=".($this->_tpl_vars['chain']['product_id'])); ?>
"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image.tpl", 'smarty_include_vars' => array('image_width' => $this->_tpl_vars['settings']['Thumbnails']['product_lists_thumbnail_width'],'image_height' => $this->_tpl_vars['settings']['Thumbnails']['product_lists_thumbnail_height'],'obj_id' => ($this->_tpl_vars['chain']['chain_id'])."_".($this->_tpl_vars['chain']['product_id']),'images' => $this->_tpl_vars['chain']['main_pair'],'object_type' => 'product','show_thumbnail' => 'Y')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></a>
					<div class="chain-note"><?php echo $this->_tpl_vars['chain']['product_name']; ?>
</div>
					<?php if ($this->_tpl_vars['chain']['product_options']): ?>
						<?php ob_start(); ?>
							<div id="buy_together_options_<?php echo $this->_tpl_vars['chain']['chain_id']; ?>
_<?php echo $this->_tpl_vars['key']; ?>
" class="chain-product-options">
								<div class="object-container cm-reload-<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['chain']['product_id']; ?>
_<?php echo $this->_tpl_vars['chain']['chain_id']; ?>
" id="buy_together_options_update_<?php echo $this->_tpl_vars['chain']['chain_id']; ?>
_<?php echo $this->_tpl_vars['key']; ?>
">
									<input type="hidden" name="appearance[show_product_options]" value="1" />
									<input type="hidden" name="appearance[bt_chain]" value="<?php echo $this->_tpl_vars['chain']['chain_id']; ?>
" />
									<input type="hidden" name="appearance[bt_id]" value="<?php echo $this->_tpl_vars['key']; ?>
" />
									
									<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/product_options.tpl", 'smarty_include_vars' => array('id' => ($this->_tpl_vars['chain']['product_id'])."_".($this->_tpl_vars['chain']['chain_id']),'product_options' => $this->_tpl_vars['chain']['product_options'],'name' => 'product_data','no_script' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
								</div>
								<div class="buttons-container">
									<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_id' => 'add_item_close','but_name' => "",'but_text' => fn_get_lang_var('save_and_close', $this->getLanguage()),'but_role' => 'action','but_meta' => "cm-popup-switch cm-cancel")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
								</div>
							</div>
						<?php $this->_smarty_vars['capture']['buy_together_product_options'] = ob_get_contents(); ob_end_clean(); ?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => "buy_together_options_".($this->_tpl_vars['chain']['chain_id'])."_".($this->_tpl_vars['key']),'text' => fn_get_lang_var('specify_options', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['buy_together_product_options'],'link_text' => fn_get_lang_var('specify_options', $this->getLanguage()),'act' => 'general')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					<?php endif; ?>
					<div class="chain-note"><strong><?php echo $this->_tpl_vars['chain']['min_qty']; ?>
</strong>&nbsp;<?php echo fn_get_lang_var('items', $this->getLanguage()); ?>
</div>

					<?php if (! ( ! $this->_tpl_vars['auth']['user_id'] && $this->_tpl_vars['settings']['General']['allow_anonymous_shopping'] == 'P' )): ?>
					<?php if ($this->_tpl_vars['chain']['price'] != $this->_tpl_vars['chain']['discounted_price']): ?>
						<div class="chain-note"><strike><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['chain']['price'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></strike></div>
					<?php endif; ?>
					<div class="chain-note"><strong><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['chain']['discounted_price'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></strong></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			
			<?php $_from = $this->_tpl_vars['chain']['products']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['_id'] => $this->_tpl_vars['_product']):
?>
				<div class="chain-plus">+</div>
				
				<div class="chain-product">
					<input type="hidden" name="product_data[<?php echo $this->_tpl_vars['_product']['product_id']; ?>
][product_id]" value="<?php echo $this->_tpl_vars['_product']['product_id']; ?>
" />
					<a href="<?php echo fn_url("products.view?product_id=".($this->_tpl_vars['_product']['product_id'])); ?>
"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image.tpl", 'smarty_include_vars' => array('image_width' => $this->_tpl_vars['settings']['Thumbnails']['product_lists_thumbnail_width'],'image_height' => $this->_tpl_vars['settings']['Thumbnails']['product_lists_thumbnail_height'],'obj_id' => ($this->_tpl_vars['chain']['chain_id'])."_".($this->_tpl_vars['_product']['product_id']),'images' => $this->_tpl_vars['_product']['main_pair'],'object_type' => 'product','show_thumbnail' => 'Y')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></a>
					<div class="chain-note"><?php echo $this->_tpl_vars['_product']['product_name']; ?>
</div>
					<?php if ($this->_tpl_vars['_product']['product_options']): ?>
						<?php $_from = $this->_tpl_vars['_product']['product_options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['option']):
?>
							<div class="chain-note"><strong><?php echo $this->_tpl_vars['option']['option_name']; ?>
</strong>: <?php echo $this->_tpl_vars['option']['variant_name']; ?>
</div>
						<?php endforeach; endif; unset($_from); ?>
					<?php elseif ($this->_tpl_vars['_product']['aoc']): ?>
						<?php ob_start(); ?>
							<div id="buy_together_options_<?php echo $this->_tpl_vars['chain']['chain_id']; ?>
_<?php echo $this->_tpl_vars['_product']['product_id']; ?>
" class="chain-product-options">
								<div class="object-container cm-reload-<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['_product']['product_id']; ?>
" id="buy_together_options_update_<?php echo $this->_tpl_vars['chain']['chain_id']; ?>
_<?php echo $this->_tpl_vars['_id']; ?>
">
									<input type="hidden" name="appearance[show_product_options]" value="1" />
									<input type="hidden" name="appearance[bt_chain]" value="<?php echo $this->_tpl_vars['chain']['chain_id']; ?>
" />
									<input type="hidden" name="appearance[bt_id]" value="<?php echo $this->_tpl_vars['_id']; ?>
" />
									<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/product_options.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['_product']['product_id'],'product_options' => $this->_tpl_vars['_product']['options'],'name' => 'product_data','no_script' => true,'product' => $this->_tpl_vars['_product'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
								</div>
								<div class="buttons-container">
									<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_id' => 'add_item_close','but_name' => "",'but_text' => fn_get_lang_var('save_and_close', $this->getLanguage()),'but_role' => 'action','but_meta' => "cm-popup-switch cm-cancel")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
								</div>
							</div>
						<?php $this->_smarty_vars['capture']['buy_together_product_options'] = ob_get_contents(); ob_end_clean(); ?>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => "buy_together_options_".($this->_tpl_vars['chain']['chain_id'])."_".($this->_tpl_vars['_product']['product_id']),'text' => fn_get_lang_var('specify_options', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['buy_together_product_options'],'link_text' => fn_get_lang_var('specify_options', $this->getLanguage()),'act' => 'general')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						
					<?php endif; ?>
					<div class="chain-note"><strong><?php echo $this->_tpl_vars['_product']['amount']; ?>
</strong>&nbsp;<?php echo fn_get_lang_var('items', $this->getLanguage()); ?>
</div>

					<?php if (! ( ! $this->_tpl_vars['auth']['user_id'] && $this->_tpl_vars['settings']['General']['allow_anonymous_shopping'] == 'P' )): ?>
					<?php if ($this->_tpl_vars['_product']['price'] != $this->_tpl_vars['_product']['discounted_price']): ?>
						<div class="chain-note"><strike><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['_product']['price'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></strike></div>
					<?php endif; ?>
					<div class="chain-note"><strong><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['_product']['discounted_price'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></strong></div>
					<?php endif; ?>
				</div>
			<?php endforeach; endif; unset($_from); ?>
			</div>
			
			<?php if ($this->_tpl_vars['chain']['description']): ?>
				<div class="chain-description">
					<?php echo smarty_modifier_unescape($this->_tpl_vars['chain']['description']); ?>

				</div>
			<?php endif; ?>
			
			<?php if (! ( ! $this->_tpl_vars['auth']['user_id'] && $this->_tpl_vars['settings']['General']['allow_anonymous_shopping'] == 'P' )): ?>
			<div class="chain-price">
				<div class="chain-old-price">
					<span class="chain-old"><?php echo fn_get_lang_var('total_list_price', $this->getLanguage()); ?>
:</span>
					<span class="chain-old-line"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['chain']['total_price'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></span>
				</div>
				<div class="chain-new-price">
					<span class="chain-new"><?php echo fn_get_lang_var('price_for_all', $this->getLanguage()); ?>
:</span>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['chain']['chain_price'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				</div>
				
				<?php if (! ( ! $this->_tpl_vars['auth']['user_id'] && $this->_tpl_vars['settings']['General']['allow_anonymous_shopping'] == 'B' )): ?>
					<div width="100%" class="right">
						<span class="button-submit-action chain-button" id="wrap_chain_button_<?php echo $this->_tpl_vars['chain']['chain_id']; ?>
">
							<input type="submit" value="<?php echo fn_get_lang_var('add_all_to_cart', $this->getLanguage()); ?>
" name="dispatch[checkout.add]" id="chain_button_<?php echo $this->_tpl_vars['chain']['chain_id']; ?>
" />
						</span>
					</div>
				<?php endif; ?>
			</div>
			<?php else: ?>
			<p class="price"><?php echo fn_get_lang_var('sign_in_to_view_price', $this->getLanguage()); ?>
</p>
			<?php endif; ?>
		</div>
		
		</form>
	<?php endforeach; endif; unset($_from); ?>
	
	</div>
<?php endif; ?>