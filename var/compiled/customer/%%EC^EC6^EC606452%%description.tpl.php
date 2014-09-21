<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:13
         compiled from blocks/product_tabs/description.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'blocks/product_tabs/description.tpl', 4, false),array('modifier', 'unescape', 'blocks/product_tabs/description.tpl', 4, false),)), $this); ?>
<?php  ob_start();  ?>
<?php echo smarty_modifier_unescape(smarty_modifier_default(@$this->_tpl_vars['product']['full_description'], @$this->_tpl_vars['product']['short_description'])); ?>

<?php  ob_end_flush();  ?>