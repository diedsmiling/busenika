<?php /* Smarty version 2.6.18, created on 2014-09-23 21:20:59
         compiled from blocks/html_block.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'unescape', 'blocks/html_block.tpl', 4, false),)), $this); ?>
<?php  ob_start();  ?>
<?php echo smarty_modifier_unescape($this->_tpl_vars['items']['block_text']); ?>
<?php  ob_end_flush();  ?>