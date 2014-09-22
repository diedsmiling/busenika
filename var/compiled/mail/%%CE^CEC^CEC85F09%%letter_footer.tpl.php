<?php /* Smarty version 2.6.18, created on 2014-09-22 22:31:19
         compiled from letter_footer.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'replace', 'letter_footer.tpl', 5, false),)), $this); ?>

<p>
<?php if ($this->_tpl_vars['user_type'] == 'A' || $this->_tpl_vars['user_data']['user_type'] == 'A'): ?>
	<?php echo smarty_modifier_replace(fn_get_lang_var('admin_text_letter_footer', $this->getLanguage()), '[company_name]', $this->_tpl_vars['settings']['Company']['company_name']); ?>

<?php elseif ($this->_tpl_vars['user_type'] == 'P' || $this->_tpl_vars['user_data']['user_type'] == 'P'): ?>
	<?php echo fn_get_lang_var('affiliate_text_letter_footer', $this->getLanguage()); ?>

<?php elseif ($this->_tpl_vars['user_type'] == 'S' || $this->_tpl_vars['user_data']['user_type'] == 'S'): ?>
	<?php echo fn_get_lang_var('supplier_text_letter_footer', $this->getLanguage()); ?>

<?php else: ?>
	<?php echo fn_get_lang_var('customer_text_letter_footer', $this->getLanguage()); ?>

<?php endif; ?>
</p>
</body>
</html>