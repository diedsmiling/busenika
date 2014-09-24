<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:12
         compiled from common_templates/popupbox.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_check_view_permissions', 'common_templates/popupbox.tpl', 3, false),array('modifier', 'default', 'common_templates/popupbox.tpl', 6, false),array('modifier', 'fn_url', 'common_templates/popupbox.tpl', 6, false),array('modifier', 'unescape', 'common_templates/popupbox.tpl', 6, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('edit','add','add','close'));
?>

<?php if (fn_check_view_permissions($this->_tpl_vars['content'])): ?>

<?php if ($this->_tpl_vars['act'] == 'edit'): ?>
	<a onclick="<?php echo $this->_tpl_vars['edit_onclick']; ?>
 jQuery.show_picker('<?php echo $this->_tpl_vars['id']; ?>
', '', '.object-container'); return false;" class="<?php if (! $this->_tpl_vars['link_text']): ?>text-button-edit<?php endif; ?><?php if ($this->_tpl_vars['href']): ?> <?php echo smarty_modifier_default(@$this->_tpl_vars['opener_ajax_class'], "cm-ajax-update"); ?>
<?php endif; ?><?php if ($this->_tpl_vars['link_class']): ?> <?php echo $this->_tpl_vars['link_class']; ?>
<?php endif; ?>"<?php if ($this->_tpl_vars['href']): ?> href="<?php echo fn_url($this->_tpl_vars['href']); ?>
"<?php endif; ?> id="opener_<?php echo $this->_tpl_vars['id']; ?>
" rev="content_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo smarty_modifier_unescape(smarty_modifier_default(@$this->_tpl_vars['link_text'], fn_get_lang_var('edit', $this->getLanguage()))); ?>
</a>
<?php elseif ($this->_tpl_vars['act'] == 'select_fields'): ?>
	<span class="submit-button"><input id="opener_<?php echo $this->_tpl_vars['id']; ?>
" type="button" onclick="<?php echo $this->_tpl_vars['edit_onclick']; ?>
 jQuery.show_picker('<?php echo $this->_tpl_vars['id']; ?>
', '', '.object-container')" value="<?php echo $this->_tpl_vars['text']; ?>
" /></span>
<?php elseif ($this->_tpl_vars['act'] == 'create'): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_onclick' => ($this->_tpl_vars['edit_onclick'])." jQuery.show_picker('".($this->_tpl_vars['id'])."', '', '.object-container')",'but_text' => $this->_tpl_vars['but_text'],'but_role' => 'add','but_meta' => "text-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php elseif ($this->_tpl_vars['act'] == 'notes'): ?>
	<p><a id="opener_<?php echo $this->_tpl_vars['id']; ?>
" onclick="<?php echo $this->_tpl_vars['edit_onclick']; ?>
 jQuery.show_picker('<?php echo $this->_tpl_vars['id']; ?>
', '', '.object-container')"><?php echo $this->_tpl_vars['link_text']; ?>
</a></p>
<?php elseif ($this->_tpl_vars['act'] == 'general'): ?>
	<div class="tools-container">
		<span class="action-add">
		<?php if ($this->_tpl_vars['content']): ?>
			<a id="opener_<?php echo $this->_tpl_vars['id']; ?>
" onclick="<?php echo $this->_tpl_vars['edit_onclick']; ?>
 jQuery.show_picker('<?php echo $this->_tpl_vars['id']; ?>
', '', '.object-container')"><?php echo smarty_modifier_default(@$this->_tpl_vars['link_text'], fn_get_lang_var('add', $this->getLanguage())); ?>
</a>
		<?php else: ?>
			<a class="cm-external-click" rev="opener_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo smarty_modifier_default(@$this->_tpl_vars['link_text'], fn_get_lang_var('add', $this->getLanguage())); ?>
</a>
		<?php endif; ?>
		</span>
	</div>
<?php elseif ($this->_tpl_vars['act'] == 'button'): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => $this->_tpl_vars['link_text'],'but_href' => $this->_tpl_vars['but_href'],'but_role' => $this->_tpl_vars['but_role'],'but_id' => "openere_".($this->_tpl_vars['id']),'but_onclick' => ($this->_tpl_vars['edit_onclick'])." jQuery.show_picker('".($this->_tpl_vars['id'])."', '', '.object-container')")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php elseif ($this->_tpl_vars['act'] == 'default'): ?>
	<a<?php if ($this->_tpl_vars['onclick']): ?> onclick="<?php echo $this->_tpl_vars['onclick']; ?>
"<?php endif; ?><?php if ($this->_tpl_vars['href']): ?> href="<?php echo fn_url($this->_tpl_vars['href']); ?>
"<?php endif; ?> class="<?php echo smarty_modifier_default(@$this->_tpl_vars['link_class'], "text-button-edit"); ?>
"><?php echo $this->_tpl_vars['link_text']; ?>
</a>
<?php endif; ?>

<?php if ($this->_tpl_vars['content'] || $this->_tpl_vars['href'] || $this->_tpl_vars['edit_picker']): ?>
<div id="<?php echo $this->_tpl_vars['id']; ?>
" class="popup-<?php if ($this->_tpl_vars['act'] == 'edit' || $this->_tpl_vars['edit_picker']): ?>edit-<?php elseif ($this->_tpl_vars['act'] == 'notes' || $this->_tpl_vars['extra_act'] == 'notes'): ?>notes-<?php endif; ?>content cm-popup-box cm-picker hidden<?php if ($this->_tpl_vars['picker_meta']): ?> <?php echo $this->_tpl_vars['picker_meta']; ?>
<?php endif; ?>">
	<div class="cm-popup-hor-resizer cm-left-resizer"></div>
	<div class="cm-popup-hor-resizer cm-right-resizer"></div>
	<div class="cm-popup-corner-resizer cm-nw-resizer"></div>
	<div class="cm-popup-corner-resizer cm-ne-resizer"></div>
	<div class="cm-popup-corner-resizer cm-sw-resizer"></div>
	<div class="cm-popup-corner-resizer cm-se-resizer"></div>
	<div class="cm-popup-vert-resizer cm-top-resizer"></div>
	<div class="cm-popup-content-header">
		<div class="float-right">
			<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/icon_close.gif" width="13" height="13" border="0" alt="<?php echo fn_get_lang_var('close', $this->getLanguage()); ?>
" class="hand cm-popup-switch" />
		</div>
		<h3><?php echo $this->_tpl_vars['text']; ?>
<?php if ($this->_tpl_vars['act'] != 'edit'): ?>:<?php endif; ?></h3>
	</div>

	<div class="cm-popup-content-footer" id="content_<?php echo $this->_tpl_vars['id']; ?>
">
		<?php echo $this->_tpl_vars['content']; ?>

	</div>

	<div class="cm-popup-vert-resizer cm-bottom-resizer"></div>
</div>
<?php endif; ?>

<?php else: ?><?php endif; ?>