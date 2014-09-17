<?php /* Smarty version 2.6.18, created on 2014-09-16 23:39:25
         compiled from views/categories/update.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'views/categories/update.tpl', 11, false),array('modifier', 'fn_show_picker', 'views/categories/update.tpl', 27, false),array('modifier', 'default', 'views/categories/update.tpl', 29, false),array('modifier', 'fn_get_plain_categories_tree', 'views/categories/update.tpl', 34, false),array('modifier', 'strpos', 'views/categories/update.tpl', 35, false),array('modifier', 'indent', 'views/categories/update.tpl', 36, false),array('modifier', 'fn_get_usergroups', 'views/categories/update.tpl', 80, false),array('modifier', 'fn_get_product_details_views', 'views/categories/update.tpl', 107, false),array('modifier', 'fn_get_products_views', 'views/categories/update.tpl', 124, false),array('modifier', 'escape', 'views/categories/update.tpl', 189, false),array('modifier', 'fn_compact_value', 'views/categories/update.tpl', 190, false),array('block', 'hook', 'views/categories/update.tpl', 157, false),array('block', 'notes', 'views/categories/update.tpl', 187, false),array('function', 'script', 'views/categories/update.tpl', 194, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('information','name','location','root_level','location','root_level','description','images','text_category_icon','text_category_detailed_image','seo_meta_data','page_title','meta_description','meta_keywords','availability','usergroups','to_all_subcats','position','created_date','product_details_layout','tt_views_categories_update_product_details_layout','use_custom_layout','product_columns','available_layouts','default_category_layout','new_category','preview','txt_page_access_link','txt_access_link_as_admin','editing_category'));
?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/file_browser.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php ob_start(); ?>

<?php ob_start(); ?>

<form action="<?php echo fn_url(""); ?>
" method="post" name="category_update_form" class="cm-form-highlight" enctype="multipart/form-data">
<input type="hidden" name="fake" value="1" />
<input type="hidden" name="category_id" value="<?php echo $this->_tpl_vars['category_data']['category_id']; ?>
" />
<input type="hidden" name="selected_section" value="<?php echo $this->_tpl_vars['_REQUEST']['selected_section']; ?>
" />

<div id="content_detailed">
	<fieldset>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('information', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<div class="form-field">
		<label for="category" class="cm-required"><?php echo fn_get_lang_var('name', $this->getLanguage()); ?>
:</label>
		<input type="text" name="category_data[category]" id="category" size="55" value="<?php echo $this->_tpl_vars['category_data']['category']; ?>
" class="input-text-large main-input" />
	</div>

	<div class="form-field">
		<?php if (fn_show_picker('categories', @CATEGORY_SHOW_ALL)): ?>
			<label class="cm-required" for="location_category_id"><?php echo fn_get_lang_var('location', $this->getLanguage()); ?>
:</label>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/categories_picker.tpl", 'smarty_include_vars' => array('data_id' => 'location_category','input_name' => "category_data[parent_id]",'item_ids' => smarty_modifier_default(@$this->_tpl_vars['category_data']['parent_id'], '0'),'hide_link' => true,'hide_delete_button' => true,'show_root' => true,'default_name' => fn_get_lang_var('root_level', $this->getLanguage()),'display_input_id' => 'location_category_id','except_id' => $this->_tpl_vars['category_data']['category_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php else: ?>
			<label for="category_data_parent_id"><?php echo fn_get_lang_var('location', $this->getLanguage()); ?>
:</label>
			<select	name="category_data[parent_id]" id="category_data_parent_id">
				<option	value="0" <?php if ($this->_tpl_vars['category_data']['parent_id'] == '0'): ?>selected="selected"<?php endif; ?>>- <?php echo fn_get_lang_var('root_level', $this->getLanguage()); ?>
 -</option>
				<?php $_from = fn_get_plain_categories_tree(0, false); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cat']):
?>
					<?php if (strpos($this->_tpl_vars['cat']['id_path'], ($this->_tpl_vars['category_data']['id_path'])."/") === false && $this->_tpl_vars['cat']['category_id'] != $this->_tpl_vars['category_data']['category_id'] || $this->_tpl_vars['mode'] == 'add'): ?>
						<option	value="<?php echo $this->_tpl_vars['cat']['category_id']; ?>
" <?php if ($this->_tpl_vars['category_data']['parent_id'] == $this->_tpl_vars['cat']['category_id']): ?>selected="selected"<?php endif; ?>><?php echo smarty_modifier_indent($this->_tpl_vars['cat']['category'], $this->_tpl_vars['cat']['level'], "&#166;&nbsp;&nbsp;&nbsp;&nbsp;", "&#166;--&nbsp;"); ?>
</option>
					<?php endif; ?>
				<?php endforeach; endif; unset($_from); ?>
			</select>
		<?php endif; ?>
	</div>

	<div class="form-field">
		<label for="cat_descr"><?php echo fn_get_lang_var('description', $this->getLanguage()); ?>
:</label>
		<textarea id="cat_descr" name="category_data[description]" cols="55" rows="8" class="input-textarea-long"><?php echo $this->_tpl_vars['category_data']['description']; ?>
</textarea>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/wysiwyg.tpl", 'smarty_include_vars' => array('id' => 'cat_descr')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_status.tpl", 'smarty_include_vars' => array('input_name' => "category_data[status]",'id' => 'category_data','obj' => $this->_tpl_vars['category_data'],'hidden' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<div class="form-field">
		<label><?php echo fn_get_lang_var('images', $this->getLanguage()); ?>
:</label>
		<div class="float-left">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/attach_images.tpl", 'smarty_include_vars' => array('image_name' => 'category_main','image_object_type' => 'category','image_pair' => $this->_tpl_vars['category_data']['main_pair'],'image_object_id' => $this->_tpl_vars['category_data']['category_id'],'icon_text' => fn_get_lang_var('text_category_icon', $this->getLanguage()),'detailed_text' => fn_get_lang_var('text_category_detailed_image', $this->getLanguage()),'no_thumbnail' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
	</div>

	</fieldset>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('seo_meta_data', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<div class="form-field">
		<label for="page_title"><?php echo fn_get_lang_var('page_title', $this->getLanguage()); ?>
:</label>
		<input type="text" name="category_data[page_title]" id="page_title" size="55" value="<?php echo $this->_tpl_vars['category_data']['page_title']; ?>
" class="input-text-large" />
	</div>

	<div class="form-field">
		<label for="meta_description"><?php echo fn_get_lang_var('meta_description', $this->getLanguage()); ?>
:</label>
		<textarea name="category_data[meta_description]" id="meta_description" cols="55" rows="4" class="input-textarea-long"><?php echo $this->_tpl_vars['category_data']['meta_description']; ?>
</textarea>
	</div>

	<div class="form-field">
		<label for="meta_keywords"><?php echo fn_get_lang_var('meta_keywords', $this->getLanguage()); ?>
:</label>
		<textarea name="category_data[meta_keywords]" id="meta_keywords" cols="55" rows="4" class="input-textarea-long"><?php echo $this->_tpl_vars['category_data']['meta_keywords']; ?>
</textarea>
	</div>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('availability', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<div class="form-field">
		<label><?php echo fn_get_lang_var('usergroups', $this->getLanguage()); ?>
:</label>
			<div class="select-field">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_usergroups.tpl", 'smarty_include_vars' => array('id' => 'ug_id','name' => "category_data[usergroup_ids]",'usergroups' => fn_get_usergroups('C', @DESCR_SL),'usergroup_ids' => $this->_tpl_vars['category_data']['usergroup_ids'],'input_extra' => "",'list_mode' => false)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<p><label for="usergroup_to_subcats"><?php echo fn_get_lang_var('to_all_subcats', $this->getLanguage()); ?>
</label>
				<input id="usergroup_to_subcats" type="checkbox" name="category_data[usergroup_to_subcats]" value="Y" /></p>
			</div>
	</div>

	<div class="form-field">
		<label for="category_position"><?php echo fn_get_lang_var('position', $this->getLanguage()); ?>
:</label>
		<input type="text" name="category_data[position]" id="category_position" size="10" value="<?php echo $this->_tpl_vars['category_data']['position']; ?>
" class="input-text-short" />
	</div>

	<div class="form-field">
		<label><?php echo fn_get_lang_var('created_date', $this->getLanguage()); ?>
:</label>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/calendar.tpl", 'smarty_include_vars' => array('date_id' => 'category_date','date_name' => "category_data[timestamp]",'date_val' => smarty_modifier_default(@$this->_tpl_vars['category_data']['timestamp'], @TIME),'start_year' => $this->_tpl_vars['settings']['Company']['company_start_year'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/localizations/components/select.tpl", 'smarty_include_vars' => array('data_from' => $this->_tpl_vars['category_data']['localization'],'data_name' => "category_data[localization]")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
	</fieldset>
</div>

<div id="content_layout">
	<fieldset>
	
	<div class="form-field">
		<label for="category_default_layout"><?php echo fn_get_lang_var('product_details_layout', $this->getLanguage()); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('tt_views_categories_update_product_details_layout', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
		<select id="category_default_layout" name="category_data[product_details_layout]">
			<?php $_from = fn_get_product_details_views('category'); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['layout'] => $this->_tpl_vars['item']):
?>
				<option <?php if ($this->_tpl_vars['category_data']['product_details_layout'] == $this->_tpl_vars['layout']): ?>selected="selected"<?php endif; ?> value="<?php echo $this->_tpl_vars['layout']; ?>
"><?php echo $this->_tpl_vars['item']; ?>
</option>
			<?php endforeach; endif; unset($_from); ?>
		</select>
	</div>
		
	<div class="form-field">
		<label for="use_custom_templates"><?php echo fn_get_lang_var('use_custom_layout', $this->getLanguage()); ?>
:</label>
		<input type="hidden" value="N" name="category_data[use_custom_templates]"/>
		<input type="checkbox" class="checkbox cm-toggle-checkbox" value="Y" name="category_data[use_custom_templates]" id="use_custom_templates"<?php if ($this->_tpl_vars['category_data']['selected_layouts']): ?> checked="checked"<?php endif; ?> />
	</div>
	
	<div class="form-field">
		<label for="category_product_columns"><?php echo fn_get_lang_var('product_columns', $this->getLanguage()); ?>
:</label>
		<input type="text" name="category_data[product_columns]" id="category_product_columns" size="10" value="<?php echo $this->_tpl_vars['category_data']['product_columns']; ?>
" class="input-text-short cm-toggle-element" <?php if (! $this->_tpl_vars['category_data']['selected_layouts']): ?>disabled="disabled"<?php endif; ?> />
	</div>

	<?php $this->assign('layouts', fn_get_products_views("", false, false), false); ?>
	<div class="form-field">
		<label for="available_layouts"><?php echo fn_get_lang_var('available_layouts', $this->getLanguage()); ?>
:</label>
		<div class="table-filters">
			<div class="scroll-y">
				<?php $_from = $this->_tpl_vars['layouts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['layout'] => $this->_tpl_vars['item']):
?>
					<div class="select-field"><input type="checkbox" class="checkbox cm-combo-checkbox cm-toggle-element" name="category_data[selected_layouts][<?php echo $this->_tpl_vars['layout']; ?>
]" id="layout_<?php echo $this->_tpl_vars['layout']; ?>
" value="<?php echo $this->_tpl_vars['layout']; ?>
" <?php if (( $this->_tpl_vars['category_data']['selected_layouts'][$this->_tpl_vars['layout']] ) || ( ! $this->_tpl_vars['category_data']['selected_layouts'] && $this->_tpl_vars['item']['active'] )): ?>checked="checked"<?php endif; ?> <?php if (! $this->_tpl_vars['category_data']['selected_layouts']): ?>disabled="disabled"<?php endif; ?> /><label for="layout_<?php echo $this->_tpl_vars['layout']; ?>
"><?php echo $this->_tpl_vars['item']['title']; ?>
</label></div>
				<?php endforeach; endif; unset($_from); ?>
			</div>
		</div>
	</div>
	
	<div class="form-field">
		<label for="category_default_layout"><?php echo fn_get_lang_var('default_category_layout', $this->getLanguage()); ?>
:</label>
		<select id="category_default_layout" class="cm-combo-select cm-toggle-element" name="category_data[default_layout]" <?php if (! $this->_tpl_vars['category_data']['selected_layouts']): ?>disabled="disabled"<?php endif; ?>>
			<?php $_from = $this->_tpl_vars['layouts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['layout'] => $this->_tpl_vars['item']):
?>
				<?php if (( $this->_tpl_vars['category_data']['selected_layouts'][$this->_tpl_vars['layout']] ) || ( ! $this->_tpl_vars['category_data']['selected_layouts'] && $this->_tpl_vars['item']['active'] )): ?>
					<option <?php if ($this->_tpl_vars['category_data']['default_layout'] == $this->_tpl_vars['layout']): ?>selected="selected"<?php endif; ?> value="<?php echo $this->_tpl_vars['layout']; ?>
"><?php echo $this->_tpl_vars['item']['title']; ?>
</option>
				<?php endif; ?>
			<?php endforeach; endif; unset($_from); ?>
		</select>
	</div>
	
	</fieldset>
</div>

<?php if ($this->_tpl_vars['mode'] != 'add'): ?>
<div id="content_blocks">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/block_manager/components/select_blocks.tpl", 'smarty_include_vars' => array('object_id' => $this->_tpl_vars['category_data']['category_id'],'data_name' => 'category_data','section' => 'categories')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<?php endif; ?>

<div id="content_addons">
<?php $this->_tag_stack[] = array('hook', array('name' => "categories:detailed_content")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<?php if ($this->_tpl_vars['addons']['seo']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/seo/hooks/categories/detailed_content.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['age_verification']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/age_verification/hooks/categories/detailed_content.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['discussion']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/discussion/hooks/categories/detailed_content.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
</div>

<?php $this->_tag_stack[] = array('hook', array('name' => "categories:tabs_content")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<?php if ($this->_tpl_vars['addons']['reward_points']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/reward_points/hooks/categories/tabs_content.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

<div class="buttons-container cm-toggle-button buttons-bg">
	<?php if ($this->_tpl_vars['mode'] == 'add'): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[categories.add]")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php else: ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[categories.update]")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
</div>

</form>

<?php if ($this->_tpl_vars['mode'] != 'add'): ?>
	<?php $this->_tag_stack[] = array('hook', array('name' => "categories:tabs_extra")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
	<?php if ($this->_tpl_vars['addons']['discussion']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/discussion/hooks/categories/tabs_extra.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php endif; ?>

<?php $this->_smarty_vars['capture']['tabsbox'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tabsbox.tpl", 'smarty_include_vars' => array('content' => $this->_smarty_vars['capture']['tabsbox'],'group_name' => $this->_tpl_vars['controller'],'active_tab' => $this->_tpl_vars['_REQUEST']['selected_section'],'track' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $this->_smarty_vars['capture']['mainbox'] = ob_get_contents(); ob_end_clean(); ?>

<?php if ($this->_tpl_vars['mode'] == 'add'): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/mainbox.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('new_category', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['mainbox'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
	<?php $this->_tag_stack[] = array('notes', array('title' => fn_get_lang_var('preview', $this->getLanguage()))); $_block_repeat=true;smarty_block_notes($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
		<?php $this->assign('view_uri', "categories.view?category_id=".($this->_tpl_vars['category_data']['category_id']), false); ?>
		<?php $this->assign('view_uri_escaped', smarty_modifier_escape(fn_url(($this->_tpl_vars['view_uri'])."`&amp;action=preview", 'C', 'http', '&', @DESCR_SL), 'url'), false); ?>
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
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/mainbox.tpl", 'smarty_include_vars' => array('title' => (fn_get_lang_var('editing_category', $this->getLanguage())).":&nbsp;".($this->_tpl_vars['category_data']['category']),'content' => $this->_smarty_vars['capture']['mainbox'],'select_languages' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?><?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>