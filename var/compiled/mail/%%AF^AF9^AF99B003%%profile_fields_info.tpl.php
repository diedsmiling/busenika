<?php /* Smarty version 2.6.18, created on 2014-09-24 21:53:09
         compiled from profiles/profile_fields_info.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'profiles/profile_fields_info.tpl', 4, false),array('modifier', 'strpos', 'profiles/profile_fields_info.tpl', 18, false),array('modifier', 'date_format', 'profiles/profile_fields_info.tpl', 24, false),)), $this); ?>

<tr>
	<td colspan="2" class="form-title"><?php echo smarty_modifier_default(@$this->_tpl_vars['title'], "&nbsp;"); ?>
<hr size="1" noshade="noshade" /></td>
</tr>
<?php $_from = $this->_tpl_vars['fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['field']):
?>
<?php if ($this->_tpl_vars['field']['field_name']): ?>
<?php $this->assign('data_id', $this->_tpl_vars['field']['field_name'], false); ?>
<?php $this->assign('value', $this->_tpl_vars['user_data'][$this->_tpl_vars['data_id']], false); ?>
<?php else: ?>
<?php $this->assign('data_id', $this->_tpl_vars['field']['field_id'], false); ?>
<?php $this->assign('value', $this->_tpl_vars['user_data']['fields'][$this->_tpl_vars['data_id']], false); ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['value']): ?>
<tr>
	<td class="form-field-caption" width="30%" nowrap="nowrap"><?php echo $this->_tpl_vars['field']['description']; ?>
:&nbsp;</td>
	<td>
		<?php if (strpos('AOL', $this->_tpl_vars['field']['field_type']) !== false): ?> 			<?php $this->assign('title', ($this->_tpl_vars['data_id'])."_descr", false); ?>
			<?php echo $this->_tpl_vars['user_data'][$this->_tpl_vars['title']]; ?>

		<?php elseif ($this->_tpl_vars['field']['field_type'] == 'C'): ?>  			<?php if ($this->_tpl_vars['value'] == 'Y'): ?><?php echo fn_get_lang_var('yes', $this->getLanguage()); ?>
<?php else: ?><?php echo fn_get_lang_var('no', $this->getLanguage()); ?>
<?php endif; ?>
		<?php elseif ($this->_tpl_vars['field']['field_type'] == 'D'): ?>  			<?php echo smarty_modifier_date_format($this->_tpl_vars['value'], $this->_tpl_vars['settings']['Appearance']['date_format']); ?>

		<?php elseif (strpos('RS', $this->_tpl_vars['field']['field_type']) !== false): ?>  			<?php echo $this->_tpl_vars['field']['values'][$this->_tpl_vars['value']]; ?>

		<?php else: ?>  			<?php echo smarty_modifier_default(@$this->_tpl_vars['value'], "-"); ?>

		<?php endif; ?>
	</td>
</tr>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>