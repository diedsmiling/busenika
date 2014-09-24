<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:11
         compiled from views/products/manage.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'views/products/manage.tpl', 3, false),array('function', 'cycle', 'views/products/manage.tpl', 37, false),array('modifier', 'fn_url', 'views/products/manage.tpl', 9, false),array('modifier', 'fn_query_remove', 'views/products/manage.tpl', 14, false),array('modifier', 'unescape', 'views/products/manage.tpl', 49, false),array('block', 'hook', 'views/products/manage.tpl', 32, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('check_uncheck_all','position_short','code','name','price','list_price','quantity','status','edit','no_data','text_select_fields2edit_note','modify_selected','clone_selected','export_selected','delete_selected','edit_selected','choose_action','select_fields_to_edit','add_product','add_product','products'));
?>
z
<?php echo smarty_function_script(array('src' => "js/picker.js"), $this);?>


<?php ob_start(); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/products_search_form.tpl", 'smarty_include_vars' => array('dispatch' => "products.manage")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<form action="<?php echo fn_url(""); ?>
" method="post" name="manage_products_form">
<input type="hidden" name="category_id" value="<?php echo $this->_tpl_vars['search']['cid']; ?>
" />

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/pagination.tpl", 'smarty_include_vars' => array('save_current_page' => true,'save_current_url' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $this->assign('c_url', fn_query_remove($this->_tpl_vars['config']['current_url'], 'sort_by', 'sort_order'), false); ?>

<?php if ($this->_tpl_vars['settings']['DHTML']['admin_ajax_based_pagination'] == 'Y'): ?>
	<?php $this->assign('ajax_class', "cm-ajax", false); ?>
<?php endif; ?>

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
<tr>
	<th class="center">
		<input type="checkbox" name="check_all" value="Y" title="<?php echo fn_get_lang_var('check_uncheck_all', $this->getLanguage()); ?>
" class="checkbox cm-check-items" /></th>
	<?php if ($this->_tpl_vars['search']['cid'] && $this->_tpl_vars['search']['subcats'] != 'Y'): ?>
	<th><a class="<?php echo $this->_tpl_vars['ajax_class']; ?>
<?php if ($this->_tpl_vars['search']['sort_by'] == 'position'): ?> sort-link-<?php echo $this->_tpl_vars['search']['sort_order']; ?>
<?php endif; ?>" href="<?php echo fn_url(($this->_tpl_vars['c_url'])."&amp;sort_by=position&amp;sort_order=".($this->_tpl_vars['search']['sort_order'])); ?>
" rev="pagination_contents"><?php echo fn_get_lang_var('position_short', $this->getLanguage()); ?>
</a></th>
	<?php endif; ?>
	<th width="10%"><a class="<?php echo $this->_tpl_vars['ajax_class']; ?>
<?php if ($this->_tpl_vars['search']['sort_by'] == 'code'): ?> sort-link-<?php echo $this->_tpl_vars['search']['sort_order']; ?>
<?php endif; ?>" href="<?php echo fn_url(($this->_tpl_vars['c_url'])."&amp;sort_by=code&amp;sort_order=".($this->_tpl_vars['search']['sort_order'])); ?>
" rev="pagination_contents"><?php echo fn_get_lang_var('code', $this->getLanguage()); ?>
</a></th>
	<th width="50%"><a class="<?php echo $this->_tpl_vars['ajax_class']; ?>
<?php if ($this->_tpl_vars['search']['sort_by'] == 'product'): ?> sort-link-<?php echo $this->_tpl_vars['search']['sort_order']; ?>
<?php endif; ?>" href="<?php echo fn_url(($this->_tpl_vars['c_url'])."&amp;sort_by=product&amp;sort_order=".($this->_tpl_vars['search']['sort_order'])); ?>
" rev="pagination_contents"><?php echo fn_get_lang_var('name', $this->getLanguage()); ?>
</a></th>
	<th width="10%"><a class="<?php echo $this->_tpl_vars['ajax_class']; ?>
<?php if ($this->_tpl_vars['search']['sort_by'] == 'price'): ?> sort-link-<?php echo $this->_tpl_vars['search']['sort_order']; ?>
<?php endif; ?>" href="<?php echo fn_url(($this->_tpl_vars['c_url'])."&amp;sort_by=price&amp;sort_order=".($this->_tpl_vars['search']['sort_order'])); ?>
" rev="pagination_contents"><?php echo fn_get_lang_var('price', $this->getLanguage()); ?>
 (<?php echo $this->_tpl_vars['currencies'][$this->_tpl_vars['primary_currency']]['symbol']; ?>
)</a></th>
	<th width="10%"><a class="<?php echo $this->_tpl_vars['ajax_class']; ?>
<?php if ($this->_tpl_vars['search']['sort_by'] == 'list_price'): ?> sort-link-<?php echo $this->_tpl_vars['search']['sort_order']; ?>
<?php endif; ?>" href="<?php echo fn_url(($this->_tpl_vars['c_url'])."&amp;sort_by=list_price&amp;sort_order=".($this->_tpl_vars['search']['sort_order'])); ?>
" rev="pagination_contents"><?php echo fn_get_lang_var('list_price', $this->getLanguage()); ?>
 (<?php echo $this->_tpl_vars['currencies'][$this->_tpl_vars['primary_currency']]['symbol']; ?>
)</a></th>
	<th width="10%"><a class="<?php echo $this->_tpl_vars['ajax_class']; ?>
<?php if ($this->_tpl_vars['search']['sort_by'] == 'amount'): ?> sort-link-<?php echo $this->_tpl_vars['search']['sort_order']; ?>
<?php endif; ?>" href="<?php echo fn_url(($this->_tpl_vars['c_url'])."&amp;sort_by=amount&amp;sort_order=".($this->_tpl_vars['search']['sort_order'])); ?>
" rev="pagination_contents"><?php echo fn_get_lang_var('quantity', $this->getLanguage()); ?>
</a></th>
	<th><?php $this->_tag_stack[] = array('hook', array('name' => "products:manage_head")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></th>
	<th width="10%"><a class="<?php echo $this->_tpl_vars['ajax_class']; ?>
<?php if ($this->_tpl_vars['search']['sort_by'] == 'status'): ?> sort-link-<?php echo $this->_tpl_vars['search']['sort_order']; ?>
<?php endif; ?>" href="<?php echo fn_url(($this->_tpl_vars['c_url'])."&amp;sort_by=status&amp;sort_order=".($this->_tpl_vars['search']['sort_order'])); ?>
" rev="pagination_contents"><?php echo fn_get_lang_var('status', $this->getLanguage()); ?>
</a></th>
	<th>&nbsp;</th>
</tr>
<?php $_from = $this->_tpl_vars['products']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['product']):
?>
<tr <?php echo smarty_function_cycle(array('values' => "class=\"table-row\", "), $this);?>
>
	<td class="center">
   		<input type="checkbox" name="product_ids[]" value="<?php echo $this->_tpl_vars['product']['product_id']; ?>
" class="checkbox cm-item" /></td>
	<?php if ($this->_tpl_vars['search']['cid'] && $this->_tpl_vars['search']['subcats'] != 'Y'): ?>
	<td>
		<input type="text" name="products_data[<?php echo $this->_tpl_vars['product']['product_id']; ?>
][position]" size="3" value="<?php echo $this->_tpl_vars['product']['position']; ?>
" class="input-text-short" /></td>
	<?php endif; ?>
	<td>
		<input type="text" name="products_data[<?php echo $this->_tpl_vars['product']['product_id']; ?>
][product_code]" size="6" value="<?php echo $this->_tpl_vars['product']['product_code']; ?>
" class="input-text" /></td>
	<td width="100%">
		<div class="float-left">
				<input type="hidden" name="products_data[<?php echo $this->_tpl_vars['product']['product_id']; ?>
][product]" value="<?php echo $this->_tpl_vars['product']['product']; ?>
" />
				<a href="<?php echo fn_url("products.update?product_id=".($this->_tpl_vars['product']['product_id'])); ?>
" <?php if ($this->_tpl_vars['product']['status'] == 'N'): ?>class="manage-root-item-disabled"<?php endif; ?>><?php echo smarty_modifier_unescape($this->_tpl_vars['product']['product']); ?>
 <?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/companies/components/company_name.tpl", 'smarty_include_vars' => array('company_id' => $this->_tpl_vars['product']['company_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></a></div>
		<div class="float-right">
		</div>
	</td>
	<td class="center">
		<input type="text" name="products_data[<?php echo $this->_tpl_vars['product']['product_id']; ?>
][price]" size="6" value="<?php echo $this->_tpl_vars['product']['price']; ?>
" class="input-text-medium" /></td>
	<td class="center">
		<input type="text" name="products_data[<?php echo $this->_tpl_vars['product']['product_id']; ?>
][list_price]" size="6" value="<?php echo $this->_tpl_vars['product']['list_price']; ?>
" class="input-text-medium" /></td>
	<td class="center">
		<?php if ($this->_tpl_vars['product']['tracking'] == 'O'): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('edit', $this->getLanguage()),'but_href' => "product_options.inventory?product_id=".($this->_tpl_vars['product']['product_id']),'but_role' => 'edit')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php else: ?>
		<input type="text" name="products_data[<?php echo $this->_tpl_vars['product']['product_id']; ?>
][amount]" size="6" value="<?php echo $this->_tpl_vars['product']['amount']; ?>
" class="input-text-short" />
		<?php endif; ?>
	</td>
	<td><?php $this->_tag_stack[] = array('hook', array('name' => "products:manage_body")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></td>
	<td>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_popup.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['product']['product_id'],'status' => $this->_tpl_vars['product']['status'],'hidden' => true,'object_id_name' => 'product_id','table' => 'products')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
	<td class="nowrap">
		<?php if ($this->_tpl_vars['product']['hasimg'] == '1'): ?>
		<img style="height: 12px; margin-bottom: 2px;" src="/images/picture_icon.png">
		<?php else: ?>
		<img style="height: 12px; margin-bottom: 2px;" src="/images/picture_icon_desaturated.png">
		<?php endif; ?>
		
		<?php if ($this->_tpl_vars['product']['hasimg_detailed'] == '1'): ?>
		<img src="/images/picture_icon.png">
		<?php else: ?>
		<img src="/images/picture_icon_desaturated.png">
		<?php endif; ?>
		
		<?php ob_start(); ?>
		<?php $this->_tag_stack[] = array('hook', array('name' => "products:list_extra_links")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
		<li><a class="cm-confirm" href="<?php echo fn_url("products.delete?product_id=".($this->_tpl_vars['product']['product_id'])); ?>
""><img src="/images/cross.png"></a></li>
		<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
		<?php $this->_smarty_vars['capture']['tools_items'] = ob_get_contents(); ob_end_clean(); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/table_tools_list.tpl", 'smarty_include_vars' => array('prefix' => $this->_tpl_vars['product']['product_id'],'tools_list' => $this->_smarty_vars['capture']['tools_items'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
</tr>
<?php endforeach; else: ?>
<tr class="no-items">
	<td colspan="<?php if ($this->_tpl_vars['search']['cid'] && $this->_tpl_vars['search']['subcats'] != 'Y'): ?>12<?php else: ?>11<?php endif; ?>"><p><?php echo fn_get_lang_var('no_data', $this->getLanguage()); ?>
</p></td>
</tr>
<?php endif; unset($_from); ?>
</table>

<?php if ($this->_tpl_vars['products']): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/table_tools.tpl", 'smarty_include_vars' => array('href' => "#products",'visibility' => 'Y')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/pagination.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php ob_start(); ?>
<div class="object-container">
	<p><?php echo fn_get_lang_var('text_select_fields2edit_note', $this->getLanguage()); ?>
</p>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/products_select_fields.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

<div class="buttons-container">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('modify_selected', $this->getLanguage()),'but_name' => "dispatch[products.store_selection]",'cancel_action' => 'close')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<?php $this->_smarty_vars['capture']['select_fields_to_edit'] = ob_get_contents(); ob_end_clean(); ?>

<div class="buttons-container buttons-bg">
	<?php if ($this->_tpl_vars['products']): ?>
	<div class="float-left">
		<?php ob_start(); ?>
		<ul>
			<li><a class="cm-process-items" name="dispatch[products.m_clone]" rev="manage_products_form"><?php echo fn_get_lang_var('clone_selected', $this->getLanguage()); ?>
</a></li>
			<li><a class="cm-process-items" name="dispatch[products.export_range]" rev="manage_products_form"><?php echo fn_get_lang_var('export_selected', $this->getLanguage()); ?>
</a></li>
			<li><a class="cm-confirm cm-process-items" name="dispatch[products.m_delete]" rev="manage_products_form"><?php echo fn_get_lang_var('delete_selected', $this->getLanguage()); ?>
</a></li>
			<li><a onclick="if ($('input.cm-item[type=checkbox]:checked', $(this).parents('form:first')).length > 0) <?php echo $this->_tpl_vars['ldelim']; ?>
jQuery.show_picker('select_fields_to_edit', '', '.object-container');<?php echo $this->_tpl_vars['rdelim']; ?>
 else <?php echo $this->_tpl_vars['ldelim']; ?>
alert(window['lang'].error_no_items_selected);<?php echo $this->_tpl_vars['rdelim']; ?>
"><?php echo fn_get_lang_var('edit_selected', $this->getLanguage()); ?>
</a></li>
		</ul>
		<?php $this->_smarty_vars['capture']['tools_list'] = ob_get_contents(); ob_end_clean(); ?>

		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[products.m_update]",'but_role' => 'button_main')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('prefix' => 'main','hide_actions' => true,'tools_list' => $this->_smarty_vars['capture']['tools_list'],'display' => 'inline','link_text' => fn_get_lang_var('choose_action', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => 'select_fields_to_edit','text' => fn_get_lang_var('select_fields_to_edit', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['select_fields_to_edit'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
	<?php endif; ?>
	
	<div class="float-right">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('tool_href' => "products.add",'prefix' => 'bottom','link_text' => fn_get_lang_var('add_product', $this->getLanguage()),'hide_tools' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
</div>

<?php ob_start(); ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('tool_href' => "products.add",'prefix' => 'top','link_text' => fn_get_lang_var('add_product', $this->getLanguage()),'hide_tools' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $this->_smarty_vars['capture']['tools'] = ob_get_contents(); ob_end_clean(); ?>

</form>

<?php $this->_smarty_vars['capture']['mainbox'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/mainbox.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('products', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['mainbox'],'title_extra' => $this->_smarty_vars['capture']['title_extra'],'tools' => $this->_smarty_vars['capture']['tools'],'select_languages' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>