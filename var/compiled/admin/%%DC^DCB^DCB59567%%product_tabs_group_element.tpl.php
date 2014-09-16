<?php /* Smarty version 2.6.18, created on 2014-09-15 23:43:08
         compiled from views/products/components/product_tabs_group_element.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'views/products/components/product_tabs_group_element.tpl', 70, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('wrapper','editing_block','no_blocks'));
?>
<?php $_from = $this->_tpl_vars['blocks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['block_data']):
?>
<?php if (( $this->_tpl_vars['block_data']['text_id'] == $this->_tpl_vars['blocks_target'] || $this->_tpl_vars['block_data']['block_id'] == $this->_tpl_vars['blocks_target'] ) && $this->_tpl_vars['block_data']['block_type'] == 'G'): ?>
	<?php if ($this->_tpl_vars['block_data']['text_id']): ?>
	<div id="<?php echo $this->_tpl_vars['blocks_target']; ?>
_column_holder"<?php if ($this->_tpl_vars['main_class']): ?> class="<?php echo $this->_tpl_vars['main_class']; ?>
"<?php endif; ?>>
		<div class="block-manager">
			<?php if ($this->_tpl_vars['block_data']['text_id'] == 'product_details'): ?>
				<input type="hidden" name="block_positions[]" class="block-position" value="<?php echo $this->_tpl_vars['block_data']['block_id']; ?>
" />
				<?php $this->assign('def_id', "block_content_".($this->_tpl_vars['block_data']['block_id']), false); ?>
			<?php else: ?>
				<?php $this->assign('def_id', $this->_tpl_vars['block_data']['text_id'], false); ?>
			<?php endif; ?>
			<h2><?php echo $this->_tpl_vars['block_data']['description']; ?>
</h2>
			<input type="hidden" name="group_id_<?php echo $this->_tpl_vars['def_id']; ?>
" value="<?php echo $this->_tpl_vars['block_data']['block_id']; ?>
" />
			<div id="<?php echo $this->_tpl_vars['def_id']; ?>
" class="cm-sortable-items grab-items<?php if ($this->_tpl_vars['block_data']['text_id'] == 'product_details'): ?> cm-disallowed-group cm-product-details<?php endif; ?>">
	<?php else: ?>
	<div id="blocks_group_<?php echo $this->_tpl_vars['blocks_target']; ?>
" class="cm-list-box cm-group-box block-container base-block">
		<div class="block-manager">
			<input type="hidden" name="block_positions[]" class="block-position" value="<?php echo $this->_tpl_vars['block_data']['block_id']; ?>
" />
			<h4 class="group-header">
				<strong>
				<?php $this->assign('block_content_id', "block_content_".($this->_tpl_vars['block_data']['block_id']), false); ?>
				<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/icon_show.gif" width="13" height="13" border="0" alt="" id="on_<?php echo $this->_tpl_vars['block_content_id']; ?>
" class="cm-combination cm-save-state<?php if ($_COOKIE[$this->_tpl_vars['block_content_id']]): ?> hidden<?php endif; ?>" /><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/icon_hide.gif" width="13" height="13" border="0" alt="" id="off_<?php echo $this->_tpl_vars['block_content_id']; ?>
" class="cm-combination cm-save-state<?php if (! $_COOKIE[$this->_tpl_vars['block_content_id']]): ?> hidden<?php endif; ?>" />
				<?php echo $this->_tpl_vars['block_data']['description']; ?>

			</strong></h4>
			<input type="hidden" name="group_id_<?php echo $this->_tpl_vars['block_content_id']; ?>
" value="<?php echo $this->_tpl_vars['block_data']['block_id']; ?>
" />
			<div id="<?php echo $this->_tpl_vars['block_content_id']; ?>
" class="<?php if (! $_COOKIE[$this->_tpl_vars['block_content_id']]): ?>hidden<?php endif; ?> cm-sortable-items grab-items group-content cm-disallowed-group">
	<?php endif; ?>
			<div class="cm-list-box list-box-invisible"></div>
			<?php $this->assign('_not_empty', false, false); ?>
			<?php $_from = $this->_tpl_vars['blocks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['inner_block']):
?>
				<?php if ($this->_tpl_vars['inner_block']['group_id'] == $this->_tpl_vars['block_data']['block_id']): ?>
					<?php if ($this->_tpl_vars['inner_block']['block_type'] == 'B'): ?>
						<?php if ($this->_tpl_vars['inner_block']['text_id'] == 'central_content'): ?>
							<div class="cm-list-box central-content">
								<h3><?php echo $this->_tpl_vars['inner_block']['description']; ?>
</h3>
								<input type="hidden" name="block_positions[]" class="block-position" value="<?php echo $this->_tpl_vars['inner_block']['block_id']; ?>
" />
								<div class="block-content clear">
								<?php if ($this->_tpl_vars['inner_block']['properties']['wrapper']): ?>
									<p><label><?php echo fn_get_lang_var('wrapper', $this->getLanguage()); ?>
:</label>
									<?php echo $this->_tpl_vars['inner_block']['properties']['wrapper']; ?>
</p>
								<?php endif; ?>

								<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/object_group.tpl", 'smarty_include_vars' => array('content' => $this->_smarty_vars['capture']['update_block'],'id' => ($this->_tpl_vars['inner_block']['block_id']).($this->_tpl_vars['inner_block']['block_type'])."_".($this->_tpl_vars['location']),'no_table' => true,'but_name' => "dispatch[block_manager.update]",'href' => "block_manager.update?block_id=".($this->_tpl_vars['inner_block']['block_id'])."&amp;location=".($this->_tpl_vars['location'])."&amp;object_id=".($this->_tpl_vars['object_id'])."&amp;r_url=".($this->_tpl_vars['redirect_url']),'header_text' => (fn_get_lang_var('editing_block', $this->getLanguage())).": ".($this->_tpl_vars['inner_block']['description']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
								</div>
							</div>
							<?php $_from = $this->_tpl_vars['blocks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['_block']):
?>
								<?php if ($this->_tpl_vars['_block']['text_id'] == 'product_details'): ?>
									<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/product_tabs_group_element.tpl", 'smarty_include_vars' => array('blocks_target' => $this->_tpl_vars['_block']['block_id'],'main_class' => "product-tabs")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
								<?php endif; ?>
							<?php endforeach; endif; unset($_from); ?>
						<?php else: ?>
							<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/product_tabs_element.tpl", 'smarty_include_vars' => array('block_data' => $this->_tpl_vars['inner_block'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<?php endif; ?>
					<?php elseif ($this->_tpl_vars['inner_block']['text_id'] != 'product_details'): ?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/product_tabs_group_element.tpl", 'smarty_include_vars' => array('blocks_target' => $this->_tpl_vars['inner_block']['block_id'],'main_class' => "")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					<?php endif; ?>
					<?php if ($this->_tpl_vars['inner_block']['block_type'] == 'B' && ! $this->_tpl_vars['inner_block']['disabled'] || $this->_tpl_vars['inner_block']['block_type'] == 'G'): ?>
						<?php $this->assign('_not_empty', true, false); ?>
					<?php endif; ?>
				<?php endif; ?>
			<?php endforeach; endif; unset($_from); ?>

			<p class="no-items<?php if ($this->_tpl_vars['_not_empty']): ?> hidden<?php endif; ?>"><?php echo fn_get_lang_var('no_blocks', $this->getLanguage()); ?>
</p>
			<div class="cm-list-box list-box-invisible"></div>
		</div>
	</div>
</div>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?><?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>