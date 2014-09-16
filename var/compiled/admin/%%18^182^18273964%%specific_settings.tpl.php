<?php /* Smarty version 2.6.18, created on 2014-09-15 23:43:08
         compiled from views/block_manager/specific_settings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'views/block_manager/specific_settings.tpl', 3, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('specific_settings'));
?>

<?php if ($this->_tpl_vars['spec_settings'] && ( ( count($this->_tpl_vars['spec_settings']) > 1 && $this->_tpl_vars['spec_settings']['settings'] ) || ( ! $this->_tpl_vars['spec_settings']['settings'] ) )): ?>
<div id="toggle_<?php echo $this->_tpl_vars['s_set_id']; ?>
">
<div class="specific-settings float-left" id="container_<?php echo $this->_tpl_vars['s_set_id']; ?>
">
<a id="sw_additional_<?php echo $this->_tpl_vars['s_set_id']; ?>
" class="cm-combo-on|off cm-combination"><?php echo fn_get_lang_var('specific_settings', $this->getLanguage()); ?>
</a>
<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/section_collapsed.gif" width="7" height="9" border="0" alt="" id="on_additional_<?php echo $this->_tpl_vars['s_set_id']; ?>
" class="cm-combination" />
<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/section_expanded.gif" width="7" height="9" border="0" alt="" id="off_additional_<?php echo $this->_tpl_vars['s_set_id']; ?>
" class="cm-combination hidden" />
</div>

<div class="hidden" id="additional_<?php echo $this->_tpl_vars['s_set_id']; ?>
">
<?php $_from = $this->_tpl_vars['spec_settings']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['set_name'] => $this->_tpl_vars['_option']):
?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/block_manager/components/setting_element.tpl", 'smarty_include_vars' => array('set_name' => $this->_tpl_vars['set_name'],'option' => $this->_tpl_vars['_option'],'block' => $this->_tpl_vars['block'],'set_id' => $this->_tpl_vars['s_set_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endforeach; endif; unset($_from); ?>
</div>
<!--toggle_<?php echo $this->_tpl_vars['s_set_id']; ?>
--></div>
<?php else: ?>
<div id="toggle_<?php echo $this->_tpl_vars['s_set_id']; ?>
"><!--toggle_<?php echo $this->_tpl_vars['s_set_id']; ?>
--></div>
<?php endif; ?>