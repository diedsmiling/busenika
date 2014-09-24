<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:16
         compiled from common_templates/tooltip.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'unescape', 'common_templates/tooltip.tpl', 3, false),)), $this); ?>
<?php  ob_start();  ?>
<?php if ($this->_tpl_vars['tooltip']): ?> (<a onclick="return false;" class="cm-tooltip<?php if ($this->_tpl_vars['params']): ?> <?php echo $this->_tpl_vars['params']; ?>
<?php endif; ?>">?</a>)<span class="hidden cm-tooltip-text"><?php echo smarty_modifier_unescape($this->_tpl_vars['tooltip']); ?>
</span><?php endif; ?><?php  ob_end_flush();  ?>