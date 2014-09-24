<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:12
         compiled from views/products/components/products_search_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'views/products/components/products_search_form.tpl', 7, false),array('modifier', 'default', 'views/products/components/products_search_form.tpl', 8, false),array('modifier', 'fn_show_picker', 'views/products/components/products_search_form.tpl', 41, false),array('modifier', 'fn_get_plain_categories_tree', 'views/products/components/products_search_form.tpl', 51, false),array('modifier', 'indent', 'views/products/components/products_search_form.tpl', 52, false),array('block', 'hook', 'views/products/components/products_search_form.tpl', 116, false),array('function', 'script', 'views/products/components/products_search_form.tpl', 191, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('find_results_with','any_words','all_words','exact_phrase','price','search_in_category','all_categories','all_categories','search_in','product_name','short_description','subcategories','full_description','keywords','search_by_product_filters','search_by_product_features','search_by_sku','search_by_vendor','all_vendors','search_by_supplier','all_suppliers','shipping_freight','weight','quantity','free_shipping','yes','no','status','active','hidden','disabled','popularity'));
?>

<?php ob_start(); ?>
<?php if ($this->_tpl_vars['page_part']): ?>
    <?php $this->assign('_page_part', "#".($this->_tpl_vars['page_part']), false); ?>
<?php endif; ?>
<form action="<?php echo fn_url(($this->_tpl_vars['index_script']).($this->_tpl_vars['_page_part'])); ?>
" name="<?php echo $this->_tpl_vars['product_search_form_prefix']; ?>
search_form" method="get" class="cm-disable-empty">
<input type="hidden" name="type" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['search_type'], 'simple'); ?>
" />
<?php if ($this->_tpl_vars['_REQUEST']['redirect_url']): ?>
<input type="hidden" name="redirect_url" value="<?php echo $this->_tpl_vars['_REQUEST']['redirect_url']; ?>
" />
<?php endif; ?>
<?php if ($this->_tpl_vars['selected_section'] != ""): ?>
<input type="hidden" id="selected_section" name="selected_section" value="<?php echo $this->_tpl_vars['selected_section']; ?>
" />
<?php endif; ?>

<?php echo $this->_tpl_vars['extra']; ?>


<table cellspacing="0" border="0" class="search-header">
<tr>
	<td class="nowrap search-field">
		<label><?php echo fn_get_lang_var('find_results_with', $this->getLanguage()); ?>
:</label>
		<div class="break">
			<input type="text" name="q" size="20" value="<?php echo $this->_tpl_vars['search']['q']; ?>
" class="search-input-text" />
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/search_go.tpl", 'smarty_include_vars' => array('search' => 'Y','but_name' => ($this->_tpl_vars['dispatch']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>&nbsp;
			<select name="match">
				<option value="any" <?php if ($this->_tpl_vars['search']['match'] == 'any'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('any_words', $this->getLanguage()); ?>
</option>
				<option value="all" <?php if ($this->_tpl_vars['search']['match'] == 'all'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('all_words', $this->getLanguage()); ?>
</option>
				<option value="exact" <?php if ($this->_tpl_vars['search']['match'] == 'exact'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('exact_phrase', $this->getLanguage()); ?>
</option>
			</select>
		</div>
	</td>
	<td class="nowrap search-field">
		<label><?php echo fn_get_lang_var('price', $this->getLanguage()); ?>
&nbsp;(<?php echo $this->_tpl_vars['currencies'][$this->_tpl_vars['primary_currency']]['symbol']; ?>
):</label>
		<div class="break">
			<input type="text" name="price_from" size="1" value="<?php echo $this->_tpl_vars['search']['price_from']; ?>
" onfocus="this.select();" class="input-text-price" />&nbsp;&ndash;&nbsp;<input type="text" size="1" name="price_to" value="<?php echo $this->_tpl_vars['search']['price_to']; ?>
" onfocus="this.select();" class="input-text-price" />
		</div>
	</td>
	<td class="nowrap search-field">
		<label><?php echo fn_get_lang_var('search_in_category', $this->getLanguage()); ?>
:</label>
		<div class="break clear correct-picker-but">
		<?php if (fn_show_picker('categories', @CATEGORY_SHOW_ALL)): ?>
			<?php if ($this->_tpl_vars['search']['cid']): ?>
				<?php $this->assign('s_cid', $this->_tpl_vars['search']['cid'], false); ?>
			<?php else: ?>
				<?php $this->assign('s_cid', '0', false); ?>
			<?php endif; ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/categories_picker.tpl", 'smarty_include_vars' => array('data_id' => 'location_category','input_name' => 'cid','item_ids' => $this->_tpl_vars['s_cid'],'hide_link' => true,'hide_delete_button' => true,'show_root' => true,'default_name' => fn_get_lang_var('all_categories', $this->getLanguage()),'extra' => "")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php else: ?>
			<select	name="cid">
				<option	value="0" <?php if ($this->_tpl_vars['category_data']['parent_id'] == '0'): ?>selected="selected"<?php endif; ?>>- <?php echo fn_get_lang_var('all_categories', $this->getLanguage()); ?>
 -</option>
				<?php $_from = fn_get_plain_categories_tree(0, false); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['search_cat']):
?>
					<option	value="<?php echo $this->_tpl_vars['search_cat']['category_id']; ?>
" <?php if ($this->_tpl_vars['search']['cid'] == $this->_tpl_vars['search_cat']['category_id']): ?>selected="selected"<?php endif; ?>><?php echo smarty_modifier_indent($this->_tpl_vars['search_cat']['category'], $this->_tpl_vars['search_cat']['level'], "&#166;&nbsp;&nbsp;&nbsp;&nbsp;", "&#166;--&nbsp;"); ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
			</select>
		<?php endif; ?>
		</div>
	</td>
	<td class="buttons-container">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/search.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[".($this->_tpl_vars['dispatch'])."]",'but_role' => 'submit')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
</tr>
</table>

<?php ob_start(); ?>

<div class="search-field">
	<label><?php echo fn_get_lang_var('search_in', $this->getLanguage()); ?>
:</label>
	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td class="select-field">
			<input type="checkbox" value="Y" <?php if ($this->_tpl_vars['search']['pname'] == 'Y'): ?>checked="checked"<?php endif; ?> name="pname" id="pname" class="checkbox" /><label for="pname"><?php echo fn_get_lang_var('product_name', $this->getLanguage()); ?>
</label></td>
		<td>&nbsp;&nbsp;&nbsp;</td>

		<td class="select-field"><input type="checkbox" value="Y" <?php if ($this->_tpl_vars['search']['pshort'] == 'Y'): ?>checked="checked"<?php endif; ?> name="pshort" id="pshort" class="checkbox" /><label for="pshort"><?php echo fn_get_lang_var('short_description', $this->getLanguage()); ?>
</label></td>
		<td>&nbsp;&nbsp;&nbsp;</td>

		<td class="select-field"><input type="checkbox" value="Y" <?php if ($this->_tpl_vars['search']['subcats'] == 'Y'): ?>checked="checked"<?php endif; ?> name="subcats" class="checkbox" id="subcats" /><label for="subcats"><?php echo fn_get_lang_var('subcategories', $this->getLanguage()); ?>
</label></td>
	</tr>
	<tr>
		<td class="select-field"><input type="checkbox" value="Y" <?php if ($this->_tpl_vars['search']['pfull'] == 'Y'): ?>checked="checked"<?php endif; ?> name="pfull" id="pfull" class="checkbox" /><label for="pfull"><?php echo fn_get_lang_var('full_description', $this->getLanguage()); ?>
</label></td>
		<td>&nbsp;&nbsp;&nbsp;</td>
		<td class="select-field"><input type="checkbox" value="Y" <?php if ($this->_tpl_vars['search']['pkeywords'] == 'Y'): ?>checked="checked"<?php endif; ?> name="pkeywords" id="pkeywords" class="checkbox" /><label for="pkeywords"><?php echo fn_get_lang_var('keywords', $this->getLanguage()); ?>
</label></td>
		<td colspan="2">&nbsp;</td>
	</tr>
	</table>
</div>
<hr />
<?php if ($this->_tpl_vars['filter_items']): ?>
<div class="search-field">
	<label>
		<a class="search-link cm-combination cm-combo-off cm-save-state" id="sw_filter"><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/plus.gif" width="14" height="9" border="0" alt="" id="on_filter" class="cm-combination cm-save-state <?php if ($_COOKIE['filter']): ?>hidden<?php endif; ?>" /><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/minus.gif" width="14" height="9" border="0" alt="" id="off_filter" class="cm-combination cm-save-state <?php if (! $_COOKIE['filter']): ?>hidden<?php endif; ?>" /><?php echo fn_get_lang_var('search_by_product_filters', $this->getLanguage()); ?>
</a>:
	</label>
	<div id="filter"<?php if (! $_COOKIE['filter']): ?> class="hidden"<?php endif; ?>>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/advanced_search_form.tpl", 'smarty_include_vars' => array('filter_features' => $this->_tpl_vars['filter_items'],'prefix' => 'filter_')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['feature_items']): ?>
<div class="search-field">
	<label>
		<a class="search-link cm-combination cm-combo-off cm-save-state" id="sw_feature"><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/plus.gif" width="14" height="9" border="0" alt="" id="on_feature" class="cm-combination cm-save-state <?php if ($_COOKIE['feature']): ?>hidden<?php endif; ?>" /><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/minus.gif" width="14" height="9" border="0" alt="" id="off_feature" class="cm-combination cm-save-state <?php if (! $_COOKIE['feature']): ?>hidden<?php endif; ?>" /><?php echo fn_get_lang_var('search_by_product_features', $this->getLanguage()); ?>
</a>:
	</label>
	<div id="feature"<?php if (! $_COOKIE['feature']): ?> class="hidden"<?php endif; ?>>
		<input type="hidden" name="advanced_filter" value="Y" />
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/advanced_search_form.tpl", 'smarty_include_vars' => array('filter_features' => $this->_tpl_vars['feature_items'],'prefix' => 'feature_')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
</div>
<?php endif; ?>

<div class="search-field">
	<label for="pcode"><?php echo fn_get_lang_var('search_by_sku', $this->getLanguage()); ?>
:</label>
	<input type="text" name="pcode" id="pcode" value="<?php echo $this->_tpl_vars['search']['pcode']; ?>
" onfocus="this.select();" class="input-text" />
</div>

<hr />
<?php $this->_tag_stack[] = array('hook', array('name' => "products:search_form")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<?php if ($this->_tpl_vars['addons']['product_configurator']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/product_configurator/hooks/products/search_form.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['bestsellers']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/bestsellers/hooks/products/search_form.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php if (@PRODUCT_TYPE == 'MULTIVENDOR' || $this->_tpl_vars['settings']['Suppliers']['enable_suppliers'] == 'Y'): ?>
	<?php if (@PRODUCT_TYPE == 'MULTIVENDOR'): ?>
		<?php $this->assign('lang_search_by_vendor_supplier', fn_get_lang_var('search_by_vendor', $this->getLanguage()), false); ?>
		<?php $this->assign('lang_all_vendors_suppliers', fn_get_lang_var('all_vendors', $this->getLanguage()), false); ?>
	<?php elseif (@PRODUCT_TYPE == 'PROFESSIONAL' || @PRODUCT_TYPE == 'COMMUNITY'): ?>
		<?php $this->assign('lang_search_by_vendor_supplier', fn_get_lang_var('search_by_supplier', $this->getLanguage()), false); ?>
		<?php $this->assign('lang_all_vendors_suppliers', fn_get_lang_var('all_suppliers', $this->getLanguage()), false); ?>
	<?php endif; ?>
	<div class="search-field">
		<label for="company_id"><?php echo $this->_tpl_vars['lang_search_by_vendor_supplier']; ?>
:</label>
		<select	name="company_id" id="company_id">
			<option	value=""<?php if (strval ( $this->_tpl_vars['search']['company_id'] ) == ''): ?> selected="selected"<?php endif; ?>>- <?php echo $this->_tpl_vars['lang_all_vendors_suppliers']; ?>
 -</option>
			<?php $_from = $this->_tpl_vars['companies']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['company_id'] => $this->_tpl_vars['company']):
?>
				<option	value="<?php echo $this->_tpl_vars['company_id']; ?>
" <?php if (strval ( $this->_tpl_vars['search']['company_id'] ) == strval ( $this->_tpl_vars['company_id'] )): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['company']; ?>
</option>
			<?php endforeach; endif; unset($_from); ?>
		</select>
	</div>
<?php endif; ?>
<div class="search-field">
	<label for="shipping_freight_from"><?php echo fn_get_lang_var('shipping_freight', $this->getLanguage()); ?>
&nbsp;(<?php echo $this->_tpl_vars['currencies'][$this->_tpl_vars['primary_currency']]['symbol']; ?>
):</label>
	<input type="text" name="shipping_freight_from" id="shipping_freight_from" value="<?php echo $this->_tpl_vars['search']['shipping_freight_from']; ?>
" onfocus="this.select();" class="input-text" />&nbsp;&ndash;&nbsp;<input type="text" name="shipping_freight_to" value="<?php echo $this->_tpl_vars['search']['shipping_freight_to']; ?>
" onfocus="this.select();" class="input-text" />
</div>

<div class="search-field">
	<label for="weight_from"><?php echo fn_get_lang_var('weight', $this->getLanguage()); ?>
&nbsp;(<?php echo $this->_tpl_vars['settings']['General']['weight_symbol']; ?>
):</label>
	<input type="text" name="weight_from" id="weight_from" value="<?php echo $this->_tpl_vars['search']['weight_from']; ?>
" onfocus="this.select();" class="input-text" />&nbsp;&ndash;&nbsp;<input type="text" name="weight_to" value="<?php echo $this->_tpl_vars['search']['weight_to']; ?>
" onfocus="this.select();" class="input-text" />
</div>

<?php $this->assign('have_amount_filter', 0, false); ?>
<?php $_from = $this->_tpl_vars['filter_items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['ff']):
?><?php if ($this->_tpl_vars['ff']['field_type'] == 'A'): ?><?php $this->assign('have_amount_filter', 1, false); ?><?php endif; ?><?php endforeach; endif; unset($_from); ?>
<?php if (! $this->_tpl_vars['have_amount_filter']): ?>
<div class="search-field">
	<label for="amount_from"><?php echo fn_get_lang_var('quantity', $this->getLanguage()); ?>
:</label>
	<input type="text" name="amount_from" id="amount_from" value="<?php echo $this->_tpl_vars['search']['amount_from']; ?>
" onfocus="this.select();" class="input-text" />&nbsp;&ndash;&nbsp;<input type="text" name="amount_to" value="<?php echo $this->_tpl_vars['search']['amount_to']; ?>
" onfocus="this.select();" class="input-text" />
</div>
<?php endif; ?>

<hr />

<div class="search-field">
	<label for="free_shipping"><?php echo fn_get_lang_var('free_shipping', $this->getLanguage()); ?>
:</label>
	<select name="free_shipping" id="free_shipping">
		<option value="">--</option>
		<option value="Y" <?php if ($this->_tpl_vars['search']['free_shipping'] == 'Y'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('yes', $this->getLanguage()); ?>
</option>
		<option value="N" <?php if ($this->_tpl_vars['search']['free_shipping'] == 'N'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('no', $this->getLanguage()); ?>
</option>
	</select>
</div>

<div class="search-field">
	<label for="status"><?php echo fn_get_lang_var('status', $this->getLanguage()); ?>
:</label>
	<select name="status" id="status">
		<option value="">--</option>
		<option value="A" <?php if ($this->_tpl_vars['search']['status'] == 'A'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('active', $this->getLanguage()); ?>
</option>
		<option value="H" <?php if ($this->_tpl_vars['search']['status'] == 'H'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('hidden', $this->getLanguage()); ?>
</option>
		<option value="D" <?php if ($this->_tpl_vars['search']['status'] == 'D'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('disabled', $this->getLanguage()); ?>
</option>
	</select>
</div>

<hr />

<div class="search-field">
	<label for="popularity_from"><?php echo fn_get_lang_var('popularity', $this->getLanguage()); ?>
:</label>
	<input type="text" name="popularity_from" id="popularity_from" value="<?php echo $this->_tpl_vars['search']['popularity_from']; ?>
" onfocus="this.select();" class="input-text" />&nbsp;&ndash;&nbsp;<input type="text" name="popularity_to" value="<?php echo $this->_tpl_vars['search']['popularity_to']; ?>
" onfocus="this.select();" class="input-text" />
</div>

<?php $this->_smarty_vars['capture']['advanced_search'] = ob_get_contents(); ob_end_clean(); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/advanced_search.tpl", 'smarty_include_vars' => array('content' => $this->_smarty_vars['capture']['advanced_search'],'dispatch' => $this->_tpl_vars['dispatch'],'view_type' => 'products')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

</form>

<?php $this->_smarty_vars['capture']['section'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/section.tpl", 'smarty_include_vars' => array('section_content' => $this->_smarty_vars['capture']['section'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>