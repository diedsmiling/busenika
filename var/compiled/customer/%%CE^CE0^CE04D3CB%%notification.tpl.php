<?php /* Smarty version 2.6.18, created on 2014-09-23 21:20:58
         compiled from common_templates/notification.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'defined', 'common_templates/notification.tpl', 3, false),array('modifier', 'fn_get_notifications', 'common_templates/notification.tpl', 4, false),array('modifier', 'lower', 'common_templates/notification.tpl', 20, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('close','close','close','close','close','close'));
?>
<?php  ob_start();  ?>
<?php if (! defined('AJAX_REQUEST')): ?>
<?php $_from = fn_get_notifications(""); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['message']):
?>
<?php if ($this->_tpl_vars['message']['type'] != 'P' && $this->_tpl_vars['message']['type'] != 'L' && $this->_tpl_vars['message']['type'] != 'C'): ?>
	<?php if ($this->_tpl_vars['message']['type'] == 'O'): ?>
		<?php ob_start(); ?>
		<?php echo $this->_smarty_vars['capture']['checkout_error_content']; ?>

		<div class="error-box-container notification-content" id="notification_<?php echo $this->_tpl_vars['key']; ?>
">
			<div class="error-box">
				<img class="cm-notification-close hand" src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/notification_close.gif" width="10" height="19" border="0" alt="<?php echo fn_get_lang_var('close', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('close', $this->getLanguage()); ?>
" />
				<p><?php echo $this->_tpl_vars['message']['message']; ?>
</p>
			</div>
		</div>
		<?php $this->_smarty_vars['capture']['checkout_error_content'] = ob_get_contents(); ob_end_clean(); ?>
	<?php else: ?>
		<?php ob_start(); ?>
		<?php echo $this->_smarty_vars['capture']['notification_content']; ?>

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
		<?php $this->_smarty_vars['capture']['notification_content'] = ob_get_contents(); ob_end_clean(); ?>
	<?php endif; ?>
<?php else: ?>
	<div class="product-notification-container<?php if ($this->_tpl_vars['message']['save_state'] == false): ?> cm-auto-hide<?php endif; ?>" id="notification_<?php echo $this->_tpl_vars['key']; ?>
">
		<div class="w-shadow"></div>
		<div class="e-shadow"></div>
		<div class="nw-shadow"></div>
		<div class="ne-shadow"></div>
		<div class="sw-shadow"></div>
		<div class="se-shadow"></div>
		<div class="n-shadow"></div>
		<div class="popupbox-closer"><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/close_popupbox.png" class="cm-notification-close" title="<?php echo fn_get_lang_var('close', $this->getLanguage()); ?>
" alt="<?php echo fn_get_lang_var('close', $this->getLanguage()); ?>
" /></div>
		<div class="product-notification notification-correct">
			<h1><?php echo $this->_tpl_vars['message']['title']; ?>
</h1>
			<?php echo $this->_tpl_vars['message']['message']; ?>

		</div>
		<div class="s-shadow"></div>
	</div>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>

<div class="cm-notification-container">
<?php echo $this->_smarty_vars['capture']['notification_content']; ?>

</div>
<?php endif; ?><?php  ob_end_flush();  ?>