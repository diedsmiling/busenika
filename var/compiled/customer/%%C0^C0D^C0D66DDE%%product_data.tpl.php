<?php /* Smarty version 2.6.18, created on 2014-09-16 21:19:58
         compiled from common_templates/product_data.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'floatval', 'common_templates/product_data.tpl', 3, false),array('modifier', 'default', 'common_templates/product_data.tpl', 11, false),array('modifier', 'fn_url', 'common_templates/product_data.tpl', 16, false),array('modifier', 'unescape', 'common_templates/product_data.tpl', 31, false),array('modifier', 'strip_tags', 'common_templates/product_data.tpl', 33, false),array('modifier', 'truncate', 'common_templates/product_data.tpl', 33, false),array('modifier', 'trim', 'common_templates/product_data.tpl', 72, false),array('modifier', 'fn_get_product_features_list', 'common_templates/product_data.tpl', 117, false),array('modifier', 'escape', 'common_templates/product_data.tpl', 117, false),array('modifier', 'strlen', 'common_templates/product_data.tpl', 131, false),array('modifier', 'replace', 'common_templates/product_data.tpl', 364, false),array('block', 'hook', 'common_templates/product_data.tpl', 57, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('sku','select_options','text_out_of_stock','more','old_price','list_price','price','enter_your_price','contact_us_for_price','sign_in_to_view_price','inc_tax','including_tax','you_save','you_save','in_stock','items','text_out_of_stock','in_stock','text_out_of_stock','quantity','text_cart_min_qty','text_edp_product'));
?>

<?php if (( floatval($this->_tpl_vars['product']['price']) || $this->_tpl_vars['product']['zero_price_action'] == 'P' || $this->_tpl_vars['product']['zero_price_action'] == 'A' || ( ! floatval($this->_tpl_vars['product']['price']) && $this->_tpl_vars['product']['zero_price_action'] == 'R' ) ) && ! ( $this->_tpl_vars['settings']['General']['allow_anonymous_shopping'] == 'P' && ! $this->_tpl_vars['auth']['user_id'] )): ?>
	<?php $this->assign('show_price_values', true, false); ?>
<?php else: ?>
	<?php $this->assign('show_price_values', false, false); ?>
<?php endif; ?>
<?php ob_start(); ?><?php echo $this->_tpl_vars['show_price_values']; ?>
<?php $this->_smarty_vars['capture']['show_price_values'] = ob_get_contents(); ob_end_clean(); ?>

<?php $this->assign('cart_button_exists', false, false); ?>
<?php $this->assign('obj_id', smarty_modifier_default(@$this->_tpl_vars['obj_id'], @$this->_tpl_vars['product']['product_id']), false); ?>
<?php $this->assign('product_amount', smarty_modifier_default(@$this->_tpl_vars['product']['inventory_amount'], @$this->_tpl_vars['product']['amount']), false); ?>

<?php ob_start(); ?>
<?php if (! $this->_tpl_vars['hide_form']): ?>
<form <?php if ($this->_tpl_vars['settings']['DHTML']['ajax_add_to_cart'] == 'Y' && ! $this->_tpl_vars['no_ajax']): ?>class="cm-ajax"<?php endif; ?> action="<?php echo fn_url(""); ?>
" method="post" name="product_form_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" enctype="multipart/form-data" class="cm-disable-empty-files">
<input type="hidden" name="result_ids" value="cart_status,wish_list" />
<?php if (! $this->_tpl_vars['stay_in_cart']): ?>
<input type="hidden" name="redirect_url" value="<?php echo $this->_tpl_vars['config']['current_url']; ?>
" />
<?php endif; ?>
<input type="hidden" name="product_data[<?php echo $this->_tpl_vars['obj_id']; ?>
][product_id]" value="<?php echo $this->_tpl_vars['product']['product_id']; ?>
" />
<?php endif; ?>
<?php $this->_smarty_vars['capture']["form_open_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "form_open_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
	<?php if ($this->_tpl_vars['show_name']): ?>
		<?php if ($this->_tpl_vars['hide_links']): ?><strong><?php else: ?><a href="<?php echo fn_url("products.view?product_id=".($this->_tpl_vars['product']['product_id'])); ?>
" class="product-title"><?php endif; ?><?php echo smarty_modifier_unescape($this->_tpl_vars['product']['product']); ?>
<?php if ($this->_tpl_vars['hide_links']): ?></strong><?php else: ?></a><?php endif; ?>
	<?php elseif ($this->_tpl_vars['show_trunc_name']): ?>
		<?php if ($this->_tpl_vars['hide_links']): ?><strong><?php else: ?><a href="<?php echo fn_url("products.view?product_id=".($this->_tpl_vars['product']['product_id'])); ?>
" class="product-title" title="<?php echo smarty_modifier_strip_tags($this->_tpl_vars['product']['product']); ?>
"><?php endif; ?><?php echo smarty_modifier_truncate(smarty_modifier_unescape($this->_tpl_vars['product']['product']), 75, "...", true); ?>
<?php if ($this->_tpl_vars['hide_links']): ?></strong><?php else: ?></a><?php endif; ?>
	<?php endif; ?>
<?php $this->_smarty_vars['capture']["name_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "name_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
	<?php if ($this->_tpl_vars['show_sku']): ?>
		<p class="sku<?php if (! $this->_tpl_vars['product']['product_code']): ?> hidden<?php endif; ?>">
			<span class="cm-reload-<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" id="sku_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">
				<input type="hidden" name="appearance[show_sku]" value="<?php echo $this->_tpl_vars['show_sku']; ?>
" />
				<span id="sku_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
"><?php echo fn_get_lang_var('sku', $this->getLanguage()); ?>
: <span id="product_code_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
"><?php echo $this->_tpl_vars['product']['product_code']; ?>
</span></span>
			<!--sku_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
--></span>
		</p>
	<?php endif; ?>
<?php $this->_smarty_vars['capture']["sku_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "sku_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
	<?php $this->_tag_stack[] = array('hook', array('name' => "products:data_block")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php if ($this->_tpl_vars['addons']['discussion']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/discussion/hooks/products/data_block.pre.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?>
	<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php $this->_smarty_vars['capture']["rating_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "rating_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
<?php if ($this->_tpl_vars['show_add_to_cart']): ?>
<div class="cm-reload-<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" id="add_to_cart_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">
<input type="hidden" name="appearance[show_add_to_cart]" value="<?php echo $this->_tpl_vars['show_add_to_cart']; ?>
" />
<input type="hidden" name="appearance[separate_buttons]" value="<?php echo $this->_tpl_vars['separate_buttons']; ?>
" />
<input type="hidden" name="appearance[show_list_buttons]" value="<?php echo $this->_tpl_vars['show_list_buttons']; ?>
" />
<input type="hidden" name="appearance[but_role]" value="<?php echo $this->_tpl_vars['but_role']; ?>
" />
<?php if ($this->_tpl_vars['addons']['product_configurator']['status'] == 'A'): ?><?php ob_start(); $this->_in_capture[] = '8bafe07ab63b336ab024574d533dd40e';
$_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/product_configurator/hooks/products/buttons_block.override.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
$this->_tpl_vars['addon_content'] = ob_get_contents(); ob_end_clean(); array_pop($this->_in_capture); if (!empty($this->_scripts['8bafe07ab63b336ab024574d533dd40e'])) { echo implode("\n", $this->_scripts['8bafe07ab63b336ab024574d533dd40e']); unset($this->_scripts['8bafe07ab63b336ab024574d533dd40e']); }
 ?><?php else: ?><?php $this->assign('addon_content', "", false); ?><?php endif; ?><?php if (trim($this->_tpl_vars['addon_content'])): ?><?php echo $this->_tpl_vars['addon_content']; ?>
<?php else: ?><?php $this->_tag_stack[] = array('hook', array('name' => "products:buttons_block")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
	<?php if (! ( $this->_tpl_vars['product']['zero_price_action'] == 'R' && $this->_tpl_vars['product']['price'] == 0 ) && ! ( $this->_tpl_vars['settings']['General']['inventory_tracking'] == 'Y' && $this->_tpl_vars['settings']['General']['allow_negative_amount'] != 'Y' && ( $this->_tpl_vars['product_amount'] <= 0 || $this->_tpl_vars['product_amount'] < $this->_tpl_vars['product']['min_qty'] ) && $this->_tpl_vars['product']['is_edp'] != 'Y' ) || ( $this->_tpl_vars['product']['has_options'] && ! $this->_tpl_vars['show_product_options'] )): ?>
		<<?php if ($this->_tpl_vars['separate_buttons']): ?>div class="buttons-container"<?php else: ?>span<?php endif; ?> id="cart_add_block_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">
			<?php if ($this->_tpl_vars['product']['avail_since'] <= @TIME || ( $this->_tpl_vars['product']['avail_since'] > @TIME && $this->_tpl_vars['product']['buy_in_advance'] == 'Y' )): ?>
				<?php if ($this->_tpl_vars['addons']['recurring_billing']['status'] == 'A'): ?><?php ob_start(); $this->_in_capture[] = '3873156d53acf01e8b9c41d836c88da9';
$_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/recurring_billing/hooks/products/add_to_cart.override.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
$this->_tpl_vars['addon_content'] = ob_get_contents(); ob_end_clean(); array_pop($this->_in_capture); if (!empty($this->_scripts['3873156d53acf01e8b9c41d836c88da9'])) { echo implode("\n", $this->_scripts['3873156d53acf01e8b9c41d836c88da9']); unset($this->_scripts['3873156d53acf01e8b9c41d836c88da9']); }
 ?><?php else: ?><?php $this->assign('addon_content', "", false); ?><?php endif; ?><?php if (trim($this->_tpl_vars['addon_content'])): ?><?php echo $this->_tpl_vars['addon_content']; ?>
<?php else: ?><?php $this->_tag_stack[] = array('hook', array('name' => "products:add_to_cart")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
				<?php if ($this->_tpl_vars['product']['has_options'] && ! $this->_tpl_vars['show_product_options'] && ! $this->_tpl_vars['details_page']): ?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_id' => "button_cart_".($this->_tpl_vars['obj_prefix']).($this->_tpl_vars['obj_id']),'but_text' => fn_get_lang_var('select_options', $this->getLanguage()),'but_href' => "products.view?product_id=".($this->_tpl_vars['product']['product_id']),'but_role' => 'text','but_name' => "")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php else: ?>
					<?php if ($this->_tpl_vars['extra_button']): ?><?php echo $this->_tpl_vars['extra_button']; ?>
&nbsp;<?php endif; ?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/add_to_cart.tpl", 'smarty_include_vars' => array('but_id' => "button_cart_".($this->_tpl_vars['obj_prefix']).($this->_tpl_vars['obj_id']),'but_name' => "dispatch[checkout.add..".($this->_tpl_vars['obj_id'])."]",'but_role' => $this->_tpl_vars['but_role'],'block_width' => $this->_tpl_vars['block_width'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					<?php $this->assign('cart_button_exists', true, false); ?>
				<?php endif; ?>
				<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php endif; ?>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['product']['avail_since'] > @TIME): ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/coming_soon_notice.tpl", 'smarty_include_vars' => array('avail_date' => $this->_tpl_vars['product']['avail_since'],'add_to_cart' => $this->_tpl_vars['product']['buy_in_advance'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?>
		</<?php if ($this->_tpl_vars['separate_buttons']): ?>div<?php else: ?>span<?php endif; ?>>
	<?php elseif (! $this->_tpl_vars['details_page'] && ( $this->_tpl_vars['settings']['General']['inventory_tracking'] == 'Y' && $this->_tpl_vars['settings']['General']['allow_negative_amount'] != 'Y' && ( $this->_tpl_vars['product_amount'] <= 0 || $this->_tpl_vars['product_amount'] < $this->_tpl_vars['product']['min_qty'] ) && $this->_tpl_vars['product']['is_edp'] != 'Y' )): ?>
		<span class="strong out-of-stock" id="out_of_stock_info_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
"><?php echo fn_get_lang_var('text_out_of_stock', $this->getLanguage()); ?>
</span>
	<?php endif; ?>

	<?php if ($this->_tpl_vars['show_list_buttons']): ?>
		<<?php if ($this->_tpl_vars['separate_buttons']): ?>div class="buttons-container"<?php else: ?>span<?php endif; ?> id="cart_buttons_block_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">
			<?php $this->_tag_stack[] = array('hook', array('name' => "products:buy_now")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
			<?php if ($this->_tpl_vars['product']['feature_comparison'] == 'Y'): ?>
				<?php if ($this->_tpl_vars['separate_buttons']): ?></div><div class="buttons-container"><?php endif; ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/add_to_compare_list.tpl", 'smarty_include_vars' => array('product_id' => $this->_tpl_vars['product']['product_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?>
			<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
		</<?php if ($this->_tpl_vars['separate_buttons']): ?>div<?php else: ?>span<?php endif; ?>>
	<?php endif; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php endif; ?>
<!--add_to_cart_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
--></div>
<?php endif; ?>
<?php $this->_smarty_vars['capture']["add_to_cart_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "add_to_cart_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
	<?php if ($this->_tpl_vars['show_features']): ?>
		<div class="cm-reload-<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" id="product_features_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">
			<input type="hidden" name="appearance[show_features]" value="<?php echo $this->_tpl_vars['show_features']; ?>
" />
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/product_features_short_list.tpl", 'smarty_include_vars' => array('features' => smarty_modifier_escape(fn_get_product_features_list($this->_tpl_vars['product']['product_id'])),'no_container' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<!--product_features_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
--></div>
	<?php endif; ?>
<?php $this->_smarty_vars['capture']["product_features_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "product_features_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
	<?php if ($this->_tpl_vars['show_descr']): ?>
		<?php if ($this->_tpl_vars['product']['short_description']): ?>
			<?php echo smarty_modifier_unescape($this->_tpl_vars['product']['short_description']); ?>

		<?php else: ?>
			<?php echo smarty_modifier_truncate(smarty_modifier_strip_tags(smarty_modifier_unescape($this->_tpl_vars['product']['full_description'])), 160); ?>
<?php if (! $this->_tpl_vars['hide_links'] && strlen($this->_tpl_vars['product']['full_description']) > 180): ?> <a href="<?php echo fn_url("products.view?product_id=".($this->_tpl_vars['product']['product_id'])); ?>
" class="lowercase"><?php echo fn_get_lang_var('more', $this->getLanguage()); ?>
</a><?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
<?php $this->_smarty_vars['capture']["prod_descr_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "prod_descr_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
	<?php if ($this->_tpl_vars['show_price_values'] && $this->_tpl_vars['show_old_price']): ?>
		<?php if ($this->_tpl_vars['product']['discount'] || $this->_tpl_vars['product']['list_discount']): ?>
		<span class="cm-reload-<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" id="old_price_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">
			<input type="hidden" name="appearance[show_price_values]" value="<?php echo $this->_tpl_vars['show_price_values']; ?>
" />
			<input type="hidden" name="appearance[show_old_price]" value="<?php echo $this->_tpl_vars['show_old_price']; ?>
" />
			<?php if ($this->_tpl_vars['product']['discount']): ?>
				<span class="list-price nowrap" id="line_old_price_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
"><?php if ($this->_tpl_vars['details_page']): ?><?php echo fn_get_lang_var('old_price', $this->getLanguage()); ?>
: <?php endif; ?><strike><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => smarty_modifier_default(@$this->_tpl_vars['product']['original_price'], @$this->_tpl_vars['product']['base_price']),'span_id' => "old_price_".($this->_tpl_vars['obj_prefix']).($this->_tpl_vars['obj_id']),'class' => "list-price nowrap")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></strike></span>
			<?php elseif ($this->_tpl_vars['product']['list_discount']): ?>
				<span class="list-price nowrap" id="line_list_price_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
"><?php if ($this->_tpl_vars['details_page']): ?><?php echo fn_get_lang_var('list_price', $this->getLanguage()); ?>
: <?php endif; ?><strike><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['product']['list_price'],'span_id' => "list_price_".($this->_tpl_vars['obj_prefix']).($this->_tpl_vars['obj_id']),'class' => "list-price nowrap")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></strike></span>
			<?php endif; ?>
		<!--old_price_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
--></span>
		<?php endif; ?>
	<?php endif; ?>
<?php $this->_smarty_vars['capture']["old_price_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "old_price_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
	<span class="cm-reload-<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" id="price_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">
		<input type="hidden" name="appearance[show_price_values]" value="<?php echo $this->_tpl_vars['show_price_values']; ?>
" />
		<input type="hidden" name="appearance[show_price]" value="<?php echo $this->_tpl_vars['show_price']; ?>
" />
		<?php if ($this->_tpl_vars['show_price_values']): ?>
			<?php if ($this->_tpl_vars['show_price']): ?>
			<?php $this->_tag_stack[] = array('hook', array('name' => "products:prices_block")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
				<?php if (floatval($this->_tpl_vars['product']['price']) || $this->_tpl_vars['product']['zero_price_action'] == 'P' || ( $this->_tpl_vars['hide_add_to_cart_button'] == 'Y' && $this->_tpl_vars['product']['zero_price_action'] == 'A' )): ?>
					<span class="price<?php if (! floatval($this->_tpl_vars['product']['price'])): ?> hidden<?php endif; ?>" id="line_discounted_price_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
"><?php if ($this->_tpl_vars['details_page']): ?><?php echo fn_get_lang_var('price', $this->getLanguage()); ?>
: <?php endif; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['product']['price'],'span_id' => "discounted_price_".($this->_tpl_vars['obj_prefix']).($this->_tpl_vars['obj_id']),'class' => 'price')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></span>
				<?php elseif ($this->_tpl_vars['product']['zero_price_action'] == 'A'): ?>
					<?php $this->assign('base_currency', $this->_tpl_vars['currencies'][@CART_PRIMARY_CURRENCY], false); ?>
					<span class="price"><?php echo fn_get_lang_var('enter_your_price', $this->getLanguage()); ?>
: <?php if ($this->_tpl_vars['base_currency']['after'] != 'Y'): ?><?php echo $this->_tpl_vars['base_currency']['symbol']; ?>
<?php endif; ?><input class="input-text-short" type="text" size="3" name="product_data[<?php echo $this->_tpl_vars['obj_id']; ?>
][price]" value="" /><?php if ($this->_tpl_vars['base_currency']['after'] == 'Y'): ?>&nbsp;<?php echo $this->_tpl_vars['base_currency']['symbol']; ?>
<?php endif; ?></span>
				<?php elseif ($this->_tpl_vars['product']['zero_price_action'] == 'R'): ?>
					<span class="price"><?php echo fn_get_lang_var('contact_us_for_price', $this->getLanguage()); ?>
</span>
				<?php endif; ?>
			<?php if ($this->_tpl_vars['addons']['recurring_billing']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/recurring_billing/hooks/products/prices_block.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
			<?php endif; ?>
		<?php elseif ($this->_tpl_vars['settings']['General']['allow_anonymous_shopping'] == 'P' && ! $this->_tpl_vars['auth']['user_id']): ?>
			<p class="price"><?php echo fn_get_lang_var('sign_in_to_view_price', $this->getLanguage()); ?>
</p>
		<?php endif; ?>
	<!--price_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
--></span>
<?php $this->_smarty_vars['capture']["price_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "price_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
	<?php if ($this->_tpl_vars['show_price_values'] && $this->_tpl_vars['show_clean_price'] && $this->_tpl_vars['settings']['Appearance']['show_prices_taxed_clean'] == 'Y' && $this->_tpl_vars['product']['taxed_price']): ?>
		<span class="cm-reload-<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" id="clean_price_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">
			<input type="hidden" name="appearance[show_price_values]" value="<?php echo $this->_tpl_vars['show_price_values']; ?>
" />
			<input type="hidden" name="appearance[show_clean_price]" value="<?php echo $this->_tpl_vars['show_clean_price']; ?>
" />
			<?php if ($this->_tpl_vars['product']['clean_price'] != $this->_tpl_vars['product']['taxed_price'] && $this->_tpl_vars['product']['included_tax']): ?>
				<span class="list-price nowrap" id="line_product_price_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">(<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['product']['taxed_price'],'span_id' => "product_price_".($this->_tpl_vars['obj_prefix']).($this->_tpl_vars['obj_id']),'class' => "list-price nowrap")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> <?php echo fn_get_lang_var('inc_tax', $this->getLanguage()); ?>
)</span>
			<?php elseif ($this->_tpl_vars['product']['clean_price'] != $this->_tpl_vars['product']['taxed_price'] && ! $this->_tpl_vars['product']['included_tax']): ?>
				<span class="list-price nowrap">(<?php echo fn_get_lang_var('including_tax', $this->getLanguage()); ?>
)</span>
			<?php endif; ?>
		<!--clean_price_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
--></span>
	<?php endif; ?>
<?php $this->_smarty_vars['capture']["clean_price_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "clean_price_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
	<?php if ($this->_tpl_vars['show_price_values'] && $this->_tpl_vars['show_list_discount'] && $this->_tpl_vars['details_page']): ?>
		<?php if ($this->_tpl_vars['product']['discount'] || $this->_tpl_vars['product']['list_discount']): ?>
			<span class="cm-reload-<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" id="line_discount_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">
				<input type="hidden" name="appearance[show_price_values]" value="<?php echo $this->_tpl_vars['show_price_values']; ?>
" />
				<input type="hidden" name="appearance[show_list_discount]" value="<?php echo $this->_tpl_vars['show_list_discount']; ?>
" />
				<?php if ($this->_tpl_vars['product']['discount']): ?>
					<span class="list-price nowrap" id="line_discount_value_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
"><?php echo fn_get_lang_var('you_save', $this->getLanguage()); ?>
: <?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['product']['discount'],'span_id' => "discount_value_".($this->_tpl_vars['obj_prefix']).($this->_tpl_vars['obj_id']),'class' => "list-price nowrap")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>&nbsp;(<span id="prc_discount_value_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" class="list-price nowrap"><?php echo $this->_tpl_vars['product']['discount_prc']; ?>
</span>%)</span>
				<?php elseif ($this->_tpl_vars['product']['list_discount']): ?>
					<span class="list-price nowrap" id="line_discount_value_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
"><?php echo fn_get_lang_var('you_save', $this->getLanguage()); ?>
: <?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['product']['list_discount'],'span_id' => "discount_value_".($this->_tpl_vars['obj_prefix']).($this->_tpl_vars['obj_id']),'class' => "list-price nowrap")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>&nbsp;(<span id="prc_discount_value_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" class="list-price nowrap"><?php echo $this->_tpl_vars['product']['list_discount_prc']; ?>
</span>%)</span>
				<?php endif; ?>
			<!--line_discount_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
--></span>
		<?php endif; ?>
	<?php endif; ?>
<?php $this->_smarty_vars['capture']["list_discount_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "list_discount_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
	<?php if ($this->_tpl_vars['show_discount_label'] && ( $this->_tpl_vars['product']['discount_prc'] || $this->_tpl_vars['product']['list_discount_prc'] ) && $this->_tpl_vars['show_price_values']): ?>
		<div class="discount-label cm-reload-<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" id="discount_label_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">
			<input type="hidden" name="appearance[show_discount_label]" value="<?php echo $this->_tpl_vars['show_discount_label']; ?>
" />
			<input type="hidden" name="appearance[show_price_values]" value="<?php echo $this->_tpl_vars['show_price_values']; ?>
" />
			<div id="line_prc_discount_value_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">
				<em><strong>-</strong><span id="prc_discount_value_label_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
"><?php if ($this->_tpl_vars['product']['discount']): ?><?php echo $this->_tpl_vars['product']['discount_prc']; ?>
<?php else: ?><?php echo $this->_tpl_vars['product']['list_discount_prc']; ?>
<?php endif; ?></span>%</em>
			</div>
		<!--discount_label_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
--></div>
	<?php endif; ?>
<?php $this->_smarty_vars['capture']["discount_label_".($this->_tpl_vars['obj_prefix']).($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "discount_label_".($this->_tpl_vars['obj_prefix']).($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
<?php if ($this->_tpl_vars['show_product_amount'] && $this->_tpl_vars['product']['is_edp'] != 'Y' && $this->_tpl_vars['settings']['General']['inventory_tracking'] == 'Y' && $this->_tpl_vars['product']['tracking'] != 'D'): ?>
	<span class="cm-reload-<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" id="product_amount_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">
		<input type="hidden" name="appearance[show_product_amount]" value="<?php echo $this->_tpl_vars['show_product_amount']; ?>
" />
		<?php if ($this->_tpl_vars['settings']['Appearance']['in_stock_field'] == 'Y'): ?>
			<?php if (( $this->_tpl_vars['product_amount'] > 0 && $this->_tpl_vars['product_amount'] >= $this->_tpl_vars['product']['min_qty'] ) && $this->_tpl_vars['settings']['General']['inventory_tracking'] == 'Y' && $this->_tpl_vars['settings']['General']['allow_negative_amount'] != 'Y' || $this->_tpl_vars['details_page']): ?>
				<?php if (( $this->_tpl_vars['product_amount'] > 0 && $this->_tpl_vars['product_amount'] >= $this->_tpl_vars['product']['min_qty'] ) && $this->_tpl_vars['settings']['General']['inventory_tracking'] == 'Y' && $this->_tpl_vars['settings']['General']['allow_negative_amount'] != 'Y'): ?>
					<div class="form-field product-list-field">
						<label><?php echo fn_get_lang_var('in_stock', $this->getLanguage()); ?>
:</label>
						<span id="qty_in_stock_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" class="qty-in-stock">
							<?php echo $this->_tpl_vars['product_amount']; ?>
&nbsp;<?php echo fn_get_lang_var('items', $this->getLanguage()); ?>

						</span>	
					</div>
				<?php else: ?>
					<p class="strong out-of-stock"><?php echo fn_get_lang_var('text_out_of_stock', $this->getLanguage()); ?>
</p>
				<?php endif; ?>
			<?php endif; ?>
		<?php else: ?>
			<?php if (( $this->_tpl_vars['product_amount'] > 0 && $this->_tpl_vars['product_amount'] >= $this->_tpl_vars['product']['min_qty'] ) && $this->_tpl_vars['settings']['General']['inventory_tracking'] == 'Y' && $this->_tpl_vars['settings']['General']['allow_negative_amount'] != 'Y'): ?>
				<span class="strong in-stock" id="in_stock_info_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
"><?php echo fn_get_lang_var('in_stock', $this->getLanguage()); ?>
</span>
			<?php elseif ($this->_tpl_vars['details_page']): ?>
				<span class="strong out-of-stock" id="out_of_stock_info_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
"><?php echo fn_get_lang_var('text_out_of_stock', $this->getLanguage()); ?>
</span>
			<?php endif; ?>
		<?php endif; ?>
	<!--product_amount_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
--></span>
<?php endif; ?>
<?php $this->_smarty_vars['capture']["product_amount_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "product_amount_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
	<?php if ($this->_tpl_vars['show_product_options']): ?>
	<div class="cm-reload-<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" id="product_options_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">
		<input type="hidden" name="appearance[show_product_options]" value="<?php echo $this->_tpl_vars['show_product_options']; ?>
" />
		<?php if ($this->_tpl_vars['addons']['product_configurator']['status'] == 'A'): ?><?php ob_start(); $this->_in_capture[] = '010df988c0f8297f113c64267c9bf81a';
$_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/product_configurator/hooks/products/product_option_content.override.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
$this->_tpl_vars['addon_content'] = ob_get_contents(); ob_end_clean(); array_pop($this->_in_capture); if (!empty($this->_scripts['010df988c0f8297f113c64267c9bf81a'])) { echo implode("\n", $this->_scripts['010df988c0f8297f113c64267c9bf81a']); unset($this->_scripts['010df988c0f8297f113c64267c9bf81a']); }
 ?><?php else: ?><?php $this->assign('addon_content', "", false); ?><?php endif; ?><?php if (trim($this->_tpl_vars['addon_content'])): ?><?php echo $this->_tpl_vars['addon_content']; ?>
<?php else: ?><?php $this->_tag_stack[] = array('hook', array('name' => "products:product_option_content")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
			<?php if ($this->_tpl_vars['disable_ids']): ?>
				<?php $this->assign('_disable_ids', ($this->_tpl_vars['disable_ids']).($this->_tpl_vars['obj_id']), false); ?>
			<?php else: ?>
				<?php $this->assign('_disable_ids', "", false); ?>
			<?php endif; ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/product_options.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['obj_id'],'product_options' => $this->_tpl_vars['product']['product_options'],'name' => 'product_data','capture_options_vs_qty' => $this->_tpl_vars['capture_options_vs_qty'],'disable_ids' => $this->_tpl_vars['_disable_ids'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php if ($this->_tpl_vars['addons']['recurring_billing']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/recurring_billing/hooks/products/product_option_content.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php endif; ?>
	<!--product_options_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
--></div>
	<?php endif; ?>
<?php $this->_smarty_vars['capture']["product_options_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "product_options_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
	<?php if ($this->_tpl_vars['show_product_options']): ?>
		<div class="cm-reload-<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" id="advanced_options_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/companies/components/product_company_data.tpl", 'smarty_include_vars' => array('company_id' => $this->_tpl_vars['product']['company_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php $this->_tag_stack[] = array('hook', array('name' => "products:options_advanced")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php if ($this->_tpl_vars['addons']['required_products']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/required_products/hooks/products/options_advanced.pre.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?>
			<?php if ($this->_tpl_vars['addons']['rma']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/rma/hooks/products/options_advanced.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['reward_points']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/reward_points/hooks/products/options_advanced.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
		<!--advanced_options_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
--></div>
	<?php endif; ?>
<?php $this->_smarty_vars['capture']["advanced_options_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "advanced_options_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
	<?php if ($this->_tpl_vars['show_qty']): ?>
		<div class="cm-reload-<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" id="qty_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">
		<input type="hidden" name="appearance[show_qty]" value="<?php echo $this->_tpl_vars['show_qty']; ?>
" />
		<input type="hidden" name="appearance[capture_options_vs_qty]" value="<?php echo $this->_tpl_vars['capture_options_vs_qty']; ?>
" />
		<?php if (! empty ( $this->_tpl_vars['product']['selected_amount'] )): ?>
			<?php $this->assign('default_amount', $this->_tpl_vars['product']['selected_amount'], false); ?>
		<?php elseif (! empty ( $this->_tpl_vars['product']['min_qty'] )): ?>
			<?php $this->assign('default_amount', $this->_tpl_vars['product']['min_qty'], false); ?>
		<?php else: ?>
			<?php $this->assign('default_amount', '1', false); ?>
		<?php endif; ?>
		
		<?php if (( $this->_tpl_vars['product']['qty_content'] || $this->_tpl_vars['show_qty'] ) && $this->_tpl_vars['product']['is_edp'] !== 'Y' && $this->_tpl_vars['cart_button_exists'] == true && ( $this->_tpl_vars['settings']['General']['allow_anonymous_shopping'] == 'Y' || $this->_tpl_vars['auth']['user_id'] )): ?>
			<div class="form-field<?php if (! $this->_tpl_vars['capture_options_vs_qty']): ?> product-list-field<?php endif; ?><?php if ($this->_tpl_vars['settings']['Appearance']['quantity_changer'] == 'Y'): ?> changer<?php endif; ?>" id="qty_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">
				<label for="qty_count_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
"><?php echo fn_get_lang_var('quantity', $this->getLanguage()); ?>
:</label>
				<?php if ($this->_tpl_vars['product']['qty_content']): ?>
				<select name="product_data[<?php echo $this->_tpl_vars['obj_id']; ?>
][amount]" id="qty_count_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
">
				<?php $this->assign('a_name', "product_amount_".($this->_tpl_vars['obj_prefix']).($this->_tpl_vars['obj_id']), false); ?>
				<?php $this->assign('selected_amount', false, false); ?>
				<?php $_from = $this->_tpl_vars['product']['qty_content']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach[($this->_tpl_vars['a_name'])] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach[($this->_tpl_vars['a_name'])]['total'] > 0):
    foreach ($_from as $this->_tpl_vars['var']):
        $this->_foreach[($this->_tpl_vars['a_name'])]['iteration']++;
?>
					<option value="<?php echo $this->_tpl_vars['var']; ?>
" <?php if ($this->_tpl_vars['product']['selected_amount'] && ( $this->_tpl_vars['product']['selected_amount'] == $this->_tpl_vars['var'] || ( ($this->_foreach[$this->_tpl_vars['a_name']]['iteration'] == $this->_foreach[$this->_tpl_vars['a_name']]['total']) && ! $this->_tpl_vars['selected_amount'] ) )): ?><?php $this->assign('selected_amount', true, false); ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['var']; ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
				</select>
				<?php else: ?>
				<?php if ($this->_tpl_vars['settings']['Appearance']['quantity_changer'] == 'Y'): ?>
				<div class="center valign cm-value-changer">
					<a class="cm-increase"><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/up_arrow.gif" width="21" height="9" border="0" /></a>
					<?php endif; ?>
<input type="text" size="5" class="input  pr-input input-text-short cm-amount" id="qty_count_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
" name="product_data[<?php echo $this->_tpl_vars['obj_id']; ?>
][amount]" onfocus="if(this.value=='<?php echo $this->_tpl_vars['default_amount']; ?>
') this.value='';" onblur="if(this.value=='') this.value='<?php echo $this->_tpl_vars['default_amount']; ?>
';" value="<?php echo $this->_tpl_vars['default_amount']; ?>
" />
					<?php if ($this->_tpl_vars['settings']['Appearance']['quantity_changer'] == 'Y'): ?>
					<a class="cm-decrease"><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/down_arrow.gif" width="21" height="9" border="0" /></a>
				</div>
				<?php endif; ?>
				<?php endif; ?>
			</div>
			<?php if ($this->_tpl_vars['product']['prices']): ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/products_qty_discounts.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?>
		<?php elseif (! $this->_tpl_vars['bulk_add']): ?>
			<input type="hidden" name="product_data[<?php echo $this->_tpl_vars['obj_id']; ?>
][amount]" value="<?php echo $this->_tpl_vars['default_amount']; ?>
" />
		<?php endif; ?>
		<!--qty_update_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['obj_id']; ?>
--></div>
	<?php endif; ?>
<?php $this->_smarty_vars['capture']["qty_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "qty_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
	<?php if ($this->_tpl_vars['min_qty'] && $this->_tpl_vars['product']['min_qty']): ?>
		<p class="description"><?php echo smarty_modifier_replace(smarty_modifier_replace(fn_get_lang_var('text_cart_min_qty', $this->getLanguage()), "[product]", $this->_tpl_vars['product']['product']), "[quantity]", $this->_tpl_vars['product']['min_qty']); ?>
.</p>
	<?php endif; ?>
<?php $this->_smarty_vars['capture']["min_qty_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "min_qty_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
	<?php if ($this->_tpl_vars['show_edp'] && $this->_tpl_vars['product']['is_edp'] == 'Y'): ?>
		<p class="description"><?php echo fn_get_lang_var('text_edp_product', $this->getLanguage()); ?>
.</p>
		<input type="hidden" name="product_data[<?php echo $this->_tpl_vars['obj_id']; ?>
][is_edp]" value="Y" />
	<?php endif; ?>
<?php $this->_smarty_vars['capture']["product_edp_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "product_edp_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php ob_start(); ?>
<?php if (! $this->_tpl_vars['hide_form']): ?>
</form>
<?php endif; ?>
<?php $this->_smarty_vars['capture']["form_close_".($this->_tpl_vars['obj_id'])] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['no_capture']): ?>
	<?php $this->assign('capture_name', "form_close_".($this->_tpl_vars['obj_id']), false); ?>
	<?php echo $this->_smarty_vars['capture'][$this->_tpl_vars['capture_name']]; ?>

<?php endif; ?>

<?php $_from = $this->_tpl_vars['images']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['object_id'] => $this->_tpl_vars['image']):
?>
	<div class="cm-reload-<?php echo $this->_tpl_vars['image']['obj_id']; ?>
" id="<?php echo $this->_tpl_vars['object_id']; ?>
">
		<input type="hidden" value="<?php echo $this->_tpl_vars['image']['obj_id']; ?>
,<?php echo $this->_tpl_vars['image']['width']; ?>
,<?php echo $this->_tpl_vars['image']['height']; ?>
,<?php echo $this->_tpl_vars['image']['type']; ?>
" name="image[<?php echo $this->_tpl_vars['object_id']; ?>
]" />
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image.tpl", 'smarty_include_vars' => array('image_width' => $this->_tpl_vars['image']['width'],'image_height' => $this->_tpl_vars['image']['height'],'show_thumbnail' => 'Y','obj_id' => $this->_tpl_vars['object_id'],'images' => $this->_tpl_vars['product']['main_pair'],'object_type' => 'product')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<!--<?php echo $this->_tpl_vars['object_id']; ?>
--></div>
<?php endforeach; endif; unset($_from); ?>

<?php $this->_tag_stack[] = array('hook', array('name' => "products:product_data")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php if ($this->_tpl_vars['addons']['buy_together']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/buy_together/hooks/products/product_data.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['recurring_billing']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/recurring_billing/hooks/products/product_data.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>