<?php /* Smarty version 2.6.18, created on 2014-09-15 23:41:06
         compiled from views/product_features/components/product_features_search_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'views/product_features/components/product_features_search_form.tpl', 5, false),array('modifier', 'fn_show_picker', 'views/product_features/components/product_features_search_form.tpl', 12, false),array('modifier', 'fn_get_plain_categories_tree', 'views/product_features/components/product_features_search_form.tpl', 22, false),array('modifier', 'indent', 'views/product_features/components/product_features_search_form.tpl', 23, false),array('modifier', 'in_array', 'views/product_features/components/product_features_search_form.tpl', 50, false),array('block', 'hook', 'views/product_features/components/product_features_search_form.tpl', 110, false),array('function', 'script', 'views/product_features/components/product_features_search_form.tpl', 121, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('category','all_categories','all_categories','feature','search','type','checkbox','single','checkbox','multiple','selectbox','text','selectbox','number','selectbox','extended','others','text','others','number','others','date','display_on','product','catalog_pages','group','ungroupped_features'));
?>

<?php ob_start(); ?>

<form action="<?php echo fn_url(""); ?>
" name="product_features_search_form" method="get">

<table cellpadding="10" cellspacing="0" border="0" class="search-header">
<tr>
	<td class="nowrap search-field">
		<label><?php echo fn_get_lang_var('category', $this->getLanguage()); ?>
:</label>
		<div class="break clear correct-picker-but">
		<?php if (fn_show_picker('categories', @CATEGORY_SHOW_ALL)): ?>
			<?php if ($this->_tpl_vars['search']['category_ids']): ?>
				<?php $this->assign('s_cid', $this->_tpl_vars['search']['category_ids'], false); ?>
			<?php else: ?>
				<?php $this->assign('s_cid', '0', false); ?>
			<?php endif; ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/categories_picker.tpl", 'smarty_include_vars' => array('data_id' => 'location_category','input_name' => 'category_ids','item_ids' => $this->_tpl_vars['s_cid'],'hide_link' => true,'hide_delete_button' => true,'show_root' => true,'default_name' => fn_get_lang_var('all_categories', $this->getLanguage()),'extra' => "")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php else: ?>
			<select	name="category_ids">
				<option	value="0" <?php if ($this->_tpl_vars['category_data']['parent_id'] == '0'): ?>selected="selected"<?php endif; ?>>- <?php echo fn_get_lang_var('all_categories', $this->getLanguage()); ?>
 -</option>
				<?php $_from = fn_get_plain_categories_tree(0, false); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['search_cat']):
?>
					<option	value="<?php echo $this->_tpl_vars['search_cat']['category_id']; ?>
" <?php if ($this->_tpl_vars['search']['category_ids'] == $this->_tpl_vars['search_cat']['category_id']): ?>selected="selected"<?php endif; ?>><?php echo smarty_modifier_indent($this->_tpl_vars['search_cat']['category'], $this->_tpl_vars['search_cat']['level'], "&#166;&nbsp;&nbsp;&nbsp;&nbsp;", "&#166;--&nbsp;"); ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
			</select>
		<?php endif; ?>
		</div>
	</td>
	<td class="nowrap search-field">
		<label for="fname"><?php echo fn_get_lang_var('feature', $this->getLanguage()); ?>
:</label>
		<div class="break">
			<input type="text" name="description" id="fname" value="<?php echo $this->_tpl_vars['search']['description']; ?>
" size="30" class="search-input-text" />
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/search_go.tpl", 'smarty_include_vars' => array('search' => 'Y','but_name' => $this->_tpl_vars['dispatch'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
	</td>
	<td class="buttons-container">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('search', $this->getLanguage()),'but_name' => "dispatch[".($this->_tpl_vars['dispatch'])."]",'but_role' => 'submit')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
</tr>
</table>

<?php ob_start(); ?>

<div class="search-field">
	<label><?php echo fn_get_lang_var('type', $this->getLanguage()); ?>
:</label>

	<table cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td class="select-field">
			<input id="elm_checkbox_single" class="checkbox" type="checkbox" name="feature_types[]" <?php if (smarty_modifier_in_array('C', $this->_tpl_vars['search']['feature_types'])): ?>checked="checked"<?php endif; ?> value="C"/>
			<label for="elm_checkbox_single"><?php echo fn_get_lang_var('checkbox', $this->getLanguage()); ?>
:&nbsp;<?php echo fn_get_lang_var('single', $this->getLanguage()); ?>
</label>
		</td>
		<td class="select-field">
			<input id="elm_checkbox_multiple" class="checkbox" type="checkbox" name="feature_types[]" <?php if (smarty_modifier_in_array('M', $this->_tpl_vars['search']['feature_types'])): ?>checked="checked"<?php endif; ?> value="M"/>
			<label for="elm_checkbox_multiple"><?php echo fn_get_lang_var('checkbox', $this->getLanguage()); ?>
:&nbsp;<?php echo fn_get_lang_var('multiple', $this->getLanguage()); ?>
</label>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="select-field">
			<input id="elm_selectbox_text" class="checkbox" type="checkbox" name="feature_types[]" <?php if (smarty_modifier_in_array('S', $this->_tpl_vars['search']['feature_types'])): ?>checked="checked"<?php endif; ?> value="S"/>
			<label for="elm_selectbox_text"><?php echo fn_get_lang_var('selectbox', $this->getLanguage()); ?>
:&nbsp;<?php echo fn_get_lang_var('text', $this->getLanguage()); ?>
</label>
		</td>
		<td class="select-field">
			<input id="elm_selectbox_number" class="checkbox" type="checkbox" name="feature_types[]" <?php if (smarty_modifier_in_array('N', $this->_tpl_vars['search']['feature_types'])): ?>checked="checked"<?php endif; ?> value="N"/>
			<label for="elm_selectbox_number"><?php echo fn_get_lang_var('selectbox', $this->getLanguage()); ?>
:&nbsp;<?php echo fn_get_lang_var('number', $this->getLanguage()); ?>
</label>
		</td>
		<td class="select-field">
			<input id="elm_selectbox_extended" class="checkbox" type="checkbox" name="feature_types[]" <?php if (smarty_modifier_in_array('E', $this->_tpl_vars['search']['feature_types'])): ?>checked="checked"<?php endif; ?> value="E"/>
			<label for="elm_selectbox_extended"><?php echo fn_get_lang_var('selectbox', $this->getLanguage()); ?>
:&nbsp;<?php echo fn_get_lang_var('extended', $this->getLanguage()); ?>
</label>
		</td>
	</tr>
	<tr>
		<td class="select-field">
			<input id="elm_others_text" class="checkbox" type="checkbox" name="feature_types[]" <?php if (smarty_modifier_in_array('T', $this->_tpl_vars['search']['feature_types'])): ?>checked="checked"<?php endif; ?> value="T"/>
			<label for="elm_others_text"><?php echo fn_get_lang_var('others', $this->getLanguage()); ?>
:&nbsp;<?php echo fn_get_lang_var('text', $this->getLanguage()); ?>
</label>
		</td>
		<td class="select-field">
			<input id="elm_others_number" class="checkbox" type="checkbox" name="feature_types[]" <?php if (smarty_modifier_in_array('O', $this->_tpl_vars['search']['feature_types'])): ?>checked="checked"<?php endif; ?> value="O"/>
			<label for="elm_others_number"><?php echo fn_get_lang_var('others', $this->getLanguage()); ?>
:&nbsp;<?php echo fn_get_lang_var('number', $this->getLanguage()); ?>
</label>
		</td>
		<td class="select-field">
			<input id="elm_others_date" class="checkbox" type="checkbox" name="feature_types[]" <?php if (smarty_modifier_in_array('D', $this->_tpl_vars['search']['feature_types'])): ?>checked="checked"<?php endif; ?> value="D"/>
			<label for="elm_others_date"><?php echo fn_get_lang_var('others', $this->getLanguage()); ?>
:&nbsp;<?php echo fn_get_lang_var('date', $this->getLanguage()); ?>
</label>
		</td>
	</tr>
	</table>
</div>

<div class="search-field">
	<label for="elm_display_on"><?php echo fn_get_lang_var('display_on', $this->getLanguage()); ?>
:</label>
	<select name="display_on" id="elm_display_on">
		<option value="">--</option>
		<option value="product" <?php if ($this->_tpl_vars['search']['display_on'] == 'product'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('product', $this->getLanguage()); ?>
</option>
		<option value="catalog" <?php if ($this->_tpl_vars['search']['display_on'] == 'catalog'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('catalog_pages', $this->getLanguage()); ?>
</option>
	</select>
</div>

<div class="search-field">
	<label for="elm_parent_id"><?php echo fn_get_lang_var('group', $this->getLanguage()); ?>
:</label>
	<select name="parent_id" id="elm_parent_id">
		<option value="">--</option>
		<option <?php if ($this->_tpl_vars['search']['parent_id'] === '0'): ?>selected="selected"<?php endif; ?> value="0"><?php echo fn_get_lang_var('ungroupped_features', $this->getLanguage()); ?>
</option>
		<?php $_from = $this->_tpl_vars['group_features']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['group_feature']):
?>
			<option value="<?php echo $this->_tpl_vars['group_feature']['feature_id']; ?>
"<?php if ($this->_tpl_vars['group_feature']['feature_id'] == $this->_tpl_vars['search']['parent_id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['group_feature']['description']; ?>
</option>
		<?php endforeach; endif; unset($_from); ?>
	</select>
</div>

<?php $this->_tag_stack[] = array('hook', array('name' => "product_features:search_form")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

<?php $this->_smarty_vars['capture']['advanced_search'] = ob_get_contents(); ob_end_clean(); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/advanced_search.tpl", 'smarty_include_vars' => array('content' => $this->_smarty_vars['capture']['advanced_search'],'dispatch' => $this->_tpl_vars['dispatch'],'view_type' => 'product_features')));
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