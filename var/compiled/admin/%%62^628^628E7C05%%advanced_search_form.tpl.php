<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:41
         compiled from views/products/components/advanced_search_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'split', 'views/products/components/advanced_search_form.tpl', 3, false),array('function', 'script', 'views/products/components/advanced_search_form.tpl', 110, false),array('modifier', 'default', 'views/products/components/advanced_search_form.tpl', 8, false),array('modifier', 'sizeof', 'views/products/components/advanced_search_form.tpl', 11, false),array('modifier', 'in_array', 'views/products/components/advanced_search_form.tpl', 19, false),array('modifier', 'fn_text_placeholders', 'views/products/components/advanced_search_form.tpl', 36, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('none','your_range','none','yes','no','any'));
?>

<?php echo smarty_function_split(array('data' => $this->_tpl_vars['filter_features'],'size' => '3','assign' => 'splitted_filter','preverse_keys' => true), $this);?>

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="table-filters">
<?php $_from = $this->_tpl_vars['splitted_filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['filters_row'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['filters_row']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['filters_row']):
        $this->_foreach['filters_row']['iteration']++;
?>
<tr>
<?php $_from = $this->_tpl_vars['filters_row']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['filter']):
?>
	<th><?php echo smarty_modifier_default(@$this->_tpl_vars['filter']['filter'], @$this->_tpl_vars['filter']['description']); ?>
</th>
<?php endforeach; endif; unset($_from); ?>
</tr>
<tr valign="top"<?php if (( sizeof($this->_tpl_vars['splitted_filter']) > 1 ) && ($this->_foreach['filters_row']['iteration'] <= 1)): ?> class="delim"<?php endif; ?>>
<?php $_from = $this->_tpl_vars['filters_row']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['filter']):
?>
	<td width="33%">
		<?php if ($this->_tpl_vars['filter']['feature_type'] == 'S' || $this->_tpl_vars['filter']['feature_type'] == 'E' || $this->_tpl_vars['filter']['feature_type'] == 'M' || $this->_tpl_vars['filter']['feature_type'] == 'N' && ! $this->_tpl_vars['filter']['filter_id']): ?>
		<div class="scroll-y">
			<?php $this->assign('filter_ranges', smarty_modifier_default(@$this->_tpl_vars['filter']['ranges'], @$this->_tpl_vars['filter']['variants']), false); ?>
			<?php $_from = $this->_tpl_vars['filter_ranges']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['range']):
?>
				<?php $this->assign('range_id', smarty_modifier_default(@$this->_tpl_vars['range']['range_id'], @$this->_tpl_vars['range']['variant_id']), false); ?>
				<div class="select-field"><input type="checkbox" class="checkbox" name="<?php if ($this->_tpl_vars['filter']['feature_type'] == 'M'): ?>multiple_<?php endif; ?>variants[]" id="<?php echo $this->_tpl_vars['prefix']; ?>
variants_<?php echo $this->_tpl_vars['range_id']; ?>
" value="<?php if ($this->_tpl_vars['filter']['feature_type'] == 'M'): ?><?php echo $this->_tpl_vars['range_id']; ?>
<?php else: ?>[V<?php echo $this->_tpl_vars['range_id']; ?>
]<?php endif; ?>" <?php if (smarty_modifier_in_array("[V".($this->_tpl_vars['range_id'])."]", $this->_tpl_vars['search']['variants']) || smarty_modifier_in_array($this->_tpl_vars['range_id'], $this->_tpl_vars['search']['multiple_variants'])): ?>checked="checked"<?php endif; ?> /><label for="variants_<?php echo $this->_tpl_vars['range_id']; ?>
"><?php echo $this->_tpl_vars['filter']['prefix']; ?>
<?php echo $this->_tpl_vars['range']['variant']; ?>
<?php echo $this->_tpl_vars['filter']['suffix']; ?>
</label></div>
			<?php endforeach; endif; unset($_from); ?>
		</div>
		<?php elseif ($this->_tpl_vars['filter']['feature_type'] == 'O' || $this->_tpl_vars['filter']['feature_type'] == 'N' && $this->_tpl_vars['filter']['filter_id'] || $this->_tpl_vars['filter']['feature_type'] == 'D' || $this->_tpl_vars['filter']['condition_type'] == 'D' || $this->_tpl_vars['filter']['condition_type'] == 'F'): ?>
			<div class="scroll-y">
				<?php if ($this->_tpl_vars['filter']['condition_type']): ?>
					<?php $this->assign('el_id', "field_".($this->_tpl_vars['filter']['filter_id']), false); ?>
				<?php else: ?>
					<?php $this->assign('el_id', "feature_".($this->_tpl_vars['filter']['feature_id']), false); ?>
				<?php endif; ?>

				<div class="select-field"><input type="radio" name="variants[<?php echo $this->_tpl_vars['el_id']; ?>
]" id="<?php echo $this->_tpl_vars['prefix']; ?>
no_ranges_<?php echo $this->_tpl_vars['el_id']; ?>
" value="" checked="checked" class="radio" /><label for="<?php echo $this->_tpl_vars['prefix']; ?>
no_ranges_<?php echo $this->_tpl_vars['el_id']; ?>
"><?php echo fn_get_lang_var('none', $this->getLanguage()); ?>
</label></div>
				<?php $this->assign('filter_ranges', smarty_modifier_default(@$this->_tpl_vars['filter']['ranges'], @$this->_tpl_vars['filter']['variants']), false); ?>
				<?php $this->assign('_type', smarty_modifier_default(@$this->_tpl_vars['filter']['field_type'], 'R'), false); ?>
				<?php $_from = $this->_tpl_vars['filter_ranges']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['range']):
?>
					<?php $this->assign('range_id', smarty_modifier_default(@$this->_tpl_vars['range']['range_id'], @$this->_tpl_vars['range']['variant_id']), false); ?>
					<?php $this->assign('range_name', smarty_modifier_default(@$this->_tpl_vars['range']['range_name'], @$this->_tpl_vars['range']['variant']), false); ?>
					<div class="select-field"><input type="radio" class="radio" name="variants[<?php echo $this->_tpl_vars['el_id']; ?>
]" id="<?php echo $this->_tpl_vars['prefix']; ?>
ranges_<?php echo $this->_tpl_vars['el_id']; ?>
<?php echo $this->_tpl_vars['range_id']; ?>
" value="<?php echo $this->_tpl_vars['_type']; ?>
<?php echo $this->_tpl_vars['range_id']; ?>
" <?php if ($this->_tpl_vars['search']['variants'][$this->_tpl_vars['el_id']] == ($this->_tpl_vars['_type']).($this->_tpl_vars['range_id'])): ?>checked="checked"<?php endif; ?> /><label for="<?php echo $this->_tpl_vars['prefix']; ?>
ranges_<?php echo $this->_tpl_vars['el_id']; ?>
<?php echo $this->_tpl_vars['range_id']; ?>
"><?php echo fn_text_placeholders($this->_tpl_vars['range_name']); ?>
</label></div>
				<?php endforeach; endif; unset($_from); ?>
			</div>
			
			<?php if ($this->_tpl_vars['filter']['condition_type'] != 'F'): ?>
			<p><input type="radio" name="variants[<?php echo $this->_tpl_vars['el_id']; ?>
]" id="<?php echo $this->_tpl_vars['prefix']; ?>
select_custom_<?php echo $this->_tpl_vars['el_id']; ?>
" value="O" <?php if ($this->_tpl_vars['search']['variants'][$this->_tpl_vars['el_id']] == 'O'): ?>checked="checked"<?php endif; ?> class="radio" /><label for="<?php echo $this->_tpl_vars['prefix']; ?>
select_custom_<?php echo $this->_tpl_vars['el_id']; ?>
"><?php echo fn_get_lang_var('your_range', $this->getLanguage()); ?>
</label></p>
			
			<div class="select-field">
				<?php if ($this->_tpl_vars['filter']['feature_type'] == 'D'): ?>
				<?php if ($this->_tpl_vars['search']['custom_range'][$this->_tpl_vars['filter']['feature_id']]['from'] || $this->_tpl_vars['search']['custom_range'][$this->_tpl_vars['filter']['feature_id']]['to']): ?>
					<?php $this->assign('date_extra', "", false); ?>
				<?php else: ?>
					<?php $this->assign('date_extra', "\"disabled=\"\\disabled\\\"\"", false); ?>
				<?php endif; ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/calendar.tpl", 'smarty_include_vars' => array('date_id' => ($this->_tpl_vars['prefix'])."range_".($this->_tpl_vars['el_id'])."_from",'date_name' => "custom_range[".($this->_tpl_vars['filter']['feature_id'])."][from]",'date_val' => $this->_tpl_vars['search']['custom_range'][$this->_tpl_vars['filter']['feature_id']]['from'],'extra' => $this->_tpl_vars['date_extra'],'start_year' => $this->_tpl_vars['settings']['Company']['company_start_year'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/calendar.tpl", 'smarty_include_vars' => array('date_id' => ($this->_tpl_vars['prefix'])."range_".($this->_tpl_vars['el_id'])."_to",'date_name' => "custom_range[".($this->_tpl_vars['filter']['feature_id'])."][to]",'date_val' => $this->_tpl_vars['search']['custom_range'][$this->_tpl_vars['filter']['feature_id']]['to'],'extra' => $this->_tpl_vars['date_extra'],'start_year' => $this->_tpl_vars['settings']['Company']['company_start_year'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<input type="hidden" name="custom_range[<?php echo $this->_tpl_vars['filter']['feature_id']; ?>
][type]" value="D" />
				<?php else: ?>
				<input type="text" name="<?php if ($this->_tpl_vars['filter']['field_type']): ?>field_range[<?php echo $this->_tpl_vars['filter']['field_type']; ?>
]<?php else: ?>custom_range[<?php echo $this->_tpl_vars['filter']['feature_id']; ?>
]<?php endif; ?>[from]" id="<?php echo $this->_tpl_vars['prefix']; ?>
range_<?php echo $this->_tpl_vars['el_id']; ?>
_from" size="3" class="input-text-short" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['search']['custom_range'][$this->_tpl_vars['filter']['feature_id']]['from'], @$this->_tpl_vars['search']['field_range'][$this->_tpl_vars['filter']['field_type']]['from']); ?>
" <?php if ($this->_tpl_vars['search']['variants'][$this->_tpl_vars['el_id']] != 'O'): ?>disabled="disabled"<?php endif; ?> />
				&nbsp;-&nbsp;
				<input type="text" name="<?php if ($this->_tpl_vars['filter']['field_type']): ?>field_range[<?php echo $this->_tpl_vars['filter']['field_type']; ?>
]<?php else: ?>custom_range[<?php echo $this->_tpl_vars['filter']['feature_id']; ?>
]<?php endif; ?>[to]" size="3" class="input-text-short" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['search']['custom_range'][$this->_tpl_vars['filter']['feature_id']]['to'], @$this->_tpl_vars['search']['field_range'][$this->_tpl_vars['filter']['field_type']]['to']); ?>
" id="<?php echo $this->_tpl_vars['prefix']; ?>
range_<?php echo $this->_tpl_vars['el_id']; ?>
_to" <?php if ($this->_tpl_vars['search']['variants'][$this->_tpl_vars['el_id']] != 'O'): ?>disabled="disabled"<?php endif; ?> />
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<script type="text/javascript">
			//<![CDATA[
			$(":radio[name='variants[<?php echo $this->_tpl_vars['el_id']; ?>
]']").change(function() {
				var el_id = '<?php echo $this->_tpl_vars['el_id']; ?>
';
				$('#<?php echo $this->_tpl_vars['prefix']; ?>
range_' + el_id + '_from').attr('disabled', this.value !== 'O');
				$('#<?php echo $this->_tpl_vars['prefix']; ?>
range_' + el_id + '_to').attr('disabled', this.value !== 'O');
				<?php if ($this->_tpl_vars['filter']['feature_type'] == 'D'): ?>
				$('#<?php echo $this->_tpl_vars['prefix']; ?>
range_' + el_id + '_from_but').attr('disabled', this.value !== 'O');
				$('#<?php echo $this->_tpl_vars['prefix']; ?>
range_' + el_id + '_to_but').attr('disabled', this.value !== 'O');
				<?php endif; ?>
			});
			//]]>
			</script>
		<?php elseif ($this->_tpl_vars['filter']['feature_type'] == 'C' || $this->_tpl_vars['filter']['condition_type'] == 'C'): ?>
			<?php if ($this->_tpl_vars['filter']['condition_type']): ?>
				<?php $this->assign('el_id', $this->_tpl_vars['filter']['field_type'], false); ?>
			<?php else: ?>
				<?php $this->assign('el_id', $this->_tpl_vars['filter']['feature_id'], false); ?>
			<?php endif; ?>
			<div class="select-field">
				<input type="radio" class="radio" name="ch_filters[<?php echo $this->_tpl_vars['el_id']; ?>
]" id="<?php echo $this->_tpl_vars['prefix']; ?>
ranges_<?php echo $this->_tpl_vars['el_id']; ?>
_none" value="" <?php if (! $this->_tpl_vars['search']['ch_filters'][$this->_tpl_vars['el_id']]): ?>checked="checked"<?php endif; ?> />
				<label for="<?php echo $this->_tpl_vars['prefix']; ?>
ranges_<?php echo $this->_tpl_vars['el_id']; ?>
_none"><?php echo fn_get_lang_var('none', $this->getLanguage()); ?>
</label>
			</div>
			
			<div class="select-field">
				<input type="radio" class="radio" name="ch_filters[<?php echo $this->_tpl_vars['el_id']; ?>
]" id="<?php echo $this->_tpl_vars['prefix']; ?>
ranges_<?php echo $this->_tpl_vars['el_id']; ?>
_yes" value="Y" <?php if ($this->_tpl_vars['search']['ch_filters'][$this->_tpl_vars['el_id']] == 'Y'): ?>checked="checked"<?php endif; ?> />
				<label for="<?php echo $this->_tpl_vars['prefix']; ?>
ranges_<?php echo $this->_tpl_vars['el_id']; ?>
_yes"><?php echo fn_get_lang_var('yes', $this->getLanguage()); ?>
</label>
			</div>
			
			<div class="select-field">
				<input type="radio" class="radio" name="ch_filters[<?php echo $this->_tpl_vars['el_id']; ?>
]" id="<?php echo $this->_tpl_vars['prefix']; ?>
ranges_<?php echo $this->_tpl_vars['el_id']; ?>
_no" value="N" <?php if ($this->_tpl_vars['search']['ch_filters'][$this->_tpl_vars['el_id']] == 'N'): ?>checked="checked"<?php endif; ?> />
				<label for="<?php echo $this->_tpl_vars['prefix']; ?>
ranges_<?php echo $this->_tpl_vars['el_id']; ?>
_no"><?php echo fn_get_lang_var('no', $this->getLanguage()); ?>
</label>
			</div>
			
			<?php if (! $this->_tpl_vars['filter']['condition_type']): ?>
			<div class="select-field">
				<input type="radio" class="radio" name="ch_filters[<?php echo $this->_tpl_vars['el_id']; ?>
]" id="<?php echo $this->_tpl_vars['prefix']; ?>
ranges_<?php echo $this->_tpl_vars['el_id']; ?>
_any" value="A" <?php if ($this->_tpl_vars['search']['ch_filters'][$this->_tpl_vars['el_id']] == 'A'): ?>checked="checked"<?php endif; ?> />
				<label for="<?php echo $this->_tpl_vars['prefix']; ?>
ranges_<?php echo $this->_tpl_vars['el_id']; ?>
_any"><?php echo fn_get_lang_var('any', $this->getLanguage()); ?>
</label>
			</div>
			<?php endif; ?>
			
		<?php elseif ($this->_tpl_vars['filter']['feature_type'] == 'T'): ?>
			<div class="select-field nowrap">
				<?php echo $this->_tpl_vars['filter']['prefix']; ?>
<input type="text" name="tx_features[<?php echo $this->_tpl_vars['filter']['feature_id']; ?>
]" class="input-text" value="<?php echo $this->_tpl_vars['search']['tx_features'][$this->_tpl_vars['filter']['feature_id']]; ?>
" /><?php echo $this->_tpl_vars['filter']['suffix']; ?>

			</div>
		<?php endif; ?>
	</td>
<?php endforeach; endif; unset($_from); ?>
</tr>
<?php endforeach; endif; unset($_from); ?>
</table><?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>