<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:12
         compiled from blocks/my_account.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'blocks/my_account.tpl', 9, false),array('modifier', 'fn_url', 'blocks/my_account.tpl', 13, false),array('block', 'hook', 'blocks/my_account.tpl', 11, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('profile_details','downloads','sign_in','register','orders','sign_out','track_my_order','track_my_order','order_id','email','go'));
?>

<!--dynamic:my_account-->
<?php if ($this->_tpl_vars['auth']['user_id']): ?>
<strong><?php echo $this->_tpl_vars['user_info']['firstname']; ?>
 <?php echo $this->_tpl_vars['user_info']['lastname']; ?>
</strong>
<?php endif; ?>

<?php $this->assign('return_current_url', smarty_modifier_escape($this->_tpl_vars['config']['current_url'], 'url'), false); ?>
<ul class="arrows-list">
<?php $this->_tag_stack[] = array('hook', array('name' => "profiles:my_account_menu")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
	<?php if ($this->_tpl_vars['auth']['user_id']): ?>
		<li><a href="<?php echo fn_url("profiles.update"); ?>
" class="underlined"><?php echo fn_get_lang_var('profile_details', $this->getLanguage()); ?>
</a></li>
		<li><a href="<?php echo fn_url("orders.downloads"); ?>
" class="underlined"><?php echo fn_get_lang_var('downloads', $this->getLanguage()); ?>
</a></li>
	<?php else: ?>
		<li><a href="<?php if ($this->_tpl_vars['controller'] == 'auth' && $this->_tpl_vars['mode'] == 'login_form'): ?><?php echo fn_url($this->_tpl_vars['config']['current_url']); ?>
<?php else: ?><?php echo fn_url("auth.login_form?return_url=".($this->_tpl_vars['return_current_url'])); ?>
<?php endif; ?>" class="underlined"><?php echo fn_get_lang_var('sign_in', $this->getLanguage()); ?>
</a> / <a href="<?php echo fn_url("profiles.add"); ?>
" class="underlined"><?php echo fn_get_lang_var('register', $this->getLanguage()); ?>
</a></li>
	<?php endif; ?>
	<li><a href="<?php echo fn_url("orders.search"); ?>
" class="underlined"><?php echo fn_get_lang_var('orders', $this->getLanguage()); ?>
</a></li>
<?php if ($this->_tpl_vars['addons']['rma']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/rma/hooks/profiles/my_account_menu.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['reward_points']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/reward_points/hooks/profiles/my_account_menu.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['recurring_billing']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/recurring_billing/hooks/profiles/my_account_menu.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

<?php if ($this->_tpl_vars['auth']['user_id']): ?>
		<li class="delim"></li>
		<li><a href="<?php echo fn_url("auth.logout?redirect_url=".($this->_tpl_vars['return_current_url'])); ?>
" class="underlined"><?php echo fn_get_lang_var('sign_out', $this->getLanguage()); ?>
</a></li>
<?php endif; ?>
</ul>

<div class="updates-wrapper">

<form action="<?php echo fn_url(""); ?>
" method="get" class="cm-ajax" name="track_order_quick">
<?php echo '<p>'; ?><?php echo fn_get_lang_var('track_my_order', $this->getLanguage()); ?><?php echo ':</p><div class="form-field"><label for="track_order_item'; ?><?php echo $this->_tpl_vars['block']['block_id']; ?><?php echo '" class="cm-required hidden">'; ?><?php echo fn_get_lang_var('track_my_order', $this->getLanguage()); ?><?php echo ':</label><input type="text" size="20" class="input-text cm-hint" id="track_order_item'; ?><?php echo $this->_tpl_vars['block']['block_id']; ?><?php echo '" name="track_data" value="'; ?><?php echo smarty_modifier_escape(fn_get_lang_var('order_id', $this->getLanguage()), 'html'); ?><?php echo ''; ?><?php if (! $this->_tpl_vars['auth']['user_id']): ?><?php echo '/'; ?><?php echo smarty_modifier_escape(fn_get_lang_var('email', $this->getLanguage()), 'html'); ?><?php echo ''; ?><?php endif; ?><?php echo '" />'; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/go.tpl", 'smarty_include_vars' => array('but_name' => "orders.track_request",'alt' => fn_get_lang_var('go', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo '</div>'; ?>

</form>

</div>
<!--/dynamic-->