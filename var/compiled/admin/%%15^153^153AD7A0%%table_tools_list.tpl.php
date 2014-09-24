<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:12
         compiled from common_templates/table_tools_list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_check_view_permissions', 'common_templates/table_tools_list.tpl', 4, false),array('modifier', 'fn_url', 'common_templates/table_tools_list.tpl', 8, false),array('modifier', 'default', 'common_templates/table_tools_list.tpl', 8, false),array('modifier', 'strpos', 'common_templates/table_tools_list.tpl', 11, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('edit','more'));
?>

<?php if ($this->_tpl_vars['popup']): ?>
	<?php if (fn_check_view_permissions($this->_tpl_vars['href'])): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['id'],'text' => $this->_tpl_vars['text'],'link_text' => $this->_tpl_vars['link_text'],'act' => $this->_tpl_vars['act'],'href' => $this->_tpl_vars['href'],'link_class' => $this->_tpl_vars['link_class'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
<?php elseif ($this->_tpl_vars['href']): ?>
	<a class="tool-link" href="<?php echo fn_url($this->_tpl_vars['href']); ?>
"><?php echo smarty_modifier_default(@$this->_tpl_vars['link_text'], fn_get_lang_var('edit', $this->getLanguage())); ?>
</a>
<?php endif; ?>
<?php if (fn_check_view_permissions($this->_tpl_vars['tools_list'])): ?>
	<?php if (strpos($this->_tpl_vars['tools_list'], "<li")): ?><?php if ($this->_tpl_vars['href']): ?>&nbsp;&nbsp;|<?php elseif ($this->_tpl_vars['separate']): ?>|<?php endif; ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('prefix' => $this->_tpl_vars['prefix'],'hide_actions' => true,'tools_list' => "<ul>".($this->_tpl_vars['tools_list'])."</ul>",'display' => 'inline','link_text' => fn_get_lang_var('more', $this->getLanguage()),'link_meta' => 'lowercase')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
<?php endif; ?>