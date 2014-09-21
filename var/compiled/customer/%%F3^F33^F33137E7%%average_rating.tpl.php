<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:13
         compiled from addons/discussion/views/discussion/components/average_rating.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_get_average_rating', 'addons/discussion/views/discussion/components/average_rating.tpl', 3, false),array('modifier', 'fn_get_discussion_rating', 'addons/discussion/views/discussion/components/average_rating.tpl', 6, false),)), $this); ?>

<?php $this->assign('average_rating', fn_get_average_rating($this->_tpl_vars['object_id'], $this->_tpl_vars['object_type']), false); ?>

<?php if ($this->_tpl_vars['average_rating']): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/discussion/views/discussion/components/stars.tpl", 'smarty_include_vars' => array('stars' => fn_get_discussion_rating($this->_tpl_vars['average_rating']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>