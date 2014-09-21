<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:45
         compiled from views/block_manager/update.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'views/block_manager/update.tpl', 2, false),array('modifier', 'fn_url', 'views/block_manager/update.tpl', 5, false),array('modifier', 'to_json', 'views/block_manager/update.tpl', 19, false),array('modifier', 'indent', 'views/block_manager/update.tpl', 91, false),array('block', 'hook', 'views/block_manager/update.tpl', 146, false),array('function', 'script', 'views/block_manager/update.tpl', 168, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('general','name','block_content','list_objects','standard_sidebox','filling','group','appearance_type','block_order','horizontal','vertical','wrapper','block_width','tt_views_block_manager_update_block_width','percents','pixels'));
?>
<?php $this->assign('id', smarty_modifier_default(@$this->_tpl_vars['block']['block_id'], '0'), false); ?>
<?php $this->assign('block_type', smarty_modifier_default(@$this->_tpl_vars['block']['block_type'], @$this->_tpl_vars['block_type']), false); ?>
<div id="content_group<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_<?php echo $this->_tpl_vars['location']; ?>
">
<form action="<?php echo fn_url(""); ?>
" method="post" class="cm-form-highlight" name="block_<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_update_form">
<input type="hidden" name="block[block_type]" value="<?php echo $this->_tpl_vars['block_type']; ?>
" />
<?php $this->assign('js_param', 'false', false); ?>
<?php if ($this->_tpl_vars['add_block']): ?>
	<?php $this->assign('js_param', 'true', false); ?>
	<input type="hidden" name="add_selected_section" id="add_selected_section_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['location'], 'all_pages'); ?>
" />
<?php else: ?>
	<input type="hidden" name="block[block_id]" value="<?php echo $this->_tpl_vars['id']; ?>
" />
	<input type="hidden" name="redirect_location" value="<?php echo $this->_tpl_vars['location']; ?>
" />
	<input type="hidden" name="block[location]" value="<?php echo $this->_tpl_vars['block']['location']; ?>
" />
	

	<script type="text/javascript">
	//<![CDATA[
	block_properties['<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_'] = <?php echo smarty_modifier_to_json($this->_tpl_vars['block']['properties']); ?>
;
	block_location['<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_'] = '<?php echo $this->_tpl_vars['block']['location']; ?>
';
	block_properties_used['<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_'] = false;
	//]]>
	</script>
<?php endif; ?>
<?php if ($this->_tpl_vars['redirect_url']): ?>
	<input type="hidden" name="redirect_url" value="<?php echo $this->_tpl_vars['redirect_url']; ?>
" />
<?php endif; ?>

<div class="object-container">
	<div class="tabs cm-j-tabs">
		<ul>
			<li class="cm-js cm-active"><a><?php echo fn_get_lang_var('general', $this->getLanguage()); ?>
</a></li>
		</ul>
	</div>

	<div class="cm-tabs-content">
	<fieldset>

		<div class="form-field">
			<label for="<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_block_name" class="cm-required"><?php echo fn_get_lang_var('name', $this->getLanguage()); ?>
:</label>
			<input type="text" name="block[description]" id="<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_block_name" size="25" value="<?php echo $this->_tpl_vars['block']['description']; ?>
" class="input-text main-input" />
		</div>

	<?php if ($this->_tpl_vars['block']['text_id'] != 'central_content'): ?>
		<?php if ($this->_tpl_vars['block_type'] == 'B'): ?>
		<div class="form-field float-left">
			<label for="<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_block_object"><?php echo fn_get_lang_var('block_content', $this->getLanguage()); ?>
:</label>
			<select name="block[list_object]" id="<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_block_object" onchange="fn_check_block_params(<?php echo $this->_tpl_vars['js_param']; ?>
, '<?php echo $this->_tpl_vars['location']; ?>
', <?php echo $this->_tpl_vars['id']; ?>
, this, '<?php echo $this->_tpl_vars['block_type']; ?>
'); fn_get_specific_settings(this.value, <?php echo $this->_tpl_vars['id']; ?>
, 'list_object', '<?php echo $this->_tpl_vars['block_type']; ?>
');">
			<optgroup label="<?php echo fn_get_lang_var('list_objects', $this->getLanguage()); ?>
">
				<?php $_from = $this->_tpl_vars['block_settings']['dynamic']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['object_name'] => $this->_tpl_vars['listed_block']):
?>
					<option value="<?php echo $this->_tpl_vars['object_name']; ?>
"<?php if ($this->_tpl_vars['block']['properties']['list_object'] == $this->_tpl_vars['object_name']): ?> selected="selected"<?php endif; ?>><?php if ($this->_tpl_vars['listed_block']['object_description']): ?><?php echo fn_get_lang_var($this->_tpl_vars['listed_block']['object_description'], $this->getLanguage()); ?>
<?php else: ?><?php echo fn_get_lang_var($this->_tpl_vars['object_name'], $this->getLanguage()); ?>
<?php endif; ?></option>
				<?php endforeach; endif; unset($_from); ?>
			</optgroup>
			<optgroup label="<?php echo fn_get_lang_var('standard_sidebox', $this->getLanguage()); ?>
">
				<?php $_from = $this->_tpl_vars['block_settings']['static']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['static_block']):
?>
					<option value="<?php echo $this->_tpl_vars['static_block']['template']; ?>
" <?php if ($this->_tpl_vars['block']['properties']['list_object'] == $this->_tpl_vars['static_block']['template']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['static_block']['name']; ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
			</optgroup>
			<?php $_from = $this->_tpl_vars['block_settings']['additional']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['section'] => $this->_tpl_vars['section_data']):
?>
				<optgroup label="<?php echo fn_get_lang_var($this->_tpl_vars['section'], $this->getLanguage()); ?>
">
				<?php $_from = $this->_tpl_vars['section_data']['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['object_name'] => $this->_tpl_vars['additional_block']):
?>
					<option value="<?php echo $this->_tpl_vars['additional_block']['template']; ?>
" <?php if ($this->_tpl_vars['block']['properties']['list_object'] == $this->_tpl_vars['additional_block']['template']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['additional_block']['name']; ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
				</optgroup>
			<?php endforeach; endif; unset($_from); ?>
			</select>
		</div>
		<?php $this->assign('index', smarty_modifier_default(@$this->_tpl_vars['block']['properties']['list_object'], 'products'), false); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/block_manager/specific_settings.tpl", 'smarty_include_vars' => array('spec_settings' => $this->_tpl_vars['specific_settings']['list_object'][$this->_tpl_vars['index']],'s_set_id' => ($this->_tpl_vars['id']).($this->_tpl_vars['block_type'])."_list_object")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

		<div class="form-field float-left">
			<label for="<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_id_fillings"><?php echo fn_get_lang_var('filling', $this->getLanguage()); ?>
:</label>
			<select name="block[fillings]" id="<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_id_fillings" onchange="fn_check_block_params(<?php echo $this->_tpl_vars['js_param']; ?>
, '<?php echo $this->_tpl_vars['location']; ?>
', <?php echo $this->_tpl_vars['id']; ?>
, this, '<?php echo $this->_tpl_vars['block_type']; ?>
');">
			</select>
		</div>

		<?php $this->assign('index', smarty_modifier_default(@$this->_tpl_vars['block']['properties']['fillings'], 'manually'), false); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/block_manager/specific_settings.tpl", 'smarty_include_vars' => array('spec_settings' => $this->_tpl_vars['specific_settings']['fillings'][$this->_tpl_vars['index']],'s_set_id' => ($this->_tpl_vars['id']).($this->_tpl_vars['block_type'])."_fillings")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>

		<?php if ($this->_tpl_vars['location'] != 'product_details' && $this->_tpl_vars['block']['text_id'] == ""): ?>
			<div class="form-field">
				<label for="<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_id_positions"><?php echo fn_get_lang_var('group', $this->getLanguage()); ?>
:</label>
				<select name="block[group_id]" id="<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_id_positions"<?php if (! $this->_tpl_vars['add_block']): ?> onchange="fn_check_block_parent(<?php echo $this->_tpl_vars['id']; ?>
, this, '<?php echo $this->_tpl_vars['location']; ?>
', <?php echo $this->_tpl_vars['object_id']; ?>
);"<?php endif; ?>>
				<?php $_from = $this->_tpl_vars['avail_positions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['pos']):
?>
					<?php if (! $this->_tpl_vars['pos']['parent_id']): ?>
					<option value="<?php echo $this->_tpl_vars['pos']['block_id']; ?>
"<?php if ($this->_tpl_vars['block_parent'] == $this->_tpl_vars['pos']['block_id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['pos']['description']; ?>
</option>
						<?php if ($this->_tpl_vars['block_type'] == 'B'): ?>
						<?php $_from = $this->_tpl_vars['avail_positions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['pos_child']):
?>
							<?php if ($this->_tpl_vars['pos_child']['parent_id'] == $this->_tpl_vars['pos']['block_id']): ?>
							<option	value="<?php echo $this->_tpl_vars['pos_child']['block_id']; ?>
"<?php if ($this->_tpl_vars['block_parent'] == $this->_tpl_vars['pos_child']['block_id']): ?> selected="selected"<?php endif; ?>><?php echo smarty_modifier_indent($this->_tpl_vars['pos_child']['description'], 1, "&#166;&nbsp;&nbsp;&nbsp;&nbsp;", "&#166;--&nbsp;"); ?>
</option>
							<?php endif; ?>
						<?php endforeach; endif; unset($_from); ?>
						<?php endif; ?>
					<?php endif; ?>
				<?php endforeach; endif; unset($_from); ?>
				</select>
				<?php if (! $this->_tpl_vars['add_block']): ?>
				<input id="<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_id_positions_rewrite" type="hidden" name="block[rewrite_positions]" value="N" />
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ($this->_tpl_vars['block_type'] == 'B'): ?>
		<div class="form-field float-left">
			<label for="<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_id_appearances"><?php echo fn_get_lang_var('appearance_type', $this->getLanguage()); ?>
:</label>
			<select name="block[appearances]" id="<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_id_appearances" onchange="fn_get_specific_settings(this.value, <?php echo $this->_tpl_vars['id']; ?>
, 'appearances', '<?php echo $this->_tpl_vars['block_type']; ?>
');">
			</select>
		</div>

		<?php $this->assign('index', smarty_modifier_default(@$this->_tpl_vars['block']['properties']['appearances'], "blocks/products_text_links.tpl"), false); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/block_manager/specific_settings.tpl", 'smarty_include_vars' => array('spec_settings' => $this->_tpl_vars['specific_settings']['appearances'][$this->_tpl_vars['index']],'s_set_id' => ($this->_tpl_vars['id']).($this->_tpl_vars['block_type'])."_appearances")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

		<?php else: ?>
		<div class="form-field">
			<label for="<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_id_block_order"><?php echo fn_get_lang_var('block_order', $this->getLanguage()); ?>
:</label>
			<select name="block[block_order]" id="<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_id_block_order">
				<option value="H"<?php if ($this->_tpl_vars['block']['properties']['block_order'] == 'H'): ?> selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('horizontal', $this->getLanguage()); ?>
</option>
				<option value="V"<?php if ($this->_tpl_vars['block']['properties']['block_order'] == 'V'): ?> selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('vertical', $this->getLanguage()); ?>
</option>
			</select>
		</div>
		<?php endif; ?>
	<?php endif; ?>

		<div class="form-field">
			<label for="<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_id_wrapper"><?php echo fn_get_lang_var('wrapper', $this->getLanguage()); ?>
:</label>
			<select name="block[wrapper]" id="<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_id_wrapper">
				<option value="">--</option>
				<?php $_from = $this->_tpl_vars['block_settings']['wrappers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['w']):
?>
				<option value="<?php echo $this->_tpl_vars['w']; ?>
" <?php if ($this->_tpl_vars['block']['properties']['wrapper'] == $this->_tpl_vars['w']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['w']; ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
			</select>
		</div>

		<?php if ($this->_tpl_vars['block_type'] == 'B'): ?>
		<div class="form-field">
			<label for="<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_block_width"><?php echo fn_get_lang_var('block_width', $this->getLanguage()); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => fn_get_lang_var('tt_views_block_manager_update_block_width', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>:</label>
			<input type="text" name="block[width]" id="<?php echo $this->_tpl_vars['location']; ?>
_<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_block_width" size="25" value="<?php echo $this->_tpl_vars['block']['properties']['width']; ?>
" class="input-text-short cm-value-integer" />
			<select name="block[width_unit]">
				<option value="P"<?php if ($this->_tpl_vars['block']['properties']['width_unit'] == 'P'): ?> selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('percents', $this->getLanguage()); ?>
</option>
				<option value="A"<?php if ($this->_tpl_vars['block']['properties']['width_unit'] == 'A'): ?> selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('pixels', $this->getLanguage()); ?>
</option>
			</select>
		</div>
		<?php endif; ?>

		<?php $this->_tag_stack[] = array('hook', array('name' => "block_manager:settings")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
		<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
	</fieldset>
	</div>
</div>

<?php if ($this->_tpl_vars['block']['text_id'] != 'central_content'): ?>
<script type="text/javascript">
//<![CDATA[
fn_check_block_params(<?php echo $this->_tpl_vars['js_param']; ?>
, '<?php echo $this->_tpl_vars['location']; ?>
', <?php echo $this->_tpl_vars['id']; ?>
, null, '<?php echo $this->_tpl_vars['block_type']; ?>
');
//]]>
</script>
<?php endif; ?>
<div class="buttons-container">
	<?php if ($this->_tpl_vars['add_block']): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('create' => true,'but_name' => "dispatch[block_manager.add]",'cancel_action' => 'close')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php else: ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[block_manager.update]",'cancel_action' => 'close')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
</div>
</form>
<!--content_group<?php echo $this->_tpl_vars['id']; ?>
<?php echo $this->_tpl_vars['block_type']; ?>
_<?php echo $this->_tpl_vars['location']; ?>
--></div>
<?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>