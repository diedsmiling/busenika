<?php /* Smarty version 2.6.18, created on 2014-09-22 22:43:52
         compiled from common_templates/tools.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'common_templates/tools.tpl', 3, false),array('modifier', 'replace', 'common_templates/tools.tpl', 7, false),)), $this); ?>
<?php  ob_start();  ?>
<<?php if ($this->_tpl_vars['no_link']): ?>span<?php else: ?>a<?php endif; ?> class="select-link cm-combo-on cm-combination <?php echo $this->_tpl_vars['class']; ?>
" id="sw_select_wrap_<?php echo $this->_tpl_vars['suffix']; ?>
"><?php echo smarty_modifier_default(@$this->_tpl_vars['link_text'], 'tools'); ?>
</<?php if ($this->_tpl_vars['no_link']): ?>span<?php else: ?>a<?php endif; ?>>

<div id="select_wrap_<?php echo $this->_tpl_vars['suffix']; ?>
" class="select-popup cm-popup-box hidden left">
	<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/icon_close.gif" width="13" height="13" border="0" alt="" class="close-icon no-margin cm-popup-switch" />
	<?php echo smarty_modifier_replace($this->_tpl_vars['tools_list'], "<ul>", "<ul class=\"cm-select-list\">"); ?>

</div><?php  ob_end_flush();  ?>