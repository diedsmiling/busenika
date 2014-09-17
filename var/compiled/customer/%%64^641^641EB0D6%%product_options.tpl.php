<?php /* Smarty version 2.6.18, created on 2014-09-17 00:24:29
         compiled from views/products/components/product_options.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'views/products/components/product_options.tpl', 3, false),array('modifier', 'default', 'views/products/components/product_options.tpl', 20, false),array('modifier', 'strpos', 'views/products/components/product_options.tpl', 28, false),array('modifier', 'floatval', 'views/products/components/product_options.tpl', 37, false),array('modifier', 'escape', 'views/products/components/product_options.tpl', 91, false),array('modifier', 'trim', 'views/products/components/product_options.tpl', 106, false),array('block', 'hook', 'views/products/components/product_options.tpl', 37, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('select_option_above','please_select_one','na','please_select_one','select_option_above','na','nocombination'));
?>

<?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>


<?php if (( $this->_tpl_vars['settings']['General']['display_options_modifiers'] == 'Y' && ( $this->_tpl_vars['auth']['user_id'] || ( $this->_tpl_vars['settings']['General']['allow_anonymous_shopping'] != 'P' && ! $this->_tpl_vars['auth']['user_id'] ) ) )): ?>
<?php $this->assign('show_modifiers', true, false); ?>
<?php endif; ?>

<input type="hidden" name="appearance[details_page]" value="<?php echo $this->_tpl_vars['details_page']; ?>
" />
<?php $_from = $this->_tpl_vars['product']['detailed_params']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['param'] => $this->_tpl_vars['value']):
?>
	<input type="hidden" name="additional_info[<?php echo $this->_tpl_vars['param']; ?>
]" value="<?php echo $this->_tpl_vars['value']; ?>
" />
<?php endforeach; endif; unset($_from); ?>

<?php if ($this->_tpl_vars['product_options']): ?>
<?php if ($this->_tpl_vars['obj_prefix']): ?>
	<input type="hidden" name="appearance[obj_prefix]" value="<?php echo $this->_tpl_vars['obj_prefix']; ?>
" />
<?php endif; ?>

<?php if ($this->_tpl_vars['location'] == 'cart' || $this->_tpl_vars['product']['object_id']): ?>
	<input type="hidden" name="<?php echo $this->_tpl_vars['name']; ?>
[<?php echo $this->_tpl_vars['id']; ?>
][object_id]" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['id'], @$this->_tpl_vars['obj_id']); ?>
" />
<?php endif; ?>

<div id="opt_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
">
	<?php $_from = $this->_tpl_vars['product_options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['product_options'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['product_options']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['po']):
        $this->_foreach['product_options']['iteration']++;
?>
	
	<?php $this->assign('selected_variant', "", false); ?>
	<div class="form-field<?php if (! $this->_tpl_vars['capture_options_vs_qty']): ?> product-list-field<?php endif; ?> clear" id="opt_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['po']['option_id']; ?>
">
		<?php if (! ( strpos('SRC', $this->_tpl_vars['po']['option_type']) !== false && ! $this->_tpl_vars['po']['variants'] && $this->_tpl_vars['po']['missing_variants_handling'] == 'H' )): ?>
		<label for="option_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['po']['option_id']; ?>
" class="<?php if ($this->_tpl_vars['po']['required'] == 'Y'): ?>cm-required<?php endif; ?> <?php if ($this->_tpl_vars['po']['regexp']): ?>cm-regexp<?php endif; ?>"><?php echo $this->_tpl_vars['po']['option_name']; ?>
<?php if ($this->_tpl_vars['po']['description']): ?>&nbsp;<?php ob_start(); ?><?php echo $this->_tpl_vars['po']['description']; ?>
<?php $this->_smarty_vars['capture']['tooltip'] = ob_get_contents(); ob_end_clean(); ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tooltip.tpl", 'smarty_include_vars' => array('tooltip' => $this->_smarty_vars['capture']['tooltip'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?>:</label>
		<?php if ($this->_tpl_vars['po']['option_type'] == 'S'): ?> 			<?php if ($this->_tpl_vars['po']['variants']): ?>
				<?php if ($this->_tpl_vars['po']['disabled'] && ! $this->_tpl_vars['po']['not_required']): ?><input type="hidden" value="" id="option_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['po']['option_id']; ?>
" /><?php endif; ?>
				<select name="<?php echo $this->_tpl_vars['name']; ?>
[<?php echo $this->_tpl_vars['id']; ?>
][product_options][<?php echo $this->_tpl_vars['po']['option_id']; ?>
]" <?php if (! $this->_tpl_vars['po']['disabled']): ?>id="option_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['po']['option_id']; ?>
"<?php endif; ?> onchange="<?php if ($this->_tpl_vars['product']['options_update']): ?>fn_change_options('<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
', '<?php echo $this->_tpl_vars['id']; ?>
', '<?php echo $this->_tpl_vars['po']['option_id']; ?>
');<?php endif; ?> fn_change_varian_image('<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
', '<?php echo $this->_tpl_vars['po']['option_id']; ?>
');" <?php if ($this->_tpl_vars['product']['exclude_from_calculate'] && ! $this->_tpl_vars['product']['aoc'] || $this->_tpl_vars['po']['disabled']): ?>disabled="disabled" class="disabled"<?php endif; ?>>
				<?php if ($this->_tpl_vars['product']['options_type'] == 'S'): ?><option value=""><?php if ($this->_tpl_vars['po']['disabled']): ?><?php echo fn_get_lang_var('select_option_above', $this->getLanguage()); ?>
<?php else: ?><?php echo fn_get_lang_var('please_select_one', $this->getLanguage()); ?>
<?php endif; ?></option><?php endif; ?>
					<?php $_from = $this->_tpl_vars['po']['variants']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['vars'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['vars']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['vr']):
        $this->_foreach['vars']['iteration']++;
?>
						<?php if (! $this->_tpl_vars['po']['disabled'] || ( $this->_tpl_vars['po']['disabled'] && $this->_tpl_vars['po']['value'] && $this->_tpl_vars['po']['value'] == $this->_tpl_vars['vr']['variant_id'] )): ?>
							<option value="<?php echo $this->_tpl_vars['vr']['variant_id']; ?>
" <?php if ($this->_tpl_vars['po']['value'] == $this->_tpl_vars['vr']['variant_id']): ?><?php $this->assign('selected_variant', $this->_tpl_vars['vr']['variant_id'], false); ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['vr']['variant_name']; ?>
 <?php if ($this->_tpl_vars['show_modifiers']): ?><?php $this->_tag_stack[] = array('hook', array('name' => "products:options_modifiers")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php if (floatval($this->_tpl_vars['vr']['modifier'])): ?>(<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/modifier.tpl", 'smarty_include_vars' => array('mod_type' => $this->_tpl_vars['vr']['modifier_type'],'mod_value' => $this->_tpl_vars['vr']['modifier'],'display_sign' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>)<?php endif; ?><?php if ($this->_tpl_vars['addons']['reward_points']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/reward_points/hooks/products/options_modifiers.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php endif; ?></option>
						<?php endif; ?>
					<?php endforeach; endif; unset($_from); ?>
				</select>
			<?php else: ?>
				<input type="hidden" name="<?php echo $this->_tpl_vars['name']; ?>
[<?php echo $this->_tpl_vars['id']; ?>
][product_options][<?php echo $this->_tpl_vars['po']['option_id']; ?>
]" value="<?php echo $this->_tpl_vars['po']['value']; ?>
" id="option_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['po']['option_id']; ?>
" />
				<?php echo fn_get_lang_var('na', $this->getLanguage()); ?>

			<?php endif; ?>
		<?php elseif ($this->_tpl_vars['po']['option_type'] == 'R'): ?> 			<?php if ($this->_tpl_vars['po']['variants']): ?>
				<ul id="option_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['po']['option_id']; ?>
_group">
					<li class="hidden"><input type="hidden" name="<?php echo $this->_tpl_vars['name']; ?>
[<?php echo $this->_tpl_vars['id']; ?>
][product_options][<?php echo $this->_tpl_vars['po']['option_id']; ?>
]" value="<?php echo $this->_tpl_vars['po']['value']; ?>
" id="option_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['po']['option_id']; ?>
" /></li>
					<?php if (! $this->_tpl_vars['po']['disabled']): ?>
						<?php $_from = $this->_tpl_vars['po']['variants']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['vars'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['vars']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['vr']):
        $this->_foreach['vars']['iteration']++;
?>
							<li><input type="radio" class="radio" name="<?php echo $this->_tpl_vars['name']; ?>
[<?php echo $this->_tpl_vars['id']; ?>
][product_options][<?php echo $this->_tpl_vars['po']['option_id']; ?>
]" value="<?php echo $this->_tpl_vars['vr']['variant_id']; ?>
" <?php if ($this->_tpl_vars['po']['value'] == $this->_tpl_vars['vr']['variant_id']): ?><?php $this->assign('selected_variant', $this->_tpl_vars['vr']['variant_id'], false); ?>checked="checked"<?php endif; ?> onclick="<?php if ($this->_tpl_vars['product']['options_update']): ?>fn_change_options('<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
', '<?php echo $this->_tpl_vars['id']; ?>
', '<?php echo $this->_tpl_vars['po']['option_id']; ?>
');<?php endif; ?> fn_change_varian_image('<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
', '<?php echo $this->_tpl_vars['po']['option_id']; ?>
', '<?php echo $this->_tpl_vars['vr']['variant_id']; ?>
');" <?php if ($this->_tpl_vars['product']['exclude_from_calculate'] && ! $this->_tpl_vars['product']['aoc'] || $this->_tpl_vars['po']['disabled']): ?>disabled="disabled"<?php endif; ?> />
							<span id="option_description_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['po']['option_id']; ?>
_<?php echo $this->_tpl_vars['vr']['variant_id']; ?>
"><?php echo $this->_tpl_vars['vr']['variant_name']; ?>
&nbsp;<?php if ($this->_tpl_vars['show_modifiers']): ?><?php $this->_tag_stack[] = array('hook', array('name' => "products:options_modifiers")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php if (floatval($this->_tpl_vars['vr']['modifier'])): ?>(<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/modifier.tpl", 'smarty_include_vars' => array('mod_type' => $this->_tpl_vars['vr']['modifier_type'],'mod_value' => $this->_tpl_vars['vr']['modifier'],'display_sign' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>)<?php endif; ?><?php if ($this->_tpl_vars['addons']['reward_points']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/reward_points/hooks/products/options_modifiers.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php endif; ?></span></li>
						<?php endforeach; endif; unset($_from); ?>
					<?php endif; ?>
				</ul>
				<?php if (! $this->_tpl_vars['po']['value'] && $this->_tpl_vars['product']['options_type'] == 'S' && ! $this->_tpl_vars['po']['disabled']): ?><p class="description clear-both"><?php echo fn_get_lang_var('please_select_one', $this->getLanguage()); ?>
</p><?php elseif (! $this->_tpl_vars['po']['value'] && $this->_tpl_vars['product']['options_type'] == 'S' && $this->_tpl_vars['po']['disabled']): ?><p class="description clear-both"><?php echo fn_get_lang_var('select_option_above', $this->getLanguage()); ?>
</p><?php endif; ?>
			<?php else: ?>
				<input type="hidden" name="<?php echo $this->_tpl_vars['name']; ?>
[<?php echo $this->_tpl_vars['id']; ?>
][product_options][<?php echo $this->_tpl_vars['po']['option_id']; ?>
]" value="<?php echo $this->_tpl_vars['po']['value']; ?>
" id="option_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['po']['option_id']; ?>
" />
				<?php echo fn_get_lang_var('na', $this->getLanguage()); ?>

			<?php endif; ?>

		<?php elseif ($this->_tpl_vars['po']['option_type'] == 'C'): ?> 
			<?php $_from = $this->_tpl_vars['po']['variants']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['vr']):
?>
			<?php if ($this->_tpl_vars['vr']['position'] == 0): ?>
				<input id="unchecked_option_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['po']['option_id']; ?>
" type="hidden" name="<?php echo $this->_tpl_vars['name']; ?>
[<?php echo $this->_tpl_vars['id']; ?>
][product_options][<?php echo $this->_tpl_vars['po']['option_id']; ?>
]" value="<?php echo $this->_tpl_vars['vr']['variant_id']; ?>
" <?php if ($this->_tpl_vars['po']['disabled']): ?>disabled="disabled"<?php endif; ?> />
			<?php else: ?>
				<input id="option_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['po']['option_id']; ?>
" type="checkbox" name="<?php echo $this->_tpl_vars['name']; ?>
[<?php echo $this->_tpl_vars['id']; ?>
][product_options][<?php echo $this->_tpl_vars['po']['option_id']; ?>
]" value="<?php echo $this->_tpl_vars['vr']['variant_id']; ?>
" class="checkbox" <?php if ($this->_tpl_vars['po']['value'] == $this->_tpl_vars['vr']['variant_id']): ?>checked="checked"<?php endif; ?> <?php if ($this->_tpl_vars['product']['exclude_from_calculate'] && ! $this->_tpl_vars['product']['aoc'] || $this->_tpl_vars['po']['disabled']): ?>disabled="disabled"<?php endif; ?> <?php if ($this->_tpl_vars['product']['options_update']): ?>onclick="fn_change_options('<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
', '<?php echo $this->_tpl_vars['id']; ?>
', '<?php echo $this->_tpl_vars['po']['option_id']; ?>
');"<?php endif; ?>/>
				<?php if ($this->_tpl_vars['show_modifiers']): ?><?php $this->_tag_stack[] = array('hook', array('name' => "products:options_modifiers")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php if (floatval($this->_tpl_vars['vr']['modifier'])): ?>(<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/modifier.tpl", 'smarty_include_vars' => array('mod_type' => $this->_tpl_vars['vr']['modifier_type'],'mod_value' => $this->_tpl_vars['vr']['modifier'],'display_sign' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>)<?php endif; ?><?php if ($this->_tpl_vars['addons']['reward_points']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/reward_points/hooks/products/options_modifiers.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php endif; ?>
			<?php endif; ?>
			<?php endforeach; endif; unset($_from); ?>

		<?php elseif ($this->_tpl_vars['po']['option_type'] == 'I'): ?> 			<input id="option_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['po']['option_id']; ?>
" type="text" name="<?php echo $this->_tpl_vars['name']; ?>
[<?php echo $this->_tpl_vars['id']; ?>
][product_options][<?php echo $this->_tpl_vars['po']['option_id']; ?>
]" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['po']['value'], @$this->_tpl_vars['po']['inner_hint']); ?>
" <?php if ($this->_tpl_vars['product']['exclude_from_calculate'] && ! $this->_tpl_vars['product']['aoc']): ?>disabled="disabled"<?php endif; ?> class="valign input-text<?php if ($this->_tpl_vars['po']['inner_hint'] && $this->_tpl_vars['po']['value'] == ""): ?> cm-hint<?php endif; ?><?php if ($this->_tpl_vars['product']['exclude_from_calculate'] && ! $this->_tpl_vars['product']['aoc']): ?> disabled<?php endif; ?>" />
		<?php elseif ($this->_tpl_vars['po']['option_type'] == 'T'): ?> 			<textarea id="option_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['po']['option_id']; ?>
" class="input-textarea-long<?php if ($this->_tpl_vars['po']['inner_hint'] && $this->_tpl_vars['po']['value'] == ""): ?> cm-hint<?php endif; ?><?php if ($this->_tpl_vars['product']['exclude_from_calculate'] && ! $this->_tpl_vars['product']['aoc']): ?> disabled<?php endif; ?>" rows="3" name="<?php echo $this->_tpl_vars['name']; ?>
[<?php echo $this->_tpl_vars['id']; ?>
][product_options][<?php echo $this->_tpl_vars['po']['option_id']; ?>
]" <?php if ($this->_tpl_vars['product']['exclude_from_calculate'] && ! $this->_tpl_vars['product']['aoc']): ?>disabled="disabled"<?php endif; ?>><?php echo smarty_modifier_default(@$this->_tpl_vars['po']['value'], @$this->_tpl_vars['po']['inner_hint']); ?>
</textarea>
		<?php elseif ($this->_tpl_vars['po']['option_type'] == 'F'): ?> 			<div class="clear">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/fileuploader.tpl", 'smarty_include_vars' => array('images' => $this->_tpl_vars['product']['extra']['custom_files'][$this->_tpl_vars['po']['option_id']],'var_name' => ($this->_tpl_vars['name'])."[".($this->_tpl_vars['po']['option_id']).($this->_tpl_vars['id'])."]",'multiupload' => $this->_tpl_vars['po']['multiupload'],'hidden_name' => ($this->_tpl_vars['name'])."[custom_files][".($this->_tpl_vars['po']['option_id']).($this->_tpl_vars['id'])."]",'hidden_value' => ($this->_tpl_vars['id'])."_".($this->_tpl_vars['po']['option_id']),'label_id' => "option_".($this->_tpl_vars['obj_prefix']).($this->_tpl_vars['id'])."_".($this->_tpl_vars['po']['option_id']),'prefix' => $this->_tpl_vars['obj_prefix'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</div>
		<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->_tpl_vars['po']['comment']): ?>
			<p class="description clear-both"><?php echo $this->_tpl_vars['po']['comment']; ?>
</p>
		<?php endif; ?>

		<?php if ($this->_tpl_vars['po']['regexp'] && ! $this->_tpl_vars['no_script']): ?>
			<script type="text/javascript">
			//<![CDATA[
				regexp['option_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['po']['option_id']; ?>
'] = <?php echo $this->_tpl_vars['ldelim']; ?>
regexp: "<?php echo smarty_modifier_escape($this->_tpl_vars['po']['regexp'], 'javascript'); ?>
", message: "<?php echo $this->_tpl_vars['po']['incorrect_message']; ?>
"<?php echo $this->_tpl_vars['rdelim']; ?>
;
			//]]>
			</script>
		<?php endif; ?>

		<?php ob_start(); ?>
			<?php if (! $this->_tpl_vars['po']['disabled']): ?>
				<?php $_from = $this->_tpl_vars['po']['variants']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['var']):
?>
					<?php if ($this->_tpl_vars['var']['image_pair']['image_id']): ?>
						<?php if ($this->_tpl_vars['var']['variant_id'] == $this->_tpl_vars['selected_variant']): ?><?php $this->assign('_class', "product-variant-image-selected", false); ?><?php else: ?><?php $this->assign('_class', "product-variant-image-unselected", false); ?><?php endif; ?>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image.tpl", 'smarty_include_vars' => array('class' => "hand ".($this->_tpl_vars['_class']),'show_thumbnail' => 'Y','images' => $this->_tpl_vars['var']['image_pair'],'object_type' => 'product_option','image_width' => '50','obj_id' => "variant_image_".($this->_tpl_vars['obj_prefix']).($this->_tpl_vars['id'])."_".($this->_tpl_vars['po']['option_id'])."_".($this->_tpl_vars['var']['variant_id']),'image_onclick' => "fn_set_option_value('".($this->_tpl_vars['obj_prefix']).($this->_tpl_vars['id'])."', '".($this->_tpl_vars['po']['option_id'])."', '".($this->_tpl_vars['var']['variant_id'])."'); void(0);")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					<?php endif; ?>
				<?php endforeach; endif; unset($_from); ?>
			<?php endif; ?>
		<?php $this->_smarty_vars['capture']['variant_images'] = ob_get_contents(); ob_end_clean(); ?>
		<?php if (trim($this->_smarty_vars['capture']['variant_images'])): ?><div class="product-variant-image clear-both"><?php echo $this->_smarty_vars['capture']['variant_images']; ?>
</div><?php endif; ?>
	</div>
	<?php endforeach; endif; unset($_from); ?>
</div>
<?php if ($this->_tpl_vars['product']['show_exception_warning'] == 'Y'): ?>
	<p id="warning_<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
" class="cm-no-combinations<?php if ($this->_tpl_vars['location'] != 'cart'): ?>-<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
<?php endif; ?> price"><?php echo fn_get_lang_var('nocombination', $this->getLanguage()); ?>
</p>
<?php endif; ?>
<?php endif; ?>

<?php if (! $this->_tpl_vars['no_script']): ?>
<script type="text/javascript">
//<![CDATA[
function fn_form_pre_<?php echo smarty_modifier_default(@$this->_tpl_vars['form_name'], "product_form_".($this->_tpl_vars['obj_prefix']).($this->_tpl_vars['id'])); ?>
()
<?php echo $this->_tpl_vars['ldelim']; ?>

<?php if ($this->_tpl_vars['location'] == 'cart'): ?>
	warning_class = '.cm-no-combinations';
<?php else: ?>
	warning_class = '.cm-no-combinations-<?php echo $this->_tpl_vars['obj_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
';
<?php endif; ?>
<?php echo '
	if ($(warning_class).length) {
		jQuery.showNotifications({\'notification\': {\'type\': \'W\', \'title\': lang.warning, \'message\': lang.cannot_buy, \'save_state\': false}});
		return false;
	} else {
		
		return true;
	}
'; ?>

<?php echo $this->_tpl_vars['rdelim']; ?>
;

//]]>
</script>
<?php endif; ?>