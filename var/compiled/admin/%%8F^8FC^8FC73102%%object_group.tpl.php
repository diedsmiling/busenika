<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:16
         compiled from common_templates/object_group.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'common_templates/object_group.tpl', 11, false),array('modifier', 'default', 'common_templates/object_group.tpl', 24, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('delete','delete','edit'));
?>

<?php if (! $this->_tpl_vars['no_table']): ?>
<div class="object-group<?php echo $this->_tpl_vars['element']; ?>
 clear cm-row-item <?php echo $this->_tpl_vars['additional_class']; ?>
">
	<div class="float-right delete">
		<?php ob_start(); ?>
			<?php if ($this->_tpl_vars['tool_items']): ?>
			<?php echo $this->_tpl_vars['tool_items']; ?>

			<?php endif; ?>
			<?php if ($this->_tpl_vars['href_delete'] && ! $this->_tpl_vars['skip_delete']): ?>
			<li><a href="<?php echo fn_url($this->_tpl_vars['href_delete']); ?>
" rev="<?php echo $this->_tpl_vars['rev_delete']; ?>
" class="cm-ajax cm-delete-row cm-confirm lowercase"><?php echo fn_get_lang_var('delete', $this->getLanguage()); ?>
</a></li>
			<?php elseif ($this->_tpl_vars['links']): ?>
			<li><?php echo $this->_tpl_vars['links']; ?>
</li>
			<?php else: ?>
			<li class="undeleted-element"><span><?php echo fn_get_lang_var('delete', $this->getLanguage()); ?>
</span></li>
			<?php endif; ?>
		<?php $this->_smarty_vars['capture']['tool_items'] = ob_get_contents(); ob_end_clean(); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/table_tools_list.tpl", 'smarty_include_vars' => array('separate' => true,'tools_list' => $this->_smarty_vars['capture']['tool_items'],'prefix' => $this->_tpl_vars['id'],'href' => "")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
	<div class="float-right">
<?php endif; ?>

	<?php if (! $this->_tpl_vars['non_editable']): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => "group".($this->_tpl_vars['id_prefix']).($this->_tpl_vars['id']),'edit_onclick' => $this->_tpl_vars['onclick'],'text' => $this->_tpl_vars['header_text'],'act' => smarty_modifier_default(@$this->_tpl_vars['act'], 'edit'),'picker_meta' => $this->_tpl_vars['picker_meta'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php else: ?>	
		<span class="unedited-element block"><?php echo smarty_modifier_default(@$this->_tpl_vars['link_text'], fn_get_lang_var('edit', $this->getLanguage())); ?>
</span>
	<?php endif; ?>

<?php if (! $this->_tpl_vars['no_table']): ?>
	</div>
	<?php if ($this->_tpl_vars['status']): ?>
	<div class="float-right">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_popup.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['id'],'status' => $this->_tpl_vars['status'],'hidden' => $this->_tpl_vars['hidden'],'object_id_name' => $this->_tpl_vars['object_id_name'],'table' => $this->_tpl_vars['table'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
	<?php endif; ?>
	<div class="object-name">
		<?php if ($this->_tpl_vars['checkbox_name']): ?>
			<input type="checkbox" name="<?php echo $this->_tpl_vars['checkbox_name']; ?>
" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['checkbox_value'], @$this->_tpl_vars['id']); ?>
"<?php if ($this->_tpl_vars['checked']): ?> checked="checked"<?php endif; ?> class="checkbox cm-item" />
		<?php endif; ?>
		<a class="cm-external-click<?php if ($this->_tpl_vars['non_editable']): ?> no-underline<?php endif; ?><?php if ($this->_tpl_vars['main_link']): ?> link<?php endif; ?>"<?php if (! $this->_tpl_vars['non_editable'] && ! $this->_tpl_vars['no_rev']): ?> rev="opener_group<?php echo $this->_tpl_vars['id_prefix']; ?>
<?php echo $this->_tpl_vars['id']; ?>
"<?php endif; ?><?php if ($this->_tpl_vars['main_link']): ?> href="<?php echo fn_url($this->_tpl_vars['main_link']); ?>
"<?php endif; ?>><?php echo $this->_tpl_vars['text']; ?>
</a><span class="object-group-details"><?php echo $this->_tpl_vars['details']; ?>
</span>
	</div>
</div>
<?php endif; ?>