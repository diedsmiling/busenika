<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:12
         compiled from common_templates/last_viewed_items.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'common_templates/last_viewed_items.tpl', 7, false),array('modifier', 'unescape', 'common_templates/last_viewed_items.tpl', 7, false),array('modifier', 'truncate', 'common_templates/last_viewed_items.tpl', 7, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('cleanup_history','no_items'));
?>
<?php  ob_start();  ?>
<div class="last-items-content cm-smart-position cm-popup-box hidden" id="last_edited_items">
<?php if ($this->_tpl_vars['last_edited_items']): ?>
	<ul>
	<?php $_from = $this->_tpl_vars['last_edited_items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lnk']):
?>
		<li><a <?php if ($this->_tpl_vars['lnk']['icon']): ?>class="<?php echo $this->_tpl_vars['lnk']['icon']; ?>
"<?php endif; ?> href="<?php echo fn_url($this->_tpl_vars['lnk']['url']); ?>
" title="<?php echo smarty_modifier_unescape($this->_tpl_vars['lnk']['name']); ?>
"><?php echo smarty_modifier_truncate(smarty_modifier_unescape($this->_tpl_vars['lnk']['name']), 40); ?>
</a></li>
	<?php endforeach; endif; unset($_from); ?>
	</ul>
	<p class="float-right"><a class="cm-ajax text-button-edit" href="<?php echo fn_url("tools.cleanup_history"); ?>
" rev="last_edited_items"><?php echo fn_get_lang_var('cleanup_history', $this->getLanguage()); ?>
</a></p>
<?php else: ?>
	<p><?php echo fn_get_lang_var('no_items', $this->getLanguage()); ?>
</p>
<?php endif; ?>
<!--last_edited_items--></div>
<?php  ob_end_flush();  ?>