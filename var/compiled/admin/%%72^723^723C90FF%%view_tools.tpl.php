<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:46
         compiled from common_templates/view_tools.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'common_templates/view_tools.tpl', 6, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('previous','next'));
?>
<?php  ob_start();  ?>
<?php ob_start(); ?>
	<div class="float-right">
		<?php if ($this->_tpl_vars['prev_id']): ?>
			<a class="lowercase" href="<?php echo fn_url(($this->_tpl_vars['url']).($this->_tpl_vars['prev_id'])); ?>
">&laquo;&nbsp;<?php echo fn_get_lang_var('previous', $this->getLanguage()); ?>
</a>&nbsp;&nbsp;&nbsp;
		<?php endif; ?>

		<?php if ($this->_tpl_vars['next_id']): ?>
			<a class="lowercase" href="<?php echo fn_url(($this->_tpl_vars['url']).($this->_tpl_vars['next_id'])); ?>
"><?php echo fn_get_lang_var('next', $this->getLanguage()); ?>
&nbsp;&raquo;</a>
		<?php endif; ?>
	</div>
<?php $this->_smarty_vars['capture']['view_tools'] = ob_get_contents(); ob_end_clean(); ?><?php  ob_end_flush();  ?>