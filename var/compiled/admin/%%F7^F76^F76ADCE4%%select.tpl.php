<?php /* Smarty version 2.6.18, created on 2014-09-15 23:39:45
         compiled from views/localizations/components/select.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_explode_localizations', 'views/localizations/components/select.tpl', 2, false),array('modifier', 'default', 'views/localizations/components/select.tpl', 10, false),array('modifier', 'escape', 'views/localizations/components/select.tpl', 12, false),array('function', 'script', 'views/localizations/components/select.tpl', 20, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('localization','multiple_selectbox_notice'));
?>
<?php  ob_start();  ?><?php $this->assign('data', fn_explode_localizations($this->_tpl_vars['data_from']), false); ?>

<?php if ($this->_tpl_vars['localizations']): ?>
<?php if (! $this->_tpl_vars['no_div']): ?>
<div class="form-field">
	<label for="<?php echo $this->_tpl_vars['id']; ?>
"><?php echo fn_get_lang_var('localization', $this->getLanguage()); ?>
:</label>
<?php endif; ?>
		<?php if (! $this->_tpl_vars['disabled']): ?><input type="hidden" name="<?php echo $this->_tpl_vars['data_name']; ?>
" value="" /><?php endif; ?>
		<select	name="<?php echo $this->_tpl_vars['data_name']; ?>
[]" multiple="multiple" size="3" id="<?php echo smarty_modifier_default(@$this->_tpl_vars['id'], @$this->_tpl_vars['data_name']); ?>
" class="<?php if ($this->_tpl_vars['disabled']): ?>elm-disabled<?php else: ?>input-text<?php endif; ?>" <?php if ($this->_tpl_vars['disabled']): ?>disabled="disabled"<?php endif; ?>>
			<?php $_from = $this->_tpl_vars['localizations']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['loc']):
?>
			<option	value="<?php echo $this->_tpl_vars['loc']['localization_id']; ?>
" <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['p_loc']):
?><?php if ($this->_tpl_vars['p_loc'] == $this->_tpl_vars['loc']['localization_id']): ?>selected="selected"<?php endif; ?><?php endforeach; endif; unset($_from); ?>><?php echo smarty_modifier_escape($this->_tpl_vars['loc']['localization']); ?>
</option>
			<?php endforeach; endif; unset($_from); ?>
		</select>
<?php if (! $this->_tpl_vars['no_div']): ?>
<?php echo fn_get_lang_var('multiple_selectbox_notice', $this->getLanguage()); ?>

</div>
<?php endif; ?>
<?php endif; ?>
<?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>
<?php  ob_end_flush();  ?>