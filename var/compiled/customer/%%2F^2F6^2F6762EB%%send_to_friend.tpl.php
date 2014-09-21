<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:14
         compiled from addons/send_to_friend/blocks/product_tabs/send_to_friend.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'addons/send_to_friend/blocks/product_tabs/send_to_friend.tpl', 5, false),array('modifier', 'unescape', 'addons/send_to_friend/blocks/product_tabs/send_to_friend.tpl', 31, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('name_of_friend','email_of_friend','your_name','your_email','your_message','send'));
?>

<div id="content_send_to_friend">
<form name="send_to_friend_form" action="<?php echo fn_url(""); ?>
" method="post">
<input type="hidden" name="selected_section" value="send_to_friend" />
<input type="hidden" name="redirect_url" value="<?php echo $this->_tpl_vars['config']['current_url']; ?>
" />

<div class="form-field">
	<label for="send_name"><?php echo fn_get_lang_var('name_of_friend', $this->getLanguage()); ?>
:</label>
	<input id="send_name" class="input-text" size="50" type="text" name="send_data[to_name]" value="<?php echo $this->_tpl_vars['send_data']['to_name']; ?>
" />
</div>

<div class="form-field">
	<label for="send_email" class="cm-required cm-email"><?php echo fn_get_lang_var('email_of_friend', $this->getLanguage()); ?>
:</label>
	<input id="send_email" class="input-text" size="50" type="text" name="send_data[to_email]" value="<?php echo $this->_tpl_vars['send_data']['to_email']; ?>
" />
</div>

<div class="form-field">
	<label for="send_yourname"><?php echo fn_get_lang_var('your_name', $this->getLanguage()); ?>
:</label>
	<input id="send_yourname" size="50" class="input-text" type="text" name="send_data[from_name]" value="<?php if ($this->_tpl_vars['send_data']['from_name']): ?><?php echo $this->_tpl_vars['send_data']['from_name']; ?>
<?php elseif ($this->_tpl_vars['auth']['user_id']): ?><?php echo $this->_tpl_vars['user_info']['firstname']; ?>
 <?php echo $this->_tpl_vars['user_info']['lastname']; ?>
<?php endif; ?>" />
</div>

<div class="form-field">
	<label for="send_youremail" class="cm-email"><?php echo fn_get_lang_var('your_email', $this->getLanguage()); ?>
:</label>
	<input id="send_youremail" class="input-text" size="50" type="text" name="send_data[from_email]" value="<?php if ($this->_tpl_vars['send_data']['from_email']): ?><?php echo $this->_tpl_vars['send_data']['from_email']; ?>
<?php elseif ($this->_tpl_vars['auth']['user_id']): ?><?php echo $this->_tpl_vars['user_info']['email']; ?>
<?php endif; ?>" />
</div>

<div class="form-field">
	<label for="send_notes" class="cm-required"><?php echo fn_get_lang_var('your_message', $this->getLanguage()); ?>
:</label>
	<textarea id="send_notes"  class="input-textarea" rows="5" cols="72" name="send_data[notes]"><?php if ($this->_tpl_vars['send_data']['notes']): ?><?php echo $this->_tpl_vars['send_data']['notes']; ?>
<?php else: ?><?php echo smarty_modifier_unescape($this->_tpl_vars['product']['product']); ?>
<?php endif; ?></textarea>
</div>

<?php if ($this->_tpl_vars['settings']['Image_verification']['use_for_send_to_friend'] == 'Y'): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image_verification.tpl", 'smarty_include_vars' => array('id' => 'send_to_friend','align' => 'left')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<div class="buttons-container">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('send', $this->getLanguage()),'but_name' => "dispatch[send_to_friend.send]")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

</form>
</div>