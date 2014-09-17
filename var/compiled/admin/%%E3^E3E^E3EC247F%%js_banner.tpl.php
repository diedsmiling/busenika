<?php /* Smarty version 2.6.18, created on 2014-09-16 23:39:25
         compiled from addons/banners/pickers/js_banner.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_get_banner_name', 'addons/banners/pickers/js_banner.tpl', 6, false),array('modifier', 'default', 'addons/banners/pickers/js_banner.tpl', 6, false),array('modifier', 'fn_url', 'addons/banners/pickers/js_banner.tpl', 11, false),array('modifier', 'escape', 'addons/banners/pickers/js_banner.tpl', 11, false),array('function', 'math', 'addons/banners/pickers/js_banner.tpl', 10, false),)), $this); ?>
<?php  ob_start();  ?>
<?php if ($this->_tpl_vars['banner_id'] == '0'): ?>
	<?php $this->assign('banner', $this->_tpl_vars['default_name'], false); ?>
<?php else: ?>
	<?php $this->assign('banner', smarty_modifier_default(fn_get_banner_name($this->_tpl_vars['banner_id']), ($this->_tpl_vars['ldelim'])."banner".($this->_tpl_vars['rdelim'])), false); ?>
<?php endif; ?>

<tr <?php if (! $this->_tpl_vars['clone']): ?>id="<?php echo $this->_tpl_vars['holder']; ?>
_<?php echo $this->_tpl_vars['banner_id']; ?>
" <?php endif; ?>class="cm-js-item<?php if ($this->_tpl_vars['clone']): ?> cm-clone hidden<?php endif; ?>">
	<?php if ($this->_tpl_vars['position_field']): ?><td><input type="text" name="<?php echo $this->_tpl_vars['input_name']; ?>
[<?php echo $this->_tpl_vars['banner_id']; ?>
]" value="<?php echo smarty_function_math(array('equation' => "a*b",'a' => $this->_tpl_vars['position'],'b' => 10), $this);?>
" size="3" class="input-text-short" <?php if ($this->_tpl_vars['clone']): ?>disabled="disabled"<?php endif; ?> /></td><?php endif; ?>
	<td><a href="<?php echo fn_url("banners.update?banner_id=".($this->_tpl_vars['banner_id'])); ?>
"><?php echo smarty_modifier_escape($this->_tpl_vars['banner']); ?>
</a></td>
	<td><?php if (! $this->_tpl_vars['hide_delete_button'] && ! $this->_tpl_vars['view_only']): ?><a onclick="jQuery.delete_js_item('<?php echo $this->_tpl_vars['holder']; ?>
', '<?php echo $this->_tpl_vars['banner_id']; ?>
', 'b'); return false;"><img width="12" height="18" border="0" class="hand valign" alt="" src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/icon_delete.gif"/></a><?php else: ?>&nbsp;<?php endif; ?></td>
</tr><?php  ob_end_flush();  ?>