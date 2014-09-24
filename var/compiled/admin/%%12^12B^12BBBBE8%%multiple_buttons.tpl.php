<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:16
         compiled from buttons/multiple_buttons.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'buttons/multiple_buttons.tpl', 3, false),array('modifier', 'default', 'buttons/multiple_buttons.tpl', 5, false),)), $this); ?>

<?php echo smarty_function_script(array('src' => "js/node_cloning.js"), $this);?>


<?php $this->assign('tag_level', smarty_modifier_default(@$this->_tpl_vars['tag_level'], '1'), false); ?>
<?php echo ''; ?><?php if ($this->_tpl_vars['only_delete'] != 'Y'): ?><?php echo '<span class="nowrap">'; ?><?php if (! $this->_tpl_vars['hide_add']): ?><?php echo ''; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/add_empty_item.tpl", 'smarty_include_vars' => array('but_onclick' => "$('#box_' + this.id).cloneNode(".($this->_tpl_vars['tag_level'])."); ".($this->_tpl_vars['on_add']),'item_id' => $this->_tpl_vars['item_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo '&nbsp;'; ?><?php endif; ?><?php echo ''; ?><?php if (! $this->_tpl_vars['hide_clone']): ?><?php echo ''; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/clone_item.tpl", 'smarty_include_vars' => array('but_onclick' => "$('#box_' + this.id).cloneNode(".($this->_tpl_vars['tag_level']).", true);",'item_id' => $this->_tpl_vars['item_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo '&nbsp;'; ?><?php endif; ?><?php echo ''; ?><?php endif; ?><?php echo ''; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/remove_item.tpl", 'smarty_include_vars' => array('only_delete' => $this->_tpl_vars['only_delete'],'but_class' => "cm-delete-row")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo '&nbsp;</span>'; ?>