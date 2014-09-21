<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:40
         compiled from pickers/categories_picker.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'pickers/categories_picker.tpl', 3, false),array('modifier', 'is_array', 'pickers/categories_picker.tpl', 15, false),array('modifier', 'explode', 'pickers/categories_picker.tpl', 16, false),array('modifier', 'implode', 'pickers/categories_picker.tpl', 35, false),array('modifier', 'defined', 'pickers/categories_picker.tpl', 63, false),array('modifier', 'escape', 'pickers/categories_picker.tpl', 76, false),array('modifier', 'fn_url', 'pickers/categories_picker.tpl', 102, false),array('function', 'math', 'pickers/categories_picker.tpl', 4, false),array('function', 'script', 'pickers/categories_picker.tpl', 9, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('position_short','name','no_items','add_categories','choose','choose','choose','choose','add_categories_and_close','add_categories','add_categories'));
?>

<?php $this->assign('data_id', smarty_modifier_default(@$this->_tpl_vars['data_id'], 'categories_list'), false); ?>
<?php echo smarty_function_math(array('equation' => "rand()",'assign' => 'rnd'), $this);?>

<?php $this->assign('data_id', ($this->_tpl_vars['data_id'])."_".($this->_tpl_vars['rnd']), false); ?>
<?php $this->assign('view_mode', smarty_modifier_default(@$this->_tpl_vars['view_mode'], 'mixed'), false); ?>
<?php $this->assign('start_pos', smarty_modifier_default(@$this->_tpl_vars['start_pos'], 0), false); ?>

<?php echo smarty_function_script(array('src' => "js/picker.js"), $this);?>


<?php if ($this->_tpl_vars['item_ids'] == ""): ?>
	<?php $this->assign('item_ids', null, false); ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['item_ids'] && $this->_tpl_vars['multiple'] && ! is_array($this->_tpl_vars['item_ids'])): ?>
	<?php $this->assign('item_ids', explode(",", $this->_tpl_vars['item_ids']), false); ?>
<?php endif; ?>

<?php if (! $this->_tpl_vars['extra_var'] && $this->_tpl_vars['view_mode'] != 'button'): ?>
	<?php if ($this->_tpl_vars['multiple']): ?>
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
		<tr>
			<?php if ($this->_tpl_vars['positions']): ?><th><?php echo fn_get_lang_var('position_short', $this->getLanguage()); ?>
</th><?php endif; ?>
			<th width="100%"><?php echo fn_get_lang_var('name', $this->getLanguage()); ?>
</th>
			<th>&nbsp;</th>
		</tr>
		<tbody id="<?php echo $this->_tpl_vars['data_id']; ?>
"<?php if (! $this->_tpl_vars['item_ids']): ?> class="hidden"<?php endif; ?>>
	<?php else: ?>
		<div id="<?php echo $this->_tpl_vars['data_id']; ?>
" class="<?php if ($this->_tpl_vars['multiple'] && ! $this->_tpl_vars['item_ids']): ?>hidden<?php elseif (! $this->_tpl_vars['multiple']): ?>cm-display-radio float-left<?php endif; ?>">
	<?php endif; ?>
	<?php if ($this->_tpl_vars['multiple']): ?>
		<tr class="hidden">
			<td colspan="<?php if ($this->_tpl_vars['positions']): ?>3<?php else: ?>2<?php endif; ?>">
	<?php endif; ?>
			<input id="<?php if ($this->_tpl_vars['input_id']): ?><?php echo $this->_tpl_vars['input_id']; ?>
<?php else: ?>c<?php echo $this->_tpl_vars['data_id']; ?>
_ids<?php endif; ?>" type="hidden" class="cm-picker-value" name="<?php echo $this->_tpl_vars['input_name']; ?>
" value="<?php if (is_array($this->_tpl_vars['item_ids'])): ?><?php echo implode(",", $this->_tpl_vars['item_ids']); ?>
<?php else: ?><?php echo $this->_tpl_vars['item_ids']; ?>
<?php endif; ?>" <?php echo $this->_tpl_vars['extra']; ?>
 />
	<?php if ($this->_tpl_vars['multiple']): ?>
			</td>
		</tr>
	<?php endif; ?>
		<?php if ($this->_tpl_vars['multiple']): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/js_category.tpl", 'smarty_include_vars' => array('category_id' => ($this->_tpl_vars['ldelim'])."category_id".($this->_tpl_vars['rdelim']),'holder' => $this->_tpl_vars['data_id'],'input_name' => $this->_tpl_vars['input_name'],'clone' => true,'hide_link' => $this->_tpl_vars['hide_link'],'hide_delete_button' => $this->_tpl_vars['hide_delete_button'],'position_field' => $this->_tpl_vars['positions'],'position' => '0')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>
		<?php $_from = $this->_tpl_vars['item_ids']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['items'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['items']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['c_id']):
        $this->_foreach['items']['iteration']++;
?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/js_category.tpl", 'smarty_include_vars' => array('category_id' => $this->_tpl_vars['c_id'],'holder' => $this->_tpl_vars['data_id'],'input_name' => $this->_tpl_vars['input_name'],'hide_link' => $this->_tpl_vars['hide_link'],'hide_delete_button' => $this->_tpl_vars['hide_delete_button'],'first_item' => ($this->_foreach['items']['iteration'] <= 1),'position_field' => $this->_tpl_vars['positions'],'position' => $this->_foreach['items']['iteration']+$this->_tpl_vars['start_pos'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endforeach; else: ?>
			<?php if (! $this->_tpl_vars['multiple']): ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/js_category.tpl", 'smarty_include_vars' => array('category_id' => "",'holder' => $this->_tpl_vars['data_id'],'input_name' => $this->_tpl_vars['input_name'],'hide_link' => $this->_tpl_vars['hide_link'],'hide_delete_button' => $this->_tpl_vars['hide_delete_button'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?>
		<?php endif; unset($_from); ?>
	<?php if ($this->_tpl_vars['multiple']): ?>
		</tbody>
		<tbody id="<?php echo $this->_tpl_vars['data_id']; ?>
_no_item"<?php if ($this->_tpl_vars['item_ids']): ?> class="hidden"<?php endif; ?>>
		<tr class="no-items">
			<td colspan="<?php if ($this->_tpl_vars['positions']): ?>3<?php else: ?>2<?php endif; ?>"><p><?php echo smarty_modifier_default(@$this->_tpl_vars['no_item_text'], fn_get_lang_var('no_items', $this->getLanguage())); ?>
</p></td>
		</tr>
		</tbody>
	</table>
	<?php else: ?></div><?php endif; ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['view_mode'] != 'list'): ?>

	<?php if (! defined('COMPANY_ID') || @CONTROLLER != 'companies'): ?>
	<?php if (! $this->_tpl_vars['no_container']): ?><div class="<?php if (! $this->_tpl_vars['multiple']): ?>choose-icon<?php else: ?>buttons-container<?php endif; ?>"><?php endif; ?>
		<?php if ($this->_tpl_vars['multiple']): ?>
			<?php $this->assign('but_text', fn_get_lang_var('add_categories', $this->getLanguage()), false); ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_id' => "opener_picker_".($this->_tpl_vars['data_id']),'but_text' => $this->_tpl_vars['but_text'],'but_onclick' => "jQuery.show_picker('picker_".($this->_tpl_vars['data_id'])."', this.id);",'but_role' => 'add','but_meta' => "text-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php else: ?>
			<?php $this->assign('but_text', fn_get_lang_var('choose', $this->getLanguage()), false); ?>
			<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/icon_choose_object.gif" width="19" height="18" border="0" class="hand" id="opener_picker_<?php echo $this->_tpl_vars['data_id']; ?>
" onclick="jQuery.show_picker('picker_<?php echo $this->_tpl_vars['data_id']; ?>
', this.id); return false;" alt="<?php echo fn_get_lang_var('choose', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('choose', $this->getLanguage()); ?>
" />
		<?php endif; ?>
	<?php if (! $this->_tpl_vars['no_container']): ?></div><?php endif; ?>
	<?php endif; ?>

	<?php ob_start(); ?>
		<?php ob_start(); ?><?php echo $this->_tpl_vars['index_script']; ?>
?dispatch=categories.picker<?php if (! $this->_tpl_vars['multiple']): ?>&amp;display=radio<?php endif; ?><?php if ($this->_tpl_vars['extra_var']): ?>&amp;extra=<?php echo smarty_modifier_escape($this->_tpl_vars['extra_var'], 'url'); ?>
<?php endif; ?><?php if ($this->_tpl_vars['default_name']): ?>&amp;root=<?php echo $this->_tpl_vars['default_name']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['checkbox_name']): ?>&amp;checkbox_name=<?php echo $this->_tpl_vars['checkbox_name']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['except_id']): ?>&amp;except_id=<?php echo $this->_tpl_vars['except_id']; ?>
<?php endif; ?><?php $this->_smarty_vars['capture']['iframe_url'] = ob_get_contents(); ob_end_clean(); ?>
		<div class="cm-picker-data-container" id="iframe_container_<?php echo $this->_tpl_vars['data_id']; ?>
"></div>
		<div class="buttons-container">
			<?php $this->assign('extra_buttons', "extra_buttons_".($this->_tpl_vars['rnd']), false); ?>
			<?php if (! $this->_tpl_vars['multiple']): ?>
				<?php $this->assign('_but_text', fn_get_lang_var('choose', $this->getLanguage()), false); ?>
				<?php $this->assign('_act', "#add_item", false); ?>
			<?php else: ?>
				<?php if (! $this->_tpl_vars['extra_var']): ?>
					<?php $this->assign('_but_text', fn_get_lang_var('add_categories_and_close', $this->getLanguage()), false); ?>
					<?php $this->assign('_act', "#add_item_close", false); ?>
					<?php ob_start(); ?>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_type' => 'button','but_onclick' => "jQuery.submit_picker('#iframe_".($this->_tpl_vars['data_id'])."', '#add_item')",'but_text' => fn_get_lang_var('add_categories', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					<?php $this->_smarty_vars['capture'][$this->_tpl_vars['extra_buttons']] = ob_get_contents(); ob_end_clean(); ?>
				<?php else: ?>
					<?php $this->assign('_but_text', fn_get_lang_var('add_categories', $this->getLanguage()), false); ?>
					<?php $this->assign('_act', "#add_item", false); ?>
				<?php endif; ?>
			<?php endif; ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_type' => 'button','but_onclick' => "jQuery.submit_picker('#iframe_".($this->_tpl_vars['data_id'])."', '".($this->_tpl_vars['_act'])."')",'but_text' => $this->_tpl_vars['_but_text'],'extra' => $this->_smarty_vars['capture'][$this->_tpl_vars['extra_buttons']],'cancel_action' => 'close')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
	<?php $this->_smarty_vars['capture']['picker_content'] = ob_get_contents(); ob_end_clean(); ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/picker_skin.tpl", 'smarty_include_vars' => array('picker_content' => $this->_smarty_vars['capture']['picker_content'],'data_id' => $this->_tpl_vars['data_id'],'but_text' => $this->_tpl_vars['but_text'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<script type="text/javascript">
	//<![CDATA[
		iframe_urls['<?php echo $this->_tpl_vars['data_id']; ?>
'] = '<?php echo smarty_modifier_escape(fn_url($this->_smarty_vars['capture']['iframe_url']), 'javascript'); ?>
';
		<?php if ($this->_tpl_vars['extra_var']): ?>
		iframe_extra['<?php echo $this->_tpl_vars['data_id']; ?>
'] = '<?php echo smarty_modifier_escape($this->_tpl_vars['extra_var'], 'javascript'); ?>
';
		<?php endif; ?>
	//]]>
	</script>
<?php endif; ?>