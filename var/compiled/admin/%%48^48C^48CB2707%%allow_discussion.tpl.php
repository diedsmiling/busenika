<?php /* Smarty version 2.6.18, created on 2014-09-15 23:39:45
         compiled from addons/discussion/views/discussion_manager/components/allow_discussion.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_get_discussion', 'addons/discussion/views/discussion_manager/components/allow_discussion.tpl', 5, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('communication','and','rating','communication','rating','disabled'));
?>
<?php  ob_start();  ?>
<div class="form-field">
	<label for="discussion_type"><?php echo $this->_tpl_vars['title']; ?>
:</label>
	<?php $this->assign('discussion', fn_get_discussion($this->_tpl_vars['object_id'], $this->_tpl_vars['object_type']), false); ?>
	<select name="<?php echo $this->_tpl_vars['prefix']; ?>
[discussion_type]" id="discussion_type">
		<option <?php if ($this->_tpl_vars['discussion']['type'] == 'B'): ?>selected="selected"<?php endif; ?> value="B"><?php echo fn_get_lang_var('communication', $this->getLanguage()); ?>
 <?php echo fn_get_lang_var('and', $this->getLanguage()); ?>
 <?php echo fn_get_lang_var('rating', $this->getLanguage()); ?>
</option>
		<option <?php if ($this->_tpl_vars['discussion']['type'] == 'C'): ?>selected="selected"<?php endif; ?> value="C"><?php echo fn_get_lang_var('communication', $this->getLanguage()); ?>
</option>
		<option <?php if ($this->_tpl_vars['discussion']['type'] == 'R'): ?>selected="selected"<?php endif; ?> value="R"><?php echo fn_get_lang_var('rating', $this->getLanguage()); ?>
</option>
		<option <?php if ($this->_tpl_vars['discussion']['type'] == 'D' || ! $this->_tpl_vars['discussion']): ?>selected="selected"<?php endif; ?> value="D"><?php echo fn_get_lang_var('disabled', $this->getLanguage()); ?>
</option>
	</select>
</div>
<?php  ob_end_flush();  ?>