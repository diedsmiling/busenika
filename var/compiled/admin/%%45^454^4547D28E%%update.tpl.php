<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:46
         compiled from views/product_options/update.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'views/product_options/update.tpl', 3, false),array('modifier', 'fn_url', 'views/product_options/update.tpl', 7, false),array('modifier', 'strpos', 'views/product_options/update.tpl', 42, false),array('modifier', 'unescape', 'views/product_options/update.tpl', 90, false),array('block', 'hook', 'views/product_options/update.tpl', 122, false),array('function', 'math', 'views/product_options/update.tpl', 185, false),array('function', 'script', 'views/product_options/update.tpl', 243, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('general','variants','name','position','inventory','tt_views_product_options_update_inventory','vendor','type','description','comment','comment_hint','required','tt_views_product_options_update_required','missing_variants_handling','display_message','hide_option_completely','regexp','tt_views_product_options_update_regexp','regexp_hint','inner_hint','tt_views_product_options_update_inner_hint','incorrect_filling_message','tt_views_product_options_update_incorrect_filling_message','allowed_extensions','allowed_extensions_hint','max_uploading_file_size','max_uploading_file_size_hint','multiupload','position_short','name','modifier','type','weight_modifier','type','status','expand_collapse_list','expand_collapse_list','expand_collapse_list','expand_collapse_list','expand_collapse_list','expand_collapse_list','expand_collapse_list','expand_collapse_list','extra','icon','expand_collapse_list','expand_collapse_list','expand_collapse_list','expand_collapse_list','extra','icon','create'));
?>

<?php $this->assign('id', smarty_modifier_default(@$this->_tpl_vars['option_id'], 0), false); ?>

<div id="content_group_product_option_<?php echo $this->_tpl_vars['id']; ?>
">

<form action="<?php echo fn_url(""); ?>
" method="post" name="option_form_<?php echo $this->_tpl_vars['id']; ?>
" class="form-highlight cm-disable-empty-files" enctype="multipart/form-data">
<input type="hidden" name="option_id" value="<?php echo $this->_tpl_vars['option_id']; ?>
" />
<?php if ($this->_tpl_vars['_REQUEST']['product_id']): ?>
<?php if (! $this->_tpl_vars['option_data']): ?>
<input type="hidden" name="option_data[product_id]" value="<?php echo $this->_tpl_vars['_REQUEST']['product_id']; ?>
" />
<?php endif; ?>
<input type="hidden" name="product_id" value="<?php echo $this->_tpl_vars['_REQUEST']['product_id']; ?>
" />
<?php endif; ?>

<div class="object-container">

<div class="tabs cm-j-tabs">
	<ul>
		<li id="tab_option_details_<?php echo $this->_tpl_vars['id']; ?>
" class="cm-js cm-active"><a><?php echo fn_get_lang_var('general', $this->getLanguage()); ?>
</a></li>
		<?php if ($this->_tpl_vars['option_data']['option_type'] == 'S' || $this->_tpl_vars['option_data']['option_type'] == 'R' || $this->_tpl_vars['option_data']['option_type'] == 'C' || ! $this->_tpl_vars['option_data']): ?>
			<li id="tab_option_variants_<?php echo $this->_tpl_vars['id']; ?>
" class="cm-js"><a><?php echo fn_get_lang_var('variants', $this->getLanguage()); ?>
</a></li>
		<?php endif; ?>
	</ul>
</div>
<div class="cm-tabs-content" id="tabs_content_<?php echo $this->_tpl_vars['id']; ?>
">
	<div id="content_tab_option_details_<?php echo $this->_tpl_vars['id']; ?>
">
	<fieldset>
		<div class="form-field">
			<label for="name_<?php echo $this->_tpl_vars['id']; ?>
" class="cm-required"><?php echo fn_get_lang_var('name', $this->getLanguage()); ?>
:</label>
			<input type="text" name="option_data[option_name]" id="name_<?php echo $this->_tpl_vars['id']; ?>
" value="<?php echo $this->_tpl_vars['option_data']['option_name']; ?>
" class="input-text-large main-input" />
		</div>

		<div class="form-field">
			<label for="position_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('position', $this->getLanguage()); ?>
:</label>
			<input type="text" name="option_data[position]" id="position_<?php echo $this->_tpl_vars['id']; ?>
" value="<?php echo $this->_tpl_vars['option_data']['position']; ?>
" size="3" class="input-text-short" />
		</div>

		<div class="form-field">
			<label for="inventory_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('inventory', $this->getLanguage()); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('tt_views_product_options_update_inventory', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
			<input type="hidden" name="option_data[inventory]" value="N" />
			<?php if (strpos('SRC', $this->_tpl_vars['option_data']['option_type']) !== false): ?>
				<input type="checkbox" name="option_data[inventory]" id="inventory_<?php echo $this->_tpl_vars['id']; ?>
" value="Y" <?php if ($this->_tpl_vars['option_data']['inventory'] == 'Y'): ?>checked="checked"<?php endif; ?> class="checkbox" />
			<?php else: ?>
				&nbsp;-&nbsp;
			<?php endif; ?>
		</div>
		
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/companies/components/company_field.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('vendor', $this->getLanguage()),'name' => "option_data[company_id]",'id' => "option_data_".($this->_tpl_vars['id']),'selected' => $this->_tpl_vars['option_data']['company_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

		<div class="form-field">
			<label for="option_type_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('type', $this->getLanguage()); ?>
:</label>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/product_options/components/option_types.tpl", 'smarty_include_vars' => array('name' => "option_data[option_type]",'value' => $this->_tpl_vars['option_data']['option_type'],'display' => 'select','tag_id' => "option_type_".($this->_tpl_vars['id']),'check' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
		
		<div class="form-field">
			<label for="description_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('description', $this->getLanguage()); ?>
:</label>
			<textarea id="description_<?php echo $this->_tpl_vars['id']; ?>
" name="option_data[description]" cols="55" rows="8" class="input-textarea-long"><?php echo $this->_tpl_vars['option_data']['description']; ?>
</textarea>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/wysiwyg.tpl", 'smarty_include_vars' => array('id' => "description_".($this->_tpl_vars['id']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
		
		<div class="form-field">
			<label for="comment_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('comment', $this->getLanguage()); ?>
:</label>
			<input type="text" name="option_data[comment]" id="comment_<?php echo $this->_tpl_vars['id']; ?>
" value="<?php echo $this->_tpl_vars['option_data']['comment']; ?>
" class="input-text-large" />
			<p class="description"><?php echo fn_get_lang_var('comment_hint', $this->getLanguage()); ?>
</p>
		</div>
		
		<div class="form-field">
			<label for="file_required_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('required', $this->getLanguage()); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('tt_views_product_options_update_required', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
			<input type="hidden" name="option_data[required]" value="N" /><input type="checkbox" id="file_required_<?php echo $this->_tpl_vars['id']; ?>
" name="option_data[required]" value="Y" <?php if ($this->_tpl_vars['option_data']['required'] == 'Y'): ?>checked="checked"<?php endif; ?> class="checkbox" />
		</div>
		
		<?php if (! $this->_tpl_vars['option_data']['option_type'] || strpos('SRC', $this->_tpl_vars['option_data']['option_type']) !== false): ?>
			<div class="form-field">
				<label for="file_required_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('missing_variants_handling', $this->getLanguage()); ?>
:</label>
				<?php if (strpos('SRC', $this->_tpl_vars['option_data']['option_type']) !== false): ?>
					<select name="option_data[missing_variants_handling]">
						<option value="M" <?php if ($this->_tpl_vars['option_data']['missing_variants_handling'] == 'M'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('display_message', $this->getLanguage()); ?>
</option>
						<option value="H" <?php if ($this->_tpl_vars['option_data']['missing_variants_handling'] == 'H'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('hide_option_completely', $this->getLanguage()); ?>
</option>
					</select>
				<?php else: ?>
					&nbsp;-&nbsp;
				<?php endif; ?>
			</div>
		<?php endif; ?>
		
		<div id="extra_options_<?php echo $this->_tpl_vars['id']; ?>
" <?php if ($this->_tpl_vars['option_data']['option_type'] != 'I' && $this->_tpl_vars['option_data']['option_type'] != 'T'): ?>class="hidden"<?php endif; ?>>
			<div class="form-field">
				<label for="regexp_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('regexp', $this->getLanguage()); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('tt_views_product_options_update_regexp', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
				<input type="text" name="option_data[regexp]" id="regexp_<?php echo $this->_tpl_vars['id']; ?>
" value="<?php echo smarty_modifier_unescape($this->_tpl_vars['option_data']['regexp']); ?>
" class="input-text-large" />
				<p class="description"><?php echo fn_get_lang_var('regexp_hint', $this->getLanguage()); ?>
</p>
			</div>
			
			<div class="form-field">
				<label for="inner_hint_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('inner_hint', $this->getLanguage()); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('tt_views_product_options_update_inner_hint', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
				<input type="text" name="option_data[inner_hint]" id="inner_hint_<?php echo $this->_tpl_vars['id']; ?>
" value="<?php echo $this->_tpl_vars['option_data']['inner_hint']; ?>
" class="input-text-large" />
			</div>
			
			<div class="form-field">
				<label for="incorrect_message_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('incorrect_filling_message', $this->getLanguage()); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('tt_views_product_options_update_incorrect_filling_message', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
				<input type="text" name="option_data[incorrect_message]" id="incorrect_message_<?php echo $this->_tpl_vars['id']; ?>
" value="<?php echo $this->_tpl_vars['option_data']['incorrect_message']; ?>
" class="input-text-large" />
			</div>
		</div>
		
		<div id="file_options_<?php echo $this->_tpl_vars['id']; ?>
" <?php if ($this->_tpl_vars['option_data']['option_type'] != 'F'): ?>class="hidden"<?php endif; ?>>
			<div class="form-field">
				<label for="allowed_extensions_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('allowed_extensions', $this->getLanguage()); ?>
:</label>
				<input type="text" name="option_data[allowed_extensions]" id="allowed_extensions_<?php echo $this->_tpl_vars['id']; ?>
" value="<?php echo $this->_tpl_vars['option_data']['allowed_extensions']; ?>
" class="input-text-large" />
				<p class="description"><?php echo fn_get_lang_var('allowed_extensions_hint', $this->getLanguage()); ?>
</p>
			</div>
			<div class="form-field">
				<label for="max_uploading_file_size_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('max_uploading_file_size', $this->getLanguage()); ?>
:</label>
				<input type="text" name="option_data[max_file_size]" id="max_uploading_file_size_<?php echo $this->_tpl_vars['id']; ?>
" value="<?php echo $this->_tpl_vars['option_data']['max_file_size']; ?>
" class="input-text-large" />
				<p class="description"><?php echo fn_get_lang_var('max_uploading_file_size_hint', $this->getLanguage()); ?>
</p>
			</div>
			<div class="form-field">
				<label for="multiupload_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('multiupload', $this->getLanguage()); ?>
:</label>
				<input type="hidden" name="option_data[multiupload]" value="N" /><input type="checkbox" id="multiupload_<?php echo $this->_tpl_vars['id']; ?>
" name="option_data[multiupload]" value="Y" <?php if ($this->_tpl_vars['option_data']['multiupload'] == 'Y'): ?>checked="checked"<?php endif; ?> class="checkbox" />
			</div>
		</div>
		
		<?php $this->_tag_stack[] = array('hook', array('name' => "product_options:properties")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
		<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
	</fieldset>
	<!--content_tab_option_details_<?php echo $this->_tpl_vars['id']; ?>
--></div>

 	<div class="hidden" id="content_tab_option_variants_<?php echo $this->_tpl_vars['id']; ?>
">
 	<fieldset>
		<table cellpadding="0" cellspacing="0" class="table">
		<tbody>
		<tr class="first-sibling">
			<th class="cm-non-cb<?php if ($this->_tpl_vars['option_data']['option_type'] == 'C'): ?> hidden<?php endif; ?>"><?php echo fn_get_lang_var('position_short', $this->getLanguage()); ?>
</th>
			<th class="cm-non-cb<?php if ($this->_tpl_vars['option_data']['option_type'] == 'C'): ?> hidden<?php endif; ?>"><?php echo fn_get_lang_var('name', $this->getLanguage()); ?>
</th>
			<th><?php echo fn_get_lang_var('modifier', $this->getLanguage()); ?>
&nbsp;/&nbsp;<?php echo fn_get_lang_var('type', $this->getLanguage()); ?>
</th>
			<th><?php echo fn_get_lang_var('weight_modifier', $this->getLanguage()); ?>
&nbsp;/&nbsp;<?php echo fn_get_lang_var('type', $this->getLanguage()); ?>
</th>
			<th class="cm-non-cb<?php if ($this->_tpl_vars['option_data']['option_type'] == 'C'): ?> hidden<?php endif; ?>"><?php echo fn_get_lang_var('status', $this->getLanguage()); ?>
</th>
			<th><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/plus_minus.gif" width="13" height="12" border="0" name="plus_minus" id="on_st_<?php echo $this->_tpl_vars['id']; ?>
" alt="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" class="hand cm-combinations-options-<?php echo $this->_tpl_vars['id']; ?>
" /><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/minus_plus.gif" width="13" height="12" border="0" name="minus_plus" id="off_st_<?php echo $this->_tpl_vars['id']; ?>
" alt="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" class="hand hidden cm-combinations-options-<?php echo $this->_tpl_vars['id']; ?>
" /></th>
			<th class="cm-non-cb<?php if ($this->_tpl_vars['option_data']['option_type'] == 'C'): ?> hidden<?php endif; ?>">&nbsp;</th>
		</tr>
		</tbody>
		<?php $_from = $this->_tpl_vars['option_data']['variants']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['fe_v'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['fe_v']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['vr']):
        $this->_foreach['fe_v']['iteration']++;
?>
		<?php $this->assign('num', $this->_foreach['fe_v']['iteration'], false); ?>
		<tbody class="hover cm-row-item" id="option_variants_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
">
		<tr>
			<td class="cm-non-cb<?php if ($this->_tpl_vars['option_data']['option_type'] == 'C'): ?> hidden<?php endif; ?>">
				<input type="text" name="option_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][position]" value="<?php echo $this->_tpl_vars['vr']['position']; ?>
" size="3" class="input-text-short" /></td>
			<td class="cm-non-cb<?php if ($this->_tpl_vars['option_data']['option_type'] == 'C'): ?> hidden<?php endif; ?>">
				<input type="text" name="option_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][variant_name]" value="<?php echo $this->_tpl_vars['vr']['variant_name']; ?>
" class="input-text-medium main-input" /></td>
			<td class="nowrap">
				<input type="text" name="option_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][modifier]" value="<?php echo $this->_tpl_vars['vr']['modifier']; ?>
" size="5" class="input-text" />&nbsp;/&nbsp;<select name="option_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][modifier_type]">
					<option value="A" <?php if ($this->_tpl_vars['vr']['modifier_type'] == 'A'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['currencies'][$this->_tpl_vars['primary_currency']]['symbol']; ?>
</option>
					<option value="P" <?php if ($this->_tpl_vars['vr']['modifier_type'] == 'P'): ?>selected="selected"<?php endif; ?>>%</option>
				</select>
			</td>
			<td class="nowrap">
				<input type="text" name="option_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][weight_modifier]" value="<?php echo $this->_tpl_vars['vr']['weight_modifier']; ?>
" size="5" class="input-text" />&nbsp;/&nbsp;<select name="option_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][weight_modifier_type]">
					<option value="A" <?php if ($this->_tpl_vars['vr']['weight_modifier_type'] == 'A'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['settings']['General']['weight_symbol']; ?>
</option>
					<option value="P" <?php if ($this->_tpl_vars['vr']['weight_modifier_type'] == 'P'): ?>selected="selected"<?php endif; ?>>%</option>
				</select>
			</td>
			<td class="cm-non-cb<?php if ($this->_tpl_vars['option_data']['option_type'] == 'C'): ?> hidden<?php endif; ?>">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_status.tpl", 'smarty_include_vars' => array('input_name' => "option_data[variants][".($this->_tpl_vars['num'])."][status]",'display' => 'select','obj' => $this->_tpl_vars['vr'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
			<td class="nowrap">
				<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/plus.gif" width="14" height="9" border="0" name="plus_minus" id="on_extra_option_variants_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" alt="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" class="hand cm-combination-options-<?php echo $this->_tpl_vars['id']; ?>
" /><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/minus.gif" width="14" height="9" border="0" name="minus_plus" id="off_extra_option_variants_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" alt="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" class="hand hidden cm-combination-options-<?php echo $this->_tpl_vars['id']; ?>
" /><a id="sw_extra_option_variants_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" class="cm-combination-options-<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('extra', $this->getLanguage()); ?>
</a>
				<input type="hidden" name="option_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][variant_id]" value="<?php echo $this->_tpl_vars['vr']['variant_id']; ?>
" />
			 </td>
			 <td class="right cm-non-cb<?php if ($this->_tpl_vars['option_data']['option_type'] == 'C'): ?> hidden<?php endif; ?>">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/multiple_buttons.tpl", 'smarty_include_vars' => array('item_id' => "option_variants_".($this->_tpl_vars['id'])."_".($this->_tpl_vars['num']),'tag_level' => '3','only_delete' => 'Y')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</td>
		</tr>
		<tr id="extra_option_variants_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" class="cm-ex-op hidden">
			<td colspan="7">
				<?php $this->_tag_stack[] = array('hook', array('name' => "product_options:edit_product_options")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
				<div class="form-field cm-non-cb">
					<label><?php echo fn_get_lang_var('icon', $this->getLanguage()); ?>
:</label>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/attach_images.tpl", 'smarty_include_vars' => array('image_name' => 'variant_image','image_key' => $this->_tpl_vars['num'],'hide_titles' => true,'no_detailed' => true,'image_object_type' => 'variant_image','image_type' => 'V','image_pair' => $this->_tpl_vars['vr']['image_pair'],'prefix' => $this->_tpl_vars['id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				</div>				
				<?php if ($this->_tpl_vars['addons']['reward_points']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/reward_points/hooks/product_options/edit_product_options.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
				</div>
			</td>
		</tr>
		</tbody>
		<?php endforeach; endif; unset($_from); ?>

		<?php echo smarty_function_math(array('equation' => "x + 1",'assign' => 'num','x' => smarty_modifier_default(@$this->_tpl_vars['num'], 0)), $this);?>
<?php $this->assign('vr', "", false); ?>
		<tbody class="hover cm-row-item <?php if ($this->_tpl_vars['option_data']['option_type'] == 'C'): ?>hidden<?php endif; ?>" id="box_add_variant_<?php echo $this->_tpl_vars['id']; ?>
">
		<tr>
			<td class="cm-non-cb<?php if ($this->_tpl_vars['option_data']['option_type'] == 'C'): ?> hidden<?php endif; ?>">
				<input type="text" name="option_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][position]" value="" size="3" class="input-text-short" /></td>
			<td class="cm-non-cb<?php if ($this->_tpl_vars['option_data']['option_type'] == 'C'): ?> hidden<?php endif; ?>">
				<input type="text" name="option_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][variant_name]" value="" class="input-text-medium main-input" /></td>
			<td>
				<input type="text" name="option_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][modifier]" value="" size="5" class="input-text" />&nbsp;/
				<select name="option_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][modifier_type]">
					<option value="A"><?php echo $this->_tpl_vars['currencies'][$this->_tpl_vars['primary_currency']]['symbol']; ?>
</option>
					<option value="P">%</option>
				</select>
			</td>
			<td>
				<input type="text" name="option_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][weight_modifier]" value="" size="5" class="input-text" />&nbsp;/&nbsp;<select name="option_data[variants][<?php echo $this->_tpl_vars['num']; ?>
][weight_modifier_type]">
					<option value="A"><?php echo $this->_tpl_vars['settings']['General']['weight_symbol']; ?>
</option>
					<option value="P">%</option>
				</select>
			</td>
			<td class="cm-non-cb<?php if ($this->_tpl_vars['option_data']['option_type'] == 'C'): ?> hidden<?php endif; ?>">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_status.tpl", 'smarty_include_vars' => array('input_name' => "option_data[variants][".($this->_tpl_vars['num'])."][status]",'display' => 'select')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
			<td>
				<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/plus.gif" width="14" height="9" border="0" name="plus_minus" id="on_extra_option_variants_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" alt="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" class="hand cm-combination-options-<?php echo $this->_tpl_vars['id']; ?>
" /><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/minus.gif" width="14" height="9" border="0" name="minus_plus" id="off_extra_option_variants_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" alt="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" class="hand hidden cm-combination-options-<?php echo $this->_tpl_vars['id']; ?>
" /><a id="sw_extra_option_variants_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" class="cm-combination-options-<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('extra', $this->getLanguage()); ?>
</a>
			</td>
			<td class="right cm-non-cb<?php if ($this->_tpl_vars['option_data']['option_type'] == 'C'): ?> hidden<?php endif; ?>">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/multiple_buttons.tpl", 'smarty_include_vars' => array('item_id' => "add_variant_".($this->_tpl_vars['id']),'tag_level' => '2')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</td>
		</tr>
		<tr id="extra_option_variants_<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['num']; ?>
" class="cm-ex-op hidden">
			<td colspan="7">
				<?php $this->_tag_stack[] = array('hook', array('name' => "product_options:edit_product_options")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
				<div class="form-field cm-non-cb">
					<label><?php echo fn_get_lang_var('icon', $this->getLanguage()); ?>
:</label>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/attach_images.tpl", 'smarty_include_vars' => array('image_name' => 'variant_image','image_key' => $this->_tpl_vars['num'],'hide_titles' => true,'no_detailed' => true,'image_object_type' => 'variant_image','image_type' => 'V','prefix' => $this->_tpl_vars['id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				</div>
				<?php if ($this->_tpl_vars['addons']['reward_points']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/reward_points/hooks/product_options/edit_product_options.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
			</td>
		</tr>
		</tbody>
		</table>
	</fieldset>
	<!--content_tab_option_variants_<?php echo $this->_tpl_vars['id']; ?>
--></div>
</div>

</div>

<div class="buttons-container">
	<?php if ($this->_tpl_vars['mode'] == 'add'): ?>
		<?php $this->assign('_but_text', fn_get_lang_var('create', $this->getLanguage()), false); ?>
	<?php else: ?>
		<?php $this->assign('_but_text', "", false); ?>
	<?php endif; ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_text' => $this->_tpl_vars['_but_text'],'but_name' => "dispatch[product_options.update]",'cancel_action' => 'close','extra' => "")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

</form>

<!--content_group_product_option_<?php echo $this->_tpl_vars['id']; ?>
--></div><?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>