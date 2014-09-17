<?php /* Smarty version 2.6.18, created on 2014-09-16 21:38:45
         compiled from buttons/add_to_cart.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'buttons/add_to_cart.tpl', 3, false),array('modifier', 'default', 'buttons/add_to_cart.tpl', 6, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('add_to_cart','text_login_to_add_to_cart','sign_in_to_buy'));
?>

<?php $this->assign('c_url', smarty_modifier_escape($this->_tpl_vars['config']['current_url'], 'url'), false); ?>

<?php if ($this->_tpl_vars['settings']['General']['allow_anonymous_shopping'] == 'Y' || $this->_tpl_vars['auth']['user_id']): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_id' => $this->_tpl_vars['but_id'],'but_text' => smarty_modifier_default(@$this->_tpl_vars['but_text'], fn_get_lang_var('add_to_cart', $this->getLanguage())),'but_name' => $this->_tpl_vars['but_name'],'but_onclick' => $this->_tpl_vars['but_onclick'],'but_href' => $this->_tpl_vars['but_href'],'but_target' => $this->_tpl_vars['but_target'],'but_role' => smarty_modifier_default(@$this->_tpl_vars['but_role'], 'text'))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
	<p class="wrapped"<?php if ($this->_tpl_vars['block_width']): ?> style="width: <?php echo $this->_tpl_vars['settings']['Thumbnails']['product_lists_thumbnail_width']; ?>
px"<?php endif; ?>><?php echo fn_get_lang_var('text_login_to_add_to_cart', $this->getLanguage()); ?>
</p>

	<?php if ($this->_tpl_vars['controller'] == 'auth' && $this->_tpl_vars['mode'] == 'login_form'): ?>
		<?php $this->assign('login_url', $this->_tpl_vars['config']['current_url'], false); ?>
	<?php else: ?>
		<?php $this->assign('login_url', "auth.login_form?return_url=".($this->_tpl_vars['c_url']), false); ?>
	<?php endif; ?>
	
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_id' => $this->_tpl_vars['but_id'],'but_text' => fn_get_lang_var('sign_in_to_buy', $this->getLanguage()),'but_href' => $this->_tpl_vars['login_url'],'but_role' => smarty_modifier_default(@$this->_tpl_vars['but_role'], 'text'),'but_name' => "")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>