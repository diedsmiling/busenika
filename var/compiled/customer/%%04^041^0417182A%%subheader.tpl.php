<?php /* Smarty version 2.6.18, created on 2014-09-24 21:48:48
         compiled from common_templates/subheader.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'common_templates/subheader.tpl', 5, false),array('modifier', 'trim', 'common_templates/subheader.tpl', 6, false),)), $this); ?>
<?php if ($this->_tpl_vars['anchor']): ?>
<a name="<?php echo $this->_tpl_vars['anchor']; ?>
"></a>
<?php endif; ?>
<h2 class="<?php echo smarty_modifier_default(@$this->_tpl_vars['class'], 'subheader'); ?>
">
	<?php if (trim($this->_tpl_vars['notes'])): ?>
		<div class="float-left"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/help.tpl", 'smarty_include_vars' => array('content' => $this->_tpl_vars['notes'],'id' => $this->_tpl_vars['notes_id'],'text' => $this->_tpl_vars['text'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>&nbsp;</div>
	<?php endif; ?>
	<?php echo $this->_tpl_vars['extra']; ?>

	<?php echo $this->_tpl_vars['title']; ?>

</h2>