<?php /* Smarty version 2.6.18, created on 2014-09-24 21:48:48
         compiled from views/profiles/components/profiles_account.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'hook', 'views/profiles/components/profiles_account.tpl', 7, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('user_account_info','email','username','password','confirm_password'));
?>

<?php if (! $this->_tpl_vars['nothing_extra']): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('user_account_info', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php $this->_tag_stack[] = array('hook', array('name' => "profiles:account_info")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php if ($this->_tpl_vars['addons']['affiliate']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/affiliate/hooks/profiles/account_info.pre.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?>
<?php if ($this->_tpl_vars['settings']['General']['use_email_as_login'] == 'Y'): ?>
<?php if ($this->_tpl_vars['location'] != 'checkout' || $this->_tpl_vars['settings']['General']['disable_anonymous_checkout'] == 'Y'): ?>
<div class="form-field">
	<label for="email" class="cm-required cm-email"><?php echo fn_get_lang_var('email', $this->getLanguage()); ?>
:</label>
	<input type="text" id="email" name="user_data[email]" size="32" maxlength="128" value="<?php echo $this->_tpl_vars['user_data']['email']; ?>
" class="input-text" />
</div>
<?php endif; ?>
<?php else: ?>
<div class="form-field">
	<label for="user_login_profile" class="cm-required"><?php echo fn_get_lang_var('username', $this->getLanguage()); ?>
:</label>
	<input id="user_login_profile" type="text" name="user_data[user_login]" size="32" maxlength="32" value="<?php echo $this->_tpl_vars['user_data']['user_login']; ?>
" class="input-text" />
</div>
<?php endif; ?>

<div class="form-field">
	<label for="password1" class="cm-required cm-password"><?php echo fn_get_lang_var('password', $this->getLanguage()); ?>
:</label>
	<input type="password" id="password1" name="user_data[password1]" size="32" maxlength="32" value="<?php if ($this->_tpl_vars['mode'] == 'update'): ?>            <?php endif; ?>" class="input-text" autocomplete="off" />
</div>

<div class="form-field">
	<label for="password2" class="cm-required cm-password"><?php echo fn_get_lang_var('confirm_password', $this->getLanguage()); ?>
:</label>
	<input type="password" id="password2" name="user_data[password2]" size="32" maxlength="32" value="<?php if ($this->_tpl_vars['mode'] == 'update'): ?>            <?php endif; ?>" class="input-text" autocomplete="off" />
</div>
<?php if ($this->_tpl_vars['addons']['age_verification']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/age_verification/hooks/profiles/account_info.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>