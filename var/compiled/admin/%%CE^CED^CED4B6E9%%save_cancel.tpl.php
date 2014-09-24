<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:12
         compiled from buttons/save_cancel.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_check_view_permissions', 'buttons/save_cancel.tpl', 12, false),array('modifier', 'default', 'buttons/save_cancel.tpl', 13, false),array('modifier', 'fn_url', 'buttons/save_cancel.tpl', 32, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('create','create_and_close','save','save_and_close','or','cancel','cancel'));
?>

<?php if ($this->_tpl_vars['create'] || $this->_tpl_vars['mode'] == 'add'): ?>
	<?php $this->assign('but_label', fn_get_lang_var('create', $this->getLanguage()), false); ?>
	<?php $this->assign('but_label2', fn_get_lang_var('create_and_close', $this->getLanguage()), false); ?>
<?php else: ?>
	<?php $this->assign('but_label', fn_get_lang_var('save', $this->getLanguage()), false); ?>
	<?php $this->assign('but_label2', fn_get_lang_var('save_and_close', $this->getLanguage()), false); ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['but_name']): ?><?php $this->assign('r', $this->_tpl_vars['but_name'], false); ?><?php else: ?><?php $this->assign('r', $this->_tpl_vars['but_href'], false); ?><?php endif; ?>
<?php if (fn_check_view_permissions($this->_tpl_vars['r'])): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => smarty_modifier_default(@$this->_tpl_vars['but_text'], @$this->_tpl_vars['but_label']),'but_onclick' => $this->_tpl_vars['but_onclick'],'but_role' => 'button_main','but_name' => $this->_tpl_vars['but_name'],'but_meta' => $this->_tpl_vars['but_meta'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php if (! $this->_tpl_vars['hide_second_button'] && $this->_tpl_vars['cancel_action'] != 'close'): ?>
	&nbsp;<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => $this->_tpl_vars['but_label2'],'but_role' => 'button_main','but_name' => $this->_tpl_vars['but_name'],'but_meta' => $this->_tpl_vars['but_meta'],'but_onclick' => "$(this).parents('form:first').append('<input type=\'hidden\' name=\'return_to_list\' value=\'Y\' />'); ".($this->_tpl_vars['but_onclick']),'allow_href' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
<?php else: ?>
	<?php $this->assign('skip_or', true, false); ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['extra']): ?>
	<?php echo $this->_tpl_vars['extra']; ?>

<?php endif; ?>

<?php if (( $this->_tpl_vars['cancel_action'] || $this->_tpl_vars['breadcrumbs'] ) && ! $this->_tpl_vars['skip_or']): ?>&nbsp;<?php echo fn_get_lang_var('or', $this->getLanguage()); ?>
&nbsp;&nbsp;<?php endif; ?>

<?php if ($this->_tpl_vars['cancel_action'] == 'close'): ?>
	<a class="cm-popup-switch cm-cancel tool-link"><?php echo fn_get_lang_var('cancel', $this->getLanguage()); ?>
</a>
<?php elseif ($this->_tpl_vars['breadcrumbs']): ?>
	<?php $_from = $this->_tpl_vars['breadcrumbs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['fe_b'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['fe_b']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['b']):
        $this->_foreach['fe_b']['iteration']++;
?>
	<?php if (($this->_foreach['fe_b']['iteration'] == $this->_foreach['fe_b']['total'])): ?>
	<a href="<?php echo fn_url($this->_tpl_vars['b']['link']); ?>
" class="underlined tool-link"><?php echo fn_get_lang_var('cancel', $this->getLanguage()); ?>
</a>
	<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>