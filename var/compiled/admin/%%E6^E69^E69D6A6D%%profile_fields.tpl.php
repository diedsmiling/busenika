<?php /* Smarty version 2.6.18, created on 2014-09-22 23:32:01
         compiled from views/profiles/components/profile_fields.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'views/profiles/components/profile_fields.tpl', 38, false),array('modifier', 'default', 'views/profiles/components/profile_fields.tpl', 78, false),array('modifier', 'escape', 'views/profiles/components/profile_fields.tpl', 130, false),array('function', 'script', 'views/profiles/components/profile_fields.tpl', 142, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('text_ship_to_billing','ship_to_another','text_ship_to_billing','select_profile','delete','select_state','select_country'));
?>

<?php if ($this->_tpl_vars['profile_fields'][$this->_tpl_vars['section']]): ?>

<?php if (! $this->_tpl_vars['nothing_extra']): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['title'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['shipping_flag']): ?>
<div class="select-field">
	<input class="checkbox hidden" id="elm_ship_to_another" type="checkbox" name="ship_to_another" value="Y" onclick="$('#sa').switchAvailability(!this.checked);" <?php if ($this->_tpl_vars['ship_to_another']): ?>checked="checked"<?php endif; ?> />

	<p <?php if ($this->_tpl_vars['ship_to_another']): ?>class="hidden"<?php endif; ?> id="on_sta_notice">
		<?php echo fn_get_lang_var('text_ship_to_billing', $this->getLanguage()); ?>
.&nbsp;<a class="cm-combination dashed" onclick="$('#elm_ship_to_another').click(); jQuery.profiles.rebuild_states('shipping');"><?php echo fn_get_lang_var('ship_to_another', $this->getLanguage()); ?>
</a>
	</p>
	<p <?php if (! $this->_tpl_vars['ship_to_another']): ?>class="hidden"<?php endif; ?> id="off_sta_notice">
		<a class="cm-combination dashed" onclick="$('#elm_ship_to_another').click();"><?php echo fn_get_lang_var('text_ship_to_billing', $this->getLanguage()); ?>
</a>
	</p>
</div>
<?php elseif ($this->_tpl_vars['section'] == 'S'): ?>
	<?php $this->assign('ship_to_another', true, false); ?>
	<input type="hidden" name="ship_to_another" value="Y" />
<?php endif; ?>

<?php if ($this->_tpl_vars['body_id']): ?>
	<div id="<?php echo $this->_tpl_vars['body_id']; ?>
" <?php if (! $this->_tpl_vars['ship_to_another']): ?>class="hidden"<?php endif; ?>>
<?php endif; ?>

<?php if ($this->_tpl_vars['section'] == 'S' && ! $this->_tpl_vars['ship_to_another']): ?>
	<?php $this->assign('disabled_param', "disabled=\"disabled\"", false); ?>
<?php else: ?>
	<?php $this->assign('disabled_param', "", false); ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['location'] == 'checkout' && $this->_tpl_vars['auth']['user_id'] && $this->_tpl_vars['settings']['General']['user_multiple_profiles'] == 'Y' && ( $this->_tpl_vars['section'] == 'B' || $this->_tpl_vars['section'] == 'S' )): ?> <div class="form-field">
	<label for="elm_profile_id"><?php echo fn_get_lang_var('select_profile', $this->getLanguage()); ?>
:</label>
	<select name="profile_id" id="elm_profile_id" onchange="jQuery.ajaxRequest('<?php echo fn_url("checkout.checkout", 'C', 'rel', '&'); ?>
', <?php echo $this->_tpl_vars['ldelim']; ?>
result_ids: 'checkout_steps, cart_items, checkout_totals', 'user_data[profile_id]': this.value, 'update_step': '<?php echo $this->_tpl_vars['update_step']; ?>
', 'edit_steps[]': '<?php echo $this->_tpl_vars['update_step']; ?>
', 'ship_to_another': '<?php echo $this->_tpl_vars['cart']['ship_to_another']; ?>
'<?php echo $this->_tpl_vars['rdelim']; ?>
);" class="select-expanded">
		<?php $_from = $this->_tpl_vars['user_profiles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['user_profile']):
?>
		<option value="<?php echo $this->_tpl_vars['user_profile']['profile_id']; ?>
" <?php if ($this->_tpl_vars['cart']['profile_id'] == $this->_tpl_vars['user_profile']['profile_id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['user_profile']['profile_name']; ?>
</option>
	<?php endforeach; endif; unset($_from); ?>
	</select>
	<?php if ($this->_tpl_vars['cart']['user_data']['profile_id'] && $this->_tpl_vars['cart']['user_data']['profile_type'] != 'P'): ?>
		<a <?php if ($this->_tpl_vars['use_ajax']): ?>class="cm-ajax"<?php endif; ?> href="<?php echo fn_url("profiles.update.delete_profile?profile_id=".($this->_tpl_vars['cart']['profile_id'])); ?>
" rev="checkout_steps,cart_items,checkout_totals"><?php echo fn_get_lang_var('delete', $this->getLanguage()); ?>
</a>
	<?php endif; ?>
</div>
<?php endif; ?>

<?php $_from = $this->_tpl_vars['profile_fields'][$this->_tpl_vars['section']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['field']):
?>
<?php if ($this->_tpl_vars['field']['field_name']): ?>
	<?php $this->assign('data_name', 'user_data', false); ?>
	<?php $this->assign('data_id', $this->_tpl_vars['field']['field_name'], false); ?>
	<?php $this->assign('value', $this->_tpl_vars['user_data'][$this->_tpl_vars['data_id']], false); ?>
<?php else: ?>
	<?php $this->assign('data_name', "user_data[fields]", false); ?>
	<?php $this->assign('data_id', $this->_tpl_vars['field']['field_id'], false); ?>
	<?php $this->assign('value', $this->_tpl_vars['user_data']['fields'][$this->_tpl_vars['data_id']], false); ?>
<?php endif; ?>

<div class="form-field">
	<label for="<?php echo $this->_tpl_vars['id_prefix']; ?>
elm_<?php echo $this->_tpl_vars['field']['field_id']; ?>
" class="<?php if ($this->_tpl_vars['field']['required'] == 'Y'): ?>cm-required<?php endif; ?><?php if ($this->_tpl_vars['field']['field_type'] == 'P'): ?> cm-phone<?php endif; ?><?php if ($this->_tpl_vars['field']['field_type'] == 'Z'): ?> cm-zipcode<?php endif; ?><?php if ($this->_tpl_vars['field']['field_type'] == 'E'): ?> cm-email<?php endif; ?><?php if ($this->_tpl_vars['field']['field_type'] == 'A'): ?> cm-state<?php endif; ?><?php if ($this->_tpl_vars['field']['field_type'] == 'O'): ?> cm-country<?php endif; ?> <?php if ($this->_tpl_vars['field']['field_type'] == 'O' || $this->_tpl_vars['field']['field_type'] == 'A' || $this->_tpl_vars['field']['field_type'] == 'Z'): ?><?php if ($this->_tpl_vars['section'] == 'S'): ?>cm-location-shipping<?php else: ?>cm-location-billing<?php endif; ?><?php endif; ?>"><?php echo $this->_tpl_vars['field']['description']; ?>
:</label>

	<?php if ($this->_tpl_vars['field']['field_type'] == 'L'): ?> 		<select id="<?php echo $this->_tpl_vars['id_prefix']; ?>
elm_<?php echo $this->_tpl_vars['field']['field_id']; ?>
" name="<?php echo $this->_tpl_vars['data_name']; ?>
[<?php echo $this->_tpl_vars['data_id']; ?>
]" <?php echo $this->_tpl_vars['disabled_param']; ?>
>
			<?php $_from = $this->_tpl_vars['titles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['t']):
?>
			<option <?php if ($this->_tpl_vars['value'] == $this->_tpl_vars['t']['param']): ?>selected="selected"<?php endif; ?> value="<?php echo $this->_tpl_vars['t']['param']; ?>
"><?php echo $this->_tpl_vars['t']['descr']; ?>
</option>
			<?php endforeach; endif; unset($_from); ?>
		</select>

	<?php elseif ($this->_tpl_vars['field']['field_type'] == 'A'): ?>  		<select id="<?php echo $this->_tpl_vars['id_prefix']; ?>
elm_<?php echo $this->_tpl_vars['field']['field_id']; ?>
" name="<?php echo $this->_tpl_vars['data_name']; ?>
[<?php echo $this->_tpl_vars['data_id']; ?>
]" <?php echo $this->_tpl_vars['disabled_param']; ?>
>
			<option value="">- <?php echo fn_get_lang_var('select_state', $this->getLanguage()); ?>
 -</option>
						<?php $this->assign('country_code', $this->_tpl_vars['settings']['General']['default_country'], false); ?>
			<?php $this->assign('state_code', smarty_modifier_default(@$this->_tpl_vars['value'], @$this->_tpl_vars['settings']['General']['default_country']), false); ?>
			<?php if ($this->_tpl_vars['states']): ?>
				<?php $_from = $this->_tpl_vars['states'][$this->_tpl_vars['country_code']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['state']):
?>
					<option <?php if ($this->_tpl_vars['state_code'] == $this->_tpl_vars['state']['code']): ?>selected="selected"<?php endif; ?> value="<?php echo $this->_tpl_vars['state']['code']; ?>
"><?php echo $this->_tpl_vars['state']['state']; ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
			<?php endif; ?>
		</select><input type="text" id="elm_<?php echo $this->_tpl_vars['field']['field_id']; ?>
_d" name="<?php echo $this->_tpl_vars['data_name']; ?>
[<?php echo $this->_tpl_vars['data_id']; ?>
]" size="32" maxlength="64" value="<?php echo $this->_tpl_vars['value']; ?>
" disabled="disabled" class="input-text hidden cm-skip-avail-switch" />
	
	<?php elseif ($this->_tpl_vars['field']['field_type'] == 'O'): ?>  		<?php $this->assign('_country', smarty_modifier_default(@$this->_tpl_vars['value'], @$this->_tpl_vars['settings']['General']['default_country']), false); ?>
		<select id="<?php echo $this->_tpl_vars['id_prefix']; ?>
elm_<?php echo $this->_tpl_vars['field']['field_id']; ?>
" class="<?php if ($this->_tpl_vars['section'] == 'S'): ?>cm-location-shipping<?php else: ?>cm-location-billing<?php endif; ?>" name="<?php echo $this->_tpl_vars['data_name']; ?>
[<?php echo $this->_tpl_vars['data_id']; ?>
]" <?php echo $this->_tpl_vars['disabled_param']; ?>
>
			<option value="">- <?php echo fn_get_lang_var('select_country', $this->getLanguage()); ?>
 -</option>
			<?php $_from = $this->_tpl_vars['countries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['country']):
?>
			<option <?php if ($this->_tpl_vars['_country'] == $this->_tpl_vars['country']['code']): ?>selected="selected"<?php endif; ?> value="<?php echo $this->_tpl_vars['country']['code']; ?>
"><?php echo $this->_tpl_vars['country']['country']; ?>
</option>
			<?php endforeach; endif; unset($_from); ?>
		</select>
	
	<?php elseif ($this->_tpl_vars['field']['field_type'] == 'C'): ?>  		<input type="hidden" name="<?php echo $this->_tpl_vars['data_name']; ?>
[<?php echo $this->_tpl_vars['data_id']; ?>
]" value="N" <?php echo $this->_tpl_vars['disabled_param']; ?>
 />
		<input type="checkbox" id="<?php echo $this->_tpl_vars['id_prefix']; ?>
elm_<?php echo $this->_tpl_vars['field']['field_id']; ?>
" name="<?php echo $this->_tpl_vars['data_name']; ?>
[<?php echo $this->_tpl_vars['data_id']; ?>
]" value="Y" <?php if ($this->_tpl_vars['value'] == 'Y'): ?>checked="checked"<?php endif; ?> class="checkbox" <?php echo $this->_tpl_vars['disabled_param']; ?>
 />

	<?php elseif ($this->_tpl_vars['field']['field_type'] == 'T'): ?>  		<textarea class="input-textarea" id="<?php echo $this->_tpl_vars['id_prefix']; ?>
elm_<?php echo $this->_tpl_vars['field']['field_id']; ?>
" name="<?php echo $this->_tpl_vars['data_name']; ?>
[<?php echo $this->_tpl_vars['data_id']; ?>
]" cols="32" rows="3" <?php echo $this->_tpl_vars['disabled_param']; ?>
><?php echo $this->_tpl_vars['value']; ?>
</textarea>
	
	<?php elseif ($this->_tpl_vars['field']['field_type'] == 'D'): ?>  		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/calendar.tpl", 'smarty_include_vars' => array('date_id' => "elm_".($this->_tpl_vars['field']['field_id']),'date_name' => ($this->_tpl_vars['data_name'])."[".($this->_tpl_vars['data_id'])."]",'date_val' => $this->_tpl_vars['value'],'start_year' => '1902','end_year' => '0','extra' => $this->_tpl_vars['disabled_param'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<?php elseif ($this->_tpl_vars['field']['field_type'] == 'S'): ?>  		<select id="<?php echo $this->_tpl_vars['id_prefix']; ?>
elm_<?php echo $this->_tpl_vars['field']['field_id']; ?>
" name="<?php echo $this->_tpl_vars['data_name']; ?>
[<?php echo $this->_tpl_vars['data_id']; ?>
]" <?php echo $this->_tpl_vars['disabled_param']; ?>
>
			<?php if ($this->_tpl_vars['field']['required'] != 'Y'): ?>
			<option value="">--</option>
			<?php endif; ?>
			<?php $_from = $this->_tpl_vars['field']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
			<option <?php if ($this->_tpl_vars['value'] == $this->_tpl_vars['k']): ?>selected="selected"<?php endif; ?> value="<?php echo $this->_tpl_vars['k']; ?>
"><?php echo $this->_tpl_vars['v']; ?>
</option>
			<?php endforeach; endif; unset($_from); ?>
		</select>
	
	<?php elseif ($this->_tpl_vars['field']['field_type'] == 'R'): ?>  		<div class="select-field">
		<?php $_from = $this->_tpl_vars['field']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['rfe'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['rfe']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
        $this->_foreach['rfe']['iteration']++;
?>
		<input class="radio" type="radio" id="<?php echo $this->_tpl_vars['id_prefix']; ?>
elm_<?php echo $this->_tpl_vars['field']['field_id']; ?>
_<?php echo $this->_tpl_vars['k']; ?>
" name="<?php echo $this->_tpl_vars['data_name']; ?>
[<?php echo $this->_tpl_vars['data_id']; ?>
]" value="<?php echo $this->_tpl_vars['k']; ?>
" <?php if (( ! $this->_tpl_vars['value'] && ($this->_foreach['rfe']['iteration'] <= 1) ) || $this->_tpl_vars['value'] == $this->_tpl_vars['k']): ?>checked="checked"<?php endif; ?> <?php echo $this->_tpl_vars['disabled_param']; ?>
 /><label for="<?php echo $this->_tpl_vars['id_prefix']; ?>
elm_<?php echo $this->_tpl_vars['field']['field_id']; ?>
_<?php echo $this->_tpl_vars['k']; ?>
"><?php echo $this->_tpl_vars['v']; ?>
</label>
		<?php endforeach; endif; unset($_from); ?>
		</div>

	<?php else: ?>  		<input type="text" id="<?php echo $this->_tpl_vars['id_prefix']; ?>
elm_<?php echo $this->_tpl_vars['field']['field_id']; ?>
" name="<?php echo $this->_tpl_vars['data_name']; ?>
[<?php echo $this->_tpl_vars['data_id']; ?>
]" size="32" value="<?php echo $this->_tpl_vars['value']; ?>
" class="input-text" <?php echo $this->_tpl_vars['disabled_param']; ?>
 />
	<?php endif; ?>

	<?php if (( $this->_tpl_vars['section'] == 'B' || $this->_tpl_vars['section'] == 'S' ) && $this->_tpl_vars['field']['field_type'] == 'A'): ?>
	<span>&nbsp;
	<script type="text/javascript">
	//<![CDATA[
	default_state[<?php if ($this->_tpl_vars['section'] == 'S'): ?>'shipping'<?php else: ?>'billing'<?php endif; ?>] = '<?php echo smarty_modifier_escape(smarty_modifier_default(@$this->_tpl_vars['value'], @$this->_tpl_vars['settings']['General']['default_country']), 'javascript'); ?>
';
	//]]>
	</script>
	</span>
	<?php endif; ?>
</div>
<?php endforeach; endif; unset($_from); ?>
<?php if ($this->_tpl_vars['body_id']): ?>
</div>
<?php endif; ?>

<?php endif; ?>
<?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>