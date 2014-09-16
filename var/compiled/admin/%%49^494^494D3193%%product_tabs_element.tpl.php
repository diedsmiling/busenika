<?php /* Smarty version 2.6.18, created on 2014-09-15 23:43:08
         compiled from views/products/components/product_tabs_element.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'views/products/components/product_tabs_element.tpl', 8, false),array('modifier', 'fn_get_lang_var', 'views/products/components/product_tabs_element.tpl', 23, false),array('function', 'script', 'views/products/components/product_tabs_element.tpl', 51, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('delete','delete','filling','static_block','enable_for_this_page','items_in_block','edit'));
?>

<?php if ($this->_tpl_vars['block_data'] && ! $this->_tpl_vars['block_data']['disabled']): ?>
<div class="cm-list-box<?php if ($this->_tpl_vars['block_data']['properties']['static_block']): ?> cm-tabs-block<?php endif; ?> base-block">
	<input type="hidden" name="block_positions[]" class="block-position" value="<?php echo $this->_tpl_vars['block_data']['block_id']; ?>
" />
	<?php echo ''; ?><?php if ($this->_tpl_vars['block_data']['location'] == 'products' && ! $this->_tpl_vars['block_data']['properties']['static_block']): ?><?php echo '<a class="float-right cm-confirm delete-block" href="'; ?><?php echo fn_url("block_manager.delete?selected_section=".($this->_tpl_vars['location'])."&amp;block_id=".($this->_tpl_vars['block_data']['block_id'])."&amp;redirect_url=".($this->_tpl_vars['redirect_url'])); ?><?php echo '"><img src="'; ?><?php echo $this->_tpl_vars['images_dir']; ?><?php echo '/icons/icon_delete.gif" width="12" height="18" border="0" title="'; ?><?php echo fn_get_lang_var('delete', $this->getLanguage()); ?><?php echo '" alt="'; ?><?php echo fn_get_lang_var('delete', $this->getLanguage()); ?><?php echo '" /></a>'; ?><?php endif; ?><?php echo '<h4'; ?><?php if ($this->_tpl_vars['block_data']['location'] != 'products'): ?><?php echo ' class="cm-fixed-block"'; ?><?php endif; ?><?php echo '><strong>'; ?><?php $this->assign('block_content_id', "block_content_".($this->_tpl_vars['block_data']['block_id']), false); ?><?php echo ''; ?><?php if ($this->_tpl_vars['block_data']['location'] == 'products' && ! $this->_tpl_vars['block_data']['properties']['static_block']): ?><?php echo '<img src="'; ?><?php echo $this->_tpl_vars['images_dir']; ?><?php echo '/icons/icon_show.gif" width="13" height="13" border="0" alt="" id="on_'; ?><?php echo $this->_tpl_vars['block_content_id']; ?><?php echo '" class="cm-combination cm-save-state'; ?><?php if ($_COOKIE[$this->_tpl_vars['block_content_id']]): ?><?php echo ' hidden'; ?><?php endif; ?><?php echo '" /><img src="'; ?><?php echo $this->_tpl_vars['images_dir']; ?><?php echo '/icons/icon_hide.gif" width="13" height="13" border="0" alt="" id="off_'; ?><?php echo $this->_tpl_vars['block_content_id']; ?><?php echo '" class="cm-combination cm-save-state'; ?><?php if (! $_COOKIE[$this->_tpl_vars['block_content_id']]): ?><?php echo ' hidden'; ?><?php endif; ?><?php echo '" />'; ?><?php endif; ?><?php echo ''; ?><?php echo $this->_tpl_vars['block_data']['description']; ?><?php echo '</strong></h4>'; ?>


	<div id=<?php echo $this->_tpl_vars['block_content_id']; ?>
 class="block-container clear<?php if (! $_COOKIE[$this->_tpl_vars['block_content_id']] || $this->_tpl_vars['block_data']['location'] != 'products'): ?> hidden<?php endif; ?>">
		<div class="block-content">
			<?php if ($this->_tpl_vars['block_data']['location'] == 'products' && ! $this->_tpl_vars['block_data']['properties']['static_block']): ?>
				<p><label><?php echo fn_get_lang_var('filling', $this->getLanguage()); ?>
:</label>
				<?php if ($this->_tpl_vars['block_data']['properties']['fillings']): ?><?php echo fn_get_lang_var($this->_tpl_vars['block_data']['properties']['fillings']); ?>
<?php else: ?><?php echo fn_get_lang_var('static_block', $this->getLanguage()); ?>
<?php endif; ?></p>
	
				<p><label for="enable_block_<?php echo $this->_tpl_vars['block_data']['block_id']; ?>
"><?php echo fn_get_lang_var('enable_for_this_page', $this->getLanguage()); ?>
:</label>
				<input id="enable_block_<?php echo $this->_tpl_vars['block_data']['block_id']; ?>
" type="checkbox" name="enable_block_<?php echo $this->_tpl_vars['block_data']['block_id']; ?>
" value="Y" <?php if ($this->_tpl_vars['block_data']['assigned'] == 'Y'): ?>checked="checked"<?php endif; ?> onclick="jQuery.ajaxRequest('<?php echo fn_url("block_manager.enable_disable?location=".($this->_tpl_vars['location'])."&amp;object_id=".($this->_tpl_vars['object_id'])."&amp;block_id=".($this->_tpl_vars['block_data']['block_id'])."&amp;enable=", 'A', 'rel', '&'); ?>
' + (this.checked ? this.value : 'N'), <?php echo '{method: \'POST\', cache: false}'; ?>
);" /></p>

				<?php if ($this->_tpl_vars['block_data']['properties']['fillings'] == 'manually' && $this->_tpl_vars['block_settings']['dynamic'][$this->_tpl_vars['block_data']['properties']['list_object']]['picker_props']['picker'] || $this->_tpl_vars['block_data']['properties']['per_object']): ?>
					<div class="info-line">
						<?php if (! $this->_tpl_vars['block_data']['properties']['per_object']): ?>
							<div class="float-right">
								<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['block_settings']['dynamic'][$this->_tpl_vars['block_data']['properties']['list_object']]['picker_props']['picker'], 'smarty_include_vars' => array('data_id' => ($this->_tpl_vars['block_data']['block_id']).($this->_tpl_vars['block_data']['block_type'])."_".($this->_tpl_vars['block_data']['location']),'checkbox_name' => 'block_items','extra_var' => "dispatch=block_manager.add_items&block_id=".($this->_tpl_vars['block_data']['block_id'])."&block_location=".($this->_tpl_vars['block_data']['location'])."&object_id=".($this->_tpl_vars['object_id'])."&redirect_location=".($this->_tpl_vars['location'])."&redirect_url=".($this->_tpl_vars['redirect_url']),'no_container' => true,'view_mode' => 'button','params_array' => $this->_tpl_vars['block_settings']['dynamic'][$this->_tpl_vars['block_data']['properties']['list_object']]['picker_props']['params'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
							</div>
							<?php echo fn_get_lang_var('items_in_block', $this->getLanguage()); ?>
:&nbsp;
							<?php $this->assign('info_line_button_text', "&nbsp;&nbsp;".($this->_tpl_vars['block_data']['items_count'])."&nbsp;", false); ?>
							<?php $this->assign('additional_url_params', "", false); ?>
						<?php else: ?>
							<?php $this->assign('info_line_button_text', fn_get_lang_var('edit', $this->getLanguage()), false); ?>
							<?php $this->assign('additional_url_params', "&product_id=".($this->_tpl_vars['object_id']), false); ?>
						<?php endif; ?>

						<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => $this->_tpl_vars['info_line_button_text'],'but_href' => "block_manager.manage_items?block_id=".($this->_tpl_vars['block_data']['block_id'])."&amp;location=".($this->_tpl_vars['location'])."&object_id=".($this->_tpl_vars['object_id'])."&redir_url=".($this->_tpl_vars['redirect_url']).($this->_tpl_vars['additional_url_params']),'but_role' => 'link','but_onclick' => "jQuery.ajaxRequest(this.href, ".($this->_tpl_vars['ldelim'])."callback: fn_show_block_picker, result_ids: 'content_edit_block_picker', caching: true".($this->_tpl_vars['rdelim']).")",'but_meta' => "text-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>

	<div class="block-bottom"><p class="no-margin">&nbsp;</p></div>
</div>
<?php endif; ?><?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>