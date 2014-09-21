<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:39
         compiled from common_templates/styles.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'hook', 'common_templates/styles.tpl', 11, false),)), $this); ?>
<?php  ob_start();  ?>
<link href="<?php echo $this->_tpl_vars['config']['skin_path']; ?>
/styles.css" rel="stylesheet" type="text/css" />
<?php if ($this->_tpl_vars['include_file_tree']): ?>
<link href="<?php echo $this->_tpl_vars['config']['skin_path']; ?>
/jqueryFileTree.css" rel="stylesheet" type="text/css" />
<?php endif; ?>
<!--[if lte IE 7]>
<link href="<?php echo $this->_tpl_vars['config']['skin_path']; ?>
/styles_ie.css" rel="stylesheet" type="text/css" />
<![endif]-->
<link href="<?php echo $this->_tpl_vars['config']['skin_path']; ?>
/custom_styles.css" rel="stylesheet" type="text/css" />
<?php $this->_tag_stack[] = array('hook', array('name' => "index:styles")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php  ob_end_flush();  ?>