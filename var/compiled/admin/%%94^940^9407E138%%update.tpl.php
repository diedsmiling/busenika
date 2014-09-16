<?php /* Smarty version 2.6.18, created on 2014-09-15 23:41:06
         compiled from views/product_features/update.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'views/product_features/update.tpl', 15, false),array('modifier', 'strpos', 'views/product_features/update.tpl', 24, false),array('modifier', 'default', 'views/product_features/update.tpl', 185, false),array('modifier', 'explode', 'views/product_features/update.tpl', 242, false),array('modifier', 'replace', 'views/product_features/update.tpl', 244, false),array('block', 'hook', 'views/product_features/update.tpl', 113, false),array('function', 'cycle', 'views/product_features/update.tpl', 132, false),array('function', 'math', 'views/product_features/update.tpl', 185, false),array('function', 'script', 'views/product_features/update.tpl', 259, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('general','variants','categories','name','position','description','type','group','checkbox','single','multiple','selectbox','text','number','extended','others','text','number','date','group','none','product','tt_views_product_features_update_product','catalog_pages','tt_views_product_features_update_catalog_pages','prefix','tt_views_product_features_update_prefix','suffix','tt_views_product_features_update_suffix','position_short','variant','expand_collapse_list','expand_collapse_list','expand_collapse_list','expand_collapse_list','expand_collapse_list','expand_collapse_list','expand_collapse_list','expand_collapse_list','extra','image','description','page_title','ttc_page_title','url','meta_description','meta_keywords','expand_collapse_list','expand_collapse_list','expand_collapse_list','expand_collapse_list','extra','image','description','page_title','ttc_page_title','url','meta_description','meta_keywords','text_all_items_included','categories'));
?>

<?php if ($this->_tpl_vars['mode'] == 'add'): ?>
	<?php if ($this->_tpl_vars['is_group'] == true): ?>
		<?php $this->assign('id', '0G', false); ?>
	<?php else: ?>
		<?php $this->assign('id', 0, false); ?>
	<?php endif; ?>
<?php else: ?>
	<?php $this->assign('id', $this->_tpl_vars['feature']['feature_id'], false); ?>
<?php endif; ?>

<div id="content_group<?php echo $this->_tpl_vars['id']; ?>
">

<form action="<?php echo fn_url(""); ?>
" method="post" name="update_features_form_<?php echo $this->_tpl_vars['id']; ?>
" class="cm-form-highlight cm-disable-empty-files" enctype="multipart/form-data">

<input type="hidden" name="redirect_url" value="<?php echo $this->_tpl_vars['_REQUEST']['return_url']; ?>
" />
<input type="hidden" name="feature_id" value="<?php echo $this->_tpl_vars['id']; ?>
" />

<div class="object-container">
	<div class="tabs cm-j-tabs">
		<ul>
			<li id="tab_details_<?php echo $this->_tpl_vars['id']; ?>
" class="cm-js cm-active"><a><?php echo fn_get_lang_var('general', $this->getLanguage()); ?>
</a></li>
			<li id="tab_variants_<?php echo $this->_tpl_vars['id']; ?>
" class="cm-js <?php if ($this->_tpl_vars['feature']['feature_type'] && strpos('SMNE', $this->_tpl_vars['feature']['feature_type']) === false || ! $this->_tpl_vars['feature']): ?>hidden<?php endif; ?>"><a><?php echo fn_get_lang_var('variants', $this->getLanguage()); ?>
</a></li>
			<li id="tab_categories_<?php echo $this->_tpl_vars['id']; ?>
" class="cm-js <?php if ($this->_tpl_vars['feature']['parent_id']): ?> hidden<?php endif; ?>"><a><?php echo fn_get_lang_var('categories', $this->getLanguage()); ?>
</a></li>
		</ul>
	</div>

	<div class="cm-tabs-content" id="tabs_content_<?php echo $this->_tpl_vars['id']; ?>
">
		
		<div id="content_tab_details_<?php echo $this->_tpl_vars['id']; ?>
">
		<fieldset>
			<div class="form-field">
				<label class="cm-required" for="feature_name_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('name', $this->getLanguage()); ?>
:</label>
				<input type="text" name="feature_data[description]" value="<?php echo $this->_tpl_vars['feature']['description']; ?>
" class="input-text-large main-input" id="feature_name_<?php echo $this->_tpl_vars['id']; ?>
" />
			</div>

			<div class="form-field">
				<label for="feature_position_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('position', $this->getLanguage()); ?>
:</label>
				<input type="text" size="3" name="feature_data[position]" value="<?php echo $this->_tpl_vars['feature']['position']; ?>
" class="input-text-short" id="feature_position_<?php echo $this->_tpl_vars['id']; ?>
" />
			</div>

			<div class="form-field">
				<label for="feature_description_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('description', $this->getLanguage()); ?>
:</label>
				<textarea name="feature_data[full_description]" cols="55" rows="4" class="input-textarea-long" id="feature_description_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo $this->_tpl_vars['feature']['full_description']; ?>
</textarea>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/wysiwyg.tpl", 'smarty_include_vars' => array('id' => "feature_description_".($this->_tpl_vars['id']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</div>

			<?php if ($this->_tpl_vars['is_group'] || $this->_tpl_vars['feature']['feature_type'] == 'G'): ?>
				<input type="hidden" name="feature_data[feature_type]" value="G" />
			<?php else: ?>
			<div class="form-field">
				<label for="feature_type_<?php echo $this->_tpl_vars['id']; ?>
" class="cm-required"><?php echo fn_get_lang_var('type', $this->getLanguage()); ?>
:</label>
				<?php if ($this->_tpl_vars['feature']['feature_type'] == 'G'): ?><?php echo fn_get_lang_var('group', $this->getLanguage()); ?>
<?php else: ?>
					<select name="feature_data[feature_type]" id="feature_type_<?php echo $this->_tpl_vars['id']; ?>
"  onchange="fn_check_product_feature_type(this.value, 'tab_variants_<?php echo $this->_tpl_vars['id']; ?>
');">
						<optgroup label="<?php echo fn_get_lang_var('checkbox', $this->getLanguage()); ?>
">
							<option value="C" <?php if ($this->_tpl_vars['feature']['feature_type'] == 'C'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('single', $this->getLanguage()); ?>
</option>
							<option value="M" <?php if ($this->_tpl_vars['feature']['feature_type'] == 'M'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('multiple', $this->getLanguage()); ?>
</option>
						</optgroup>
						<optgroup label="<?php echo fn_get_lang_var('selectbox', $this->getLanguage()); ?>
">
							<option value="S" <?php if ($this->_tpl_vars['feature']['feature_type'] == 'S'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('text', $this->getLanguage()); ?>
</option>
							<option value="N" <?php if ($this->_tpl_vars['feature']['feature_type'] == 'N'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('number', $this->getLanguage()); ?>
</option>
							<option value="E" <?php if ($this->_tpl_vars['feature']['feature_type'] == 'E'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('extended', $this->getLanguage()); ?>
</option>
						</optgroup>
						<optgroup label="<?php echo fn_get_lang_var('others', $this->getLanguage()); ?>
">
							<option value="T" <?php if ($this->_tpl_vars['feature']['feature_type'] == 'T'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('text', $this->getLanguage()); ?>
</option>
							<option value="O" <?php if ($this->_tpl_vars['feature']['feature_type'] == 'O'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('number', $this->getLanguage()); ?>
</option>
							<option value="D" <?php if ($this->_tpl_vars['feature']['feature_type'] == 'D'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('date', $this->getLanguage()); ?>
</option>
						</optgroup>
					</select>
				<?php endif; ?>
			</div>

			<div class="form-field">
				<label for="feature_group_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('group', $this->getLanguage()); ?>
:</label>
				<?php if ($this->_tpl_vars['feature']['feature_type'] == 'G'): ?>-<?php else: ?>
					<select name="feature_data[parent_id]" id="feature_group_<?php echo $this->_tpl_vars['id']; ?>
" onchange="$('#tab_categories_<?php echo $this->_tpl_vars['id']; ?>
').toggleBy(this.value != 0); $('#feature_display_on_product_<?php echo $this->_tpl_vars['id']; ?>
, #feature_catalog_pages_<?php echo $this->_tpl_vars['id']; ?>
').attr('disabled', this.value != 0 ? 'disabled' : '');">
						<option value="0">-<?php echo fn_get_lang_var('none', $this->getLanguage()); ?>
-</option>
						<?php $_from = $this->_tpl_vars['group_features']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['group_feature']):
?>
							<?php if ($this->_tpl_vars['group_feature']['feature_type'] == 'G'): ?>
								<option value="<?php echo $this->_tpl_vars['group_feature']['feature_id']; ?>
"<?php if ($this->_tpl_vars['group_feature']['feature_id'] == $this->_tpl_vars['feature']['parent_id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['group_feature']['description']; ?>
</option>
							<?php endif; ?>
						<?php endforeach; endif; unset($_from); ?>
					</select>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<div class="form-field">
				<label for="feature_display_on_product_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('product', $this->getLanguage()); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('tt_views_product_features_update_product', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
				<input type="hidden" name="feature_data[display_on_product]" value="0" />
				<input type="checkbox" class="checkbox" name="feature_data[display_on_product]" value="1" <?php if ($this->_tpl_vars['feature']['display_on_product']): ?>checked="checked"<?php endif; ?> id="feature_display_on_product_<?php echo $this->_tpl_vars['id']; ?>
" <?php if ($this->_tpl_vars['feature']['parent_id']): ?>disabled="disabled"<?php endif; ?> />
			</div>

			<div class="form-field">
				<label for="feature_catalog_pages_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('catalog_pages', $this->getLanguage()); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('tt_views_product_features_update_catalog_pages', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
				<input type="hidden" name="feature_data[display_on_catalog]" value="0" />
				<input type="checkbox" class="checkbox" name="feature_data[display_on_catalog]" value="1" <?php if ($this->_tpl_vars['feature']['display_on_catalog']): ?>checked="checked"<?php endif; ?> id="feature_catalog_pages_<?php echo $this->_tpl_vars['id']; ?>
" <?php if ($this->_tpl_vars['feature']['parent_id']): ?>disabled="disabled"<?php endif; ?>/>
			</div>

			<?php if (( ! $this->_tpl_vars['feature'] && ! $this->_tpl_vars['is_group'] ) || ( $this->_tpl_vars['feature']['feature_type'] && $this->_tpl_vars['feature']['feature_type'] != 'G' )): ?>
			<div class="form-field">
				<label for="feature_prefix_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('prefix', $this->getLanguage()); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('tt_views_product_features_update_prefix', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
				<input type="text" name="feature_data[prefix]" value="<?php echo $this->_tpl_vars['feature']['prefix']; ?>
" class="input-text-medium" id="feature_prefix_<?php echo $this->_tpl_vars['id']; ?>
" />
			</div>

			<div class="form-field">
				<label for="feature_suffix_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('suffix', $this->getLanguage()); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('tt_views_product_features_update_suffix', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
				<input type="text" name="feature_data[suffix]" value="<?php echo $this->_tpl_vars['feature']['suffix']; ?>
" class="input-text-medium" id="feature_suffix_<?php echo $this->_tpl_vars['id']; ?>
" />
			</div>
			<?php endif; ?>
			
			<?php $this->_tag_stack[] = array('hook', array('name' => "product_features:properties")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
			<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
		</fieldset>
		<!--content_tab_details_<?php echo $this->_tpl_vars['id']; ?>
--></div>

		<div class="hidden" id="content_tab_variants_<?php echo $this->_tpl_vars['id']; ?>
">

		<table cellpadding="0" cellspacing="0" border="0" class="table" width="100%">
		<tbody>
		<tr class="cm-first-sibling">
			<th><?php echo fn_get_lang_var('position_short', $this->getLanguage()); ?>
</th>
			<th><?php echo fn_get_lang_var('variant', $this->getLanguage()); ?>
</th>
			<th class="cm-extended-feature <?php if ($this->_tpl_vars['feature']['feature_type'] != 'E'): ?>hidden<?php endif; ?>"><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/plus_minus.gif" width="13" height="12" border="0" name="plus_minus" id="on_st_<?php echo $this->_tpl_vars['id']; ?>
" alt="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" class="hand cm-combinations-features-<?php echo $this->_tpl_vars['id']; ?>
" /><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/minus_plus.gif" width="13" height="12" border="0" name="minus_plus" id="off_st_<?php echo $this->_tpl_vars['id']; ?>
" alt="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" class="hand hidden cm-combinations-features-<?php echo $this->_tpl_vars['id']; ?>
" /></th>
			<th>&nbsp;</th>
		</tr>
		</tbody>
		<?php $_from = $this->_tpl_vars['feature']['variants']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['fe_f'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['fe_f']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['var']):
        $this->_foreach['fe_f']['iteration']++;
?>
		<?php $this->assign('num', $this->_foreach['fe_f']['iteration'], false); ?>
		<tbody class="hover" id="box_feature_variants_<?php echo $this->_tpl_vars['var']['variant_id']; ?>
">
		<tr class="cm-first-sibling <?php echo smarty_function_cycle(array('values' => "table-row, "), $this);?>
">
			<td>
				<input type="hidden" name="feature_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][variant_id]" value="<?php echo $this->_tpl_vars['var']['variant_id']; ?>
">
				<input type="text" name="feature_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][position]" value="<?php echo $this->_tpl_vars['var']['position']; ?>
" size="4" class="input-text-short" /></td>
			<td>
				<input type="text" name="feature_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][variant]" value="<?php echo $this->_tpl_vars['var']['variant']; ?>
" class="input-text-large cm-feature-value <?php if ($this->_tpl_vars['feature']['feature_type'] == 'N'): ?>cm-value-integer<?php endif; ?>" /></td>
			<td class="cm-extended-feature <?php if ($this->_tpl_vars['feature']['feature_type'] != 'E'): ?>hidden<?php endif; ?>">
				<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/plus.gif" width="14" height="9" border="0" name="plus_minus" id="on_extra_feature_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" alt="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" class="hand cm-combination-features-<?php echo $this->_tpl_vars['id']; ?>
" /><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/minus.gif" width="14" height="9" border="0" name="minus_plus" id="off_extra_feature_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" alt="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" class="hand hidden cm-combination-features-<?php echo $this->_tpl_vars['id']; ?>
" /><a id="sw_extra_feature_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" class="cm-combination-features-<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('extra', $this->getLanguage()); ?>
</a>
			</td>
			<td class="right nowrap">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/multiple_buttons.tpl", 'smarty_include_vars' => array('item_id' => "feature_variants_".($this->_tpl_vars['var']['variant_id']),'tag_level' => '3','only_delete' => 'Y')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</td>
		</tr>
		<tr class="hidden" id="extra_feature_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
">
			<td colspan="4">

				<div class="form-field">
					<label for="elm_image_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
"><?php echo fn_get_lang_var('image', $this->getLanguage()); ?>
</label>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/attach_images.tpl", 'smarty_include_vars' => array('image_name' => 'variant_image','image_key' => $this->_tpl_vars['num'],'hide_titles' => true,'no_detailed' => true,'image_object_type' => 'feature_variant','image_type' => 'V','image_pair' => $this->_tpl_vars['var']['image_pair'],'prefix' => $this->_tpl_vars['id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				</div>

				<div class="form-field">
					<label for="elm_description_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
"><?php echo fn_get_lang_var('description', $this->getLanguage()); ?>
</label>
					<textarea id="elm_description_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" name="feature_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][description]" cols="55" rows="8" class="input-textarea-long"><?php echo $this->_tpl_vars['var']['description']; ?>
</textarea>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/wysiwyg.tpl", 'smarty_include_vars' => array('id' => "elm_description_".($this->_tpl_vars['id'])."_".($this->_tpl_vars['num']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				</div>

				<div class="form-field">
					<label for="elm_page_title_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
"><?php echo fn_get_lang_var('page_title', $this->getLanguage()); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('ttc_page_title', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
					<input type="text" name="feature_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][page_title]" id="elm_page_title_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" size="55" value="<?php echo $this->_tpl_vars['var']['page_title']; ?>
" class="input-text-large" />
				</div>

				<div class="form-field">
					<label for="elm_url_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
"><?php echo fn_get_lang_var('url', $this->getLanguage()); ?>
:</label>
					<input type="text" name="feature_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][url]" id="elm_url_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" size="55" value="<?php echo $this->_tpl_vars['var']['url']; ?>
" class="input-text-large" />
				</div>

				<div class="form-field">
					<label for="elm_meta_description_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
"><?php echo fn_get_lang_var('meta_description', $this->getLanguage()); ?>
:</label>
					<textarea name="feature_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][meta_description]" id="elm_meta_description_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" cols="55" rows="2" class="input-textarea-long"><?php echo $this->_tpl_vars['var']['meta_description']; ?>
</textarea>
				</div>

				<div class="form-field">
					<label for="elm_meta_keywords_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
"><?php echo fn_get_lang_var('meta_keywords', $this->getLanguage()); ?>
:</label>
					<textarea name="feature_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][meta_keywords]" id="elm_meta_keywords_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" cols="55" rows="2" class="input-textarea-long"><?php echo $this->_tpl_vars['var']['meta_keywords']; ?>
</textarea>
				</div>

				<?php $this->_tag_stack[] = array('hook', array('name' => "product_features:extended_feature")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php if ($this->_tpl_vars['addons']['seo']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/seo/hooks/product_features/extended_feature.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
			</td>
		</tr>
		</tbody>
		<?php endforeach; endif; unset($_from); ?>

		<?php echo smarty_function_math(array('equation' => "x + 1",'assign' => 'num','x' => smarty_modifier_default(@$this->_tpl_vars['num'], 0)), $this);?>

		<tbody class="hover" id="box_add_variants_for_existing_<?php echo $this->_tpl_vars['id']; ?>
">
		<tr>
			<td>
				<input type="text" name="feature_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][position]" value="" size="4" class="input-text-short" /></td>
			<td>
				<input type="text" name="feature_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][variant]" value="" class="input-text-large cm-feature-value <?php if ($this->_tpl_vars['feature']['feature_type'] == 'N'): ?>cm-value-integer<?php endif; ?>" /></td>
			<td class="cm-extended-feature <?php if ($this->_tpl_vars['feature']['feature_type'] != 'E'): ?>hidden<?php endif; ?>">
				<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/plus.gif" width="14" height="9" border="0" name="plus_minus" id="on_extra_feature_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" alt="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" class="hand cm-combination-features-<?php echo $this->_tpl_vars['id']; ?>
" /><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/minus.gif" width="14" height="9" border="0" name="minus_plus" id="off_extra_feature_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" alt="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" class="hand hidden cm-combination-features-<?php echo $this->_tpl_vars['id']; ?>
" /><a id="sw_extra_feature_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" class="cm-combination-features-<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('extra', $this->getLanguage()); ?>
</a>
			</td>
			<td class="right">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/multiple_buttons.tpl", 'smarty_include_vars' => array('item_id' => "add_variants_for_existing_".($this->_tpl_vars['id']),'tag_level' => 2)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
		</tr>
		<tr class="hidden" id="extra_feature_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
">
			<td colspan="4">

				<div class="form-field">
					<label for="elm_image_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
"><?php echo fn_get_lang_var('image', $this->getLanguage()); ?>
</label>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/attach_images.tpl", 'smarty_include_vars' => array('image_name' => 'variant_image','image_key' => $this->_tpl_vars['num'],'hide_titles' => true,'no_detailed' => true,'image_object_type' => 'feature_variant','image_type' => 'V','image_pair' => "",'prefix' => $this->_tpl_vars['id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				</div>

				<div class="form-field">
					<label for="elm_description_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
"><?php echo fn_get_lang_var('description', $this->getLanguage()); ?>
</label>
					<textarea id="elm_description_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" name="feature_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][description]" cols="55" rows="8" class="input-textarea-long"></textarea>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/wysiwyg.tpl", 'smarty_include_vars' => array('id' => "elm_description_".($this->_tpl_vars['id'])."_".($this->_tpl_vars['num']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				</div>

				<div class="form-field">
					<label for="elm_page_title_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
"><?php echo fn_get_lang_var('page_title', $this->getLanguage()); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('ttc_page_title', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
					<input type="text" name="feature_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][page_title]" id="elm_page_title_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" size="55" value="" class="input-text-large" />
				</div>

				<div class="form-field">
					<label for="elm_url_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
"><?php echo fn_get_lang_var('url', $this->getLanguage()); ?>
:</label>
					<input type="text" name="feature_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][url]" id="elm_url_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" size="55" value="" class="input-text-large" />
				</div>

				<div class="form-field">
					<label for="elm_meta_description_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
"><?php echo fn_get_lang_var('meta_description', $this->getLanguage()); ?>
:</label>
					<textarea name="feature_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][meta_description]" id="elm_meta_description_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" cols="55" rows="2" class="input-textarea-long"></textarea>
				</div>

				<div class="form-field">
					<label for="elm_meta_keywords_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
"><?php echo fn_get_lang_var('meta_keywords', $this->getLanguage()); ?>
:</label>
					<textarea name="feature_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][meta_keywords]" id="elm_meta_keywords_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" cols="55" rows="2" class="input-textarea-long"></textarea>
				</div>

				<?php $this->_tag_stack[] = array('hook', array('name' => "product_features:extended_feature")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php if ($this->_tpl_vars['addons']['seo']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/seo/hooks/product_features/extended_feature.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
			</td>
		</tr>
		</tbody>
		</table>
		<!--content_tab_variants_<?php echo $this->_tpl_vars['id']; ?>
--></div>

		<?php if (! $this->_tpl_vars['feature']['parent_id']): ?>
		<div class="hidden" id="content_tab_categories_<?php echo $this->_tpl_vars['id']; ?>
">
		<?php if ($this->_tpl_vars['feature']['categories_path']): ?>
			<?php $this->assign('items', explode(",", $this->_tpl_vars['feature']['categories_path']), false); ?>
		<?php endif; ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/categories_picker.tpl", 'smarty_include_vars' => array('multiple' => true,'input_name' => "feature_data[categories_path]",'item_ids' => $this->_tpl_vars['items'],'data_id' => "category_ids_".($this->_tpl_vars['id']),'no_item_text' => smarty_modifier_replace(fn_get_lang_var('text_all_items_included', $this->getLanguage()), "[items]", fn_get_lang_var('categories', $this->getLanguage())))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

		<!--content_tab_categories_<?php echo $this->_tpl_vars['id']; ?>
--></div>
		<?php endif; ?>

	</div>
</div>

<div class="buttons-container">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[product_features.update]",'cancel_action' => 'close')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>


</form>

<!--content_group<?php echo $this->_tpl_vars['id']; ?>
--></div><?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>