<?php /* Smarty version 2.6.18, created on 2014-09-24 21:53:09
         compiled from profiles/create_profile.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_get_user_type_description', 'profiles/create_profile.tpl', 5, false),array('modifier', 'lower', 'profiles/create_profile.tpl', 5, false),array('modifier', 'escape', 'profiles/create_profile.tpl', 5, false),)), $this); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "letter_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php echo fn_get_lang_var('dear', $this->getLanguage()); ?>
 <?php if ($this->_tpl_vars['user_data']['firstname']): ?><?php echo $this->_tpl_vars['user_data']['firstname']; ?>
<?php else: ?><?php echo smarty_modifier_escape(smarty_modifier_lower(fn_get_user_type_description($this->_tpl_vars['user_data']['user_type']))); ?>
<?php endif; ?>,<br><br>

<?php echo fn_get_lang_var('create_profile_notification_header', $this->getLanguage()); ?>
 <?php echo $this->_tpl_vars['settings']['Company']['company_name']; ?>
.<br><br>

<?php if ($this->_tpl_vars['user_data']['user_type'] == 'P'): ?>
	<p><?php echo fn_get_lang_var('affiliate_backend', $this->getLanguage()); ?>
:	<?php echo $this->_tpl_vars['config']['http_location']; ?>
/<?php echo $this->_tpl_vars['config']['partner_index']; ?>
<br />
	<?php echo fn_get_lang_var('text_partner_create_profile', $this->getLanguage()); ?>
</p><br /><br />

<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "profiles/profiles_info.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "letter_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>