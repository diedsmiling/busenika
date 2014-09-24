<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:16
         compiled from common_templates/select_usergroups.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'explode', 'common_templates/select_usergroups.tpl', 3, false),array('modifier', 'fn_get_default_usergroups', 'common_templates/select_usergroups.tpl', 7, false),array('modifier', 'in_array', 'common_templates/select_usergroups.tpl', 9, false),array('modifier', 'count', 'common_templates/select_usergroups.tpl', 9, false),array('modifier', 'escape', 'common_templates/select_usergroups.tpl', 10, false),array('modifier', 'defined', 'common_templates/select_usergroups.tpl', 21, false),array('modifier', 'define', 'common_templates/select_usergroups.tpl', 22, false),)), $this); ?>
<?php  ob_start();  ?><?php if ($this->_tpl_vars['usergroup_ids'] !== ""): ?>
<?php $this->assign('ug_ids', explode(",", $this->_tpl_vars['usergroup_ids']), false); ?>
<?php endif; ?>

<input type="hidden" name="<?php echo $this->_tpl_vars['name']; ?>
" value="" <?php echo $this->_tpl_vars['input_extra']; ?>
/>
<?php $_from = fn_get_default_usergroups(""); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['usergroup']):
?>
	<?php if ($this->_tpl_vars['list_mode']): ?><p><?php endif; ?>
	<input type="checkbox" name="<?php echo $this->_tpl_vars['name']; ?>
[]" id="<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['usergroup']['usergroup_id']; ?>
"<?php if (( $this->_tpl_vars['ug_ids'] && smarty_modifier_in_array($this->_tpl_vars['usergroup']['usergroup_id'], $this->_tpl_vars['ug_ids']) ) || ( ! $this->_tpl_vars['ug_ids'] && $this->_tpl_vars['usergroup']['usergroup_id'] == @USERGROUP_ALL )): ?> checked="checked"<?php endif; ?> class="checkbox" value="<?php echo $this->_tpl_vars['usergroup']['usergroup_id']; ?>
" <?php echo $this->_tpl_vars['input_extra']; ?>
<?php if (( ! $this->_tpl_vars['ug_ids'] || ( $this->_tpl_vars['ug_ids'] && count($this->_tpl_vars['ug_ids']) == 1 && smarty_modifier_in_array($this->_tpl_vars['usergroup']['usergroup_id'], $this->_tpl_vars['ug_ids']) ) ) && $this->_tpl_vars['usergroup']['usergroup_id'] == @USERGROUP_ALL): ?> disabled="disabled"<?php endif; ?> onclick="fn_switch_default_box(this, '<?php echo $this->_tpl_vars['id']; ?>
', <?php echo @USERGROUP_ALL; ?>
);" />
	<label for="<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['usergroup']['usergroup_id']; ?>
"><?php echo smarty_modifier_escape($this->_tpl_vars['usergroup']['usergroup']); ?>
</label>
	<?php if ($this->_tpl_vars['list_mode']): ?></p><?php endif; ?>
<?php endforeach; endif; unset($_from); ?>

<?php $_from = $this->_tpl_vars['usergroups']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['usergroup']):
?>
	<?php if ($this->_tpl_vars['list_mode']): ?><p><?php endif; ?>
	<input type="checkbox" name="<?php echo $this->_tpl_vars['name']; ?>
[]" id="<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['usergroup']['usergroup_id']; ?>
"<?php if ($this->_tpl_vars['ug_ids'] && smarty_modifier_in_array($this->_tpl_vars['usergroup']['usergroup_id'], $this->_tpl_vars['ug_ids'])): ?> checked="checked"<?php endif; ?> class="checkbox" value="<?php echo $this->_tpl_vars['usergroup']['usergroup_id']; ?>
" <?php echo $this->_tpl_vars['input_extra']; ?>
 onclick="fn_switch_default_box(this, '<?php echo $this->_tpl_vars['id']; ?>
', <?php echo @USERGROUP_ALL; ?>
);" />
	<label for="<?php echo $this->_tpl_vars['id']; ?>
_<?php echo $this->_tpl_vars['usergroup']['usergroup_id']; ?>
"><?php echo smarty_modifier_escape($this->_tpl_vars['usergroup']['usergroup']); ?>
</label>
	<?php if ($this->_tpl_vars['list_mode']): ?></p><?php endif; ?>
<?php endforeach; endif; unset($_from); ?>

<?php if (! defined('SMARTY_USERGROUPS_LOADED')): ?>
<?php $this->assign('tmp', define('SMARTY_USERGROUPS_LOADED', true), false); ?>
<script type="text/javascript">
	//<![CDATA[
	<?php echo '
	function fn_switch_default_box(holder, prefix, default_id)
	{
		var default_box = $(\'#\' + prefix + \'_\' + default_id);
		var checked_items = $(\'input[id^=\' + prefix + \'_].checkbox:checked\').not(default_box).length + holder.checked ? 1 : 0;
		if (checked_items == 0) {
			default_box.attr(\'disabled\', \'disabled\');
			default_box.attr(\'checked\', \'checked\');
		} else {
			default_box.removeAttr(\'disabled\');
		}
	}
	'; ?>

	//]]>
</script>
<?php endif; ?>
<?php  ob_end_flush();  ?>