<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:12
         compiled from views/products/components/products_select_fields.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'math', 'views/products/components/products_select_fields.tpl', 4, false),array('function', 'split', 'views/products/components/products_select_fields.tpl', 6, false),array('modifier', 'count', 'views/products/components/products_select_fields.tpl', 4, false),array('modifier', 'default', 'views/products/components/products_select_fields.tpl', 4, false),array('modifier', 'sort_by', 'views/products/components/products_select_fields.tpl', 6, false),array('modifier', 'md5', 'views/products/components/products_select_fields.tpl', 17, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('select_all','unselect_all'));
?>
<?php  ob_start();  ?>
<input type="hidden" name="selected_fields[object]" value="product" />
<?php echo smarty_function_math(array('equation' => "ceil(n/c)",'assign' => 'rows','n' => count($this->_tpl_vars['selected_fields']),'c' => smarty_modifier_default(@$this->_tpl_vars['columns'], '5')), $this);?>


<?php echo smarty_function_split(array('data' => smarty_modifier_sort_by($this->_tpl_vars['selected_fields'], 'text'),'size' => $this->_tpl_vars['rows'],'assign' => 'splitted_selected_fields','vertical_delimition' => false,'size_is_horizontal' => true), $this);?>


<table cellpadding="5" cellspacing="0" border="0" width="100%">
<tr valign="top">
	<?php $_from = $this->_tpl_vars['splitted_selected_fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['sfs']):
?>
		<td>
		<ul>
			<?php $_from = $this->_tpl_vars['sfs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foreach_sfs'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foreach_sfs']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['sf']):
        $this->_foreach['foreach_sfs']['iteration']++;
?>
				<li class="select-field">
					<?php if ($this->_tpl_vars['sf']): ?>
						<?php if ($this->_tpl_vars['sf']['disabled'] == 'Y'): ?><input type="hidden" value="Y" name="selected_fields<?php echo $this->_tpl_vars['sf']['name']; ?>
" /><?php endif; ?>
						<input type="checkbox" value="Y" name="selected_fields<?php echo $this->_tpl_vars['sf']['name']; ?>
" id="elm_<?php echo md5($this->_tpl_vars['sf']['name']); ?>
" checked="checked" <?php if ($this->_tpl_vars['sf']['disabled'] == 'Y'): ?>disabled="disabled"<?php endif; ?> class="checkbox cm-item-s" />
						<label for="elm_<?php echo md5($this->_tpl_vars['sf']['name']); ?>
"><?php echo $this->_tpl_vars['sf']['text']; ?>
&nbsp;</label>
					<?php endif; ?>
				</li>
			<?php endforeach; endif; unset($_from); ?>
		</ul>
		</td>
	<?php endforeach; endif; unset($_from); ?>
</tr></table>
<p>
<a name="check_all" class="cm-check-items-s cm-on underlined"><?php echo fn_get_lang_var('select_all', $this->getLanguage()); ?>
</a>&nbsp;/&nbsp;<a href="#sfields" name="check_all" class="cm-check-items-s cm-off underlined"><?php echo fn_get_lang_var('unselect_all', $this->getLanguage()); ?>
</a>
</p>
<?php  ob_end_flush();  ?>