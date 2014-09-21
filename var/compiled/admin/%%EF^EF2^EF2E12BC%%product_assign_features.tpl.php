<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:44
         compiled from views/products/components/product_assign_features.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'defined', 'views/products/components/product_assign_features.tpl', 16, false),array('modifier', 'default', 'views/products/components/product_assign_features.tpl', 39, false),array('function', 'script', 'views/products/components/product_assign_features.tpl', 56, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('none','enter_other','enter_other'));
?>

<?php $_from = $this->_tpl_vars['product_features']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['feature_id'] => $this->_tpl_vars['feature']):
?>
	<?php if ($this->_tpl_vars['feature']['feature_type'] != 'G'): ?>
		<div class="form-field">
			<label for="feature_<?php echo $this->_tpl_vars['feature_id']; ?>
"><?php echo $this->_tpl_vars['feature']['description']; ?>
:</label>
			<div class="select-field">
			<strong><?php echo $this->_tpl_vars['feature']['prefix']; ?>
</strong>
			<?php if ($this->_tpl_vars['feature']['feature_type'] == 'S' || $this->_tpl_vars['feature']['feature_type'] == 'N' || $this->_tpl_vars['feature']['feature_type'] == 'E'): ?>
				<?php $this->assign('value_selected', false, false); ?>
				<select name="product_data[product_features][<?php echo $this->_tpl_vars['feature_id']; ?>
]" id="feature_<?php echo $this->_tpl_vars['feature_id']; ?>
" onchange="$('#input_<?php echo $this->_tpl_vars['feature_id']; ?>
').toggleBy((this.value != 'disable_select'));">
					<option value="">-<?php echo fn_get_lang_var('none', $this->getLanguage()); ?>
-</option>
					<?php $_from = $this->_tpl_vars['feature']['variants']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['var']):
?>
					<option value="<?php echo $this->_tpl_vars['var']['variant_id']; ?>
" <?php if ($this->_tpl_vars['var']['variant_id'] == $this->_tpl_vars['feature']['variant_id']): ?><?php $this->assign('value_selected', true, false); ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['var']['variant']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
					<?php if (! defined('COMPANY_ID')): ?>
					<option value="disable_select">-<?php echo fn_get_lang_var('enter_other', $this->getLanguage()); ?>
-</option>
					<?php endif; ?>
				</select>
				<input type="text" class="input-text input-empty hidden<?php if ($this->_tpl_vars['feature']['feature_type'] == 'N'): ?> cm-value-integer<?php endif; ?>" name="product_data[add_new_variant][<?php echo $this->_tpl_vars['feature']['feature_id']; ?>
][variant]" id="input_<?php echo $this->_tpl_vars['feature_id']; ?>
" />

			<?php elseif ($this->_tpl_vars['feature']['feature_type'] == 'M'): ?>
				<div class="select-field">
					<input type="hidden" name="product_data[product_features][<?php echo $this->_tpl_vars['feature_id']; ?>
]" value="" />
					<?php $_from = $this->_tpl_vars['feature']['variants']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['var']):
?>
						<p><label class="label-html-checkboxes" for="variant_<?php echo $this->_tpl_vars['var']['variant_id']; ?>
"><input type="checkbox" class="html-checkboxes" id="variant_<?php echo $this->_tpl_vars['var']['variant_id']; ?>
" name="product_data[product_features][<?php echo $this->_tpl_vars['feature_id']; ?>
][<?php echo $this->_tpl_vars['var']['variant_id']; ?>
]" <?php if ($this->_tpl_vars['var']['selected']): ?>checked="checked"<?php endif; ?> value="<?php echo $this->_tpl_vars['var']['variant_id']; ?>
" /><?php echo $this->_tpl_vars['var']['variant']; ?>
</label></p>
					<?php endforeach; endif; unset($_from); ?>
					<?php if (! defined('COMPANY_ID')): ?>
					<p><label for="input_<?php echo $this->_tpl_vars['feature_id']; ?>
"><?php echo fn_get_lang_var('enter_other', $this->getLanguage()); ?>
:</label>&nbsp;
					<input type="text" class="input-text" name="product_data[add_new_variant][<?php echo $this->_tpl_vars['feature']['feature_id']; ?>
][variant]" id="feature_<?php echo $this->_tpl_vars['feature_id']; ?>
" />
					</p>
					<?php endif; ?>
				</div>
			<?php elseif ($this->_tpl_vars['feature']['feature_type'] == 'C'): ?>
				<input type="hidden" name="product_data[product_features][<?php echo $this->_tpl_vars['feature_id']; ?>
]" value="N" />
				<input type="checkbox" name="product_data[product_features][<?php echo $this->_tpl_vars['feature_id']; ?>
]" value="Y" id="feature_<?php echo $this->_tpl_vars['feature_id']; ?>
" class="checkbox" <?php if ($this->_tpl_vars['feature']['value'] == 'Y'): ?>checked="checked"<?php endif; ?> />

			<?php elseif ($this->_tpl_vars['feature']['feature_type'] == 'D'): ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/calendar.tpl", 'smarty_include_vars' => array('date_id' => "date_".($this->_tpl_vars['feature_id']),'date_name' => "product_data[product_features][".($this->_tpl_vars['feature_id'])."]",'date_val' => smarty_modifier_default(@$this->_tpl_vars['feature']['value_int'], @TIME),'start_year' => $this->_tpl_vars['settings']['Company']['company_start_year'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

			<?php else: ?>
				<input type="text" name="product_data[product_features][<?php echo $this->_tpl_vars['feature_id']; ?>
]" value="<?php if ($this->_tpl_vars['feature']['feature_type'] == 'O'): ?><?php echo $this->_tpl_vars['feature']['value_int']; ?>
<?php else: ?><?php echo $this->_tpl_vars['feature']['value']; ?>
<?php endif; ?>" id="feature_<?php echo $this->_tpl_vars['feature_id']; ?>
" class="input-text<?php if ($this->_tpl_vars['feature']['feature_type'] == 'O'): ?> cm-value-integer<?php endif; ?>" />
			<?php endif; ?>
			<strong><?php echo $this->_tpl_vars['feature']['suffix']; ?>
</strong>
			</div>
		</div>
	<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>

<?php $_from = $this->_tpl_vars['product_features']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['feature_id'] => $this->_tpl_vars['feature']):
?>
	<?php if ($this->_tpl_vars['feature']['feature_type'] == 'G' && $this->_tpl_vars['feature']['subfeatures']): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['feature']['description'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/product_assign_features.tpl", 'smarty_include_vars' => array('product_features' => $this->_tpl_vars['feature']['subfeatures'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
<?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>