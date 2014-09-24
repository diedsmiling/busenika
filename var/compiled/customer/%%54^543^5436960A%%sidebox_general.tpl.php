<?php /* Smarty version 2.6.18, created on 2014-09-23 21:20:59
         compiled from blocks/wrappers/sidebox_general.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'blocks/wrappers/sidebox_general.tpl', 3, false),)), $this); ?>
<?php  ob_start();  ?>
<div class="<?php echo smarty_modifier_default(@$this->_tpl_vars['sidebox_wrapper'], "sidebox-wrapper"); ?>
 <?php if ($this->_tpl_vars['hide_wrapper']): ?>hidden cm-hidden-wrapper<?php endif; ?>">
	<h3 class="sidebox-title<?php if ($this->_tpl_vars['header_class']): ?> <?php echo $this->_tpl_vars['header_class']; ?>
<?php endif; ?>"><span><?php echo $this->_tpl_vars['title']; ?>
</span></h3>
	<div class="sidebox-body"><?php echo smarty_modifier_default(@$this->_tpl_vars['content'], "&nbsp;"); ?>
</div>
	<div class="sidebox-bottom"><span>&nbsp;</span></div>
</div><?php  ob_end_flush();  ?>