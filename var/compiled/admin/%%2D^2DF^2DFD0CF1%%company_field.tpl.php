<?php /* Smarty version 2.6.18, created on 2014-09-15 23:39:45
         compiled from views/companies/components/company_field.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'views/companies/components/company_field.tpl', 12, false),array('modifier', 'defined', 'views/companies/components/company_field.tpl', 13, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('vendor','supplier'));
?>
<?php  ob_start();  ?>
<?php if (@PRODUCT_TYPE == 'MULTIVENDOR' || ( $this->_tpl_vars['settings']['Suppliers']['enable_suppliers'] == 'Y' && ( @CONTROLLER == 'products' || @CONTROLLER == 'shippings' ) )): ?>

<?php if (@PRODUCT_TYPE == 'MULTIVENDOR'): ?>
<?php $this->assign('lang_vendor_supplier', fn_get_lang_var('vendor', $this->getLanguage()), false); ?>
<?php else: ?>
<?php $this->assign('lang_vendor_supplier', fn_get_lang_var('supplier', $this->getLanguage()), false); ?>
<?php endif; ?>

<div class="form-field">
	<label for="<?php echo smarty_modifier_default(@$this->_tpl_vars['id'], 'company_id'); ?>
"><?php echo $this->_tpl_vars['lang_vendor_supplier']; ?>
:</label>
	<?php if (defined('COMPANY_ID')): ?>
		<?php echo $this->_tpl_vars['companies'][@COMPANY_ID]; ?>

		<input type="hidden" name="<?php echo $this->_tpl_vars['name']; ?>
" id="<?php echo smarty_modifier_default(@$this->_tpl_vars['id'], 'company_id'); ?>
" value="<?php echo @COMPANY_ID; ?>
">
	<?php else: ?>
	<select name="<?php echo $this->_tpl_vars['name']; ?>
" id="<?php echo smarty_modifier_default(@$this->_tpl_vars['id'], 'company_id'); ?>
" class="select-box">
	<?php $_from = $this->_tpl_vars['companies']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['company_id'] => $this->_tpl_vars['company']):
?>
		<option value="<?php echo $this->_tpl_vars['company_id']; ?>
" <?php if ($this->_tpl_vars['company_id'] == $this->_tpl_vars['selected']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['company']; ?>
</option>
	<?php endforeach; endif; unset($_from); ?>
	</select>
	<?php endif; ?>
</div>

<?php endif; ?><?php  ob_end_flush();  ?>