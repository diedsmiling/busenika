<?php /* Smarty version 2.6.18, created on 2014-09-22 22:31:19
         compiled from profiles/create_profile_subj.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_get_user_type_description', 'profiles/create_profile_subj.tpl', 3, false),array('modifier', 'lower', 'profiles/create_profile_subj.tpl', 3, false),array('modifier', 'escape', 'profiles/create_profile_subj.tpl', 3, false),array('modifier', 'unescape', 'profiles/create_profile_subj.tpl', 4, false),array('modifier', 'replace', 'profiles/create_profile_subj.tpl', 4, false),)), $this); ?>

<?php $this->assign('u_type', smarty_modifier_escape(smarty_modifier_lower(fn_get_user_type_description($this->_tpl_vars['user_data']['user_type']))), false); ?>
<?php echo smarty_modifier_unescape($this->_tpl_vars['settings']['Company']['company_name']); ?>
: <?php echo smarty_modifier_replace(fn_get_lang_var('new_profile_notification', $this->getLanguage()), '[user_type]', $this->_tpl_vars['u_type']); ?>