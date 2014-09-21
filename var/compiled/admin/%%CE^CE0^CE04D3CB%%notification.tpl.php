<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:40
         compiled from common_templates/notification.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'defined', 'common_templates/notification.tpl', 3, false),array('modifier', 'fn_get_notifications', 'common_templates/notification.tpl', 6, false),array('modifier', 'lower', 'common_templates/notification.tpl', 8, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('close','close'));
?>
<?php  ob_start();  ?>
<?php if (! defined('AJAX_REQUEST')): ?>

<div class="cm-notification-container">
<?php $_from = fn_get_notifications(""); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['message']):
?>
<div class="notification-content<?php if ($this->_tpl_vars['message']['save_state'] == false): ?> cm-auto-hide<?php endif; ?>" id="notification_<?php echo $this->_tpl_vars['key']; ?>
">
	<div class="notification-<?php echo smarty_modifier_lower($this->_tpl_vars['message']['type']); ?>
">
		<img class="cm-notification-close hand" src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/notification_close.gif" width="10" height="19" border="0" alt="<?php echo fn_get_lang_var('close', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('close', $this->getLanguage()); ?>
" />
		<div class="notification-body">
			<?php echo $this->_tpl_vars['message']['message']; ?>

		</div>
	</div>
	<h1 class="notification-header-<?php echo smarty_modifier_lower($this->_tpl_vars['message']['type']); ?>
"><?php echo $this->_tpl_vars['message']['title']; ?>
</h1>
</div>
<?php endforeach; endif; unset($_from); ?>
</div>

<?php endif; ?>
<?php  ob_end_flush();  ?>