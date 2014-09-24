<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:18
         compiled from common_templates/file_browser_dirs.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'common_templates/file_browser_dirs.tpl', 6, false),)), $this); ?>

<ul class="cm-filetree">
<?php $_from = $this->_tpl_vars['file_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['file']):
?>
	<?php if ($this->_tpl_vars['file']['ext']): ?>
		<li class="file ext_<?php echo $this->_tpl_vars['file']['ext']; ?>
 cm-selectable" ondblclick="fileuploader.set_file('<?php echo smarty_modifier_escape($this->_tpl_vars['current_dir'], 'javascript'); ?>
<?php echo smarty_modifier_escape($this->_tpl_vars['file']['file'], 'javascript'); ?>
', false);"><a rel="<?php echo $this->_tpl_vars['current_dir']; ?>
<?php echo $this->_tpl_vars['file']['file']; ?>
"><?php echo $this->_tpl_vars['file']['file']; ?>
</a></li>
	<?php else: ?>
		<?php if ($this->_tpl_vars['file']['next']): ?>
			<li class="directory cm-expanded"><a rel="<?php echo $this->_tpl_vars['current_dir']; ?>
<?php echo $this->_tpl_vars['file']['file']; ?>
/"><?php echo $this->_tpl_vars['file']['file']; ?>
</a>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/file_browser_dirs.tpl", 'smarty_include_vars' => array('file_list' => $this->_tpl_vars['file']['next'],'current_dir' => ($this->_tpl_vars['current_dir']).($this->_tpl_vars['file']['file'])."/")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></li>
		<?php else: ?>
			<li class="directory cm-collapsed"><a rel="<?php echo $this->_tpl_vars['current_dir']; ?>
<?php echo $this->_tpl_vars['file']['file']; ?>
/"><?php echo $this->_tpl_vars['file']['file']; ?>
</a></li>
		<?php endif; ?>
	<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
</ul>