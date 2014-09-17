<?php /* Smarty version 2.6.18, created on 2014-09-16 23:39:25
         compiled from addons/banners/pickers/banners_picker.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'math', 'addons/banners/pickers/banners_picker.tpl', 3, false),array('function', 'script', 'addons/banners/pickers/banners_picker.tpl', 7, false),array('modifier', 'default', 'addons/banners/pickers/banners_picker.tpl', 5, false),array('modifier', 'implode', 'addons/banners/pickers/banners_picker.tpl', 11, false),array('modifier', 'escape', 'addons/banners/pickers/banners_picker.tpl', 43, false),array('modifier', 'fn_url', 'addons/banners/pickers/banners_picker.tpl', 63, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('position_short','name','no_items','add','add_banners_and_close','add_banners','add_banners','add_banners'));
?>

<?php echo smarty_function_math(array('equation' => "rand()",'assign' => 'rnd'), $this);?>

<?php $this->assign('data_id', ($this->_tpl_vars['data_id'])."_".($this->_tpl_vars['rnd']), false); ?>
<?php $this->assign('view_mode', smarty_modifier_default(@$this->_tpl_vars['view_mode'], 'mixed'), false); ?>

<?php echo smarty_function_script(array('src' => "js/picker.js"), $this);?>


<?php if ($this->_tpl_vars['view_mode'] != 'button'): ?>
	<?php if (! $this->_tpl_vars['positions']): ?>
	<input id="b<?php echo $this->_tpl_vars['data_id']; ?>
_ids" type="hidden" name="<?php echo $this->_tpl_vars['input_name']; ?>
" value="<?php if ($this->_tpl_vars['item_ids']): ?><?php echo implode(",", $this->_tpl_vars['item_ids']); ?>
<?php endif; ?>" />
	<?php endif; ?>
	
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
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/banners/pickers/js_banner.tpl", 'smarty_include_vars' => array('banner_id' => ($this->_tpl_vars['ldelim'])."banner_id".($this->_tpl_vars['rdelim']),'holder' => $this->_tpl_vars['data_id'],'input_name' => $this->_tpl_vars['input_name'],'clone' => true,'hide_link' => $this->_tpl_vars['hide_link'],'hide_delete_button' => $this->_tpl_vars['hide_delete_button'],'position_field' => $this->_tpl_vars['positions'],'position' => '0')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php if ($this->_tpl_vars['item_ids']): ?>
	<?php $_from = $this->_tpl_vars['item_ids']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['items'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['items']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['p_id']):
        $this->_foreach['items']['iteration']++;
?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/banners/pickers/js_banner.tpl", 'smarty_include_vars' => array('banner_id' => $this->_tpl_vars['p_id'],'holder' => $this->_tpl_vars['data_id'],'input_name' => $this->_tpl_vars['input_name'],'hide_link' => $this->_tpl_vars['hide_link'],'hide_delete_button' => $this->_tpl_vars['hide_delete_button'],'first_item' => ($this->_foreach['items']['iteration'] <= 1),'position_field' => $this->_tpl_vars['positions'],'position' => $this->_foreach['items']['iteration'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endforeach; endif; unset($_from); ?>
	<?php endif; ?>
	</tbody>
	<tbody id="<?php echo $this->_tpl_vars['data_id']; ?>
_no_item"<?php if ($this->_tpl_vars['item_ids']): ?> class="hidden"<?php endif; ?>>
	<tr class="no-items">
		<td colspan="<?php if ($this->_tpl_vars['positions']): ?>3<?php else: ?>2<?php endif; ?>"><p><?php echo smarty_modifier_default(@$this->_tpl_vars['no_item_text'], fn_get_lang_var('no_items', $this->getLanguage())); ?>
</p></td>
	</tr>
	</tbody>
	</table>
<?php endif; ?>

<?php if ($this->_tpl_vars['view_mode'] != 'list'): ?>

	<?php if (! $this->_tpl_vars['no_container']): ?><div class="buttons-container"><?php endif; ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_id' => "opener_picker_".($this->_tpl_vars['data_id']),'but_text' => fn_get_lang_var('add', $this->getLanguage()),'but_onclick' => "jQuery.show_picker('picker_".($this->_tpl_vars['data_id'])."', this.id);",'but_role' => 'add','but_meta' => "text-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php if (! $this->_tpl_vars['no_container']): ?></div><?php endif; ?>

	<?php ob_start(); ?>
		<?php ob_start(); ?><?php echo $this->_tpl_vars['index_script']; ?>
?dispatch=banners.picker<?php if ($this->_tpl_vars['extra_var']): ?>&amp;extra=<?php echo smarty_modifier_escape($this->_tpl_vars['extra_var'], 'url'); ?>
<?php endif; ?><?php if ($this->_tpl_vars['checkbox_name']): ?>&amp;checkbox_name=<?php echo $this->_tpl_vars['checkbox_name']; ?>
<?php endif; ?><?php $this->_smarty_vars['capture']['iframe_url'] = ob_get_contents(); ob_end_clean(); ?>
		<div class="cm-picker-data-container" id="iframe_container_<?php echo $this->_tpl_vars['data_id']; ?>
"></div>
		<div class="buttons-container">
			<?php $this->assign('extra_buttons', "extra_buttons_".($this->_tpl_vars['rnd']), false); ?>
			<?php if (! $this->_tpl_vars['extra_var']): ?>
				<?php $this->assign('_but_text', fn_get_lang_var('add_banners_and_close', $this->getLanguage()), false); ?>
				<?php $this->assign('_act', "#add_item_close", false); ?>
				<?php ob_start(); ?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_type' => 'button','but_onclick' => "jQuery.submit_picker('#iframe_".($this->_tpl_vars['data_id'])."', '#add_item')",'but_text' => fn_get_lang_var('add_banners', $this->getLanguage()),'breadcrumbs' => "",'hide_second_button' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php $this->_smarty_vars['capture'][$this->_tpl_vars['extra_buttons']] = ob_get_contents(); ob_end_clean(); ?>
			<?php else: ?>
				<?php $this->assign('_but_text', fn_get_lang_var('add_banners', $this->getLanguage()), false); ?>
				<?php $this->assign('_act', "#add_item", false); ?>
			<?php endif; ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_type' => 'button','but_onclick' => "jQuery.submit_picker('#iframe_".($this->_tpl_vars['data_id'])."', '".($this->_tpl_vars['_act'])."');",'but_text' => $this->_tpl_vars['_but_text'],'cancel_action' => 'close','extra' => $this->_smarty_vars['capture'][$this->_tpl_vars['extra_buttons']],'hide_second_button' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
	<?php $this->_smarty_vars['capture']['picker_content'] = ob_get_contents(); ob_end_clean(); ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/picker_skin.tpl", 'smarty_include_vars' => array('picker_content' => $this->_smarty_vars['capture']['picker_content'],'data_id' => $this->_tpl_vars['data_id'],'but_text' => fn_get_lang_var('add_banners', $this->getLanguage()))));
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