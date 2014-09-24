<?php /* Smarty version 2.6.18, created on 2014-09-23 22:34:33
         compiled from views/profiles/components/profiles_account.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'defined', 'views/profiles/components/profiles_account.tpl', 5, false),array('modifier', 'default', 'views/profiles/components/profiles_account.tpl', 39, false),array('modifier', 'fn_query_remove', 'views/profiles/components/profiles_account.tpl', 45, false),array('modifier', 'fn_url', 'views/profiles/components/profiles_account.tpl', 46, false),array('block', 'hook', 'views/profiles/components/profiles_account.tpl', 47, false),array('function', 'script', 'views/profiles/components/profiles_account.tpl', 72, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('user_account_information','email','username','password','confirm_password','account_type','customer','administrator','tax_exempt','language'));
?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('user_account_information', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['uid'] == 1 || ( $this->_tpl_vars['user_data']['user_type'] == 'A' && defined('RESTRICTED_ADMIN') )): ?>
	<input type="hidden" name="user_data[status]" value="A" />
	<input type="hidden" name="user_data[user_type]" value="A" />
<?php endif; ?>

<?php if ($this->_tpl_vars['settings']['General']['use_email_as_login'] == 'Y'): ?>
<div class="form-field">
	<label for="email" class="cm-required cm-email"><?php echo fn_get_lang_var('email', $this->getLanguage()); ?>
:</label>
	<input type="text" id="email" name="user_data[email]" class="input-text" size="32" maxlength="128" value="<?php echo $this->_tpl_vars['user_data']['email']; ?>
" />
</div>

<?php else: ?>

<div class="form-field">
	<label for="user_login_profile" class="cm-required"><?php echo fn_get_lang_var('username', $this->getLanguage()); ?>
:</label>
	<input id="user_login_profile" type="text" name="user_data[user_login]" class="input-text" size="32" maxlength="32" value="<?php echo $this->_tpl_vars['user_data']['user_login']; ?>
" />
</div>
<?php endif; ?>

<div class="form-field">
	<label for="password1" class="cm-required"><?php echo fn_get_lang_var('password', $this->getLanguage()); ?>
:</label>
	<input type="password" id="password1" name="user_data[password1]" class="input-text" size="32" maxlength="32" value="<?php if ($this->_tpl_vars['mode'] == 'update'): ?>            <?php endif; ?>" autocomplete="off" />
</div>

<div class="form-field">
	<label for="password2" class="cm-required"><?php echo fn_get_lang_var('confirm_password', $this->getLanguage()); ?>
:</label>
	<input type="password" id="password2" name="user_data[password2]" class="input-text" size="32" maxlength="32" value="<?php if ($this->_tpl_vars['mode'] == 'update'): ?>            <?php endif; ?>" autocomplete="off" />
</div>


<?php if ($this->_tpl_vars['uid'] != 1 || $this->_tpl_vars['user_data']['user_type'] != 'A' || defined('RESTRICTED_ADMIN')): ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_status.tpl", 'smarty_include_vars' => array('input_name' => "user_data[status]",'id' => 'user_data','obj' => $this->_tpl_vars['user_data'],'hidden' => false)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $this->assign('_u_type', smarty_modifier_default(@$this->_tpl_vars['_REQUEST']['user_type'], @$this->_tpl_vars['user_data']['user_type']), false); ?>
<?php if ($this->_tpl_vars['mode'] == 'add'): ?>
	<input type="hidden" name="user_data[user_type]" value="<?php echo $this->_tpl_vars['_u_type']; ?>
" />
<?php else: ?>
<div class="form-field">
	<label for="user_type" class="cm-required"><?php echo fn_get_lang_var('account_type', $this->getLanguage()); ?>
:</label>
	<?php $this->assign('r_url', fn_query_remove($this->_tpl_vars['config']['current_url'], 'user_type'), false); ?>
	<select id="user_type" name="user_data[user_type]"<?php if (! $this->_tpl_vars['redirect_denied']): ?> onchange="jQuery.redirect('<?php echo fn_url(($this->_tpl_vars['r_url'])."&user_type="); ?>
' + this.value);"<?php endif; ?>>
		<?php $this->_tag_stack[] = array('hook', array('name' => "profiles:account")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
		<option value="C" <?php if ($this->_tpl_vars['_u_type'] == 'C'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('customer', $this->getLanguage()); ?>
</option>
		<?php if (@RESTRICTED_ADMIN != 1 || $this->_tpl_vars['user_data']['user_id'] == $this->_tpl_vars['auth']['user_id']): ?>
		<option value="A" <?php if ($this->_tpl_vars['_u_type'] == 'A'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('administrator', $this->getLanguage()); ?>
</option>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['addons']['affiliate']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/affiliate/hooks/profiles/account.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
	</select>
</div>
<?php endif; ?>

<div class="form-field">
	<label for="tax_exempt"><?php echo fn_get_lang_var('tax_exempt', $this->getLanguage()); ?>
:</label>
	<input type="hidden" name="user_data[tax_exempt]" value="N" />
	<input id="tax_exempt" type="checkbox" name="user_data[tax_exempt]" value="Y" <?php if ($this->_tpl_vars['user_data']['tax_exempt'] == 'Y'): ?>checked="checked"<?php endif; ?> class="checkbox" />
</div>

<?php endif; ?>

<div class="form-field">
	<label for="user_language"><?php echo fn_get_lang_var('language', $this->getLanguage()); ?>
</label>
	<select name="user_data[lang_code]" id="user_language">
		<?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lang_code'] => $this->_tpl_vars['language']):
?>
			<option value="<?php echo $this->_tpl_vars['lang_code']; ?>
" <?php if ($this->_tpl_vars['lang_code'] == $this->_tpl_vars['user_data']['lang_code']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['language']['name']; ?>
</option>
		<?php endforeach; endif; unset($_from); ?>
	</select>
</div><?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>