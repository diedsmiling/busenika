<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:43
         compiled from views/products/update.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'views/products/update.tpl', 12, false),array('modifier', 'fn_show_picker', 'views/products/update.tpl', 32, false),array('modifier', 'default', 'views/products/update.tpl', 34, false),array('modifier', 'fn_get_plain_categories_tree', 'views/products/update.tpl', 38, false),array('modifier', 'indent', 'views/products/update.tpl', 39, false),array('modifier', 'in_array', 'views/products/update.tpl', 173, false),array('modifier', 'fn_get_usergroups', 'views/products/update.tpl', 213, false),array('modifier', 'fn_get_product_details_views', 'views/products/update.tpl', 240, false),array('modifier', 'escape', 'views/products/update.tpl', 378, false),array('modifier', 'fn_compact_value', 'views/products/update.tpl', 379, false),array('modifier', 'unescape', 'views/products/update.tpl', 382, false),array('block', 'hook', 'views/products/update.tpl', 59, false),array('block', 'notes', 'views/products/update.tpl', 376, false),array('function', 'script', 'views/products/update.tpl', 384, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('information','name','main_category','main_category','price','full_description','vendor','images','text_product_thumbnail','text_product_detailed_image','options_settings','options_type','simultaneous','sequential','exceptions_type','forbidden','allowed','pricing_inventory','product_code','list_price','tt_views_products_update_list_price','in_stock','edit','zero_price_action','zpa_refuse','zpa_permit','zpa_ask_price','inventory','track_with_options','track_without_options','dont_track','min_order_qty','max_order_qty','quantity_step','list_quantity_count','weight','free_shipping','shipping_freight','taxes','seo_meta_data','page_title','ttc_page_title','meta_description','meta_keywords','search_words','availability','usergroups','ttc_usergroups','created_date','available_since','buy_in_advance','extra','product_details_layout','tt_views_products_update_product_details_layout','feature_comparison','downloadable','edp_enable_shipping','time_unlimited_download','short_description','popularity','ttc_popularity','additional_images','additional_thumbnail','additional_popup_larger_image','text_additional_thumbnail','text_additional_detailed_image','additional_thumbnail','additional_popup_larger_image','text_additional_thumbnail','text_additional_detailed_image','new_product','preview','txt_page_access_link','txt_access_link_as_admin','editing_product'));
?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/file_browser.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php ob_start(); ?>

<?php ob_start(); ?>

<form action="<?php echo fn_url(""); ?>
" method="post" name="product_update_form" class="cm-form-highlight cm-disable-empty-files" enctype="multipart/form-data"> <input type="hidden" name="fake" value="1" />
<input type="hidden" name="selected_section" id="selected_section" value="<?php echo $this->_tpl_vars['_REQUEST']['selected_section']; ?>
" />
<input type="hidden" name="product_id" value="<?php echo $this->_tpl_vars['product_data']['product_id']; ?>
" />


<div id="content_detailed"> 
<fieldset>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('information', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="form-field">
	<label for="product_description_product" class="cm-required"><?php echo fn_get_lang_var('name', $this->getLanguage()); ?>
:</label>
	<input type="text" name="product_data[product]" id="product_description_product" size="55" value="<?php echo $this->_tpl_vars['product_data']['product']; ?>
" class="input-text-large main-input" />
</div>

<div class="form-field">
	<?php if (fn_show_picker('categories', @CATEGORY_SHOW_ALL)): ?>
		<label for="main_category_id" class="cm-required"><?php echo fn_get_lang_var('main_category', $this->getLanguage()); ?>
:</label>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/categories_picker.tpl", 'smarty_include_vars' => array('data_id' => 'main_category','input_name' => "product_data[main_category]",'item_ids' => smarty_modifier_default(@$this->_tpl_vars['product_data']['main_category'], @$this->_tpl_vars['_REQUEST']['category_id']),'hide_link' => true,'hide_delete_button' => true,'display_input_id' => 'main_category_id','disable_no_item_text' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php else: ?>
		<label for="products_categories_M" class="cm-required"><?php echo fn_get_lang_var('main_category', $this->getLanguage()); ?>
:</label>
		<select	name="product_data[main_category]" id="products_categories_M">
			<?php $_from = fn_get_plain_categories_tree(0, false); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cat']):
?>
				<option	value="<?php echo $this->_tpl_vars['cat']['category_id']; ?>
" <?php if ($this->_tpl_vars['product_data']['main_category'] == $this->_tpl_vars['cat']['category_id'] || $this->_tpl_vars['cat']['category_id'] == $this->_tpl_vars['_REQUEST']['category_id']): ?>selected="selected"<?php endif; ?>><?php echo smarty_modifier_indent($this->_tpl_vars['cat']['category'], $this->_tpl_vars['cat']['level'], "&#166;&nbsp;&nbsp;&nbsp;&nbsp;", "&#166;--&nbsp;"); ?>
</option>
			<?php endforeach; endif; unset($_from); ?>
		</select>
	<?php endif; ?>
</div>

<div class="form-field">
	<label for="price_price" class="cm-required"><?php echo fn_get_lang_var('price', $this->getLanguage()); ?>
 (<?php echo $this->_tpl_vars['currencies'][$this->_tpl_vars['primary_currency']]['symbol']; ?>
) :</label>
	<input type="text" name="product_data[price]" id="price_price" size="10" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['product_data']['price'], "0.00"); ?>
" class="input-text-medium" />
</div>

<div class="form-field">
	<label for="product_full_descr"><?php echo fn_get_lang_var('full_description', $this->getLanguage()); ?>
:</label>
	<textarea id="product_full_descr" name="product_data[full_description]" cols="55" rows="8" class="input-textarea-long"><?php echo $this->_tpl_vars['product_data']['full_description']; ?>
</textarea>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/wysiwyg.tpl", 'smarty_include_vars' => array('id' => 'product_full_descr')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_status.tpl", 'smarty_include_vars' => array('input_name' => "product_data[status]",'id' => 'product_data','obj' => $this->_tpl_vars['product_data'],'hidden' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $this->_tag_stack[] = array('hook', array('name' => "companies:product_details_fields")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/companies/components/company_field.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('vendor', $this->getLanguage()),'name' => "product_data[company_id]",'id' => 'product_data_company_id','selected' => $this->_tpl_vars['product_data']['company_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

<div class="form-field">
	<label><?php echo fn_get_lang_var('images', $this->getLanguage()); ?>
:</label>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/attach_images.tpl", 'smarty_include_vars' => array('image_name' => 'product_main','image_object_type' => 'product','image_pair' => $this->_tpl_vars['product_data']['main_pair'],'icon_text' => fn_get_lang_var('text_product_thumbnail', $this->getLanguage()),'detailed_text' => fn_get_lang_var('text_product_detailed_image', $this->getLanguage()),'no_thumbnail' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
</fieldset>

<fieldset>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('options_settings', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="form-field">
	<label for="product_options_type"><?php echo fn_get_lang_var('options_type', $this->getLanguage()); ?>
:</label>
	<select name="product_data[options_type]" id="options_type">
		<option value="P" <?php if ($this->_tpl_vars['product_data']['options_type'] == 'P'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('simultaneous', $this->getLanguage()); ?>
</option>
		<option value="S" <?php if ($this->_tpl_vars['product_data']['options_type'] == 'S'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('sequential', $this->getLanguage()); ?>
</option>
	</select>
</div>
<div class="form-field">
	<label for="product_exceptions_type"><?php echo fn_get_lang_var('exceptions_type', $this->getLanguage()); ?>
:</label>
	<select name="product_data[exceptions_type]" id="exceptions_type">
		<option value="F" <?php if ($this->_tpl_vars['product_data']['exceptions_type'] == 'F'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('forbidden', $this->getLanguage()); ?>
</option>
		<option value="A" <?php if ($this->_tpl_vars['product_data']['exceptions_type'] == 'A'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('allowed', $this->getLanguage()); ?>
</option>
	</select>
</div>
</fieldset>

<fieldset>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('pricing_inventory', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="form-field">
	<label for="product_product_code"><?php echo fn_get_lang_var('product_code', $this->getLanguage()); ?>
:</label>
	<input type="text" name="product_data[product_code]" id="product_product_code" size="20" value="<?php echo $this->_tpl_vars['product_data']['product_code']; ?>
" class="input-text-medium" />
</div>

<div class="form-field">
	<label for="product_list_price"><?php echo fn_get_lang_var('list_price', $this->getLanguage()); ?>
 (<?php echo $this->_tpl_vars['currencies'][$this->_tpl_vars['primary_currency']]['symbol']; ?>
) <?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('tt_views_products_update_list_price', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
	<input type="text" name="product_data[list_price]" id="product_data_list_price" size="10" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['product_data']['list_price'], "0.00"); ?>
" class="input-text-medium" />
</div>

<div class="form-field">
	<label for="product_amount"><?php echo fn_get_lang_var('in_stock', $this->getLanguage()); ?>
:</label>
	<?php if ($this->_tpl_vars['product_data']['tracking'] == 'O'): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('edit', $this->getLanguage()),'but_href' => "product_options.inventory?product_id=".($this->_tpl_vars['product_data']['product_id']),'but_role' => 'edit')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php else: ?>
		<input type="text" name="product_data[amount]" id="product_amount" size="10" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['product_data']['amount'], '1'); ?>
" class="input-text-short" />
	<?php endif; ?>
</div>

<div class="form-field">
	<label for="zero_price_action"><?php echo fn_get_lang_var('zero_price_action', $this->getLanguage()); ?>
:</label>
	<select name="product_data[zero_price_action]" id="zero_price_action">
		<option value="R" <?php if ($this->_tpl_vars['product_data']['zero_price_action'] == 'R'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('zpa_refuse', $this->getLanguage()); ?>
</option>
		<option value="P" <?php if ($this->_tpl_vars['product_data']['zero_price_action'] == 'P'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('zpa_permit', $this->getLanguage()); ?>
</option>
		<option value="A" <?php if ($this->_tpl_vars['product_data']['zero_price_action'] == 'A'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('zpa_ask_price', $this->getLanguage()); ?>
</option>
	</select>
</div>

<div class="form-field">
	<label for="product_tracking"><?php echo fn_get_lang_var('inventory', $this->getLanguage()); ?>
:</label>
	<select name="product_data[tracking]" id="product_tracking">
		<?php if ($this->_tpl_vars['product_options']): ?>
			<option value="O" <?php if ($this->_tpl_vars['product_data']['tracking'] == 'O'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('track_with_options', $this->getLanguage()); ?>
</option>
		<?php endif; ?>
		<option value="B" <?php if ($this->_tpl_vars['product_data']['tracking'] == 'B'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('track_without_options', $this->getLanguage()); ?>
</option>
		<option value="D" <?php if ($this->_tpl_vars['product_data']['tracking'] == 'D'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('dont_track', $this->getLanguage()); ?>
</option>
	</select>
</div>

<div class="form-field">
	<label for="min_qty"><?php echo fn_get_lang_var('min_order_qty', $this->getLanguage()); ?>
:</label>
	<input type="text" name="product_data[min_qty]" size="10" id="min_qty" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['product_data']['min_qty'], '0'); ?>
" class="input-text-short" />
</div>

<div class="form-field">
	<label for="max_qty"><?php echo fn_get_lang_var('max_order_qty', $this->getLanguage()); ?>
:</label>
	<input type="text" name="product_data[max_qty]" id="max_qty" size="10" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['product_data']['max_qty'], '0'); ?>
" class="input-text-short" />
</div>

<div class="form-field">
	<label for="qty_step"><?php echo fn_get_lang_var('quantity_step', $this->getLanguage()); ?>
:</label>
	<input type="text" name="product_data[qty_step]" id="qty_step" size="10" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['product_data']['qty_step'], '0'); ?>
" class="input-text-short" />
</div>

<div class="form-field">
	<label for="list_qty_count"><?php echo fn_get_lang_var('list_quantity_count', $this->getLanguage()); ?>
:</label>
	<input type="text" name="product_data[list_qty_count]" id="list_qty_count" size="10" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['product_data']['list_qty_count'], '0'); ?>
" class="input-text-short" />
</div>

<div class="form-field">
	<label for="product_weight"><?php echo fn_get_lang_var('weight', $this->getLanguage()); ?>
 (<?php echo $this->_tpl_vars['settings']['General']['weight_symbol']; ?>
) :</label>
	<input type="text" name="product_data[weight]" id="product_weight" size="10" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['product_data']['weight'], '0'); ?>
" class="input-text-medium" />
</div>

<div class="form-field">
	<label for="product_free_shipping"><?php echo fn_get_lang_var('free_shipping', $this->getLanguage()); ?>
:</label>
	<input type="hidden" name="product_data[free_shipping]" value="N" />
	<input type="checkbox" name="product_data[free_shipping]" id="product_free_shipping" value="Y" <?php if ($this->_tpl_vars['product_data']['free_shipping'] == 'Y'): ?>checked="checked"<?php endif; ?> class="checkbox" />
</div>

<div class="form-field">
	<label for="product_shipping_freight"><?php echo fn_get_lang_var('shipping_freight', $this->getLanguage()); ?>
 (<?php echo $this->_tpl_vars['currencies'][$this->_tpl_vars['primary_currency']]['symbol']; ?>
):</label>
	<input type="text" name="product_data[shipping_freight]" id="product_shipping_freight" size="10" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['product_data']['shipping_freight'], "0.00"); ?>
" class="input-text-medium" />
</div>

<div class="form-field">
	<label for="products_tax_id"><?php echo fn_get_lang_var('taxes', $this->getLanguage()); ?>
:</label>
	<div class="select-field">
		<input type="hidden" name="product_data[tax_ids]" value="" />
		<?php $_from = $this->_tpl_vars['taxes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['tax']):
?>
			<input type="checkbox" name="product_data[tax_ids][<?php echo $this->_tpl_vars['tax']['tax_id']; ?>
]" id="product_data_<?php echo $this->_tpl_vars['tax']['tax_id']; ?>
" <?php if (smarty_modifier_in_array($this->_tpl_vars['tax']['tax_id'], $this->_tpl_vars['product_data']['taxes']) || $this->_tpl_vars['product_data']['taxes'][$this->_tpl_vars['tax']['tax_id']]): ?>checked="checked"<?php endif; ?> class="checkbox" value="<?php echo $this->_tpl_vars['tax']['tax_id']; ?>
" />
			<label for="product_data_<?php echo $this->_tpl_vars['tax']['tax_id']; ?>
"><?php echo $this->_tpl_vars['tax']['tax']; ?>
</label>
		<?php endforeach; else: ?>
			&ndash;
		<?php endif; unset($_from); ?>
	</div>
</div>
</fieldset>

<fieldset>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('seo_meta_data', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="form-field">
	<label for="product_page_title"><?php echo fn_get_lang_var('page_title', $this->getLanguage()); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('ttc_page_title', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
	<input type="text" name="product_data[page_title]" id="product_page_title" size="55" value="<?php echo $this->_tpl_vars['product_data']['page_title']; ?>
" class="input-text-large" />
</div>

<div class="form-field">
	<label for="product_meta_descr"><?php echo fn_get_lang_var('meta_description', $this->getLanguage()); ?>
:</label>
	<textarea name="product_data[meta_description]" id="product_meta_descr" cols="55" rows="2" class="input-textarea-long"><?php echo $this->_tpl_vars['product_data']['meta_description']; ?>
</textarea>
</div>

<div class="form-field">
	<label for="product_meta_keywords"><?php echo fn_get_lang_var('meta_keywords', $this->getLanguage()); ?>
:</label>
	<textarea name="product_data[meta_keywords]" id="product_meta_keywords" cols="55" rows="2" class="input-textarea-long"><?php echo $this->_tpl_vars['product_data']['meta_keywords']; ?>
</textarea>
</div>

<div class="form-field">
	<label for="product_search_words"><?php echo fn_get_lang_var('search_words', $this->getLanguage()); ?>
:</label>
	<textarea name="product_data[search_words]" id="product_search_words" cols="55" rows="2" class="input-textarea-long"><?php echo $this->_tpl_vars['product_data']['search_words']; ?>
</textarea>
</div>
</fieldset>

<fieldset>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('availability', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="form-field">
	<label><?php echo fn_get_lang_var('usergroups', $this->getLanguage()); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('ttc_usergroups', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
		<div class="select-field">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_usergroups.tpl", 'smarty_include_vars' => array('id' => 'ug_id','name' => "product_data[usergroup_ids]",'usergroups' => fn_get_usergroups('C', @DESCR_SL),'usergroup_ids' => $this->_tpl_vars['product_data']['usergroup_ids'],'input_extra' => "",'list_mode' => false)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
</div>
<div class="form-field">
	<label><?php echo fn_get_lang_var('created_date', $this->getLanguage()); ?>
:</label>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/calendar.tpl", 'smarty_include_vars' => array('date_id' => 'date_holder','date_name' => "product_data[timestamp]",'date_val' => smarty_modifier_default(@$this->_tpl_vars['product_data']['timestamp'], @TIME),'start_year' => $this->_tpl_vars['settings']['Company']['company_start_year'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

<div class="form-field">
	<label for="date_avail_holder"><?php echo fn_get_lang_var('available_since', $this->getLanguage()); ?>
:</label>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/calendar.tpl", 'smarty_include_vars' => array('date_id' => 'date_avail_holder','date_name' => "product_data[avail_since]",'date_val' => smarty_modifier_default(@$this->_tpl_vars['product_data']['avail_since'], ""),'start_year' => $this->_tpl_vars['settings']['Company']['company_start_year'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

<div class="form-field">
	<label for="buy_in_advance"><?php echo fn_get_lang_var('buy_in_advance', $this->getLanguage()); ?>
:</label>
	<input type="hidden" name="product_data[buy_in_advance]" value="N" />
	<input type="checkbox" id="buy_in_advance" name="product_data[buy_in_advance]" value="Y" <?php if ($this->_tpl_vars['product_data']['buy_in_advance'] == 'Y'): ?>checked="checked"<?php endif; ?> class="checkbox" />
</div>
</fieldset>

<fieldset>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('extra', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="form-field">
	<label for="details_layout"><?php echo fn_get_lang_var('product_details_layout', $this->getLanguage()); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('tt_views_products_update_product_details_layout', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
	<select id="details_layout" name="product_data[details_layout]">
		<?php $_from = fn_get_product_details_views($this->_tpl_vars['product_data']['product_id']); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['layout'] => $this->_tpl_vars['item']):
?>
			<option <?php if ($this->_tpl_vars['product_data']['details_layout'] == $this->_tpl_vars['layout']): ?>selected="selected"<?php endif; ?> value="<?php echo $this->_tpl_vars['layout']; ?>
"><?php echo $this->_tpl_vars['item']; ?>
</option>
		<?php endforeach; endif; unset($_from); ?>
	</select>
</div>

<div class="form-field">
	<label for="product_feature_comparison"><?php echo fn_get_lang_var('feature_comparison', $this->getLanguage()); ?>
:</label>
	<input type="hidden" name="product_data[feature_comparison]" value="N" />
	<input type="checkbox" name="product_data[feature_comparison]" id="product_feature_comparison" value="Y" <?php if ($this->_tpl_vars['product_data']['feature_comparison'] == 'Y'): ?>checked="checked"<?php endif; ?> class="checkbox" />
</div>

<div class="form-field">
	<label for="product_is_edp"><?php echo fn_get_lang_var('downloadable', $this->getLanguage()); ?>
:</label>
	<input type="hidden" name="product_data[is_edp]" value="N" />
	<input type="checkbox" name="product_data[is_edp]" id="product_is_edp" value="Y" <?php if ($this->_tpl_vars['product_data']['is_edp'] == 'Y'): ?>checked="checked"<?php endif; ?> onclick="$('#edp_shipping').toggleBy(); $('#edp_unlimited').toggleBy();" class="checkbox" />
</div>

<div class="form-field <?php if ($this->_tpl_vars['product_data']['is_edp'] != 'Y'): ?>hidden<?php endif; ?>" id="edp_shipping">
	<label for="product_edp_shipping"><?php echo fn_get_lang_var('edp_enable_shipping', $this->getLanguage()); ?>
:</label>
	<input type="hidden" name="product_data[edp_shipping]" value="N" />
	<input type="checkbox" name="product_data[edp_shipping]" id="product_edp_shipping" value="Y" <?php if ($this->_tpl_vars['product_data']['edp_shipping'] == 'Y'): ?>checked="checked"<?php endif; ?> class="checkbox" />
</div>

<div class="form-field <?php if ($this->_tpl_vars['product_data']['is_edp'] != 'Y'): ?>hidden<?php endif; ?>" id="edp_unlimited">
	<label for="product_edp_unlimited"><?php echo fn_get_lang_var('time_unlimited_download', $this->getLanguage()); ?>
:</label>
	<input type="hidden" name="product_data[unlimited_download]" value="N" />
	<input type="checkbox" name="product_data[unlimited_download]" id="product_edp_unlimited" value="Y" <?php if ($this->_tpl_vars['product_data']['unlimited_download'] == 'Y'): ?>checked="checked"<?php endif; ?> class="checkbox" />
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/localizations/components/select.tpl", 'smarty_include_vars' => array('data_from' => $this->_tpl_vars['product_data']['localization'],'data_name' => "product_data[localization]")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="form-field">
	<label for="product_short_descr"><?php echo fn_get_lang_var('short_description', $this->getLanguage()); ?>
:</label>
	<textarea id="product_short_descr" name="product_data[short_description]" cols="55" rows="2" class="input-textarea-long"><?php echo $this->_tpl_vars['product_data']['short_description']; ?>
</textarea>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/wysiwyg.tpl", 'smarty_include_vars' => array('id' => 'product_short_descr')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

<div class="form-field">
	<label for="product_popularity"><?php echo fn_get_lang_var('popularity', $this->getLanguage()); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('ttc_popularity', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
	<input type="text" name="product_data[popularity]" id="product_popularity" size="55" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['product_data']['popularity'], 0); ?>
" class="input-text-medium" />
</div>

</fieldset>
</div> 

<div id="content_categories" class="hidden"> 	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/categories_picker.tpl", 'smarty_include_vars' => array('input_name' => "product_data[add_categories]",'item_ids' => $this->_tpl_vars['product_data']['add_categories'],'multiple' => true,'single_line' => true,'positions' => "")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div> 
<div id="content_images" class="hidden"> <fieldset>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('additional_images', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php $_from = $this->_tpl_vars['product_data']['image_pairs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['detailed_images'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['detailed_images']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['pair']):
        $this->_foreach['detailed_images']['iteration']++;
?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/attach_images.tpl", 'smarty_include_vars' => array('image_name' => 'product_additional','image_object_type' => 'product','image_key' => $this->_tpl_vars['pair']['pair_id'],'image_type' => 'A','image_pair' => $this->_tpl_vars['pair'],'icon_title' => fn_get_lang_var('additional_thumbnail', $this->getLanguage()),'detailed_title' => fn_get_lang_var('additional_popup_larger_image', $this->getLanguage()),'icon_text' => fn_get_lang_var('text_additional_thumbnail', $this->getLanguage()),'detailed_text' => fn_get_lang_var('text_additional_detailed_image', $this->getLanguage()),'delete_pair' => true,'no_thumbnail' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<hr />
	<?php endforeach; endif; unset($_from); ?>
</fieldset>

<div id="box_new_image" class="margin-top">
	<div class="clear cm-row-item">
		<div class="float-left"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/attach_images.tpl", 'smarty_include_vars' => array('image_name' => 'product_add_additional','image_object_type' => 'product','image_type' => 'A','icon_title' => fn_get_lang_var('additional_thumbnail', $this->getLanguage()),'detailed_title' => fn_get_lang_var('additional_popup_larger_image', $this->getLanguage()),'icon_text' => fn_get_lang_var('text_additional_thumbnail', $this->getLanguage()),'detailed_text' => fn_get_lang_var('text_additional_detailed_image', $this->getLanguage()),'no_thumbnail' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
		<div class="buttons-container"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/multiple_buttons.tpl", 'smarty_include_vars' => array('item_id' => 'new_image')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
	</div>
	<hr />
</div>

</div> 
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/products_update_qty_discounts.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/products_update_features.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>


<div id="content_addons">
<?php $this->_tag_stack[] = array('hook', array('name' => "products:detailed_content")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<?php if ($this->_tpl_vars['addons']['product_configurator']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/product_configurator/hooks/products/detailed_content.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['rma']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/rma/hooks/products/detailed_content.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['seo']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/seo/hooks/products/detailed_content.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['bestsellers']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/bestsellers/hooks/products/detailed_content.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['age_verification']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/age_verification/hooks/products/detailed_content.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['discussion']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/discussion/hooks/products/detailed_content.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
</div>


<?php $this->_tag_stack[] = array('hook', array('name' => "products:tabs_content")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<?php if ($this->_tpl_vars['addons']['reward_points']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/reward_points/hooks/products/tabs_content.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['required_products']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/required_products/hooks/products/tabs_content.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>


<div class="buttons-container cm-toggle-button buttons-bg">
	<?php if ($this->_tpl_vars['mode'] == 'add'): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[products.add]")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php else: ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[products.update]")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
</div>

</form> 
<?php if ($this->_tpl_vars['mode'] != 'add'): ?>
<div id="content_blocks" class="cm-hide-save-button">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/product_tabs.tpl", 'smarty_include_vars' => array('location' => 'products','object_id' => $this->_tpl_vars['product_data']['product_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<?php endif; ?>

<?php $this->_tag_stack[] = array('hook', array('name' => "products:tabs_extra")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php if ($this->_tpl_vars['addons']['buy_together']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/buy_together/hooks/products/tabs_extra.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['product_configurator']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/product_configurator/hooks/products/tabs_extra.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['attachments']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/attachments/hooks/products/tabs_extra.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['discussion']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/discussion/hooks/products/tabs_extra.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

<?php if ($this->_tpl_vars['mode'] == 'update'): ?>
<div class="cm-hide-save-button hidden" id="content_options">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/products_update_options.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

<div id="content_files" class="cm-hide-save-button hidden">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/products_update_files.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<?php endif; ?>

<?php $this->_smarty_vars['capture']['tabsbox'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tabsbox.tpl", 'smarty_include_vars' => array('content' => $this->_smarty_vars['capture']['tabsbox'],'group_name' => $this->_tpl_vars['controller'],'active_tab' => $this->_tpl_vars['_REQUEST']['selected_section'],'track' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $this->_smarty_vars['capture']['mainbox'] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['mode'] == 'add'): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/mainbox.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('new_product', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['mainbox'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/view_tools.tpl", 'smarty_include_vars' => array('url' => "products.update?product_id=")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php $this->_tag_stack[] = array('notes', array('title' => fn_get_lang_var('preview', $this->getLanguage()))); $_block_repeat=true;smarty_block_notes($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
		<?php $this->assign('view_uri', "products.view?product_id=".($this->_tpl_vars['product_data']['product_id']), false); ?>
		<?php $this->assign('view_uri_escaped', smarty_modifier_escape(fn_url(($this->_tpl_vars['view_uri'])."&amp;action=preview", 'C', 'http', '&', @DESCR_SL), 'url'), false); ?>
		<p><?php echo fn_get_lang_var('txt_page_access_link', $this->getLanguage()); ?>
: <a target="_blank" title="<?php echo fn_url($this->_tpl_vars['view_uri'], 'C', 'http', '&', @DESCR_SL); ?>
" href="<?php echo fn_url($this->_tpl_vars['view_uri'], 'C', 'http', '&', @DESCR_SL); ?>
"><?php echo fn_compact_value(fn_url($this->_tpl_vars['view_uri'], 'C', 'http', '&', @DESCR_SL), 28); ?>
</a></p>
		<p><?php echo fn_get_lang_var('txt_access_link_as_admin', $this->getLanguage()); ?>
: <a target="_blank" title="<?php echo fn_url($this->_tpl_vars['view_uri'], 'C', 'http', '&', @DESCR_SL); ?>
" href="<?php echo fn_url("profiles.act_as_user?user_id=".($this->_tpl_vars['auth']['user_id'])."&amp;area=C&amp;redirect_url=".($this->_tpl_vars['view_uri_escaped'])); ?>
"><?php echo fn_compact_value(fn_url($this->_tpl_vars['view_uri'], 'C', 'http', '&', @DESCR_SL), 28); ?>
</a></p>
	<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_notes($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/mainbox.tpl", 'smarty_include_vars' => array('title' => smarty_modifier_unescape((fn_get_lang_var('editing_product', $this->getLanguage())).":&nbsp;".($this->_tpl_vars['product_data']['product'])),'content' => $this->_smarty_vars['capture']['mainbox'],'select_languages' => true,'tools' => $this->_smarty_vars['capture']['view_tools'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>