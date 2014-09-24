<?php /* Smarty version 2.6.18, created on 2014-09-25 00:06:29
         compiled from views/auth/login_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'views/auth/login_form.tpl', 3, false),array('modifier', 'fn_url', 'views/auth/login_form.tpl', 6, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('email','username','password','remember_me','forgot_password_question','sign_in'));
?>

<?php $this->assign('form_name', smarty_modifier_default(@$this->_tpl_vars['form_name'], 'main_login_form'), false); ?>

<?php ob_start(); ?>
<form name="<?php echo $this->_tpl_vars['form_name']; ?>
" action="<?php echo fn_url(""); ?>
" method="post">
<input type="hidden" name="form_name" value="<?php echo $this->_tpl_vars['form_name']; ?>
" />
<input type="hidden" name="return_url" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['_REQUEST']['return_url'], @$this->_tpl_vars['config']['current_url']); ?>
" />

<div class="form-field">
	<label for="login_<?php echo $this->_tpl_vars['id']; ?>
" <?php if ($this->_tpl_vars['settings']['General']['use_email_as_login'] == 'Y'): ?>class="cm-email"<?php endif; ?>><?php if ($this->_tpl_vars['settings']['General']['use_email_as_login'] == 'Y'): ?><?php echo fn_get_lang_var('email', $this->getLanguage()); ?>
<?php else: ?><?php echo fn_get_lang_var('username', $this->getLanguage()); ?>
<?php endif; ?>:</label>
	<input type="text" id="login_<?php echo $this->_tpl_vars['id']; ?>
" name="user_login" size="30" value="<?php echo $this->_tpl_vars['config']['demo_username']; ?>
" class="input-text cm-focus" />
</div>

<div class="form-field">
	<label for="psw_<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('password', $this->getLanguage()); ?>
:</label>
	<input type="password" id="psw_<?php echo $this->_tpl_vars['id']; ?>
" name="password" size="30" value="<?php echo $this->_tpl_vars['config']['demo_password']; ?>
" class="input-text password" />
</div>

<?php if ($this->_tpl_vars['settings']['Image_verification']['use_for_login'] == 'Y'): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image_verification.tpl", 'smarty_include_vars' => array('id' => "login_".($this->_tpl_vars['form_name']),'align' => 'left')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<div class="clear">
	<div class="float-left">
		<input class="valign checkbox" type="checkbox" name="remember_me" id="remember_me_<?php echo $this->_tpl_vars['id']; ?>
" value="Y" />
		<label for="remember_me_<?php echo $this->_tpl_vars['id']; ?>
" class="valign lowercase"><?php echo fn_get_lang_var('remember_me', $this->getLanguage()); ?>
</label>
	</div>

	<div class="float-right">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/login.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[auth.login]",'but_role' => 'action')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
</div>
<p class="center"><a href="<?php echo fn_url("auth.recover_password"); ?>
"><?php echo fn_get_lang_var('forgot_password_question', $this->getLanguage()); ?>
</a></p>
</form>
<?php $this->_smarty_vars['capture']['login'] = ob_get_contents(); ob_end_clean(); ?>

<?php if ($this->_tpl_vars['style'] == 'popup'): ?>
	<?php echo $this->_smarty_vars['capture']['login']; ?>

<?php else: ?>
	<div<?php if ($this->_tpl_vars['controller'] != 'checkout'): ?> class="login"<?php endif; ?>>
		<?php echo $this->_smarty_vars['capture']['login']; ?>

	</div>

	<?php ob_start(); ?><?php echo fn_get_lang_var('sign_in', $this->getLanguage()); ?>
<?php $this->_smarty_vars['capture']['mainbox_title'] = ob_get_contents(); ob_end_clean(); ?>
<?php endif; ?>