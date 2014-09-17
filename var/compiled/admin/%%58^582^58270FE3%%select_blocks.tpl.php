<?php /* Smarty version 2.6.18, created on 2014-09-16 23:39:25
         compiled from views/block_manager/components/select_blocks.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'views/block_manager/components/select_blocks.tpl', 9, false),array('modifier', 'unescape', 'views/block_manager/components/select_blocks.tpl', 26, false),array('modifier', 'fn_get_lang_var', 'views/block_manager/components/select_blocks.tpl', 60, false),array('function', 'script', 'views/block_manager/components/select_blocks.tpl', 84, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('html_block','listed_items','general','block_name','filling','static_block','enable_for_this_page','disabled','no_blocks_defined','manage_custom_blocks'));
?>

<?php if ($this->_tpl_vars['blocks']): ?>
	<div class="clear">
		<div id="content_block_manager_blocks" class="listmania-lists">
			<?php $_from = $this->_tpl_vars['blocks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['block_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['block_list']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['block']):
        $this->_foreach['block_list']['iteration']++;
?>
				&nbsp;<span class="bull">&bull;</span>&nbsp;<?php if ($this->_tpl_vars['selected_block']['block_id'] == $this->_tpl_vars['block']['block_id']): ?><span class="strong"><?php else: ?>
				<?php ob_start(); ?><?php echo $this->_tpl_vars['index_script']; ?>
?dispatch=<?php echo @CONTROLLER; ?>
.<?php echo @MODE; ?>
<?php if ($this->_tpl_vars['location']): ?>&amp;page_section=<?php echo $this->_tpl_vars['location']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['selected_block']['object_id'] && $this->_tpl_vars['object_id']): ?>&amp;<?php echo $this->_tpl_vars['selected_block']['object_id']; ?>
=<?php echo $this->_tpl_vars['object_id']; ?>
<?php endif; ?>&amp;selected_section=<?php if ($this->_tpl_vars['location']): ?><?php echo $this->_tpl_vars['location']; ?>
_<?php endif; ?>blocks&amp;selected_block_id=<?php echo $this->_tpl_vars['block']['block_id']; ?>
<?php $this->_smarty_vars['capture']['_href'] = ob_get_contents(); ob_end_clean(); ?>
				<a href="<?php echo fn_url($this->_smarty_vars['capture']['_href']); ?>
"><?php endif; ?><?php echo $this->_tpl_vars['block']['description']; ?>
<?php if ($this->_tpl_vars['selected_block']['block_id'] == $this->_tpl_vars['block']['block_id']): ?></span><?php else: ?></a><?php endif; ?><?php if ($this->_tpl_vars['lm_list']['use'] == 'Y'): ?>&nbsp;(+)<?php else: ?><?php endif; ?>&nbsp;&nbsp;&nbsp;&nbsp;
			<?php endforeach; endif; unset($_from); ?>
		</div>
	</div>

<?php if ($this->_tpl_vars['selected_block']['properties']['fillings'] == 'manually'): ?>
	<?php $this->assign('_view_mode', 'mixed', false); ?>
	<?php $this->assign('_hide_delete_button', false, false); ?>
<?php else: ?>
	<?php $this->assign('_view_mode', 'list', false); ?>
	<?php $this->assign('_hide_delete_button', true, false); ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['selected_block']['item_ids']['block_text'] || $this->_tpl_vars['selected_block']['properties']['per_object']): ?>
	<div class="listed-items">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('html_block', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
	<?php if (! $this->_tpl_vars['selected_block']['properties']['per_object']): ?>
		<?php echo smarty_modifier_unescape($this->_tpl_vars['selected_block']['item_ids']['block_text']); ?>

	<?php else: ?>
		<fieldset>
			<input type="hidden" name="<?php echo $this->_tpl_vars['data_name']; ?>
[block_id]" value="<?php echo $this->_tpl_vars['selected_block']['block_id']; ?>
" />
			<textarea id="block_text" name="<?php echo $this->_tpl_vars['data_name']; ?>
[add_items][block_data][block_text]" cols="65" rows="8" class="input-textarea"><?php echo $this->_tpl_vars['selected_block']['item_ids']['block_text']; ?>
</textarea>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/wysiwyg.tpl", 'smarty_include_vars' => array('id' => 'block_text')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<fieldset>
	<?php endif; ?>
	</div>
<?php endif; ?>
	<div class="clear">
		<?php if ($this->_tpl_vars['selected_block']['properties']['fillings'] && $this->_tpl_vars['block_properties'][$this->_tpl_vars['selected_block']['properties']['list_object']]['picker_props']['picker']): ?>
		<div class="listed-items">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('listed_items', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<input type="hidden" name="<?php echo $this->_tpl_vars['data_name']; ?>
[block_id]" value="<?php echo $this->_tpl_vars['selected_block']['block_id']; ?>
" />
			<?php if ($this->_tpl_vars['selected_block']['properties']['fillings'] == 'manually'): ?>
				<?php $this->assign('show_position', true, false); ?>
			<?php else: ?>
				<?php $this->assign('show_position', false, false); ?>
			<?php endif; ?>

			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['block_properties'][$this->_tpl_vars['selected_block']['properties']['list_object']]['picker_props']['picker'], 'smarty_include_vars' => array('data_id' => "added_".($this->_tpl_vars['selected_block']['block_id']),'input_name' => ($this->_tpl_vars['data_name'])."[add_items]",'item_ids' => $this->_tpl_vars['selected_block']['item_ids'],'positions' => $this->_tpl_vars['show_position'],'view_mode' => $this->_tpl_vars['_view_mode'],'hide_delete_button' => $this->_tpl_vars['_hide_delete_button'],'params_array' => $this->_tpl_vars['block_properties'][$this->_tpl_vars['selected_block']['properties']['list_object']]['picker_props']['params'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
		<?php endif; ?>

		<div class="general-items">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('general', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<div class="form-field">
				<label><?php echo fn_get_lang_var('block_name', $this->getLanguage()); ?>
:</label>
				<a href="<?php echo fn_url("block_manager.manage?selected_section=".($this->_tpl_vars['section'])); ?>
"><?php echo $this->_tpl_vars['selected_block']['description']; ?>
</a>
			</div>

			<div class="form-field">
				<label><?php echo fn_get_lang_var('filling', $this->getLanguage()); ?>
:</label>
				<?php if ($this->_tpl_vars['selected_block']['properties']['fillings']): ?><?php echo fn_get_lang_var($this->_tpl_vars['selected_block']['properties']['fillings']); ?>
<?php else: ?><?php echo fn_get_lang_var('static_block', $this->getLanguage()); ?>
<?php endif; ?>
			</div>

			<div class="form-field">
				<label for="enable_block_<?php echo $this->_tpl_vars['selected_block']['block_id']; ?>
"><?php echo fn_get_lang_var('enable_for_this_page', $this->getLanguage()); ?>
:</label>
				<?php if ($this->_tpl_vars['object_id']): ?>
					<?php $this->assign('location_url', "&amp;location=".(@CONTROLLER)."&amp;object_id=".($this->_tpl_vars['object_id']), false); ?>
				<?php elseif ($this->_tpl_vars['location']): ?>
					<?php $this->assign('location_url', "&amp;location=".($this->_tpl_vars['location']), false); ?>
				<?php endif; ?>
				<input id="enable_block_<?php echo $this->_tpl_vars['selected_block']['block_id']; ?>
" type="checkbox" name="enable_block_<?php echo $this->_tpl_vars['selected_block']['block_id']; ?>
" value="Y" <?php if ($this->_tpl_vars['selected_block']['assigned'] == 'Y'): ?>checked="checked"<?php endif; ?> onclick="jQuery.ajaxRequest('<?php echo fn_url("block_manager.enable_disable?block_id=".($this->_tpl_vars['selected_block']['block_id']).($this->_tpl_vars['location_url'])."&amp;enable=", 'A', 'rel', '&'); ?>
' + (this.checked ? this.value : 'N'), <?php echo '{method: \'POST\', cache: false}'; ?>
);" />
			</div>
			<?php if ($this->_tpl_vars['selected_block']['disabled']): ?>
				<div class="form-field">
					<label><?php echo fn_get_lang_var('disabled', $this->getLanguage()); ?>
:</label>
					<?php echo $this->_tpl_vars['selected_block']['disabled']; ?>

				</div>
			<?php endif; ?>
		</div>
	</div>

<?php else: ?>
	<p class="no-items"><?php echo fn_get_lang_var('no_blocks_defined', $this->getLanguage()); ?>
 <a href="<?php echo fn_url("block_manager.manage?selected_section=".($this->_tpl_vars['section'])); ?>
"><?php echo fn_get_lang_var('manage_custom_blocks', $this->getLanguage()); ?>
 &raquo;</a></p>
<?php endif; ?>
<?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>